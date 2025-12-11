<?php
// Incluir el modelo. Ajustar rutas según estructura API (Regla 2: Rutas absolutas)
require_once __DIR__ . "/../../../model/finanzas.moneda.php";
require_once __DIR__ . "/../../../model/finanzas.tasa.php";
require_once __DIR__ . "/../index.functions.php";

function obtener_resumen_tasas()
{
    // Obtener todas las monedas activas y sus tasas actuales
    // La sincronización con API se hace SOLO al presionar el botón "Sincronizar Tasas (API)"
    $monedas = Moneda::obtenerTodas();
    $data = [];

    $conn = conectar_base_datos();

    foreach ($monedas as $m) {
        // Obtener ultima tasa
        $sql = "SELECT tasa, fecha, origen FROM finanzas.tasa_cambio 
                WHERE id_moneda = $1 AND activo = true 
                ORDER BY fecha DESC, id_tasa DESC LIMIT 1";
        $n = "apiGetRate_" . uniqid();
        pg_prepare($conn, $n, $sql);
        $res = pg_execute($conn, $n, [$m['id_moneda']]);
        $row = pg_fetch_assoc($res);

        $data[] = [
            'id_moneda' => $m['id_moneda'],
            'nombre' => $m['nombre'],
            'codigo' => $m['codigo'],
            'simbolo' => $m['simbolo'],
            'tasa' => $row ? $row['tasa'] : (($m['codigo'] == 'USD') ? 1 : null),
            'fecha' => $row ? $row['fecha'] : null,
            'origen' => $row ? $row['origen'] : null
        ];
    }
    return ["data" => $data];
}

function sincronizar_api()
{
    $resultado = TasaCambio::sincronizarTasasApi();
    return $resultado; // {status, msg}
}

function guardar_tasa_manual($id_moneda, $valor)
{
    if (!$id_moneda || !$valor) {
        return ["error" => "Faltan datos (id_moneda, valor)"];
    }

    if (TasaCambio::registrarTasa($id_moneda, $valor, 'Manual')) {
        return ["status" => "success", "msg" => "Tasa registrada correctamente."];
    } else {
        return ["error" => "Error al registrar tasa en BD."];
    }
}

function obtener_historial($limit = 50)
{
    $historia = TasaCambio::obtenerHistorial($limit);
    return ["filas" => $historia];
}
