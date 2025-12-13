<?php

function obtenerVentasFiltradas($fecha_desde = null, $fecha_hasta = null, $id_usuario = null, $monto_min = null, $monto_max = null, $id_sucursal = null, $id_metodo_pago = null, $id_moneda = null)
{
    $conn = conectar_base_datos();

    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

    $sql = "SELECT DISTINCT
                v.id_venta,
                v.fecha,
                (SELECT COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) FROM ventas.detalle_venta dv WHERE dv.id_venta = v.id_venta AND dv.activo = 't') AS total,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                u.nombre || ' ' || u.apellido AS vendedor,
                s.nombre AS sucursal,
                (SELECT mp.nombre FROM ventas.pago_venta pv2 
                 LEFT JOIN finanzas.metodo_pago mp ON pv2.id_metodo_pago = mp.id_metodo_pago 
                 WHERE pv2.id_venta = v.id_venta AND pv2.activo = 't' LIMIT 1) AS metodo_pago,
                (SELECT m.nombre FROM ventas.pago_venta pv3 
                 LEFT JOIN finanzas.moneda m ON pv3.id_moneda = m.id_moneda 
                 WHERE pv3.id_venta = v.id_venta AND pv3.activo = 't' LIMIT 1) AS moneda
            FROM ventas.venta v
            LEFT JOIN core.cliente c ON v.id_cliente = c.id_cliente
            LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            LEFT JOIN core.sucursal s ON u.id_sucursal = s.id_sucursal
            WHERE v.activo = 't'";

    $params = [];
    $param_count = 1;

    if ($sucursal_sesion !== null) {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $sucursal_sesion;
    }

    if ($fecha_desde !== null && $fecha_desde !== '') {
        $sql .= " AND v.fecha >= $" . $param_count++;
        $params[] = $fecha_desde;
    }

    if ($fecha_hasta !== null && $fecha_hasta !== '') {
        $sql .= " AND v.fecha <= $" . $param_count++;
        $params[] = $fecha_hasta;
    }

    if ($id_usuario !== null && $id_usuario !== '') {
        $sql .= " AND v.id_usuario = $" . $param_count++;
        $params[] = $id_usuario;
    }

    if ($monto_min !== null && $monto_min !== '') {
        $sql .= " AND (SELECT COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) FROM ventas.detalle_venta dv WHERE dv.id_venta = v.id_venta AND dv.activo = 't') >= $" . $param_count++;
        $params[] = $monto_min;
    }

    if ($monto_max !== null && $monto_max !== '') {
        $sql .= " AND (SELECT COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) FROM ventas.detalle_venta dv WHERE dv.id_venta = v.id_venta AND dv.activo = 't') <= $" . $param_count++;
        $params[] = $monto_max;
    }

    if ($id_sucursal !== null && $id_sucursal !== '') {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $id_sucursal;
    }

    if ($id_metodo_pago !== null && $id_metodo_pago !== '') {
        $sql .= " AND EXISTS (SELECT 1 FROM ventas.pago_venta pv WHERE pv.id_venta = v.id_venta AND pv.id_metodo_pago = $" . $param_count++ . " AND pv.activo = 't')";
        $params[] = $id_metodo_pago;
    }

    if ($id_moneda !== null && $id_moneda !== '') {
        $sql .= " AND EXISTS (SELECT 1 FROM ventas.pago_venta pv WHERE pv.id_venta = v.id_venta AND pv.id_moneda = $" . $param_count++ . " AND pv.activo = 't')";
        $params[] = $id_moneda;
    }

    $sql .= " ORDER BY v.fecha DESC";

    $query_name = "obtener_ventas_filtradas_" . uniqid();
    pg_prepare($conn, $query_name, $sql);
    $result = pg_execute($conn, $query_name, $params);

    if (!$result) {
        return ["error" => "Error al obtener ventas"];
    }

    $ventas = pg_fetch_all($result);
    return ["ventas" => $ventas ?: []];
}

