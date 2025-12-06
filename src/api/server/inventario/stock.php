<?php



function ajustarStock($id_producto, $id_sucursal, $cantidad, $tipo_ajuste, $motivo, $comentario = null)
{
    $conn = conectar_base_datos();

    // 1. Validaciones básicas
    if (!$id_producto || !$id_sucursal || !$cantidad || !$tipo_ajuste || !$motivo) {
        return ["status" => "error", "message" => "Faltan datos obligatorios para el ajuste."];
    }

    $cantidad = (int)$cantidad;
    if ($cantidad <= 0) {
        return ["status" => "error", "message" => "La cantidad debe ser mayor a 0."];
    }

    // 2. Verificar existencia del producto en inventario de la sucursal
    $sql_check = "SELECT cantidad, minimo FROM inventario.inventario WHERE id_producto = $1 AND id_sucursal = $2";
    $stmt_check = "check_inv_" . uniqid();
    pg_prepare($conn, $stmt_check, $sql_check);
    $res_check = pg_execute($conn, $stmt_check, [$id_producto, $id_sucursal]);

    if (!$res_check || pg_num_rows($res_check) === 0) {
        // Si no existe, intentar crearlo (solo si es entrada)
        if ($tipo_ajuste === 'entrada') {
            $sql_insert = "INSERT INTO inventario.inventario (id_producto, id_sucursal, cantidad, minimo) VALUES ($1, $2, 0, 5)"; // Minimo default 5
            $stmt_insert = "insert_inv_" . uniqid();
            pg_prepare($conn, $stmt_insert, $sql_insert);
            pg_execute($conn, $stmt_insert, [$id_producto, $id_sucursal]);
            $stock_actual = 0;
        } else {
            return ["status" => "error", "message" => "El producto no existe en esta sucursal y no se puede restar stock."];
        }
    } else {
        $row = pg_fetch_assoc($res_check);
        $stock_actual = (int)$row['cantidad'];
    }

    // 3. Calcular nuevo stock
    if ($tipo_ajuste === 'entrada') {
        $nuevo_stock = $stock_actual + $cantidad;
    } elseif ($tipo_ajuste === 'salida') {
        $nuevo_stock = $stock_actual - $cantidad;
        if ($nuevo_stock < 0) {
            return ["status" => "error", "message" => "Stock insuficiente. Stock actual: $stock_actual"];
        }
    } else {
        return ["status" => "error", "message" => "Tipo de ajuste inválido."];
    }

    // 4. Actualizar inventario
    $sql_update = "UPDATE inventario.inventario SET cantidad = $1 WHERE id_producto = $2 AND id_sucursal = $3";
    $stmt_update = "update_inv_" . uniqid();
    pg_prepare($conn, $stmt_update, $sql_update);
    $res_update = pg_execute($conn, $stmt_update, [$nuevo_stock, $id_producto, $id_sucursal]);

    if (!$res_update) {
        return ["status" => "error", "message" => "Error al actualizar el stock en base de datos."];
    }

    // 5. Registrar movimiento (Opcional pero recomendado, por ahora solo retornamos éxito)
    // Aquí podrías insertar en una tabla de historial de movimientos si existiera.

    // 6. Verificar Alerta de Stock Bajo
    if ($nuevo_stock <= $row['minimo']) {
        require_once __DIR__ . "/../mailer.php";

        // Obtener nombre del producto y sucursal si no los tenemos a mano (aunque nombre prod no lo tenemos en params, lo buscamos o lo pasamos)
        // Para optimizar, haremos una pequeña query para obtener nombre producto y sucursal
        $sql_info = "SELECT p.nombre as producto, s.nombre as sucursal 
                     FROM inventario.producto p, core.sucursal s 
                     WHERE p.id_producto = $1 AND s.id_sucursal = $2";
        $stmt_info = "info_alert_" . uniqid();
        pg_prepare($conn, $stmt_info, $sql_info);
        $res_info = pg_execute($conn, $stmt_info, [$id_producto, $id_sucursal]);
        $info = pg_fetch_assoc($res_info);

        if ($info) {
            enviarCorreoAlerta($info['producto'], $nuevo_stock, $row['minimo'], $info['sucursal']);
        }
    }

    return [
        "status" => "success",
        "message" => "Stock actualizado correctamente.",
        "nuevo_stock" => $nuevo_stock,
        "id_producto" => $id_producto
    ];
}
