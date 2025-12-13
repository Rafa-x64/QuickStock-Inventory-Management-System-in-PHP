<?php

class venta extends mainModel
{
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

                // Sanitizar cédula: si es solo números, añadir prefijo "V-"
                if (ctype_digit($cedula)) {
                    $cedula = "V-" . $cedula;
                }

                if (empty($cedula) || empty($cli['nombre'])) {
                    throw new Exception("Datos del cliente incompletos: Cédula y Nombre son obligatorios.");
                }

                // Verificar si ya existe por cédula para evitar duplicados
                $queryCheckCli = "SELECT id_cliente FROM core.cliente WHERE cedula = $1 LIMIT 1";
                $resCheck = pg_query_params($conn, $queryCheckCli, [$cedula]);

                if (pg_num_rows($resCheck) > 0) {
                    // Ya existe, usamos su ID
                    $rowCli = pg_fetch_assoc($resCheck);
                    $idCliente = $rowCli['id_cliente'];
                } else {
                    // No existe, lo creamos
                    $queryInsertCli = "INSERT INTO core.cliente (cedula, nombre, apellido, correo, telefono, direccion, activo) 
                                       VALUES ($1, $2, $3, $4, $5, $6, 't') RETURNING id_cliente";
                    $resInsertCli = pg_query_params($conn, $queryInsertCli, [
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

            $queryVenta = "INSERT INTO ventas.venta (id_cliente, id_usuario, fecha, activo) 
                           VALUES ($1, $2, NOW(), 't') RETURNING id_venta";
            $resVenta = pg_query_params($conn, $queryVenta, [$idCliente, $idUsuario]);

            if (!$resVenta) {
                throw new Exception("Error al registrar la cabecera de la venta. Detalle: " . pg_last_error($conn));
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

            // Definir queries (sin preparar, usaremos pg_query_params directamente)
            $queryInsertDetalle = "INSERT INTO ventas.detalle_venta (id_venta, id_producto, cantidad, precio_unitario, activo) 
                                   VALUES ($1, $2, $3, $4, 't')";

            // Query seguro de descuento de stock: Solo actualiza si cantidad >= descuento
            $queryUpdateStock = "UPDATE inventario.inventario 
                                 SET cantidad = cantidad - $1 
                                 WHERE id_producto = $2 AND id_sucursal = $3 AND cantidad >= $1";

            // Query para verificar existencia si falla el update (para dar mejor error)
            $queryCheckStock = "SELECT cantidad FROM inventario.inventario WHERE id_producto = $1 AND id_sucursal = $2";

            foreach ($detalles as $item) {
                $idProducto = $item['id_producto'];
                $cantidad = $item['cantidad'];
                $precio = $item['precio_unitario'];
                $subtotal = $item['subtotal'];

                // A. Insertar Detalle
                $resDetalle = pg_query_params($conn, $queryInsertDetalle, [$idVenta, $idProducto, $cantidad, $precio]);
                if (!$resDetalle) {
                    throw new Exception("Error al guardar detalle del producto ID: $idProducto. Detalle: " . pg_last_error($conn));
                }

                // B. Descontar Inventario
                $resStock = pg_query_params($conn, $queryUpdateStock, [$cantidad, $idProducto, $idSucursal]);
                $affected = pg_affected_rows($resStock);

                if ($affected == 0) {
                    // Si no se actualizó ninguna fila, puede ser:
                    // 1. No existe el registro en inventario para esa sucursal.
                    // 2. El stock es insuficiente.

                    $resCheck = pg_query_params($conn, $queryCheckStock, [$idProducto, $idSucursal]);
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
            $queryPago = "INSERT INTO ventas.pago_venta (id_venta, id_metodo_pago, monto, id_moneda, tasa, referencia, activo) 
                          VALUES ($1, $2, $3, $4, $5, $6, 't')";

            foreach ($pagos as $pago) {
                $resPago = pg_query_params($conn, $queryPago, [
                    $idVenta,
                    $pago['id_metodo_pago'],
                    $pago['monto'],
                    $pago['id_moneda'],
                    $pago['tasa'],
                    $pago['referencia'] ?? null
                ]);

                if (!$resPago) {
                    throw new Exception("Error al registrar el pago: " . pg_last_error($conn));
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
