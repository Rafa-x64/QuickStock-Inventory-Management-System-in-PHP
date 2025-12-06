<?php
require_once __DIR__ . "/../index.functions.php";

function obtenerClientePorCedula($cedula)
{
    $conn = conectar_base_datos();
    $sql = "SELECT * FROM core.cliente WHERE cedula = $1 AND activo = true";
    $queryName = "get_cliente_cedula_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, [$cedula]);

    if ($result && pg_num_rows($result) > 0) {
        return ["status" => true, "cliente" => pg_fetch_assoc($result)];
    } else {
        return ["status" => false, "mensaje" => "Cliente no encontrado"];
    }
}

/**
 * Obtener todos los clientes con filtros opcionales
 */
function obtenerTodosLosClientes($nombre = null, $apellido = null, $cedula = null, $estado = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT * FROM core.cliente WHERE 1=1";
    $params = [];
    $paramIndex = 1;

    if ($nombre !== null && $nombre !== "") {
        $sql .= " AND LOWER(nombre) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $nombre . "%";
        $paramIndex++;
    }

    if ($apellido !== null && $apellido !== "") {
        $sql .= " AND LOWER(apellido) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $apellido . "%";
        $paramIndex++;
    }

    if ($cedula !== null && $cedula !== "") {
        $sql .= " AND LOWER(cedula) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $cedula . "%";
        $paramIndex++;
    }

    if ($estado !== null && $estado !== "" && $estado !== "todos") {
        if ($estado === "activo" || $estado === "true" || $estado === "1") {
            $sql .= " AND activo = true";
        } elseif ($estado === "inactivo" || $estado === "false" || $estado === "0") {
            $sql .= " AND activo = false";
        }
    }

    $sql .= " ORDER BY id_cliente ASC";

    $queryName = "obtener_todos_clientes_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al realizar la consulta de clientes"];
    }

    $clientes = pg_fetch_all($result);

    return ["cliente" => $clientes ?: []];
}

/**
 * Obtener un cliente por ID
 */
function obtenerClientePorId($id_cliente)
{
    $conn = conectar_base_datos();

    $queryName = "obtener_cliente_por_id_" . uniqid();
    pg_prepare($conn, $queryName, "SELECT * FROM core.cliente WHERE id_cliente = $1");
    $result = pg_execute($conn, $queryName, [$id_cliente]);

    if (!$result || pg_num_rows($result) === 0) {
        return ["error" => "Cliente no encontrado"];
    }

    return ["cliente" => pg_fetch_assoc($result)];
}