function obtenerDetalleVentaPorId($id_venta)
{
    $conn = conectar_base_datos();

    $sql_venta = "SELECT 
                    v.id_venta,
                    v.fecha,
                    (SELECT COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) FROM ventas.detalle_venta dv WHERE dv.id_venta = v.id_venta AND dv.activo = 't') AS total,
                    c.id_cliente,
                    c.cedula,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.correo,
                    c.telefono,
                    c.direccion,
                    u.id_usuario,
                    u.nombre || ' ' || u.apellido AS vendedor,
                    s.id_sucursal,
                    s.nombre AS sucursal
                FROM ventas.venta v
                LEFT JOIN core.cliente c ON v.id_cliente = c.id_cliente
                LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                LEFT JOIN core.sucursal s ON u.id_sucursal = s.id_sucursal
                WHERE v.id_venta = $1 AND v.activo = 't'";

    $query_venta_name = "get_venta_detalle_" . uniqid();
    pg_prepare($conn, $query_venta_name, $sql_venta);
    $result_venta = pg_execute($conn, $query_venta_name, [$id_venta]);

    if (!$result_venta || pg_num_rows($result_venta) === 0) {
        return ["error" => "Venta no encontrada"];
    }

    $venta = pg_fetch_assoc($result_venta);

    $sql_detalle = "SELECT 
                        dv.id_detalle,
                        dv.cantidad,
                        dv.precio_unitario,
                        (dv.cantidad * dv.precio_unitario) AS subtotal,
                        p.nombre AS producto_nombre,
                        p.codigo_barra,
                        cat.nombre AS categoria,
                        col.nombre AS color,
                        t.rango_talla AS talla
                    FROM ventas.detalle_venta dv
                    LEFT JOIN inventario.producto p ON dv.id_producto = p.id_producto
                    LEFT JOIN core.categoria cat ON p.id_categoria = cat.id_categoria
                    LEFT JOIN core.color col ON p.id_color = col.id_color
                    LEFT JOIN core.talla t ON p.id_talla = t.id_talla
                    WHERE dv.id_venta = $1 AND dv.activo = 't'";

    $query_detalle_name = "get_venta_detalles_" . uniqid();
    pg_prepare($conn, $query_detalle_name, $sql_detalle);
    $result_detalle = pg_execute($conn, $query_detalle_name, [$id_venta]);

    $detalles = pg_fetch_all($result_detalle) ?: [];

    $sql_pagos = "SELECT 
                    pv.id_pago,
                    pv.monto,
                    pv.tasa,
                    pv.referencia,
                    mp.nombre AS metodo_pago,
                    m.nombre AS moneda,
                    m.codigo
                FROM ventas.pago_venta pv
                LEFT JOIN finanzas.metodo_pago mp ON pv.id_metodo_pago = mp.id_metodo_pago
                LEFT JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
                WHERE pv.id_venta = $1 AND pv.activo = 't'";

    $query_pagos_name = "get_venta_pagos_" . uniqid();
    pg_prepare($conn, $query_pagos_name, $sql_pagos);
    $result_pagos = pg_execute($conn, $query_pagos_name, [$id_venta]);

    $pagos = pg_fetch_all($result_pagos) ?: [];

    return [
        "venta" => $venta,
        "detalles" => $detalles,
        "pagos" => $pagos
    ];
}

