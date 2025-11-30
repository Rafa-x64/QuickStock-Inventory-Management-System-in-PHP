<?php
include_once __DIR__ . "/../model/mainModel.php";
include_once __DIR__ . "/../model/ventas.venta.php";
include_once __DIR__ . "/../model/inventario.producto.php";

class ventas_punto_venta_C extends mainModel
{
    public static function procesarVenta($request)
    {
        // 1. Validar datos básicos
        $idCliente = isset($request['id_cliente']) ? trim($request['id_cliente']) : null;
        $idUsuario = isset($request['id_usuario']) ? trim($request['id_usuario']) : null;
        $idSucursal = isset($request['id_sucursal']) ? trim($request['id_sucursal']) : null;
        $detalles = $request['detalles'] ?? [];
        $pagos = $request['pagos'] ?? [];
        $totalVenta = $request['total_venta'] ?? 0;

        if (empty($idUsuario) || empty($idSucursal) || empty($detalles) || empty($pagos)) {
            return ["status" => false, "error" => "Datos incompletos para procesar la venta. Falta sucursal, usuario o detalles."];
        }

        // 2. Validar y Estructurar Detalles
        $detallesProcesados = [];
        $totalCalculado = 0;

        foreach ($detalles as $item) {
            $idProducto = $item['id_producto'];
            $cantidad = intval($item['cantidad']);
            $precio = floatval($item['precio_unitario']);

            if ($cantidad <= 0) {
                return ["status" => false, "error" => "Cantidad inválida para el producto ID: $idProducto"];
            }

            $subtotal = $cantidad * $precio;
            $totalCalculado += $subtotal;

            $detallesProcesados[] = [
                "id_producto" => $idProducto,
                "cantidad" => $cantidad,
                "precio_unitario" => $precio,
                "subtotal" => $subtotal
            ];
        }

        // 3. Validar Pagos
        $pagosProcesados = [];
        foreach ($pagos as $pago) {
            $pagosProcesados[] = [
                "id_metodo_pago" => $pago['id_metodo_pago'],
                "monto" => floatval($pago['monto']),
                "id_moneda" => $pago['id_moneda'],
                "tasa" => floatval($pago['tasa']),
                "referencia" => $pago['referencia'] ?? null
            ];
        }

        // 4. Ejecutar Venta en Modelo
        $datosVenta = [
            "id_cliente" => $idCliente,
            "id_usuario" => $idUsuario,
            "id_sucursal" => $idSucursal,
            "total" => $totalVenta,
            "cliente_nuevo" => $request['cliente_nuevo'] ?? null
        ];

        $resultado = venta::crearVentaCompleta($datosVenta, $detallesProcesados, $pagosProcesados);

        return $resultado;
    }
}
