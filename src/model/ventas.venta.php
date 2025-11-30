<?php
include_once "model/mainModel.php";

class venta extends mainModel
{
    // Atributos de la venta
    public $id_cliente;
    public $id_usuario;
    public $total;
    public $fecha;

    public function __construct($id_cliente, $id_usuario, $total, $fecha = null)
    {
        $this->id_cliente = $id_cliente;
        $this->id_usuario = $id_usuario;
        $this->total = $total;
        $this->fecha = $fecha ?? date('Y-m-d H:i:s');
    }

    /**
     * Crea una venta completa de manera transaccional.
     * 
     * @param array $datosVenta Array con datos de la venta (id_cliente, id_usuario, total)
     * @param array $detalles Array de arrays con detalles (id_producto, cantidad, precio_unitario, subtotal)
     * @param array $pagos Array de arrays con pagos (id_metodo_pago, monto, id_moneda, tasa)
     * @return array|bool Retorna array con respuesta o false en error grave.
     */
    public static function crearVentaCompleta($datosVenta, $detalles, $pagos)
    {
        $conn = parent::conectar_base_datos();

        // Iniciar transacción
        pg_query($conn, "BEGIN");

        try {
            // 0. Insertar Cliente Nuevo si es necesario
            if (!empty($datosVenta['cliente_nuevo'])) {
                $cli = $datosVenta['cliente_nuevo'];
                // Validar campos mínimos
                if (empty($cli['cedula']) || empty($cli['nombre'])) {
                    throw new Exception("Datos del nuevo cliente incompletos (Cédula y Nombre requeridos).");
                }

                $queryCliente = "INSERT INTO core.cliente (cedula, nombre, apellido, email, telefono, direccion, activo, fecha_registro) 
                                 VALUES ($1, $2, $3, $4, $5, $6, 't', NOW()) RETURNING id_cliente";
                pg_prepare($conn, "insert_cliente_nuevo", $queryCliente);
                $resCli = pg_execute($conn, "insert_cliente_nuevo", [
                    $cli['cedula'],
                    $cli['nombre'],
                    $cli['apellido'],
                    $cli['email'] ?? null,
                    $cli['telefono'] ?? null,
                    $cli['direccion'] ?? null
                ]);

                if (!$resCli) {
                    throw new Exception("Error al registrar nuevo cliente. Verifique si la cédula ya existe.");
                }

                $rowCli = pg_fetch_assoc($resCli);
                $datosVenta['id_cliente'] = $rowCli['id_cliente'];
            }

            // 1. Insertar Venta
            $queryVenta = "INSERT INTO ventas.venta (id_cliente, id_usuario, total, fecha, activo) VALUES ($1, $2, $3, NOW(), 't') RETURNING id_venta";
            $stmtVenta = pg_prepare($conn, "insert_venta", $queryVenta);
            $resVenta = pg_execute($conn, "insert_venta", [
                $datosVenta['id_cliente'],
                $datosVenta['id_usuario'],
                $datosVenta['total']
            ]);

            if (!$resVenta) {
                throw new Exception("Error al insertar la venta.");
            }

            $rowVenta = pg_fetch_assoc($resVenta);
            $idVenta = $rowVenta['id_venta'];

            // 2. Insertar Detalles
            $queryDetalle = "INSERT INTO ventas.detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal, activo) VALUES ($1, $2, $3, $4, $5, 't')";
            pg_prepare($conn, "insert_detalle", $queryDetalle);

            // Query para descontar inventario (Asumiendo que se descuenta de la sucursal del usuario o una por defecto)
            // NOTA: El sistema actual parece requerir id_sucursal para inventario. 
            // Asumiremos que el usuario tiene una sucursal asignada o se pasa en $datosVenta.
            // Por ahora, usaremos una lógica simple de actualización si existe el registro en inventario.inventario
            $queryInventario = "UPDATE inventario.inventario SET cantidad = cantidad - $1 WHERE id_producto = $2 AND id_sucursal = $3";
            pg_prepare($conn, "update_inventario", $queryInventario);

            $idSucursal = $datosVenta['id_sucursal'] ?? 1; // Fallback a 1 si no se especifica (Debería venir del controller)

            foreach ($detalles as $detalle) {
                $resDetalle = pg_execute($conn, "insert_detalle", [
                    $idVenta,
                    $detalle['id_producto'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['subtotal']
                ]);

                if (!$resDetalle) {
                    throw new Exception("Error al insertar detalle del producto ID: " . $detalle['id_producto']);
                }

                // Descontar inventario
                $resInv = pg_execute($conn, "update_inventario", [
                    $detalle['cantidad'],
                    $detalle['id_producto'],
                    $idSucursal
                ]);

                // Opcional: Verificar si se actualizó alguna fila, si no, podría ser que no existe registro de inventario para esa sucursal
            }

            // 3. Insertar Pagos
            $queryPago = "INSERT INTO ventas.pago_venta (id_venta, id_metodo_pago, monto, id_moneda, tasa, activo) VALUES ($1, $2, $3, $4, $5, 't')";
            pg_prepare($conn, "insert_pago", $queryPago);

            foreach ($pagos as $pago) {
                $resPago = pg_execute($conn, "insert_pago", [
                    $idVenta,
                    $pago['id_metodo_pago'],
                    $pago['monto'],
                    $pago['id_moneda'],
                    $pago['tasa']
                ]);

                if (!$resPago) {
                    throw new Exception("Error al insertar pago.");
                }
            }

            // Confirmar transacción
            pg_query($conn, "COMMIT");
            return ["status" => true, "id_venta" => $idVenta, "mensaje" => "Venta registrada exitosamente"];
        } catch (Exception $e) {
            // Revertir cambios
            pg_query($conn, "ROLLBACK");
            return ["status" => false, "error" => $e->getMessage()];
        }
    }
}
