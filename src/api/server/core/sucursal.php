<?php
function obtenerSucursales()
{
    $con = conectar_base_datos();
    pg_prepare($con, "obtener_sucursales", "SELECT * FROM core.sucursal ORDER BY id_sucursal ASC");
    $res = pg_execute($con, "obtener_sucursales", []);

    if (!$res) {
        return ["error" => "Error al obtener las sucursales"];
    }

    $filas = pg_fetch_all($res);

    if (!$filas) {
        return [];
    }

    return ["filas" => $filas ?: []];
}

function obtenerUnaSucursalPorId($id_sucursal)
{
    $conn = conectar_base_datos();

    // Normalizar y validar el ID
    $id_sucursal = (int)$id_sucursal;
    if ($id_sucursal <= 0) {
        return ["error" => "ID de sucursal inválido"];
    }

    $query = "
        SELECT 
            id_sucursal,
            nombre,
            rif,
            direccion,
            telefono,
            fecha_registro,
            activo
        FROM core.sucursal
        WHERE id_sucursal = $1
        LIMIT 1
    ";

    $stmtName = "obtener_una_sucursal_" . uniqid();

    // Preparar y ejecutar la consulta
    if (!pg_prepare($conn, $stmtName, $query)) {
        return ["error" => "Error al preparar la consulta", "detalle" => pg_last_error($conn)];
    }
    $result = pg_execute($conn, $stmtName, [$id_sucursal]);

    if (!$result) {
        return ["error" => "Error al ejecutar la consulta", "detalle" => pg_last_error($conn)];
    }

    $sucursal = pg_fetch_assoc($result);

    if (!$sucursal) {
        return ["error" => "Sucursal no encontrada"];
    }

    // ⭐⭐⭐ PUNTO CLAVE DE CORRECCIÓN (PHP) ⭐⭐⭐
    if (isset($sucursal['activo'])) {
        // En PHP, el valor 't' de PostgreSQL es tratado como true (booleano)
        // La comparación directa con 't' es la más segura.
        // Forzamos que el valor sea el string literal "true" o "false".
        $valorDB = strtolower(trim($sucursal['activo']));

        if ($valorDB === 't' || $valorDB === 'true' || $valorDB === '1') {
            $sucursal['activo'] = 'true';
        } else {
            $sucursal['activo'] = 'false';
        }
    }

    // Formatear la fecha para el input type="date"
    if (isset($sucursal['fecha_registro'])) {
        $sucursal['fecha_registro'] = date('Y-m-d', strtotime($sucursal['fecha_registro']));
    }

    return ["sucursal" => $sucursal];
}

function obtenerDetalleSucursal($id_sucursal)
{
    if (!$id_sucursal) {
        return ["status" => "error", "message" => "ID de sucursal es requerido."];
    }

    $conn = conectar_base_datos();
    $id_sucursal = (int)$id_sucursal; // Asegurar que es un entero

    // --- 1. Consulta Principal de la Sucursal ---
    // (Esta parte no cambia, ya que obtiene los datos de core.sucursal)
    $sql_sucursal = "
        SELECT 
            id_sucursal,
            nombre,
            rif,
            direccion,
            telefono,
            fecha_registro,
            activo
        FROM core.sucursal 
        WHERE id_sucursal = $1
    ";

    $stmt_suc = "stmt_detalle_sucursal_" . uniqid();
    if (!pg_prepare($conn, $stmt_suc, $sql_sucursal)) {
        return ["status" => "error", "message" => "Error preparando consulta de sucursal", "detalle" => pg_last_error($conn)];
    }
    $result_suc = pg_execute($conn, $stmt_suc, [$id_sucursal]);
    $sucursal = pg_fetch_assoc($result_suc);

    if (!$sucursal) {
        return ["status" => "error", "message" => "Sucursal no encontrada.", "detalle" => null];
    }

    if (isset($sucursal['fecha_registro'])) {
        $sucursal['fecha_registro'] = date('Y-m-d', strtotime($sucursal['fecha_registro']));
    }


    // --- 2. Nueva Consulta de Empleados (Usuarios) Relacionados ---

    // ⭐ CAMBIAMOS LA TABLA A seguridad_acceso.usuario
    // Mapeamos los campos: nombre, apellido, id_rol (como cargo), telefono
    $sql_empleados = "
        SELECT 
            u.nombre || ' ' || u.apellido AS nombre,
            u.activo AS activo,
            r.nombre_rol AS cargo,                     -- Obtenemos el nombre del Rol
            u.telefono
        FROM seguridad_acceso.usuario u           
        JOIN seguridad_acceso.rol r ON u.id_rol = r.id_rol
        WHERE u.id_sucursal = $1
        ORDER BY u.nombre ASC
    ";

    $stmt_emp = "stmt_empleados_sucursal_" . uniqid();
    if (!pg_prepare($conn, $stmt_emp, $sql_empleados)) {
        $sucursal['empleados_error'] = "Error consultando empleados.";
        $empleados = [];
    } else {
        $result_emp = pg_execute($conn, $stmt_emp, [$id_sucursal]);
        $empleados = pg_fetch_all($result_emp) ?: [];
    }


    // --- 3. Retorno Final ---
    return [
        "status" => "success",
        "sucursal" => $sucursal,
        "empleados" => $empleados
    ];
}