function obtenerProductosPopulares($limite = 10, $fecha_desde = null, $fecha_hasta = null)
{
    $conn = conectar_base_datos();
    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

    $sql = "SELECT 
                p.id_producto,
                p.nombre AS producto_nombre,
                p.codigo_barra,
                SUM(dv.cantidad) AS total_vendido,
                SUM(dv.cantidad * dv.precio_unitario) AS ingresos_totales,
                cat.nombre AS categoria
            FROM ventas.detalle_venta dv
            LEFT JOIN inventario.producto p ON dv.id_producto = p.id_producto
            LEFT JOIN core.categoria cat ON p.id_categoria = cat.id_categoria
            LEFT JOIN ventas.venta v ON dv.id_venta = v.id_venta
            LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE dv.activo = 't' AND v.activo = 't'";

    $params = [];
    $param_count = 1;

    if ($sucursal_sesion !== null) {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $sucursal_sesion;
    }

    if ($fecha_desde !== null && $fecha_desde !== '') {
        $sql .= " AND v.fecha >= $" . $param_count++;
        $params[] = $fecha_desde;
    }

    if ($fecha_hasta !== null && $fecha_hasta !== '') {
        $sql .= " AND v.fecha <= $" . $param_count++;
        $params[] = $fecha_hasta;
    }

    $sql .= " GROUP BY p.id_producto, p.nombre, p.codigo_barra, cat.nombre
              ORDER BY total_vendido DESC
              LIMIT $" . $param_count++;
    $params[] = $limite;

    $query_name = "productos_populares_" . uniqid();
    pg_prepare($conn, $query_name, $sql);
    $result = pg_execute($conn, $query_name, $params);

    if (!$result) {
        return ["error" => "Error al obtener productos populares"];
    }

    $productos = pg_fetch_all($result) ?: [];
    return ["productos" => $productos];
}

function obtenerCategoriasPopulares($limite = 10, $fecha_desde = null, $fecha_hasta = null)
{
    $conn = conectar_base_datos();
    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

    $sql = "SELECT 
                cat.id_categoria,
                cat.nombre AS categoria,
                SUM(dv.cantidad) AS total_vendido,
                SUM(dv.cantidad * dv.precio_unitario) AS ingresos_totales
            FROM ventas.detalle_venta dv
            LEFT JOIN inventario.producto p ON dv.id_producto = p.id_producto
            LEFT JOIN core.categoria cat ON p.id_categoria = cat.id_categoria
            LEFT JOIN ventas.venta v ON dv.id_venta = v.id_venta
            LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE dv.activo = 't' AND v.activo = 't'";

    $params = [];
    $param_count = 1;

    if ($sucursal_sesion !== null) {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $sucursal_sesion;
    }

    if ($fecha_desde !== null && $fecha_desde !== '') {
        $sql .= " AND v.fecha >= $" . $param_count++;
        $params[] = $fecha_desde;
    }

    if ($fecha_hasta !== null && $fecha_hasta !== '') {
        $sql .= " AND v.fecha <= $" . $param_count++;
        $params[] = $fecha_hasta;
    }

    $sql .= " GROUP BY cat.id_categoria, cat.nombre
              ORDER BY total_vendido DESC
              LIMIT $" . $param_count++;
    $params[] = $limite;

    $query_name = "categorias_populares_" . uniqid();
    pg_prepare($conn, $query_name, $sql);
    $result = pg_execute($conn, $query_name, $params);

    if (!$result) {
        return ["error" => "Error al obtener categorías populares"];
    }

    $categorias = pg_fetch_all($result) ?: [];
    return ["categorias" => $categorias];
}

function obtenerVentasPorSucursal($fecha_desde = null, $fecha_hasta = null)
{
    $conn = conectar_base_datos();
    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

    $sql = "SELECT 
                s.id_sucursal,
                s.nombre AS sucursal,
                COUNT(DISTINCT v.id_venta) AS total_ventas,
                (SELECT COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) FROM ventas.detalle_venta dv JOIN ventas.venta v2 ON dv.id_venta = v2.id_venta WHERE v2.id_usuario IN (SELECT id_usuario FROM seguridad_acceso.usuario WHERE id_sucursal = s.id_sucursal) AND dv.activo = 't' AND v2.activo = 't') AS ingresos_totales
            FROM ventas.venta v
            LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            LEFT JOIN core.sucursal s ON u.id_sucursal = s.id_sucursal
            WHERE v.activo = 't'";

    $params = [];
    $param_count = 1;

    if ($sucursal_sesion !== null) {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $sucursal_sesion;
    }

    if ($fecha_desde !== null && $fecha_desde !== '') {
        $sql .= " AND v.fecha >= $" . $param_count++;
        $params[] = $fecha_desde;
    }

    if ($fecha_hasta !== null && $fecha_hasta !== '') {
        $sql .= " AND v.fecha <= $" . $param_count++;
        $params[] = $fecha_hasta;
    }

    $sql .= " GROUP BY s.id_sucursal, s.nombre
              ORDER BY ingresos_totales DESC";

    $query_name = "ventas_por_sucursal_" . uniqid();
    pg_prepare($conn, $query_name, $sql);
    $result = pg_execute($conn, $query_name, $params);

    if (!$result) {
        return ["error" => "Error al obtener ventas por sucursal"];
    }

    $sucursales = pg_fetch_all($result) ?: [];
    return ["sucursales" => $sucursales];
}

