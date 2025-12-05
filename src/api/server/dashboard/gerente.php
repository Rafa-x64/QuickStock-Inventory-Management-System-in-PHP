<?php

/**
 * Funciones del Dashboard Gerente
 * Consultas para obtener datos en tiempo real del dashboard
 */

/**
 * Obtiene el resumen financiero del día (ingresos por moneda)
 * @param int|null $id_sucursal - ID de sucursal para filtrar (null = todas)
 */
function obtenerResumenFinanciero($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                m.codigo AS codigo_moneda,
                COALESCE(SUM(pv.monto), 0) AS total_moneda
            FROM ventas.venta v
            INNER JOIN ventas.pago_venta pv ON v.id_venta = pv.id_venta
            INNER JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
            INNER JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = true 
              AND pv.activo = true
              AND DATE(v.fecha) = CURRENT_DATE";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND u.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $sql .= " GROUP BY m.codigo ORDER BY m.codigo";

    $queryName = "resumen_financiero_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener resumen financiero"];
    }

    $data = pg_fetch_all($result);
    return ["resumen" => $data ?: []];
}

/**
 * Obtiene cantidad de ventas del día
 */
function obtenerVentasHoy($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT COUNT(DISTINCT v.id_venta) AS total_ventas
            FROM ventas.venta v
            INNER JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = true 
              AND DATE(v.fecha) = CURRENT_DATE";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND u.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $queryName = "ventas_hoy_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener ventas del día"];
    }

    $row = pg_fetch_assoc($result);
    return ["total_ventas" => intval($row['total_ventas'] ?? 0)];
}

/**
 * Obtiene producto más vendido del día
 */
function obtenerProductoMasVendidoHoy($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                p.nombre AS producto,
                COALESCE(SUM(dv.cantidad), 0) AS cantidad_vendida
            FROM ventas.venta v
            INNER JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
            INNER JOIN inventario.producto p ON dv.id_producto = p.id_producto
            INNER JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = true 
              AND dv.activo = true
              AND DATE(v.fecha) = CURRENT_DATE";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND u.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $sql .= " GROUP BY p.id_producto, p.nombre
              ORDER BY cantidad_vendida DESC
              LIMIT 1";

    $queryName = "mas_vendido_hoy_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener producto más vendido"];
    }

    $row = pg_fetch_assoc($result);
    if (!$row) {
        return ["producto" => "Sin ventas hoy", "cantidad" => 0];
    }
    return ["producto" => $row['producto'], "cantidad" => intval($row['cantidad_vendida'])];
}

/**
 * Obtiene producto más vendido de la semana
 */
function obtenerProductoMasVendidoSemana($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                p.nombre AS producto,
                COALESCE(SUM(dv.cantidad), 0) AS cantidad_vendida
            FROM ventas.venta v
            INNER JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
            INNER JOIN inventario.producto p ON dv.id_producto = p.id_producto
            INNER JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = true 
              AND dv.activo = true
              AND v.fecha >= DATE_TRUNC('week', CURRENT_DATE)
              AND v.fecha < DATE_TRUNC('week', CURRENT_DATE) + INTERVAL '7 days'";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND u.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $sql .= " GROUP BY p.id_producto, p.nombre
              ORDER BY cantidad_vendida DESC
              LIMIT 1";

    $queryName = "mas_vendido_semana_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener producto más vendido de la semana"];
    }

    $row = pg_fetch_assoc($result);
    if (!$row) {
        return ["producto" => "Sin ventas esta semana", "cantidad" => 0];
    }
    return ["producto" => $row['producto'], "cantidad" => intval($row['cantidad_vendida'])];
}

/**
 * Obtiene Top 5 productos más vendidos (para gráfica)
 */
