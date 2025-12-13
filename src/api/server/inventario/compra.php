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

    // Lógica para filtrar por TOTAL CALCULADO (subconsulta o HAVING) es compleja.
    // Simplificación: Si filtramos por total, lo haremos después (menos eficiente) o con subquery.
    // Dado el requerimiento de NO usar columnas calculadas, el filtro por TOTAL exacto se vuelve costoso.
    // Sin embargo, mantendremos la lógica de filtros básicos.

    // Moneda (Búsqueda exacta por ID)
    if ($codigo !== '') { // El parámetro se llama 'codigo' en el frontend por ahora, luego lo renombraremos a moneda o lo interpretamos aquí
        // OJO: El frontend enviará el ID de la moneda en la variable 'codigo' o 'moneda'
        // Adaptaremos el backend para leer 'moneda' si viene, o usar 'codigo' comofallback si no.
        // Pero para ser limpios, leeremos $peticion['moneda'] abajo.
    }

    // RE-LECTURA DE PARÁMETROS CORRECTOS
    $id_moneda = trim($peticion["moneda"] ?? ''); // Nuevo parámetro esperado
    $id_proveedor = trim($peticion["proveedor"] ?? ''); // Ahora esperamos ID, no nombre

    // Filtro por MONEDA
    if ($id_moneda !== '') {
        $whereClauses[] = "C.id_moneda = $$paramIndex::INT";
        $params[] = $id_moneda;
        $paramIndex++;
    }

    // N° Factura (Búsqueda parcial - LIKE)
    if ($factura !== '') {
        $whereClauses[] = "C.numero_factura ILIKE $$paramIndex";
        $params[] = '%' . $factura . '%';
        $paramIndex++;
    }

    // Fecha (Búsqueda exacta)
    if ($fecha !== '') {
        $whereClauses[] = "C.fecha_compra::TEXT = $$paramIndex";
        $params[] = $fecha;
        $paramIndex++;
    }

    // Total (COMPLEJO: Requiere HAVING o Subquery si eliminamos la columna)
    if ($total !== '') {
        // Opción A: No soportar filtro por total en esta fase.
        // Opción B: Calcularlo en el WHERE (lento).
        // Opción C: Usar HAVING.
        // Dado el scope, usaremos una cláusula HAVING en la consulta principal si es necesario.
        // Por ahora, asumiremos que si eliminamos la columna, este filtro debe adaptarse.
        // Lo comentaremos o cambiaremos a un HAVING SUM(...) posterior.

        // $whereClauses[] = "C.total = $$paramIndex::NUMERIC"; <--- ESTO FALLARIA
        // $params[] = $total;
        // $paramIndex++;
    }

    // Proveedor (Búsqueda exacta por ID)
    if ($id_proveedor !== '') {
        $whereClauses[] = "C.id_proveedor = $$paramIndex::INT";
        $params[] = $id_proveedor;
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
        // En BD el estado es string, asegurarse que coincida con los values del select
        $whereClauses[] = "C.estado = $$paramIndex";
        $params[] = $estado;
        $paramIndex++;
    }

    $whereString = implode(' AND ', $whereClauses);

    // Modificación para calcular totales al vuelo
    $sql = "
        SELECT
            C.id_compra,
            C.fecha_compra,
            C.numero_factura,
            C.estado,
            COALESCE(Sub.subtotal_calc, 0) AS subtotal,
            ROUND(COALESCE(Sub.subtotal_calc, 0) * 0.16, 2) AS monto_impuesto,
            ROUND(COALESCE(Sub.subtotal_calc, 0) * 1.16, 2) AS total,
            P.nombre AS nombre_proveedor,
            S.nombre AS nombre_sucursal,
            M.codigo AS codigo_moneda,
            (U.nombre || ' ' || U.apellido) AS nombre_empleado_responsable
        FROM 
            inventario.compra AS C
        LEFT JOIN (
            SELECT id_compra, SUM(cantidad * precio_unitario) AS subtotal_calc
            FROM inventario.detalle_compra
            GROUP BY id_compra
        ) AS Sub ON C.id_compra = Sub.id_compra
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
            C.id_compra DESC;
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

