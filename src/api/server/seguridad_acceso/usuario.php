<?php
function obtenerNombreApellido()
{
    $nombre_completo = $_SESSION["sesion_usuario"]["usuario"]["nombre"];
    $apellido_completo = $_SESSION["sesion_usuario"]["usuario"]["apellido"];

    $nombre = explode(" ", $nombre_completo);
    $apellido = explode(" ", $apellido_completo);

    if (!$nombre || !$apellido) {
        return ["error" => "Error al filtrar el nombre y apellido"];
    }

    return ["nombre" => $nombre[0], "apellido" => $apellido[0]];
}

function obtenerEmpleados($sucursal, $rol, $estado)
{
    // --- Normalizar sucursal y rol (enteros o NULL) ---
    $sucursal = ($sucursal === "" || $sucursal === null) ? null : (int)$sucursal;
    $rol      = ($rol === "" || $rol === null) ? null : (int)$rol;

    // --- Normalizar estado para pasarlo como texto 'true'|'false'|null ---
    // El endpoint JS puede enviar: "activo", "inactivo", "todos" o ""
    if ($estado === null || $estado === "" || strtolower($estado) === "todos") {
        $estadoParam = null;
    } elseif (strtolower($estado) === "activo") {
        $estadoParam = 'true';
    } elseif (strtolower($estado) === "inactivo") {
        $estadoParam = 'false';
    } else {
        // valor inesperado -> no aplicar filtro
        $estadoParam = null;
    }

    // --- Conectar ---
    $conn = conectar_base_datos();
    if (!$conn) {
        return ["error" => "Error al conectar con la base de datos"];
    }

    // --- Consulta segura: usamos $3::TEXT IS NULL para evitar NULL::BOOLEAN ---
    $sql = "
        SELECT 
            U.id_usuario,
            U.nombre,
            U.apellido,
            U.cedula,
            U.email,
            U.activo,
            U.telefono,
            U.fecha_registro,
            U.direccion,
            R.id_rol,
            R.nombre_rol,
            S.id_sucursal,
            S.nombre AS sucursal_nombre,
            S.direccion AS sucursal_direccion,
            S.telefono AS sucursal_telefono,
            S.rif AS sucursal_rif
        FROM seguridad_acceso.usuario U
        INNER JOIN seguridad_acceso.rol R 
            ON U.id_rol = R.id_rol
        LEFT JOIN core.sucursal S
            ON U.id_sucursal = S.id_sucursal
        WHERE 
            ($1::INT IS NULL OR U.id_sucursal = $1)
            AND ($2::INT IS NULL OR U.id_rol = $2)
            AND ($3::TEXT IS NULL OR U.activo = $3::BOOLEAN)
        ORDER BY U.id_usuario ASC
    ";

    // --- Ejecutar con parámetros (nota: $estadoParam es NULL o 'true'/'false') ---
    $res = pg_query_params($conn, $sql, [$sucursal, $rol, $estadoParam]);

    if (!$res) {
        // devolver error con detalle (útil para debug). Quita 'detalle' si no quieres exponerlo.
        return [
            "error" => "Error ejecutando consulta",
            "detalle" => pg_last_error($conn)
        ];
    }

    $filas = pg_fetch_all($res);
    if (!$filas) $filas = []; // siempre devolver array

    return ["filas" => $filas];
}

function obtenerUnUsuario($email)
{
    if (!$email || trim($email) === "") {
        return ["error" => "Email no proporcionado"];
    }

    $conn = conectar_base_datos();

    $sql = "
        SELECT 
            U.id_usuario,
            U.nombre,
            U.apellido,
            U.cedula,
            U.email,
            U.activo,
            U.telefono,
            U.fecha_registro,
            U.direccion,
            R.id_rol,
            R.nombre_rol,
            S.id_sucursal,
            S.nombre AS sucursal_nombre,
            S.direccion AS sucursal_direccion,
            S.telefono AS sucursal_telefono,
            S.rif AS sucursal_rif
        FROM seguridad_acceso.usuario U
        INNER JOIN seguridad_acceso.rol R 
            ON U.id_rol = R.id_rol
        LEFT JOIN core.sucursal S
            ON U.id_sucursal = S.id_sucursal
        WHERE U.email = $1
        LIMIT 1
    ";

    $res = pg_query_params($conn, $sql, [$email]);

    if (!$res) {
        return ["error" => "Error ejecutando consulta"];
    }

    $fila = pg_fetch_assoc($res);

    if (!$fila) {
        return ["error" => "No se encontró ningún empleado con ese email"];
    }

    return ["empleado" => $fila];
}

function obtenerEmpleadosResponsables(array $rolesPermitidos = [1, 2, 6]) // <-- CAMBIO CLAVE AQUÍ
{
    // Asegúrate de que los IDs de roles son enteros
    $rolesStr = implode(', ', array_map('intval', $rolesPermitidos));

    // --- Conectar ---
    $conn = conectar_base_datos();
    if (!$conn) {
        return ["error" => "Error al conectar con la base de datos"];
    }

    // --- Consulta: solo usuarios activos y con roles permitidos ---
    $sql = "
        SELECT 
            U.id_usuario,
            (U.nombre || ' ' || U.apellido) AS nombre_completo,
            U.id_rol,
            R.nombre_rol
        FROM seguridad_acceso.usuario U
        INNER JOIN seguridad_acceso.rol R 
            ON U.id_rol = R.id_rol
        WHERE 
            U.activo = TRUE 
            AND U.id_rol IN ({$rolesStr})
        ORDER BY U.nombre, U.apellido ASC
    ";

    $res = pg_query($conn, $sql);

    if (!$res) {
        return [
            "error" => "Error ejecutando consulta de empleados responsables",
            "detalle" => pg_last_error($conn)
        ];
    }

    $empleados = pg_fetch_all($res);
    if (!$empleados) $empleados = [];

    // La clave 'empleados' es necesaria para que la función JS 'cargarSelect' funcione correctamente.
    return ["empleados" => $empleados];
}