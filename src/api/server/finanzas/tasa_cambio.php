<?php
require_once __DIR__ . "/../index.functions.php";

function obtenerTasasCambioActivas()
{
    $conn = conectar_base_datos();
    // Obtener la tasa más reciente para cada moneda activa
    $sql = "
        SELECT DISTINCT ON (m.id_moneda)
            m.id_moneda,
            m.nombre,
            m.codigo,
            t.tasa,
            t.fecha,
            t.origen
        FROM finanzas.moneda m
        JOIN finanzas.tasa_cambio t ON t.id_moneda = m.id_moneda
        WHERE m.activo = true AND t.activo = true
        ORDER BY m.id_moneda, t.fecha DESC
    ";

    pg_prepare($conn, "get_tasas_activas", $sql);
    $result = pg_execute($conn, "get_tasas_activas", []);

    if ($result) {
        $tasas = pg_fetch_all($result);
        return ["status" => true, "tasas" => $tasas ?: []];
    } else {
        return ["status" => false, "mensaje" => "Error al obtener tasas"];
    }
}
