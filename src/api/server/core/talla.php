<?php
require_once(__DIR__ . "/../index.functions.php");

function obtenerTallas()
{
    $conn = conectar_base_datos();

    $sql = "SELECT id_talla, rango_talla, activo FROM core.talla ORDER BY id_talla";

    pg_prepare($conn, "obtener_tallas_api", $sql);
    $resultado = pg_execute($conn, "obtener_tallas_api", []);

    if (!$resultado) {
        return ["error" => "No se pudieron obtener las tallas"];
    }

    $tallas = pg_fetch_all($resultado);

    if ($tallas === false) {
        return ["tallas" => []];
    }

    return ["tallas" => $tallas];
}

function seleccionarTallaPorId($id_talla)
{
    if ($id_talla == null || $id_talla == '') {
        return ["talla" => null];
    }

    $conn = conectar_base_datos();
    pg_prepare($conn, "seleccionar_talla_por_id", "SELECT id_talla, rango_talla, activo FROM core.talla WHERE id_talla = $1");
    $resultado = pg_execute($conn, "seleccionar_talla_por_id", [$id_talla]);

    if (!$resultado) {
        return ["error" => "No se pudo obtener la talla"];
    }

    $talla = pg_fetch_assoc($resultado);

    return ["talla" => $talla];
}
