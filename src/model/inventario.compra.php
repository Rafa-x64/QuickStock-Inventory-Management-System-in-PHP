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
            fecha_compra, subtotal, monto_impuesto, total, observaciones, estado
        ) VALUES (
            $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11
        ) RETURNING id_compra";

        $result = pg_query_params($conn, $sql, [
            $data['id_proveedor'],      // $1
            $data['id_sucursal'],       // $2
            $data['id_usuario'],        // $3
            $data['id_moneda'],         // $4
            $data['numero_factura'],    // $5
            $data['fecha_compra'],      // $6
            $subtotal,                  // $7
            $montoImpuesto,             // $8
            $total,                     // $9
            $data['observaciones'],     // $10
            $data['estado']             // $11
        ]);

        if (!$result || pg_num_rows($result) == 0) {
            throw new Exception("No se pudo insertar la compra principal.");
        }
        return intval(pg_fetch_result($result, 0, 'id_compra'));
    }

    protected function crearDetalleCompra($conn, int $id_compra, array $item): void
    {
        $sql = "INSERT INTO inventario.detalle_compra (
            id_compra, id_producto, cantidad, precio_unitario, subtotal
        ) VALUES (
            $1, $2, $3, $4, $5
        )";

        $result = pg_query_params($conn, $sql, [
            $id_compra,                 // $1
            $item['id_producto'],       // $2
            $item['cantidad'],          // $3
            $item['precio_compra'],     // $4 (Precio Unitario)
            $item['subtotal_detalle']   // $5
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
}
