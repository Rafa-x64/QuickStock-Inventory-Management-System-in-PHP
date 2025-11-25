<?php
function obtenerMonedas()
{
    $conn = conectar_base_datos();

    $sql = "SELECT id_moneda, nombre, activo, codigo
            FROM finanzas.moneda
            ORDER BY id_moneda";

    // Preparar y ejecutar
    pg_prepare($conn, "obtener_monedas", $sql);
    $resultado = pg_execute($conn, "obtener_monedas", []);

    if (!$resultado) {
        return ["error" => "No se pudieron obtener las monedas"];
    }

    $monedas = pg_fetch_all($resultado);

    if ($monedas === false) {
        return ["monedas" => []];
    }

    return ["monedas" => $monedas];
}
?>