function obtenerTop5Productos($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                p.nombre AS producto,
                COALESCE(SUM(dv.cantidad), 0) AS cantidad_vendida
            FROM ventas.venta v
            INNER JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
            INNER JOIN inventario.producto p ON dv.id_producto = p.id_producto
            INNER JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
            WHERE v.activo = true 
              AND dv.activo = true";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND u.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $sql .= " GROUP BY p.id_producto, p.nombre
              ORDER BY cantidad_vendida DESC
              LIMIT 5";

    $queryName = "top5_productos_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener top 5 productos"];
    }

    $data = pg_fetch_all($result);
    return ["top5" => $data ?: []];
}

/**
 * Obtiene productos con stock bajo (cantidad <= mínimo)
 */
function obtenerProductosStockBajo($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                p.codigo_barra AS codigo,
                p.nombre AS producto,
                i.cantidad
            FROM inventario.inventario i
            INNER JOIN inventario.producto p ON i.id_producto = p.id_producto
            WHERE i.activo = true 
              AND p.activo = true
              AND i.cantidad <= i.minimo";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND i.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $sql .= " ORDER BY i.cantidad ASC LIMIT 10";

    $queryName = "stock_bajo_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener productos con stock bajo"];
    }

    $data = pg_fetch_all($result);
    return ["productos" => $data ?: []];
}

/**
 * Obtiene categorías con categoría padre
 */
function obtenerCategoriasConPadre()
{
    $conn = conectar_base_datos();

    $sql = "SELECT 
                c.id_categoria,
                c.nombre,
                COALESCE(cp.nombre, '-') AS categoria_padre
            FROM core.categoria c
            LEFT JOIN core.categoria cp ON c.id_categoria_padre = cp.id_categoria
            WHERE c.activo = true
            ORDER BY c.id_categoria
            LIMIT 10";

    $queryName = "categorias_padre_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, []);

    if (!$result) {
        return ["error" => "Error al obtener categorías"];
    }

    $data = pg_fetch_all($result);
    return ["categorias" => $data ?: []];
}

/**
 * Obtiene total de productos activos
 */
function obtenerTotalProductosActivos()
{
    $conn = conectar_base_datos();

    $sql = "SELECT COUNT(*) AS total FROM inventario.producto WHERE activo = true";

    $queryName = "total_productos_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, []);

    if (!$result) {
        return ["error" => "Error al obtener total de productos"];
    }

    $row = pg_fetch_assoc($result);
    return ["total" => intval($row['total'] ?? 0)];
}

/**
 * Obtiene cantidad de productos sin stock
 */
function obtenerProductosSinStock($id_sucursal = null)
{
    $conn = conectar_base_datos();

    $sql = "SELECT COUNT(DISTINCT i.id_producto) AS total
            FROM inventario.inventario i
            INNER JOIN inventario.producto p ON i.id_producto = p.id_producto
            WHERE i.activo = true 
              AND p.activo = true
              AND i.cantidad = 0";

    $params = [];
    $paramIndex = 1;

    if ($id_sucursal !== null && $id_sucursal > 0) {
        $sql .= " AND i.id_sucursal = $" . $paramIndex;
        $params[] = $id_sucursal;
    }

    $queryName = "sin_stock_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, $params);

    if (!$result) {
        return ["error" => "Error al obtener productos sin stock"];
    }

    $row = pg_fetch_assoc($result);
    return ["total" => intval($row['total'] ?? 0)];
}

/**
 * Obtiene nombre de sucursal para el dashboard
 */
function obtenerNombreSucursalDashboard($id_sucursal)
{
    $conn = conectar_base_datos();

    if (!$id_sucursal) {
        return ["nombre" => "Todas las Sucursales"];
    }

    $sql = "SELECT nombre FROM core.sucursal WHERE id_sucursal = $1 AND activo = true";

    $queryName = "nombre_sucursal_dash_" . uniqid();
    pg_prepare($conn, $queryName, $sql);
    $result = pg_execute($conn, $queryName, [$id_sucursal]);

    if (!$result) {
        return ["error" => "Error al obtener nombre de sucursal"];
    }

    $row = pg_fetch_assoc($result);
    return ["nombre" => $row['nombre'] ?? "Sucursal Desconocida"];
}
