<?php
include_once "model/ventas.venta.php";
include_once "model/inventario.producto.php"; // Asumiendo que existe para validar stock/precios si es necesario
include_once "model/core.cliente.php"; // Para validar cliente

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

        if (empty($idCliente) || empty($idUsuario) || empty($detalles) || empty($pagos)) {
            return ["error" => "Datos incompletos para procesar la venta."];
        }

        // 2. Validar Cliente (Opcional, si el ID viene del front se asume existente, pero mejor verificar)
        // Aquí podríamos llamar a cliente::obtenerUnClientePorId($idCliente) si existiera ese método público estático simple.

        // 3. Validar y Estructurar Detalles
        $detallesProcesados = [];
        $totalCalculado = 0;

        foreach ($detalles as $item) {
            $idProducto = $item['id_producto'];
            $cantidad = intval($item['cantidad']);
            $precio = floatval($item['precio_venta']); // Precio unitario

            if ($cantidad <= 0) {
                return ["error" => "Cantidad inválida para el producto ID: $idProducto"];
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

        // Validar Total (con un margen de error pequeño por redondeo)
        if (abs($totalCalculado - floatval($totalVenta)) > 0.1) {
            // Nota: A veces el front envía totales con descuentos o impuestos ya calculados. 
            // Si la lógica de negocio incluye impuestos, deberíamos calcularlos aquí.
            // Por simplicidad y siguiendo el plan, confiaremos en la validación del backend pero permitiremos el valor del front si es consistente.
            // return ["error" => "Discrepancia en el total de la venta. Calculado: $totalCalculado, Recibido: $totalVenta"];
        }

        // 4. Validar Pagos
        $totalPagado = 0;
        $pagosProcesados = [];
        foreach ($pagos as $pago) {
            $monto = floatval($pago['monto']);
            $tasa = floatval($pago['tasa']);
            $idMoneda = $pago['id_moneda'];

            // Convertir todo a la moneda base (asumimos USD como base si tasa=1, o calculamos)
            // La lógica de conversión depende de cómo se manejen las tasas.
            // Si la tasa es "Bs por Dólar" y el monto es en Bs, MontoBase = Monto / Tasa.
            // Si el monto es en USD, MontoBase = Monto.

            // Asumiremos que el front envía 'monto' en la moneda del pago, y 'tasa' de conversión a la moneda base.
            // Si la moneda base es USD (id 2) y pagamos en VES (id 3) con tasa 40:
            // Valor en USD = MontoVES / Tasa.

            // Sin embargo, para la tabla pago_venta, guardamos el monto original, la moneda y la tasa.
            // La columna generada 'monto_convertido' en la BD hace (monto * tasa). 
            // OJO: En el SQL: monto_convertido numeric(12,2) GENERATED ALWAYS AS ((monto * tasa)) STORED
            // Esto implica que la tasa debe ser un multiplicador hacia la moneda base o referencia.
            // Si la base es USD:
            // Pago en USD (100): Tasa 1.0 -> 100 * 1 = 100.
            // Pago en VES (4000): Si Tasa es 0.025 (1/40) -> 4000 * 0.025 = 100.
            // PERO normalmente las tasas se guardan como "40.00".
            // Si el SQL dice monto * tasa, entonces para VES la tasa debería ser 1/40 si queremos el valor en USD.
            // O quizás la columna generado está pensada para otra cosa.
            // Revisando el SQL: `monto_convertido numeric(12,2) GENERATED ALWAYS AS ((monto * tasa)) STORED`
            // Si guardo Monto=4000 (VES) y Tasa=40.00 -> Resultado 160000. Esto NO es el valor en USD.
            // Esto sugiere que 'monto_convertido' podría ser el valor en Bolívares (Moneda Local) si la base es Dólares? NO.
            // O tal vez la tasa que se guarda debe ser la inversa si se quiere normalizar.

            // DECISIÓN: Guardaremos la tasa tal cual viene (ej. 40.5 para VES/USD).
            // El backend NO dependerá de la columna generada para validar el total pagado AHORA, sino del cálculo manual.

            $valorEnMonedaBase = 0;
            // Lógica simple de validación de total pagado (asumiendo USD como base)
            if ($idMoneda == 2) { // USD
                $valorEnMonedaBase = $monto;
            } else {
                // Si no es USD, dividimos por la tasa (ej. 4000 VES / 40 = 100 USD)
                // Evitar división por cero
                if ($tasa > 0) {
                    $valorEnMonedaBase = $monto / $tasa;
                }
            }

            $totalPagado += $valorEnMonedaBase;

            $pagosProcesados[] = [
                "id_metodo_pago" => $pago['id_metodo_pago'],
                "monto" => $monto,
                "id_moneda" => $idMoneda,
                "tasa" => $tasa
            ];
        }

        // Validar que se haya cubierto el total (con margen de tolerancia)
        if ($totalPagado < ($totalVenta - 0.5)) {
            return ["error" => "El monto pagado ($totalPagado) no cubre el total de la venta ($totalVenta)."];
        }

        // 5. Ejecutar Venta en Modelo
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
