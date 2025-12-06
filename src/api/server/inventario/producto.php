<?php
function obtenerTodosLosProductos(
    $nombre = null,
    $codigo = null,
    $categoria = null,
    $proveedor = null,
    $sucursal = null,
    $estado = null,
    $color = null,
    $talla = null
) {
    // Asegúrate de que esta función está definida para conectar a tu DB
    $conn = conectar_base_datos();

    // --- 1. NORMALIZACIÓN Y PREPARACIÓN DE PARÁMETROS ---
    $nombre    = $nombre !== null ? trim($nombre) : null;
    $codigo    = $codigo !== null ? trim($codigo) : null;

    // Los IDs de los selects deben ser enteros, o null si están vacíos ("")
    $categoria = $categoria ? (int)$categoria : null;
    $proveedor = $proveedor ? (int)$proveedor : null;
    $sucursal  = $sucursal ? (int)$sucursal : null;
    $color     = $color ? (int)$color : null;
    $talla     = $talla ? (int)$talla : null;

    // Manejo del estado: convertir el string JS ("true"/"false") a booleano PHP (true/false) o null
    $estado_filtrar = null;
    if ($estado === "true") {
        $estado_filtrar = true;
    } elseif ($estado === "false") {
        $estado_filtrar = false;
    }

    // --- 2. GENERACIÓN DEL WHERE DINÁMICO ---
    $clauses = [];
    $params = [];
    $i = 1;

    // Nombre (búsqueda parcial insensible a mayúsculas/minúsculas)
    if ($nombre) {
        $clauses[] = "p.nombre ILIKE $" . $i;
        $params[] = "%" . $nombre . "%";
        $i++;
    }

    // Código de barra (búsqueda parcial insensible a mayúsculas/minúsculas)
    if ($codigo) {
        $clauses[] = "p.codigo_barra ILIKE $" . $i;
        $params[] = "%" . $codigo . "%";
        $i++;
    }

    // Categoría (ID exacto)
    if ($categoria) {
        $clauses[] = "p.id_categoria = $" . $i;
        $params[] = $categoria;
        $i++;
    }

    // Proveedor (ID exacto)
    if ($proveedor) {
        $clauses[] = "p.id_proveedor = $" . $i;
        $params[] = $proveedor;
        $i++;
    }

    // Color (ID exacto)
    if ($color) {
        $clauses[] = "p.id_color = $" . $i;
        $params[] = $color;
        $i++;
    }

    // Talla (ID exacto)
    if ($talla) {
        $clauses[] = "p.id_talla = $" . $i;
        $params[] = $talla;
        $i++;
    }

    // Sucursal (ID exacto, filtrando por la tabla de inventario)
    if ($sucursal) {
        $clauses[] = "i.id_sucursal = $" . $i;
        $params[] = $sucursal;
        $i++;
    }

    // Estado (booleano)
    if ($estado_filtrar !== null) {
        $clauses[] = "p.activo = $" . $i;

        // CORRECCIÓN: Convertir el booleano PHP a un string de PostgreSQL ('t' o 'f')
        $param_estado = $estado_filtrar ? 't' : 'f';

        $params[] = $param_estado;
        $i++;
    }

    // Construcción de la cláusula WHERE
    $where = !empty($clauses) ? "WHERE " . implode(" AND ", $clauses) : "";

    // --- 3. CONSULTA SQL PRINCIPAL ---
    $sql = "
        SELECT 
            p.id_producto,
            p.nombre,
            p.descripcion,
            p.codigo_barra AS codigo,
            p.precio_compra,
            p.precio_venta,
            p.activo AS estado,
            c.nombre AS categoria_nombre,
            col.nombre AS color,
            t.rango_talla AS talla,
            pr.nombre AS proveedor_nombre,
            i.cantidad AS stock,
            i.minimo,
            s.nombre AS sucursal_nombre,
            s.id_sucursal
        FROM inventario.producto p
        LEFT JOIN core.categoria c ON c.id_categoria = p.id_categoria
        LEFT JOIN core.color col ON col.id_color = p.id_color
        LEFT JOIN core.talla t ON t.id_talla = p.id_talla
        LEFT JOIN core.proveedor pr ON pr.id_proveedor = p.id_proveedor
        LEFT JOIN inventario.inventario i ON i.id_producto = p.id_producto
        LEFT JOIN core.sucursal s ON s.id_sucursal = i.id_sucursal
        $where
        ORDER BY p.id_producto ASC
    ";

    // --- 4. EJECUCIÓN DE LA CONSULTA ---

    // Preparar y ejecutar la consulta con sentencias preparadas
    $stmt = "stmt_" . uniqid();
    if (!pg_prepare($conn, $stmt, $sql)) {
        return [
            "status" => "error",
            "message" => "Error al preparar la consulta",
            "detalle" => pg_last_error($conn)
        ];
    }

    $result = pg_execute($conn, $stmt, $params);

    if (!$result) {
        return [
            "status" => "error",
            "message" => "Error al ejecutar la consulta",
            "detalle" => pg_last_error($conn)
        ];
    }

    $productos = pg_fetch_all($result) ?: [];

    // --- 5. RETORNO DE RESULTADOS ---
    return [
        "status" => "success",
        "data" => $productos
    ];
}

