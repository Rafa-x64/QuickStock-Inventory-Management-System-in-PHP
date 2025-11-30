<?php

function obtenerVentasFiltradas($fecha_desde = null, $fecha_hasta = null, $id_usuario = null, $monto_min = null, $monto_max = null, $id_sucursal = null, $id_metodo_pago = null, $id_moneda = null)
{
    $conn = conectar_base_datos();

    $sucursal_sesion = $_SESSION['sesion_usuario']['id_sucursal'] ?? null;

    $sql = "SELECT DISTINCT
                v.id_venta,
                v.fecha,
                v.total,
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
        $sql .= " AND v.total >= $" . $param_count++;
        $params[] = $monto_min;
    }

    if ($monto_max !== null && $monto_max !== '') {
        $sql .= " AND v.total <= $" . $param_count++;
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
                    v.total,
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
                        dv.subtotal,
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
