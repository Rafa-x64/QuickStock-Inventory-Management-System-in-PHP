<?php
require_once __DIR__ . "/../index.functions.php";

/**
 * Verificar si existe un gerente registrado
 */
function existeGerente()
{
    $conn = conectar_base_datos();
    $sql = "SELECT COUNT(*) AS total FROM seguridad_acceso.usuario WHERE id_rol = 1";
    $res = pg_query($conn, $sql);

    if (!$res) {
        return ["error" => "Error en la consulta"];
    }

    $data = pg_fetch_assoc($res);
    return ["existe" => ($data["total"] > 0)];
}

/**
 * Obtener todos los roles (sin filtro)
 */
function obtenerRoles()
{
    $conn = conectar_base_datos();
    $queryName = "obtener_roles_" . uniqid();
    pg_prepare($conn, $queryName, "SELECT * FROM seguridad_acceso.rol ORDER BY id_rol ASC");
    $res = pg_execute($conn, $queryName, []);
    if (!$res) {
        return ["error" => "Error al realizar la consulta de roles"];
    }
    $filas = pg_fetch_all($res);
    if (!$filas) {
        return ["rol" => []];
    }
    return ["rol" => $filas ?: []];
}

/**
 * Obtener todos los roles con filtros opcionales y conteo de usuarios
 */
function obtenerTodosLosRoles($nombre = null, $estado = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT r.*, 
            (SELECT COUNT(*) FROM seguridad_acceso.usuario u WHERE u.id_rol = r.id_rol) as usuarios_asignados
            FROM seguridad_acceso.rol r WHERE 1=1";
    $params = [];
    $paramIndex = 1;

    if ($nombre !== null && $nombre !== "") {
        $sql .= " AND LOWER(r.nombre_rol) LIKE LOWER($" . $paramIndex . ")";
        $params[] = "%" . $nombre . "%";
        $paramIndex++;
    }

    if ($estado !== null && $estado !== "" && $estado !== "todos") {
        if ($estado === "activo" || $estado === "true" || $estado === "1") {
            $sql .= " AND r.activo = true";
        } elseif ($estado === "inactivo" || $estado === "false" || $estado === "0") {
            $sql .= " AND r.activo = false";
        }
    }

    $sql .= " ORDER BY r.id_rol ASC";

    $queryName = "obtener_todos_roles_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al realizar la consulta de roles"];
    }

    $roles = pg_fetch_all($result);

    return ["rol" => $roles ?: []];
}

/**
 * Obtener un rol por ID
 */
function obtenerRolPorId($id_rol)
{
    $conn = conectar_base_datos();

    $queryName = "obtener_rol_por_id_" . uniqid();
    pg_prepare(
        $conn,
        $queryName,
        "SELECT r.*, 
        (SELECT COUNT(*) FROM seguridad_acceso.usuario u WHERE u.id_rol = r.id_rol) as usuarios_asignados
        FROM seguridad_acceso.rol r WHERE r.id_rol = $1"
    );
    $result = pg_execute($conn, $queryName, [$id_rol]);

    if (!$result || pg_num_rows($result) === 0) {
        return ["error" => "Rol no encontrado"];
    }

    return ["rol" => pg_fetch_assoc($result)];
}