function traerProductosSucursal() {}

function obtenerUnProducto($id_producto)
{
    $conn = conectar_base_datos();
    $id_producto = (int)$id_producto;
    if ($id_producto <= 0) return null;

    $query = "
        SELECT 
            p.id_producto,
            p.nombre,
            p.descripcion,
            p.codigo_barra,
            p.precio_venta AS precio,
            p.precio_compra,
            p.id_categoria,
            c.nombre AS nombre_categoria,
            c.descripcion AS descripcion_categoria,
            p.id_color,
            col.nombre AS nombre_color,
            p.id_talla,
            t.rango_talla,
            p.id_proveedor,
            prov.nombre AS nombre_proveedor,
            p.activo
        FROM inventario.producto p
        LEFT JOIN core.categoria c ON c.id_categoria = p.id_categoria
        LEFT JOIN core.color col ON col.id_color = p.id_color
        LEFT JOIN core.talla t ON t.id_talla = p.id_talla
        LEFT JOIN core.proveedor prov ON prov.id_proveedor = p.id_proveedor
        WHERE p.id_producto = $1
        LIMIT 1
    ";

    $stmtName = "obtener_producto_" . uniqid();
    pg_prepare($conn, $stmtName, $query);
    $result = pg_execute($conn, $stmtName, [$id_producto]);

    if (!$result) {
        return ["error" => "Error al ejecutar la consulta", "detalle" => pg_last_error($conn)];
    }

    $producto = pg_fetch_assoc($result);
    if (!$producto) return null;

    if (isset($producto['activo'])) {
        // Correccion: PostgreSQL devuelve 't'/'f', filter_var no reconoce 't' como true por defecto.
        $val = $producto['activo'];
        $producto['activo'] = ($val === 't' || $val === 'true' || $val === '1' || $val === 1 || $val === true);
    }

    $queryInventario = "
        SELECT 
            i.id_sucursal,
            s.nombre AS nombre_sucursal,
            i.cantidad,
            i.minimo
        FROM inventario.inventario i
        LEFT JOIN core.sucursal s ON s.id_sucursal = i.id_sucursal
        WHERE i.id_producto = $1
    ";

    $stmtInvName = "obtener_inventario_" . uniqid();
    pg_prepare($conn, $stmtInvName, $queryInventario);
    $resInv = pg_execute($conn, $stmtInvName, [$id_producto]);

    $inventario = [];
    while ($row = pg_fetch_assoc($resInv)) {
        $inventario[] = $row;
    }

    $producto["inventario"] = $inventario;

    return $producto;
}

