<?php
include_once __DIR__ . "/mainModel.php";
include_once __DIR__ . "/../library/notificador.php";
include_once __DIR__ . "/../api/server/finanzas/servicio_api_externa.php";

class TasaCambio extends mainModel
{
    // Hora de actualización automática (4:02 PM)
    const HORA_ACTUALIZACION = 16;
    const MINUTO_ACTUALIZACION = 2;

    public static function registrarTasa($id_moneda, $valor, $origen)
    {
        $conn = parent::conectar_base_datos();

        // 1. Obtener tasa actual para comparación (Alertas)
        $sqlLast = "SELECT tasa FROM finanzas.tasa_cambio WHERE id_moneda = $1 AND activo = true ORDER BY fecha DESC LIMIT 1";
        $stmtLast = "getLastTasa_" . uniqid();
        pg_prepare($conn, $stmtLast, $sqlLast);
        $resLast = pg_execute($conn, $stmtLast, [$id_moneda]);
        $lastTasa = ($resLast && pg_num_rows($resLast) > 0) ? pg_fetch_result($resLast, 0, 0) : null;

        // 2. Insertar nueva tasa
        $sql = "INSERT INTO finanzas.tasa_cambio (id_moneda, tasa, fecha, activo, origen) VALUES ($1, $2, CURRENT_DATE, true, $3)";
        $stmt = "insertTasa_" . uniqid();
        pg_prepare($conn, $stmt, $sql);
        $result = pg_execute($conn, $stmt, [$id_moneda, $valor, $origen]);

        if ($result) {
            // SIEMPRE Notificar cambio si se registró correctamente, como pidió el usuario "cuando halla un cambio"
            // Incluso si es el primer valor.
            $monedaInfo = self::obtenerMonedaPorId($id_moneda);
            $nombreMoneda = $monedaInfo ? $monedaInfo['nombre'] : "ID: $id_moneda";

            // Solo notificar si cambió el valor
            if ($lastTasa != $valor) {
                Notificador::enviarAlertaCambioTasa($nombreMoneda, $lastTasa ?? 'N/A', $valor, $origen);
            }
        }

        return (bool)$result;
    }

    public static function verificarActualizacionDiaria()
    {
        // 1. Verificar si ya se actualizó hoy via API (Primero verificamos existencia, luego hora)
        $conn = parent::conectar_base_datos();
        $hoy = date('Y-m-d');

        $sqlCheck = "SELECT COUNT(*) FROM finanzas.tasa_cambio WHERE origen = 'API' AND fecha = $1";
        $stmtCheck = "checkDailyExample_" . uniqid();
        pg_prepare($conn, $stmtCheck, $sqlCheck);
        $resCheck = pg_execute($conn, $stmtCheck, [$hoy]);
        $count = pg_fetch_result($resCheck, 0, 0);

        // Si YA existe actualización de hoy, terminamos.
        if ($count > 0) {
            return ["status" => "skip", "msg" => "Ya se actualizó hoy."];
        }

        // Si NO existe, intentamos actualizar de todas formas (Lazy Force)
        return self::sincronizarTasasApi();
    }

