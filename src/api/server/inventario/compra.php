<?php 

function obtenerHistorialCompras()
{
    global $peticion;

    $codigo = trim($peticion["codigo"] ?? '');
    $factura = trim($peticion["factura"] ?? '');
    $fecha = trim($peticion["fecha"] ?? '');
    $total = trim($peticion["total"] ?? '');
    $proveedor = trim($peticion["proveedor"] ?? '');
    $empleado = trim($peticion["empleado"] ?? '');
    $sucursal = trim($peticion["sucursal"] ?? '');
    $estado = trim($peticion["estado"] ?? '');

    // 2. Conectar a la base de datos
    // Supongamos que esta función existe en tu entorno para conectar a la DB
    $conn = conectar_base_datos();
    if (!$conn) {
        return ["error" => "Error al conectar con la base de datos"];
    }

    // 3. Inicializar arrays para cláusulas y parámetros
    $whereClauses = ["C.activo = TRUE"]; // Siempre activo por defecto
    $params = [];
    $paramIndex = 1;

    // 4. Construir cláusulas WHERE dinámicamente

    // Código de Compra (Búsqueda exacta por número)
    if ($codigo !== '') {
        $whereClauses[] = "C.id_compra = $$paramIndex::INT";
        $params[] = $codigo;
        $paramIndex++;
    }

    // N° Factura (Búsqueda parcial - LIKE)
    if ($factura !== '') {
        $whereClauses[] = "C.numero_factura ILIKE $$paramIndex";
        $params[] = '%' . $factura . '%';
        $paramIndex++;
    }

    // Fecha (Búsqueda exacta, idealmente)
    if ($fecha !== '') {
        $whereClauses[] = "C.fecha_compra::TEXT LIKE $$paramIndex";
        $params[] = $fecha . '%'; // Permite buscar por año, año-mes, o fecha completa
        $paramIndex++;
    }

    // Total (Búsqueda exacta por número)
    if ($total !== '') {
        $whereClauses[] = "C.total = $$paramIndex::NUMERIC";
        $params[] = $total;
        $paramIndex++;
    }

    // Proveedor (Búsqueda parcial por nombre - ILIKE)
    if ($proveedor !== '') {
        $whereClauses[] = "P.nombre ILIKE $$paramIndex";
        $params[] = '%' . $proveedor . '%';
        $paramIndex++;
    }

    // Empleado (Búsqueda parcial por nombre completo - ILIKE)
    if ($empleado !== '') {
        $whereClauses[] = "(U.nombre || ' ' || U.apellido) ILIKE $$paramIndex";
        $params[] = '%' . $empleado . '%';
        $paramIndex++;
    }

    // Sucursal (Búsqueda parcial por nombre - ILIKE)
    if ($sucursal !== '') {
        $whereClauses[] = "S.nombre ILIKE $$paramIndex";
        $params[] = '%' . $sucursal . '%';
        $paramIndex++;
    }

    if ($estado !== '') {
        $whereClauses[] = "C.estado = $$paramIndex";
        $params[] = $estado;
        $paramIndex++;
    }

    $whereString = implode(' AND ', $whereClauses);

    $sql = "
        SELECT
            C.id_compra,
            C.fecha_compra,
            C.numero_factura,
            C.estado,
            C.total,
            C.monto_impuesto,
            C.subtotal,
            P.nombre AS nombre_proveedor,
            S.nombre AS nombre_sucursal,
            M.codigo AS codigo_moneda,
            (U.nombre || ' ' || U.apellido) AS nombre_empleado_responsable
        FROM 
            inventario.compra AS C
        INNER JOIN 
            core.proveedor AS P ON C.id_proveedor = P.id_proveedor
        INNER JOIN 
            core.sucursal AS S ON C.id_sucursal = S.id_sucursal
        INNER JOIN 
            seguridad_acceso.usuario AS U ON C.id_usuario = U.id_usuario
        LEFT JOIN 
            finanzas.moneda AS M ON C.id_moneda = M.id_moneda
        WHERE
            {$whereString}
        ORDER BY 
            C.fecha_compra DESC;
    ";

    $res = pg_query_params($conn, $sql, $params);

    if (!$res) {
        return [
            "error" => "Error ejecutando consulta de historial de compras",
            "detalle" => pg_last_error($conn),
        ];
    }

    $filas = pg_fetch_all($res);
    if (!$filas) $filas = [];

    return ["compras" => $filas];
}