function obtenerDetalleProducto($id_producto)
{
    if (!$id_producto) {
        return ["status" => "error", "message" => "ID de producto es requerido."];
    }

    $conn = conectar_base_datos();

    // --- 1. Obtener la información principal del producto ---
    $sql_producto = "
        SELECT 
            p.nombre,
            p.descripcion,
            p.codigo_barra,
            p.precio_compra,
            p.precio_venta,
            p.activo AS estado,
            c.nombre AS categoria_nombre,
            col.nombre AS color_nombre,
            t.rango_talla AS talla,
            pr.nombre AS proveedor_nombre
        FROM inventario.producto p
        LEFT JOIN core.categoria c ON c.id_categoria = p.id_categoria
        LEFT JOIN core.color col ON col.id_color = p.id_color
        LEFT JOIN core.talla t ON t.id_talla = p.id_talla
        LEFT JOIN core.proveedor pr ON pr.id_proveedor = p.id_proveedor
        WHERE p.id_producto = $1
    ";

    $stmt_prod = "stmt_detalle_prod_" . uniqid();
    if (!pg_prepare($conn, $stmt_prod, $sql_producto)) {
        return ["status" => "error", "message" => "Error preparando producto", "detalle" => pg_last_error($conn)];
    }

    $result_prod = pg_execute($conn, $stmt_prod, [(int)$id_producto]);
    $producto = pg_fetch_assoc($result_prod);

    if (!$producto) {
        return ["status" => "error", "message" => "Producto no encontrado."];
    }

    // --- 2. Obtener la información de inventario por sucursal ---
    $sql_inventario = "
        SELECT 
            s.nombre AS sucursal_nombre,
            i.cantidad AS stock,
            i.minimo
        FROM inventario.inventario i
        JOIN core.sucursal s ON s.id_sucursal = i.id_sucursal
        WHERE i.id_producto = $1
        ORDER BY s.nombre
    ";

    // Si no tienes 'ultima_actualizacion', reemplázalo con otra columna o usa NULL.

    $stmt_inv = "stmt_detalle_inv_" . uniqid();
    if (!pg_prepare($conn, $stmt_inv, $sql_inventario)) {
        return ["status" => "error", "message" => "Error preparando inventario", "detalle" => pg_last_error($conn)];
    }

    $result_inv = pg_execute($conn, $stmt_inv, [(int)$id_producto]);
    $inventario = pg_fetch_all($result_inv) ?: [];

    // --- 3. Calcular estadísticas globales (totales) ---
    $total_inventario = 0;
    $stock_minimo_global = 0;
    $sucursales_bajo_stock = 0;

    foreach ($inventario as $item) {
        $total_inventario += (int)$item['stock'];
        $stock_minimo_global += (int)$item['minimo'];

        if ((int)$item['stock'] < (int)$item['minimo']) {
            $sucursales_bajo_stock++;
        }
    }

    // --- 4. Retorno final ---
    return [
        "status" => "success",
        "producto" => $producto,
        "inventario" => $inventario,
        "estadisticas" => [
            "total_inventario" => $total_inventario,
            "stock_minimo_global" => $stock_minimo_global,
            "sucursales_bajo_stock" => $sucursales_bajo_stock,
        ]
    ];
}

function obtenerProductoPorCodigoBarra($codigo, $id_sucursal = null)
{
    $conn = conectar_base_datos();

    // 1. Buscar el producto por código de barra o ID
    $sql = "
        SELECT 
            p.id_producto,
            p.nombre,
            p.codigo_barra,
            p.precio_venta,
            p.activo,
            t.rango_talla as talla,
            t.id_talla,
            c.nombre as color,
            c.id_color,
            cat.nombre as categoria,
            cat.id_categoria
        FROM inventario.producto p
        LEFT JOIN core.talla t ON t.id_talla = p.id_talla
        LEFT JOIN core.color c ON c.id_color = p.id_color
        LEFT JOIN core.categoria cat ON cat.id_categoria = p.id_categoria
        WHERE (p.codigo_barra = $1 OR CAST(p.id_producto AS TEXT) = $1) AND p.activo = true
    ";

    $stmtName = "get_prod_codigo_" . uniqid();
    pg_prepare($conn, $stmtName, $sql);
    $result = pg_execute($conn, $stmtName, [$codigo]);

    if (!$result || pg_num_rows($result) === 0) {
        return ["status" => false, "mensaje" => "Producto no encontrado en el sistema."];
    }

    $producto = pg_fetch_assoc($result);

    // 2. Si se proporciona sucursal, verificar existencia en inventario
    if ($id_sucursal) {
        $sqlInv = "SELECT cantidad FROM inventario.inventario WHERE id_producto = $1 AND id_sucursal = $2";
        $stmtInv = "check_inv_suc_" . uniqid();
        pg_prepare($conn, $stmtInv, $sqlInv);
        $resInv = pg_execute($conn, $stmtInv, [$producto['id_producto'], $id_sucursal]);

        if (!$resInv || pg_num_rows($resInv) === 0) {
            return [
                "status" => false,
                "mensaje" => "El producto existe pero NO está registrado en esta sucursal.",
                "producto_basico" => $producto // Opcional: devolver datos básicos si se quiere mostrar qué es
            ];
        }

        // Opcional: Verificar stock > 0 si se desea restringir venta sin stock aquí
        // $inv = pg_fetch_assoc($resInv);
        // if ($inv['cantidad'] <= 0) { ... }
    }

    return ["status" => true, "producto" => $producto];
}
