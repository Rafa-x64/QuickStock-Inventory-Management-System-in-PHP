<?php
require_once(__DIR__ . "/../index.functions.php");

function obtenerColores()
{
    $conn = conectar_base_datos();

    $sql = "SELECT id_color, nombre, activo FROM core.color ORDER BY id_color";

    pg_prepare($conn, "obtener_colores_api", $sql);
    $resultado = pg_execute($conn, "obtener_colores_api", []);

    if (!$resultado) {
        return ["error" => "No se pudieron obtener los colores"];
    }

    $colores = pg_fetch_all($resultado);

    if ($colores === false) {
        return ["colores" => []];
    }

    return ["colores" => $colores];
}

function seleccionarColorPorId($id_color)
{
    if ($id_color == null || $id_color == '') {
        return ["color" => null];
    }

    $conn = conectar_base_datos();
    pg_prepare($conn, "seleccionar_color_por_id", "SELECT id_color, nombre, activo FROM core.color WHERE id_color = $1");
    $resultado = pg_execute($conn, "seleccionar_color_por_id", [$id_color]);

    if (!$resultado) {
        return ["error" => "No se pudo obtener el color"];
    }

    $color = pg_fetch_assoc($resultado);

    return ["color" => $color];
}
