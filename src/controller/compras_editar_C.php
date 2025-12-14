<?php
require_once "model/inventario.compra.php";
require_once "model/inventario.producto.php";
require_once "model/core.color.php";
require_once "model/core.talla.php";

class compras_editar_C extends mainModel
{
    public function actualizarCompra()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ["error" => "Método no permitido"];
        }

        $datosCompraPrincipal = [];
        $productosProcesados = [];

        // 1. Validar ID de Compra
        $id_compra = $this->limpiar_cadena($_POST['id_compra'] ?? '');
        if (empty($id_compra)) {
            return ["success" => false, "error" => "ID de compra no recibido."];
        }

        // 2. Limpiar y asignar datos principales de la cabecera
        $camposCompra = [
            'fecha_compra',
            'id_proveedor',
            'id_sucursal',
            'id_usuario',
            'numero_factura',
            'id_moneda',
            'estado',
            'observaciones'
        ];

        foreach ($camposCompra as $campo) {
            // CORRECCIÓN 1: Usar '??' para asegurar que si no existe, sea una cadena vacía y no NULL
            $valor = $_POST[$campo] ?? '';
            $valorLimpio = $this->limpiar_cadena($valor);

            if ($campo === 'numero_factura') {
                // CORRECCIÓN 2: Forzar conversión a string con (string)
                // Esto evita el "TypeError: Argument #1 must be of type string, null given"
                $datosCompraPrincipal[$campo] = $this->normalizarTexto((string)$valorLimpio, false);
            } else {
                $datosCompraPrincipal[$campo] = $valorLimpio;
            }
        }
        $datosCompraPrincipal['id_compra'] = $id_compra;

        // 3. Procesar productos
        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $producto) {

                // Ignorar filas vacías
                if (empty($producto['nombre']) && empty($producto['codigo_barra'])) continue;

                $productoLimpio = [];

                // IDs de control
                $productoLimpio['id_detalle_compra'] = !empty($producto['id_detalle_compra']) ? intval($producto['id_detalle_compra']) : null;
                $productoLimpio['id_producto_existente'] = !empty($producto['id_producto_existente']) ? intval($producto['id_producto_existente']) : null;

                // Datos básicos (Aquí también aplicamos la corrección de seguridad)
                $productoLimpio['codigo_barra'] = $this->normalizarTexto((string)($producto['codigo_barra'] ?? ''), false);
                $productoLimpio['nombre']       = $this->normalizarTexto((string)($producto['nombre'] ?? ''));

                $id_categoria = $this->limpiar_cadena($producto['id_categoria'] ?? '');
                $productoLimpio['id_categoria'] = (!empty($id_categoria) && intval($id_categoria) > 0) ? intval($id_categoria) : null;

                $productoLimpio['cantidad']      = (int) $this->limpiar_cadena($producto['cantidad'] ?? 0);
                $productoLimpio['precio_compra'] = (float) $this->limpiar_cadena($producto['precio_compra'] ?? 0);
                $productoLimpio['precio_venta']  = (float) $this->limpiar_cadena($producto['precio_venta'] ?? 0);
                $productoLimpio['minimo']        = (int) $this->limpiar_cadena($producto['minimo'] ?? 0);

                // --- Lógica de Color ---
                $id_color_existente = $this->limpiar_cadena($producto['id_color'] ?? '');
                $nombre_color_nuevo = $this->normalizarTexto((string)($producto['nombre_color_disabled'] ?? ''));

                if (!empty($id_color_existente) && intval($id_color_existente) > 0) {
                    $productoLimpio['tipo_color'] = 'existente';
                    $productoLimpio['id_color']   = intval($id_color_existente);
                } else if (!empty($nombre_color_nuevo)) {
                    $productoLimpio['tipo_color']   = 'nuevo';
                    $productoLimpio['nombre_color'] = $nombre_color_nuevo;
                } else {
                    $productoLimpio['tipo_color'] = 'opcional';
                    $productoLimpio['id_color']   = null;
                }

                // --- Lógica de Talla ---
                $id_talla_existente = $this->limpiar_cadena($producto['id_talla'] ?? '');
                $rango_talla_nuevo  = $this->normalizarTexto((string)($producto['rango_talla_disabled'] ?? ''));

                if (!empty($id_talla_existente) && intval($id_talla_existente) > 0) {
                    $productoLimpio['tipo_talla'] = 'existente';
                    $productoLimpio['id_talla']   = intval($id_talla_existente);
                } else if (!empty($rango_talla_nuevo)) {
                    $productoLimpio['tipo_talla']  = 'nuevo';
                    $productoLimpio['rango_talla'] = $rango_talla_nuevo;
                } else {
                    $productoLimpio['tipo_talla'] = 'error';
                }

                if ($productoLimpio['cantidad'] <= 0 || $productoLimpio['precio_compra'] <= 0) {
                    continue;
                }

                if ($productoLimpio['precio_venta'] < $productoLimpio['precio_compra']) {
                    return ["error" => "El precio de venta no puede ser menor al precio de compra para el producto: " . $productoLimpio['nombre']];
                }

                if ($productoLimpio['minimo'] < 0) {
                    return ["success" => false, "error" => "El stock mínimo no puede ser negativo para el producto: " . $productoLimpio['nombre']];
                }

                $productosProcesados[] = $productoLimpio;
            }
        }

        if (count($productosProcesados) === 0) {
            return ["success" => false, "error" => "Debe haber al menos un producto válido."];
        }

        $modeloCompra = new compra();
        return $modeloCompra->actualizarTransaccionCompra($datosCompraPrincipal, $productosProcesados);
    }

    // --- Helpers corregidos para no fallar con NULL ---

    protected function normalizarTexto(string $cadena, bool $capitalizeFirst = true): string
    {
        // 1. Limpiamos la cadena
        $cadena = $this->limpiar_cadena($cadena);

        // 2. Si es NULL (porque limpiar_cadena devolvió NULL), lo convertimos a string vacío
        if ($cadena === null) {
            $cadena = "";
        }

        if (empty($cadena)) return "";

        $cadena = trim($cadena);
        $cadena = preg_replace('/\s+/', ' ', $cadena);
        $cadena = mb_strtolower($cadena, 'UTF-8');
        if ($capitalizeFirst) {
            $cadena = mb_strtoupper(mb_substr($cadena, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($cadena, 1, mb_strlen($cadena, 'UTF-8'), 'UTF-8');
        }
        return $cadena;
    }

    protected function limpiar_cadena($cadena)
    {
        // Si no es string (ej. NULL), devolvemos NULL o string vacío según prefieras.
        // Aquí mantengo tu lógica original, pero normalizarTexto ya maneja el null que esto devuelve.
        if (is_string($cadena)) {
            $cadena = trim($cadena);
            $cadena = stripslashes($cadena);
            $cadena = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
        }
        return $cadena;
    }
}