function obtenerTendenciaMensual($meses = 12)
{
    $conn = conectar_base_datos();
    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

    $sql = "SELECT 
                TO_CHAR(v.fecha, 'YYYY-MM') AS mes,
                COUNT(DISTINCT v.id_venta) AS total_ventas,
                SUM(dv.cantidad * dv.precio_unitario) AS ingresos_totales
            FROM ventas.venta v
            JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
            LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = 't' AND dv.activo = 't'
              AND v.fecha >= CURRENT_DATE - MAKE_INTERVAL(months => $1)";

    $params = [$meses];
    $param_count = 2;

    if ($sucursal_sesion !== null) {
        $sql .= " AND u.id_sucursal = $" . $param_count++;
        $params[] = $sucursal_sesion;
    }

    $sql .= " GROUP BY TO_CHAR(v.fecha, 'YYYY-MM')
              ORDER BY mes ASC";

    $query_name = "tendencia_mensual_" . uniqid();
    pg_prepare($conn, $query_name, $sql);
    $result = pg_execute($conn, $query_name, $params);

    if (!$result) {
        return ["error" => "Error al obtener tendencia mensual"];
    }

    $tendencia = pg_fetch_all($result) ?: [];
    return ["tendencia" => $tendencia];
}

function obtenerCierreCaja($fecha = null)
{
    $conn = conectar_base_datos();
    $sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? 5; // Default to 5 if not set
    $fecha_filtro = $fecha ?? date('Y-m-d');

    // 1. Resumen General de Ventas (Conteo y Totales Base)
    $sql_resumen = "SELECT 
                        COUNT(*) FILTER (WHERE v.activo = 't') as total_ventas,
                        COUNT(*) FILTER (WHERE v.activo = 'f') as ventas_anuladas,
                        COALESCE((
                            SELECT SUM(dv.cantidad * dv.precio_unitario) 
                            FROM ventas.detalle_venta dv 
                            JOIN ventas.venta v2 ON dv.id_venta = v2.id_venta 
                            LEFT JOIN seguridad_acceso.usuario u2 ON v2.id_usuario = u2.id_usuario
                            WHERE DATE(v2.fecha) = $1 AND u2.id_sucursal = $2 AND v2.activo = 't' AND dv.activo = 't'
                        ), 0) as total_esperado_base,
                        MIN(fecha) FILTER (WHERE v.activo = 't') as primera_venta,
                        MAX(fecha) FILTER (WHERE v.activo = 't') as ultima_venta
                    FROM ventas.venta v
                    LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                    WHERE DATE(v.fecha) = $1 AND u.id_sucursal = $2";

    $query_resumen = "cierre_resumen_" . uniqid();
    pg_prepare($conn, $query_resumen, $sql_resumen);
    $res_resumen = pg_execute($conn, $query_resumen, [$fecha_filtro, $sucursal_sesion]);
    $resumen = pg_fetch_assoc($res_resumen);

    // 2. Totales por Moneda (Dinero Real Recaudado)
    $sql_monedas = "SELECT 
                        m.nombre,
                        m.codigo,
                        m.simbolo,
                        COALESCE(SUM(pv.monto), 0) as total_recaudado,
                        COUNT(pv.id_pago) as total_transacciones
                    FROM ventas.pago_venta pv
                    JOIN ventas.venta v ON pv.id_venta = v.id_venta
                    JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
                    LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                    WHERE DATE(v.fecha) = $1 
                      AND u.id_sucursal = $2
                      AND v.activo = 't' 
                      AND pv.activo = 't'
                    GROUP BY m.id_moneda, m.nombre, m.codigo, m.simbolo
                    ORDER BY m.nombre ASC";

    $query_monedas = "cierre_monedas_" . uniqid();
    pg_prepare($conn, $query_monedas, $sql_monedas);
    $res_monedas = pg_execute($conn, $query_monedas, [$fecha_filtro, $sucursal_sesion]);
    $totales_moneda = pg_fetch_all($res_monedas) ?: [];

    // 3. Desglose por Método de Pago y Moneda
    $sql_metodos = "SELECT 
                        mp.nombre as metodo_pago,
                        m.codigo as moneda,
                        m.simbolo as simbolo_moneda,
                        COALESCE(SUM(pv.monto), 0) as monto,
                        COUNT(pv.id_pago) as transacciones
                    FROM ventas.pago_venta pv
                    JOIN ventas.venta v ON pv.id_venta = v.id_venta
                    JOIN finanzas.metodo_pago mp ON pv.id_metodo_pago = mp.id_metodo_pago
                    JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
                    LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                    WHERE DATE(v.fecha) = $1 
                      AND u.id_sucursal = $2
                      AND v.activo = 't' 
                      AND pv.activo = 't'
                    GROUP BY mp.id_metodo_pago, mp.nombre, m.id_moneda, m.codigo, m.simbolo
                    ORDER BY mp.nombre, m.codigo";

    $query_metodos = "cierre_metodos_" . uniqid();
    pg_prepare($conn, $query_metodos, $sql_metodos);
    $res_metodos = pg_execute($conn, $query_metodos, [$fecha_filtro, $sucursal_sesion]);
    $desglose_pagos = pg_fetch_all($res_metodos) ?: [];

    // 4. Ventas por Hora (Tendencia)
    $sql_horas = "SELECT 
                    EXTRACT(HOUR FROM v.fecha) as hora,
                    COUNT(DISTINCT v.id_venta) as cantidad_ventas,
                    SUM(dv.cantidad * dv.precio_unitario) as total_base
                  FROM ventas.venta v
                  JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
                  LEFT JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                  WHERE DATE(v.fecha) = $1 
                    AND u.id_sucursal = $2
                    AND v.activo = 't'
                    AND dv.activo = 't'
                  GROUP BY EXTRACT(HOUR FROM v.fecha)
                  ORDER BY hora ASC";

    $query_horas = "cierre_horas_" . uniqid();
    pg_prepare($conn, $query_horas, $sql_horas);
    $res_horas = pg_execute($conn, $query_horas, [$fecha_filtro, $sucursal_sesion]);
    $ventas_hora = pg_fetch_all($res_horas) ?: [];

    // 5. Información de la Sucursal y Usuario (Contexto)
    $sql_contexto = "SELECT nombre FROM core.sucursal WHERE id_sucursal = $1";
    $query_contexto = "cierre_contexto_" . uniqid();
    pg_prepare($conn, $query_contexto, $sql_contexto);
    $res_contexto = pg_execute($conn, $query_contexto, [$sucursal_sesion]);
    $sucursal_info = pg_fetch_assoc($res_contexto);

    return [
        "fecha_cierre" => $fecha_filtro,
        "sucursal" => $sucursal_info['nombre'] ?? 'Desconocida',
        "resumen" => $resumen,
        "totales_moneda" => $totales_moneda,
        "desglose_pagos" => $desglose_pagos,
        "ventas_hora" => $ventas_hora
    ];
}
