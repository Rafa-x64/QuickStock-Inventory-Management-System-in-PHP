<?php
require_once __DIR__ . "/../index.functions.php";

function obtenerProveedores()
{
    $conn = conectar_base_datos();
    $queryName = "obtener_proveedores_" . uniqid();
    pg_prepare($conn, $queryName, "SELECT * FROM core.proveedor ORDER BY id_proveedor ASC");
    $resultado = pg_execute($conn, $queryName, []);

    if (!$resultado) {
        return ["error" => "No se pudieron obtener los proveedores"];
    }

    $proveedores = pg_fetch_all($resultado);

    return ["proveedor" => $proveedores ?: []];
}

/**
 * Obtener todos los proveedores con filtros opcionales
 */
function obtenerTodosLosProveedores($nombre = null, $correo = null, $estado = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT * FROM core.proveedor WHERE 1=1";
    $params = [];
    $paramIndex = 1;

    if ($nombre !== null && $nombre !== "") {
        $sql .= " AND LOWER(nombre) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $nombre . "%";
        $paramIndex++;
    }

    if ($correo !== null && $correo !== "") {
        $sql .= " AND LOWER(correo) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $correo . "%";
        $paramIndex++;
    }

    if ($estado !== null && $estado !== "" && $estado !== "todos") {
        if ($estado === "activo" || $estado === "true" || $estado === "1") {
            $sql .= " AND activo = true";
        } elseif ($estado === "inactivo" || $estado === "false" || $estado === "0") {
            $sql .= " AND activo = false";
        }
    }

    $sql .= " ORDER BY id_proveedor ASC";

    $queryName = "obtener_todos_proveedores_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al realizar la consulta de proveedores"];
    }

    $proveedores = pg_fetch_all($result);

    return ["proveedor" => $proveedores ?: []];
}

/**
 * Obtener un proveedor por ID
 */
function obtenerProveedorPorId($id_proveedor)
{
    $conn = conectar_base_datos();

    $queryName = "obtener_proveedor_por_id_" . uniqid();
    pg_prepare($conn, $queryName, "SELECT * FROM core.proveedor WHERE id_proveedor = $1");
    $result = pg_execute($conn, $queryName, [$id_proveedor]);

    if (!$result || pg_num_rows($result) === 0) {
        return ["error" => "Proveedor no encontrado"];
    }

    return ["proveedor" => pg_fetch_assoc($result)];
}