    public static function sincronizarTasasApi()
    {
        $tasasExternas = ServicioApiExterna::obtenerTasasExternas();

        if (!$tasasExternas) {
            return ["status" => "error", "msg" => "Fallo al conectar con API externa."];
        }

        $monedasSistema = Moneda::obtenerTodas();
        $actualizadas = 0;
        $conn = parent::conectar_base_datos(); // Necesario para verificaciones

        // 1. Obtener la tasa base del Dólar en Bolívares directamente de la API
        // La API retorna "USD" como base, por lo que $tasasExternas['VES'] es cuantos Bs vale 1 USD.
        $baseBs = $tasasExternas['VES'] ?? 1;

        foreach ($monedasSistema as $m) {
            $codigo = $m['codigo'];
            if ($codigo == 'VES') continue; // VES no se toca directo (es base derivada)

            // PROTECCION MANUAL:
            // "logicamente solo se deberia actualizar el $ y el euro al darle a boton de actualizar tasas
            // si no obviamente se pierde lo que el usuario registra manualmente"
            // Verificamos si YA existe una tasa MANUAL para hoy.
            // Usamos ILIKE para ignorar mayusculas/minusculas por seguridad

            $sqlCheck = "SELECT COUNT(*) FROM finanzas.tasa_cambio WHERE id_moneda = $1 AND fecha = CURRENT_DATE AND origen ILIKE 'Manual'";
            $stmtN = "checkMan_" . $m['id_moneda'] . "_" . uniqid();
            pg_prepare($conn, $stmtN, $sqlCheck);
            $resCheck = pg_execute($conn, $stmtN, [$m['id_moneda']]);
            $isManualToday = (pg_fetch_result($resCheck, 0, 0) > 0);

            if ($isManualToday) {
                // Si el usuario ya puso una manual hoy, la API NO la sobreescribe.
                continue;
            }

            $valor = null;
            if ($codigo == 'USD') {
                // Si la moneda es Dólar, su valor en Bs es la tasa base que obtuvimos.
                $valor = $baseBs;
            } elseif (isset($tasasExternas[$codigo])) {
                // CALCULO DE TASA CRUZADA (Cross Rate) para mostrar valor en Bs
                // API retorna: 1 USD = X EUR (ej: 0.86)
                // Nosotros queremos: 1 EUR = Y Bs
                // Formula: tasaVES / tasaAPI = (Bs/USD) / (EUR/USD) = Bs/EUR

                $apiRate = $tasasExternas[$codigo];
                if ($apiRate > 0) {
                    $valor = $baseBs / $apiRate;
                }
            }

            if ($valor !== null) {
                // EVITAR DUPLICADOS "el dolar se actualiza muchas veces"
                // Verificar si la ULTIMA tasa registrada para esta moneda es de HOY y tiene el MISMO valor.
                $sqlDup = "SELECT tasa FROM finanzas.tasa_cambio WHERE id_moneda = $1 AND fecha = CURRENT_DATE ORDER BY id_tasa DESC LIMIT 1";
                $stmtDup = "checkDup_" . $m['id_moneda'] . "_" . uniqid();
                pg_prepare($conn, $stmtDup, $sqlDup);
                $resDup = pg_execute($conn, $stmtDup, [$m['id_moneda']]);

                if ($resDup && pg_num_rows($resDup) > 0) {
                    $lastValToday = pg_fetch_result($resDup, 0, 0);
                    // Si el valor es casí idéntico (float comparison), no insertamos.
                    // Usamos una pequeña tolerancia epsilon o comparación directa si son strings de BD.
                    if (abs($lastValToday - $valor) < 0.00001) {
                        $actualizadas++; // Contamos como "actualizada" (checked)
                        continue;
                    }
                }

                if (self::registrarTasa($m['id_moneda'], $valor, 'API')) {
                    $actualizadas++;
                }
            }
        }

        return ["status" => "success", "msg" => "Tasas sincronizadas correctamente."];
    }

    public static function obtenerHistorial($limit = 50, $offset = 0)
    {
        $conn = parent::conectar_base_datos();
        $sql = "
            SELECT t.id_tasa, m.nombre as moneda, m.codigo, m.simbolo, t.tasa, t.fecha, t.origen 
            FROM finanzas.tasa_cambio t
            JOIN finanzas.moneda m ON m.id_moneda = t.id_moneda
            WHERE t.activo = true
            ORDER BY t.fecha DESC, t.id_tasa DESC
            LIMIT $1 OFFSET $2
        ";
        $stmt = "getHistorial_" . uniqid();
        pg_prepare($conn, $stmt, $sql);
        $res = pg_execute($conn, $stmt, [$limit, $offset]);

        return pg_fetch_all($res) ?: [];
    }

    // Helper privado
    private static function obtenerMonedaPorId($id)
    {
        $conn = parent::conectar_base_datos();
        $sql = "SELECT * FROM finanzas.moneda WHERE id_moneda = $1";
        $n = "getMonInfo_" . uniqid();
        pg_prepare($conn, $n, $sql);
        $r = pg_execute($conn, $n, [$id]);
        return pg_fetch_assoc($r);
    }
}
