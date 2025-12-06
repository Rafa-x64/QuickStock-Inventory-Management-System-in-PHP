<?php
require_once(__DIR__ . "/../index.functions.php");
require_once(__DIR__ . "/../../../model/finanzas.moneda.php");

function obtenerTodasMonedas()
{
    // Usamos el modelo existente
    $monedas = Moneda::obtenerTodas();
    return ["filas" => $monedas];
}

function crearMoneda($datos)
{
    if (!isset($datos['nombre']) || !isset($datos['codigo']) || !isset($datos['simbolo'])) {
        return ["error" => "Faltan datos obligatorios"];
    }

    // Normalizar
    $nombre = trim($datos['nombre']);
    $codigo = strtoupper(trim($datos['codigo']));
    $simbolo = trim($datos['simbolo']);
    $activo = isset($datos['activo']) ? $datos['activo'] : true;

    // Validación básica
    if (empty($nombre) || empty($codigo) || empty($simbolo)) {
        return ["error" => "Campos vacíos no permitidos"];
    }

    $resultado = Moneda::crear($nombre, $codigo, $simbolo, $activo);

    if ($resultado === true) {
        return ["status" => "success", "msg" => "Moneda creada exitosamente"];
    } else {
        return ["error" => "Error al crear moneda. Verifique que no exista ya."];
    }
}

function editarMoneda($datos)
{
    if (!isset($datos['id_moneda'])) {
        return ["error" => "Falta ID moneda"];
    }

    // Si viene nombre, codigo, etc, se actualiza. 
    // Por ahora asumimos que el array $_POST (o json body) trae todo lo necesario.
    // El modelo Moneda::editar requiere todos los campos.

    // NOTA: Para este endpoint simplificado, asumiremos que el cliente envia todos los datos.
    // Si queremos hacer patch parcial, necesitamos lógica extra.
    // Usaremos el metodo editar del modelo.

    $id = $datos['id_moneda'];
    $nombre = $datos['nombre'] ?? '';
    $codigo = $datos['codigo'] ?? '';
    $simbolo = $datos['simbolo'] ?? '';
    $activo = $datos['activo'] ?? true;

    // OJO: El modelo `editar` de Moneda (src/model/finanzas.moneda.php) espera:
    // editar($id_moneda, $nombre, $codigo, $simbolo, $activo)

    // Necesitamos asegurar que si algun campo falta, no lo borramos accidentalmente.
    // PERO, en una API REST, un PUT suele reemplazar. 
    // Vamos a asumir que el cliente envía los datos completos para editar.

    $resultado = Moneda::editar($id, $nombre, $codigo, $simbolo, $activo);

    if ($resultado === true) {
        return ["status" => "success", "msg" => "Moneda actualizada"];
    } else {
        return ["error" => "Error al actualizar moneda"];
    }
}
