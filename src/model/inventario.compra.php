<?php
// Asegúrate de incluir las dependencias
require_once 'model/core.color.php';
require_once 'model/core.talla.php';
require_once 'model/inventario.producto.php';

class compra extends mainModel
{
    /**
     * Realiza la transacción completa de registro de una compra y sus productos.
     * @param array $datosCompra Array con datos de la compra principal.
     * @param array $productosAdquiridos Array de productos (limpios y válidos) para la compra.
     * @return array Resultado de la operación (success/error).
     */
    public function registrarTransaccionCompra(array $datosCompra, array $productosAdquiridos): array
    {
        $conn = parent::conectar_base_datos();
        pg_query($conn, "BEGIN"); // ⬅️ INICIAR TRANSACCIÓN

        try {
            // ***************************************************************
            // PASO 1: Procesar productos y obtener los IDs definitivos
            // ***************************************************************
            $productosParaDetalle = [];

            foreach ($productosAdquiridos as $producto) {

                // 1.1 Obtener ID de Color (si es nuevo, se busca o se crea)
                $id_color = $producto['id_color'];
                if ($producto['tipo_color'] === 'nuevo') {
                    // ASUMIDO: color::buscarOCrearPorNombre maneja la lógica de buscar por nombre/crear y devuelve el ID.
                    $id_color = color::buscarOCrearPorNombre($producto['nombre_color']);
                }
                if (!$id_color) throw new Exception("Error al obtener ID de Color o color nulo.");

                // 1.2 Obtener ID de Talla (si es nuevo, se busca o se crea)
                $id_talla = $producto['id_talla'];
                if ($producto['tipo_talla'] === 'nuevo') {
                    // ASUMIDO: talla::buscarOCrearPorRango maneja la lógica de buscar por rango/crear y devuelve el ID.
                    $id_talla = talla::buscarOCrearPorRango($producto['rango_talla']);
                }
                if (!$id_talla) throw new Exception("Error al obtener ID de Talla o talla nula.");

                // 1.3 Buscar o Crear Producto
                $productoExistente = producto::buscarPorNombreOCodigo($producto['nombre'], $producto['codigo_barra']);
                $id_producto = null;

                if ($productoExistente) {
                    // Si existe, reusamos el ID
                    $id_producto = intval($productoExistente['id_producto']);
                } else {
                    // 1.4 Crear nuevo producto si no existe
                    $nuevoProducto = new producto(
                        0,
                        $producto['nombre'],
                        null,
                        $producto['id_categoria'] ?? null, // 👈 Nullable Category
                        $id_color,
                        $id_talla,
                        $producto['precio_venta'],
                        $datosCompra['id_proveedor'],
                        true, // activo
                        $producto['codigo_barra'],
                        $producto['precio_compra']
                    );
                    $id_producto = $nuevoProducto->crear();
                }
                if (!$id_producto) throw new Exception("Error al obtener ID de Producto.");


                // Preparar datos finales del producto para el detalle y el inventario
                $productosParaDetalle[] = [
                    'id_producto'       => $id_producto,
                    'cantidad'          => (int) $producto['cantidad'],
                    'precio_compra'     => (float) $producto['precio_compra'],
                    'subtotal_detalle'  => (float) $producto['cantidad'] * (float) $producto['precio_compra']
                ];
            }

            // Verificación final (doble chequeo)
            if (count($productosParaDetalle) === 0) {
                throw new Exception("No hay productos válidos para registrar en la compra.");
            }

            // ***************************************************************
            // PASO 2: Calcular totales y registrar la COMPRA PRINCIPAL
            // ***************************************************************
            $subtotalGlobal = array_sum(array_column($productosParaDetalle, 'subtotal_detalle'));
            $ivaRate = 0.16; // Asumimos 16% fijo
            $montoImpuesto = round($subtotalGlobal * $ivaRate, 2);
            $totalGlobal = $subtotalGlobal + $montoImpuesto;

            $id_compra = $this->crearCompraPrincipal($conn, $datosCompra, $subtotalGlobal, $montoImpuesto, $totalGlobal);
            if (!$id_compra) throw new Exception("No se pudo obtener el ID de la compra principal.");


            // ***************************************************************
            // PASO 3: Registrar el DETALLE DE COMPRA y actualizar INVENTARIO
            // ***************************************************************
            $id_sucursal = (int)$datosCompra['id_sucursal'];

            foreach ($productosParaDetalle as $item) {
                // 3.1 Insertar Detalle de Compra
                $this->crearDetalleCompra($conn, $id_compra, $item);

                // 3.2 Actualizar Inventario (función que maneja INSERT/UPDATE)
                $this->actualizarInventario($conn, $item['id_producto'], $id_sucursal, $item['cantidad']);
            }

            pg_query($conn, "COMMIT"); // ⬅️ FINALIZAR TRANSACCIÓN (ÉXITO)
            return ["success" => true, "id_compra" => $id_compra];
        } catch (Exception $e) {
            pg_query($conn, "ROLLBACK"); // ⬅️ REVERTIR TRANSACCIÓN (FALLO)
            return ["error" => "Error en la transacción de compra: " . $e->getMessage()];
        }
    }