function obtenerDetalleCompra($id_compra)
{
    if (empty($id_compra)) {
        return ["error" => "ID de compra es requerido."];
    }

    // Nota: Asumo que conectar_base_datos() y pg_query_params() están definidos.
    $conn = conectar_base_datos();
    if (!$conn) {
        return ["error" => "Error al conectar con la base de datos"];
    }

    // --- 1. Obtener Cabecera de la Compra ---
    $sql_cabecera = "
        SELECT
            C.id_compra,
            C.fecha_compra,
            C.numero_factura,
            C.estado,
            COALESCE(Sub.subtotal_calc, 0) AS subtotal,
            ROUND(COALESCE(Sub.subtotal_calc, 0) * 0.16, 2) AS monto_impuesto,
            ROUND(COALESCE(Sub.subtotal_calc, 0) * 1.16, 2) AS total,
            C.observaciones,
            P.nombre AS nombre_proveedor,
            S.nombre AS nombre_sucursal,
            M.codigo AS codigo_moneda,
            (U.nombre || ' ' || U.apellido) AS nombre_empleado_responsable
        FROM 
            inventario.compra AS C
        LEFT JOIN (
            SELECT id_compra, SUM(cantidad * precio_unitario) AS subtotal_calc
            FROM inventario.detalle_compra
            GROUP BY id_compra
        ) AS Sub ON C.id_compra = Sub.id_compra
        INNER JOIN 
            core.proveedor AS P ON C.id_proveedor = P.id_proveedor
        INNER JOIN 
            core.sucursal AS S ON C.id_sucursal = S.id_sucursal
        INNER JOIN 
            seguridad_acceso.usuario AS U ON C.id_usuario = U.id_usuario
        LEFT JOIN 
            finanzas.moneda AS M ON C.id_moneda = M.id_moneda
        WHERE
            C.id_compra = $1 AND C.activo = TRUE;
    ";

    $res_cabecera = pg_query_params($conn, $sql_cabecera, [$id_compra]);
    if (!$res_cabecera) {
        return ["error" => "Error al buscar la cabecera de la compra", "detalle" => pg_last_error($conn)];
    }

    $compra = pg_fetch_assoc($res_cabecera);
    if (!$compra) {
        return ["error" => "Compra no encontrada o inactiva."];
    }

    // --- 2. Obtener Detalle de Productos ---
    $sql_detalle = "
        SELECT
            DC.id_detalle_compra,
            DC.cantidad,
            DC.precio_unitario,
            (DC.cantidad * DC.precio_unitario) AS subtotal, -- Calculado al vuelo
            PR.nombre AS nombre_producto,
            CA.nombre AS nombre_categoria,
            CO.nombre AS nombre_color,
            T.rango_talla AS nombre_talla
        FROM
            inventario.detalle_compra AS DC
        INNER JOIN
            inventario.producto AS PR ON DC.id_producto = PR.id_producto
        LEFT JOIN 
            core.categoria AS CA ON PR.id_categoria = CA.id_categoria
        LEFT JOIN 
            core.color AS CO ON PR.id_color = CO.id_color
        LEFT JOIN 
            core.talla AS T ON PR.id_talla = T.id_talla
        WHERE
            DC.id_compra = $1
        ORDER BY
            PR.nombre;
    ";

    $res_detalle = pg_query_params($conn, $sql_detalle, [$id_compra]);
    if (!$res_detalle) {
        return ["error" => "Error al buscar los detalles de la compra", "detalle" => pg_last_error($conn)];
    }

    $detalles = pg_fetch_all($res_detalle);
    if (!$detalles) $detalles = [];

    // 3. Devolver los resultados combinados
    return [
        "compra" => $compra,
        "detalles" => $detalles
    ];
}
function obtenerCompraPorId($id_compra)
{
    // Verificación básica del ID
    if (empty($id_compra)) {
        return ["error" => "ID de compra es requerido para la edición."];
    }

    // 1. Conectar a la base de datos
    // ASUMO que 'conectar_base_datos()' existe en el contexto global o está incluido.
    $conn = conectar_base_datos();
    if (!$conn) {
        return ["error" => "Error al conectar con la base de datos."];
    }

    $data = ['compra' => null, 'detalles' => []];

    // --- 1. Consulta para obtener los datos principales de la compra (Cabecera) ---
    // (Esta sección queda igual)
    $sql_compra = "
        SELECT 
            id_proveedor, id_sucursal, id_usuario, id_moneda, numero_factura, 
            TO_CHAR(fecha_compra, 'YYYY-MM-DD') as fecha_compra,
            observaciones, estado,
            -- Totales calculados con subquery
            (SELECT COALESCE(SUM(cantidad * precio_unitario), 0) FROM inventario.detalle_compra WHERE id_compra = C.id_compra) as subtotal,
            (SELECT ROUND(COALESCE(SUM(cantidad * precio_unitario), 0) * 0.16, 2) FROM inventario.detalle_compra WHERE id_compra = C.id_compra) as monto_impuesto,
            (SELECT ROUND(COALESCE(SUM(cantidad * precio_unitario), 0) * 1.16, 2) FROM inventario.detalle_compra WHERE id_compra = C.id_compra) as total
        FROM inventario.compra C
        WHERE id_compra = $1 AND activo = TRUE
    ";
    $res_compra = pg_query_params($conn, $sql_compra, [$id_compra]);

    if (!$res_compra) {
        return ["error" => "Error al buscar la cabecera de la compra.", "detalle" => pg_last_error($conn)];
    }

    $data['compra'] = pg_fetch_assoc($res_compra);

    if (!$data['compra']) {
        return ["error" => "Compra ID $id_compra no encontrada o está inactiva."];
    }

    // --- 2. Consulta para obtener los detalles de los productos adquiridos (Líneas) ---
    // MODIFICACIÓN CLAVE AQUÍ: Se excluye dc.subtotal y se añaden campos de producto
    $sql_detalles = "
        SELECT 
            dc.id_detalle_compra, 
            dc.cantidad, 
            dc.precio_unitario AS precio_compra, -- Renombrado para el JS de edición
            p.id_producto, 
            p.nombre, 
            p.codigo_barra,         -- <<-- AGREGADO
            p.precio_venta,         -- <<-- AGREGADO
            p.id_categoria,
            p.id_color,             -- <<-- AGREGADO
            p.id_talla              -- <<-- AGREGADO
        FROM inventario.detalle_compra dc
        JOIN inventario.producto p ON dc.id_producto = p.id_producto
        WHERE dc.id_compra = $1
        ORDER BY dc.id_detalle_compra
    ";
    $res_detalles = pg_query_params($conn, $sql_detalles, [$id_compra]);

    if (!$res_detalles) {
        return ["error" => "Error al buscar los detalles de los productos.", "detalle" => pg_last_error($conn)];
    }

    $data['detalles'] = pg_fetch_all($res_detalles) ?: [];

    // 3. Devolver los resultados combinados
    return ["success" => true, "data" => $data];
}
