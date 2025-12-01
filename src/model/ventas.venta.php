<?php

class venta extends mainModel
{
    /**
     * Crea una venta completa de manera transaccional (ACID).
     * 
     * @param array $datosVenta Array con datos de la venta (id_cliente, id_usuario, id_sucursal, total, cliente_nuevo)
     * @param array $detalles Array de arrays con detalles (id_producto, cantidad, precio_unitario, subtotal)
     * @param array $pagos Array de arrays con pagos (id_metodo_pago, monto, id_moneda, tasa)
     * @return array Retorna array con status (true/false), mensaje o error, y datos resultantes.
     */
    public static function crearVentaCompleta($datosVenta, $detalles, $pagos)
    {
        $conn = parent::conectar_base_datos();

        // Iniciar transacción explícita
        pg_query($conn, "BEGIN");

        try {
            // =================================================================================
            // 1. GESTIÓN DE CLIENTE (Verificar existencia o Registrar)
            // =================================================================================
            $idCliente = $datosVenta['id_cliente'] ?? null;

            // Si viene información de cliente nuevo, priorizamos la verificación/creación
            if (!empty($datosVenta['cliente_nuevo'])) {
                $cli = $datosVenta['cliente_nuevo'];
                $cedula = trim($cli['cedula']);

                if (empty($cedula) || empty($cli['nombre'])) {
                    throw new Exception("Datos del cliente incompletos: Cédula y Nombre son obligatorios.");
                }

                // Verificar si ya existe por cédula para evitar duplicados
                $queryCheckCli = "SELECT id_cliente FROM core.cliente WHERE cedula = $1 LIMIT 1";
                pg_prepare($conn, "check_cliente_existente", $queryCheckCli);
                $resCheck = pg_execute($conn, "check_cliente_existente", [$cedula]);

                if (pg_num_rows($resCheck) > 0) {
                    // Ya existe, usamos su ID
                    $rowCli = pg_fetch_assoc($resCheck);
                    $idCliente = $rowCli['id_cliente'];
                } else {
                    // No existe, lo creamos
                    $queryInsertCli = "INSERT INTO core.cliente (cedula, nombre, apellido, correo, telefono, direccion, activo) 
                                       VALUES ($1, $2, $3, $4, $5, $6, 't') RETURNING id_cliente";
                    pg_prepare($conn, "insert_cliente_nuevo", $queryInsertCli);
                    $resInsertCli = pg_execute($conn, "insert_cliente_nuevo", [
                        $cedula,
                        $cli['nombre'],
                        $cli['apellido'],
                        $cli['email'] ?? null,
                        $cli['telefono'] ?? null,
                        $cli['direccion'] ?? null
                    ]);

                    if (!$resInsertCli) {
                        throw new Exception("Error al registrar el nuevo cliente.");
                    }
                    $rowNewCli = pg_fetch_assoc($resInsertCli);
                    $idCliente = $rowNewCli['id_cliente'];
                }
            }

            if (empty($idCliente)) {
                throw new Exception("No se ha identificado un cliente válido para la venta.");
            }

            // =================================================================================
            // 2. REGISTRO DE LA VENTA
            // =================================================================================
            $idUsuario = $datosVenta['id_usuario'];
            $totalVenta = $datosVenta['total'];

            $queryVenta = "INSERT INTO ventas.venta (id_cliente, id_usuario, total, fecha, activo) 
                           VALUES ($1, $2, $3, NOW(), 't') RETURNING id_venta";
            pg_prepare($conn, "insert_venta_header", $queryVenta);
            $resVenta = pg_execute($conn, "insert_venta_header", [$idCliente, $idUsuario, $totalVenta]);

            if (!$resVenta) {
                throw new Exception("Error al registrar la cabecera de la venta.");
            }

            $rowVenta = pg_fetch_assoc($resVenta);
            $idVenta = $rowVenta['id_venta'];

            // =================================================================================
            // 3. PROCESAMIENTO DE DETALLES E INVENTARIO (CRÍTICO)
            // =================================================================================
            $idSucursal = $datosVenta['id_sucursal'];
            if (empty($idSucursal)) {
                throw new Exception("Sucursal no definida para el descuento de inventario.");
            }

            // Preparar queries repetitivos
            $queryInsertDetalle = "INSERT INTO ventas.detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal, activo) 
                                   VALUES ($1, $2, $3, $4, $5, 't')";
            pg_prepare($conn, "insert_detalle_item", $queryInsertDetalle);

            // Query seguro de descuento de stock: Solo actualiza si cantidad >= descuento
            $queryUpdateStock = "UPDATE inventario.inventario 
                                 SET cantidad = cantidad - $1 
                                 WHERE id_producto = $2 AND id_sucursal = $3 AND cantidad >= $1";
            pg_prepare($conn, "update_stock_seguro", $queryUpdateStock);

            // Query para verificar existencia si falla el update (para dar mejor error)
            $queryCheckStock = "SELECT cantidad FROM inventario.inventario WHERE id_producto = $1 AND id_sucursal = $2";
            pg_prepare($conn, "check_stock_actual", $queryCheckStock);

            foreach ($detalles as $item) {
                $idProducto = $item['id_producto'];
                $cantidad = $item['cantidad'];
                $precio = $item['precio_unitario'];
                $subtotal = $item['subtotal'];

                // A. Insertar Detalle
                $resDetalle = pg_execute($conn, "insert_detalle_item", [$idVenta, $idProducto, $cantidad, $precio, $subtotal]);
                if (!$resDetalle) {
                    throw new Exception("Error al guardar detalle del producto ID: $idProducto");
                }

                // B. Descontar Inventario
                $resStock = pg_execute($conn, "update_stock_seguro", [$cantidad, $idProducto, $idSucursal]);
                $affected = pg_affected_rows($resStock);

                if ($affected == 0) {
                    // Si no se actualizó ninguna fila, puede ser:
                    // 1. No existe el registro en inventario para esa sucursal.
                    // 2. El stock es insuficiente.

                    $resCheck = pg_execute($conn, "check_stock_actual", [$idProducto, $idSucursal]);
                    if (pg_num_rows($resCheck) == 0) {
                        throw new Exception("El producto ID $idProducto no está registrado en el inventario de esta sucursal.");
                    } else {
                        $rowStock = pg_fetch_assoc($resCheck);
                        $stockActual = $rowStock['cantidad'];
                        throw new Exception("Stock insuficiente para el producto ID $idProducto. Disponible: $stockActual, Solicitado: $cantidad.");
                    }
                }
            }

            // =================================================================================
            // 4. REGISTRO DE PAGOS
            // =================================================================================
            $queryPago = "INSERT INTO ventas.pago_venta (id_venta, id_metodo_pago, monto, id_moneda, tasa, activo) 
                          VALUES ($1, $2, $3, $4, $5, 't')";
            pg_prepare($conn, "insert_pago_item", $queryPago);

            foreach ($pagos as $pago) {
                $resPago = pg_execute($conn, "insert_pago_item", [
                    $idVenta,
                    $pago['id_metodo_pago'],
                    $pago['monto'],
                    $pago['id_moneda'],
                    $pago['tasa']
                ]);

                if (!$resPago) {
                    throw new Exception("Error al registrar el pago.");
                }
            }

            // =================================================================================
            // 5. CONFIRMACIÓN (COMMIT)
            // =================================================================================
            pg_query($conn, "COMMIT");

            return [
                "status" => true,
                "mensaje" => "Venta procesada exitosamente.",
                "id_venta" => $idVenta
            ];
        } catch (Exception $e) {
            // =================================================================================
            // 6. REVERSIÓN (ROLLBACK)
            // =================================================================================
            pg_query($conn, "ROLLBACK");

            return [
                "status" => false,
                "error" => $e->getMessage()
            ];
        }
    }
}