    protected function crearCompraPrincipal($conn, array $data, float $subtotal, float $montoImpuesto, float $total): int
    {
        $sql = "INSERT INTO inventario.compra (
            id_proveedor, id_sucursal, id_usuario, id_moneda, numero_factura, 
            fecha_compra, observaciones, estado
        ) VALUES (
            $1, $2, $3, $4, $5, $6, $7, $8
        ) RETURNING id_compra";

        $result = pg_query_params($conn, $sql, [
            $data['id_proveedor'],      // $1
            $data['id_sucursal'],       // $2
            $data['id_usuario'],        // $3
            $data['id_moneda'],         // $4
            $data['numero_factura'],    // $5
            $data['fecha_compra'],      // $6
            $data['observaciones'],     // $7
            $data['estado']             // $8
        ]);

        if (!$result || pg_num_rows($result) == 0) {
            throw new Exception("No se pudo insertar la compra principal.");
        }
        return intval(pg_fetch_result($result, 0, 'id_compra'));
    }

    protected function crearDetalleCompra($conn, int $id_compra, array $item): void
    {
        $sql = "INSERT INTO inventario.detalle_compra (
            id_compra, id_producto, cantidad, precio_unitario
        ) VALUES (
            $1, $2, $3, $4
        )";

        $result = pg_query_params($conn, $sql, [
            $id_compra,                 // $1
            $item['id_producto'],       // $2
            $item['cantidad'],          // $3
            $item['precio_compra'],     // $4 (Precio Unitario)
        ]);

        if (!$result) {
            throw new Exception("Error al insertar el detalle para el producto ID: " . $item['id_producto']);
        }
    }

    protected function actualizarInventario($conn, int $id_producto, int $id_sucursal, int $cantidad): void
    {
        // 1. Intentar actualizar (si ya existe)
        $sql_update = "
            UPDATE inventario.inventario
            SET cantidad = cantidad + $1
            WHERE id_producto = $2 AND id_sucursal = $3
        ";
        $result = pg_query_params($conn, $sql_update, [$cantidad, $id_producto, $id_sucursal]);

        if (!$result) {
            throw new Exception("Error al intentar actualizar el inventario (UPDATE).");
        }

        // 2. Si no se actualizó (no existía), insertar
        if (pg_affected_rows($result) === 0) {
            $sql_insert = "
                INSERT INTO inventario.inventario (id_producto, id_sucursal, cantidad, minimo, activo)
                VALUES ($1, $2, $3, 0, true)
            ";
            $result_insert = pg_query_params($conn, $sql_insert, [$id_producto, $id_sucursal, $cantidad]);

            if (!$result_insert) {
                throw new Exception("Error al insertar el inventario (INSERT) para el producto ID: " . $id_producto);
            }
        }
    }

    public function actualizarTransaccionCompra(array $datosCompra, array $productosEntrantes): array
    {
        $conn = parent::conectar_base_datos();
        pg_query($conn, "BEGIN"); // ⬅️ START TRANSACTION

        try {
            $id_compra = intval($datosCompra['id_compra']);
            $id_sucursal = intval($datosCompra['id_sucursal']);

            // ***************************************************************
            // PASO 1: Obtener detalles actuales de la BD (para detectar eliminados y cambios)
            // ***************************************************************
            $sql_detalles = "SELECT id_detalle_compra, id_producto, cantidad FROM inventario.detalle_compra WHERE id_compra = $1";
            $res_detalles = pg_query_params($conn, $sql_detalles, [$id_compra]);

            $detallesEnBD = []; // Mapa: id_detalle_compra => [id_producto, cantidad]
            while ($row = pg_fetch_assoc($res_detalles)) {
                $detallesEnBD[intval($row['id_detalle_compra'])] = [
                    'id_producto' => intval($row['id_producto']),
                    'cantidad' => intval($row['cantidad'])
                ];
            }

            // Identificar IDs que vienen en el POST
            $idsEntrantes = [];
            $subtotalGlobal = 0;

            // ***************************************************************
            // PASO 2: Procesar productos ENTRANTES (Updates e Inserts)
            // ***************************************************************

            foreach ($productosEntrantes as $item) {
                // 2.1 Resolver IDs de Color, Talla y Producto (Lógica compartida con Crear)
                // -----------------------------------------------------------------------
                $id_color = $item['id_color'];
                if (isset($item['tipo_color']) && $item['tipo_color'] === 'nuevo') {
                    $id_color = color::buscarOCrearPorNombre($item['nombre_color']);
                }

                $id_talla = $item['id_talla'];
                if (isset($item['tipo_talla']) && $item['tipo_talla'] === 'nuevo') {
                    $id_talla = talla::buscarOCrearPorRango($item['rango_talla']);
                }

                // Resolver Producto (Buscar existente o Crear nuevo)
                // Nota: Si es una línea existente editada, id_producto_existente ya viene, pero verificamos integridad.
                $id_producto = null;
                $productoExistente = producto::buscarPorNombreOCodigo($item['nombre'], $item['codigo_barra']);

                if ($productoExistente) {
                    $id_producto = intval($productoExistente['id_producto']);
                    // Opcional: Actualizar precio venta si cambió
                    // producto::actualizarPrecioVenta($id_producto, $item['precio_venta']); 
                } else {
                    // Si no existe, CREAR NUEVO PRODUCTO
                    $nuevoProducto = new producto(
                        0,
                        $item['nombre'],
                        null,
                        $item['id_categoria'] ?? null,
                        $id_color,
                        $id_talla,
                        $item['precio_venta'],
                        $datosCompra['id_proveedor'],
                        true,
                        $item['codigo_barra'],
                        $item['precio_compra']
                    );
                    $id_producto = $nuevoProducto->crear();
                }

                if (!$id_producto) throw new Exception("Error al resolver producto: " . $item['nombre']);

                // Calculamos subtotal de la línea
                $subtotalLinea = $item['cantidad'] * $item['precio_compra'];
                $subtotalGlobal += $subtotalLinea;

                // 2.2 Distinguir entre UPDATE e INSERT
                // -----------------------------------------------------------------------
                if (!empty($item['id_detalle_compra']) && isset($detallesEnBD[$item['id_detalle_compra']])) {
                    // --- CASO: ACTUALIZACIÓN ---
                    $id_detalle = intval($item['id_detalle_compra']);
                    $datosAntiguos = $detallesEnBD[$id_detalle];
                    $cantidadNueva = intval($item['cantidad']);
                    $cantidadAntigua = intval($datosAntiguos['cantidad']);

                    // Actualizar registro en detalle_compra
                    $sql_upd_det = "UPDATE inventario.detalle_compra SET id_producto = $1, cantidad = $2, precio_unitario = $3 WHERE id_detalle_compra = $4";
                    pg_query_params($conn, $sql_upd_det, [$id_producto, $cantidadNueva, $item['precio_compra'], $id_detalle]);

                    // Actualizar Inventario (Diferencial)
                    // Si el producto cambió (raro en edición, pero posible), revertimos stock del viejo y sumamos al nuevo
                    if ($datosAntiguos['id_producto'] != $id_producto) {
                        $this->actualizarInventario($conn, $datosAntiguos['id_producto'], $id_sucursal, - ($cantidadAntigua)); // Revertir antiguo
                        $this->actualizarInventario($conn, $id_producto, $id_sucursal, $cantidadNueva); // Sumar nuevo
                    } else {
                        // Mismo producto, solo ajustamos diferencia (Nueva - Antigua)
                        // Ej: Tenia 5, ahora 8. Diff = 3. Sumar 3.
                        // Ej: Tenia 5, ahora 2. Diff = -3. Restar 3.
                        $diferencia = $cantidadNueva - $cantidadAntigua;
                        if ($diferencia != 0) {
                            $this->actualizarInventario($conn, $id_producto, $id_sucursal, $diferencia);
                        }
                    }

                    // Marcar ID como procesado
                    $idsEntrantes[] = $id_detalle;
                } else {
                    // --- CASO: INSERCIÓN (Nueva línea en edición) ---
                    $this->crearDetalleCompra($conn, $id_compra, [
                        'id_producto' => $id_producto,
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $item['precio_compra'],
                        'subtotal_detalle' => $subtotalLinea
                    ]);

                    // Sumar al inventario
                    $this->actualizarInventario($conn, $id_producto, $id_sucursal, $item['cantidad']);
                }
            }

            // ***************************************************************
            // PASO 3: Procesar ELIMINACIONES (IDs en BD que no vinieron en POST)
            // ***************************************************************
            foreach ($detallesEnBD as $id_detalle_bd => $infoBD) {
                if (!in_array($id_detalle_bd, $idsEntrantes)) {
                    // Revertir Stock (Restar lo que se había comprado)
                    $this->actualizarInventario($conn, $infoBD['id_producto'], $id_sucursal, - ($infoBD['cantidad']));

                    // Eliminar fila
                    pg_query_params($conn, "DELETE FROM inventario.detalle_compra WHERE id_detalle_compra = $1", [$id_detalle_bd]);
                }
            }

            // ***************************************************************
            // PASO 4: Actualizar Cabecera de Compra
            // ***************************************************************
            $ivaRate = 0.16;
            $montoImpuesto = round($subtotalGlobal * $ivaRate, 2);
            $totalGlobal = $subtotalGlobal + $montoImpuesto;

            $sql_update_header = "UPDATE inventario.compra SET 
                id_proveedor=$1, id_sucursal=$2, id_usuario=$3, id_moneda=$4, 
                numero_factura=$5, fecha_compra=$6, observaciones=$7, estado=$8 
                WHERE id_compra=$9";

            $res_head = pg_query_params($conn, $sql_update_header, [
                $datosCompra['id_proveedor'],
                $datosCompra['id_sucursal'],
                $datosCompra['id_usuario'],
                $datosCompra['id_moneda'],
                $datosCompra['numero_factura'],
                $datosCompra['fecha_compra'],
                $datosCompra['observaciones'],
                $datosCompra['estado'],
                $id_compra
            ]);

            if (!$res_head) throw new Exception("Error al actualizar la cabecera de la compra.");

            pg_query($conn, "COMMIT"); // ⬅️ COMMIT
            return ["success" => true, "id_compra" => $id_compra];
        } catch (Exception $e) {
            pg_query($conn, "ROLLBACK"); // ⬅️ ROLLBACK
            return ["success" => false, "error" => "Error en actualización: " . $e->getMessage()];
        }
    }
}
