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

        // Si NO existe, verificamos la hora, PERO si el usuario pide "traer una aunque no sean las 4pm" 
        // podemos ser flexibles: Si no hay NADA de hoy, intentamos actualizar de todas formas (Lazy Force), 
        // o mantenemos la regla de hora.
        // El usuario dijo: "al menos trae una desde la api del dia de hoy aunque no sean las 4pm"
        // Así que quitamos la restricción de hora SI no hay datos de hoy.

        /* 
        $hora = (int)date('H');
        $minuto = (int)date('i');
        if ($hora < self::HORA_ACTUALIZACION || ($hora == self::HORA_ACTUALIZACION && $minuto < self::MINUTO_ACTUALIZACION)) {
             return ["status" => "skip", "msg" => "Aún no es la hora de actualización."];
        }
        */

        // 3. Ejecutar Sincronización
        return self::sincronizarTasasApi();
    }

    public static function sincronizarTasasApi()
    {
        $tasasExternas = ServicioApiExterna::obtenerTasasExternas();

        if (!$tasasExternas) {
            return ["status" => "error", "msg" => "Fallo al conectar con API externa."];
        }

        // Obtener monedas activas del sistema para saber qué buscar
        $monedasSistema = Moneda::obtenerTodas();
        $actualizadas = 0;

        foreach ($monedasSistema as $m) {
            $codigo = $m['codigo']; // USD, VES, EUR

            // La API base es USD.
            if ($codigo == 'USD') continue;

            if (isset($tasasExternas[$codigo])) {
                $valor = $tasasExternas[$codigo];
                if (self::registrarTasa($m['id_moneda'], $valor, 'API')) {
                    $actualizadas++;
                }
            }
        }

        return ["status" => "success", "msg" => "Se actualizaron $actualizadas monedas."];
    }

    public static function obtenerHistorial($limit = 50)
    {
        $conn = parent::conectar_base_datos();
        $sql = "
            SELECT t.id_tasa, m.nombre as moneda, m.codigo, m.simbolo, t.tasa, t.fecha, t.origen 
            FROM finanzas.tasa_cambio t
            JOIN finanzas.moneda m ON m.id_moneda = t.id_moneda
            WHERE t.activo = true
            ORDER BY t.fecha DESC, t.id_tasa DESC
            LIMIT $1
        ";
        $stmt = "getHistorial_" . uniqid();
        pg_prepare($conn, $stmt, $sql);
        $res = pg_execute($conn, $stmt, [$limit]);

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
