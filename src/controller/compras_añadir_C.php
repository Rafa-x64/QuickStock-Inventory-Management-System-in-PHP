<?php
include_once "model/inventario.compra.php";
include_once "model/inventario.producto.php";
include_once "model/core.color.php";
include_once "model/core.talla.php";

class compras_añadir_C extends mainModel
{
    public function crearCompra()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ["error" => "Método no permitido"];
        }

        $datosCompraPrincipal = [];
        $productosAdquiridos = [];

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

        // 1. Limpiar y asignar datos principales de la compra
        foreach ($camposCompra as $campo) {
            $valor = isset($_POST[$campo]) ? $_POST[$campo] : null;
            $valorLimpio = $this->limpiar_cadena($valor);

            if ($campo === 'observaciones') {
                $datosCompraPrincipal[$campo] = $valorLimpio;
            } else if ($campo === 'numero_factura') {
                $datosCompraPrincipal[$campo] = $this->normalizarTexto($valorLimpio, false);
            } else {
                $datosCompraPrincipal[$campo] = $valorLimpio;
            }
        }

        // 2. Procesar productos adquiridos (soporte para múltiples productos)
        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $producto) {
                $productoLimpio = [];

                $productoLimpio['codigo_barra'] = $this->normalizarTexto($producto['codigo_barra'] ?? '', false);
                $productoLimpio['nombre']       = $this->normalizarTexto($producto['nombre'] ?? '');

                // PUNTO 1: Nullable Category
                $id_categoria = $this->limpiar_cadena($producto['id_categoria'] ?? '');
                $productoLimpio['id_categoria'] = (!empty($id_categoria) && intval($id_categoria) > 0)
                    ? intval($id_categoria) : null;


                $productoLimpio['cantidad']      = (int) $this->limpiar_cadena($producto['cantidad'] ?? 0);
                $productoLimpio['precio_compra'] = (float) $this->limpiar_cadena($producto['precio_compra'] ?? 0);
                $productoLimpio['precio_venta']  = (float) $this->limpiar_cadena($producto['precio_venta'] ?? 0);
                $productoLimpio['minimo']        = (int) $this->limpiar_cadena($producto['minimo'] ?? 0);

                // 🟢 Lógica de Color CORREGIDA: Permite ser nulo/opcional 🟢
                $id_color_existente = $this->limpiar_cadena($producto['id_color'] ?? '');
                $nombre_color_nuevo = $this->normalizarTexto($producto['nombre_color'] ?? '');

                if (!empty($id_color_existente) && intval($id_color_existente) > 0) {
                    // Opción 1: Color Existente
                    $productoLimpio['tipo_color']   = 'existente';
                    $productoLimpio['id_color']     = intval($id_color_existente);
                    $productoLimpio['nombre_color'] = null;
                } else if (!empty($nombre_color_nuevo)) {
                    // Opción 2: Nuevo Nombre de Color
                    $productoLimpio['tipo_color']   = 'nuevo';
                    $productoLimpio['id_color']     = null;
                    $productoLimpio['nombre_color'] = $nombre_color_nuevo;
                } else {
                    // Opción 3: Color Opcional / No Seleccionado
                    $productoLimpio['tipo_color']   = 'opcional';
                    $productoLimpio['id_color']     = null;
                    $productoLimpio['nombre_color'] = null;
                }

                // Lógica de Talla (Asumida como Requerida. Si es opcional, usa lógica similar a color)
                $id_talla_existente = $this->limpiar_cadena($producto['id_talla'] ?? '');
                $rango_talla_nuevo  = $this->normalizarTexto($producto['rango_talla'] ?? '');

                if (!empty($id_talla_existente) && intval($id_talla_existente) > 0) {
                    $productoLimpio['tipo_talla']   = 'existente';
                    $productoLimpio['id_talla']     = intval($id_talla_existente);
                    $productoLimpio['rango_talla']  = null;
                } else if (!empty($rango_talla_nuevo)) {
                    $productoLimpio['tipo_talla']   = 'nuevo';
                    $productoLimpio['id_talla']     = null;
                    $productoLimpio['rango_talla']  = $rango_talla_nuevo;
                } else {
                    $productoLimpio['tipo_talla'] = 'error'; // Error si la talla no se define
                }

                // Validación mínima CORREGIDA: Ahora permite 'opcional' para el color.
                if (
                    $productoLimpio['tipo_talla'] === 'error' ||
                    empty($productoLimpio['nombre']) ||
                    $productoLimpio['cantidad'] <= 0 ||
                    $productoLimpio['precio_compra'] <= 0
                ) {
                    // Si el producto no es válido, se salta al siguiente.
                    continue;
                }

                // Si la talla es opcional, deberías cambiar $productoLimpio['tipo_talla'] === 'error' por una lógica 'opcional' similar a la del color.

                // Validación de Precios Lógica
                if ($productoLimpio['precio_venta'] < $productoLimpio['precio_compra']) {
                    return ["error" => "El precio de venta no puede ser menor al precio de compra para el producto: " . $productoLimpio['nombre']];
                }

                if ($productoLimpio['minimo'] < 0) {
                    return ["error" => "El stock mínimo no puede ser negativo para el producto: " . $productoLimpio['nombre']];
                }

                $productosAdquiridos[] = $productoLimpio;
            }
        }

        // 3. Validación de datos principales
        $idsRequeridos = ['id_proveedor', 'id_sucursal', 'id_usuario', 'id_moneda'];
        foreach ($idsRequeridos as $id) {
            if (empty($datosCompraPrincipal[$id]) || intval($datosCompraPrincipal[$id]) < 1) {
                return ["error" => "Datos de compra incompletos: Falta seleccionar **$id**."];
            }
        }

        // 4. Validación de que hay productos válidos para procesar
        if (count($productosAdquiridos) === 0) {
            return ["error" => "Debe agregar al menos un producto válido para realizar la compra."];
        }

        // 5. Ejecutar transacción
        $modeloCompra = new compra();
        $resultado = $modeloCompra->registrarTransaccionCompra($datosCompraPrincipal, $productosAdquiridos);

        return $resultado;
    }

    protected function normalizarTexto(string $cadena, bool $capitalizeFirst = true): string
    {
        $cadena = $this->limpiar_cadena($cadena);
        if (empty($cadena)) {
            return $cadena;
        }
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
        if (is_string($cadena)) {
            $cadena = trim($cadena);
            $cadena = stripslashes($cadena);
            $cadena = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
        }
        return $cadena;
    }
}
