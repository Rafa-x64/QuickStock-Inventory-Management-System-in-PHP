<?php
include_once "model/inventario.compra.php";
include_once "model/inventario.producto.php";
include_once "model/core.color.php";
include_once "model/core.talla.php";

class compras_añadir_C extends mainModel
{

    // --- (Métodos limpiar_cadena y normalizarTexto deben estar aquí) ---

    public function crearCompra()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return ["error" => "Método no permitido"];
        }

        // --- 1. OBTENER Y NORMALIZAR DATOS ---
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

        foreach ($camposCompra as $campo) {
            $valor = isset($_POST[$campo]) ? $_POST[$campo] : null;
            $valorLimpio = $this->limpiar_cadena($valor);

            if ($campo === 'observaciones') {
                $datosCompraPrincipal[$campo] = $valorLimpio;
            } else if ($campo === 'numero_factura') {
                // No aplicar capitalize a números de factura
                $datosCompraPrincipal[$campo] = $this->normalizarTexto($valorLimpio, false);
            } else {
                $datosCompraPrincipal[$campo] = $valorLimpio;
            }
        }

        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $index => $producto) {
                $productoLimpio = [];

                $productoLimpio['codigo_barra'] = $this->normalizarTexto($producto['codigo_barra'] ?? '', false);
                $productoLimpio['nombre']       = $this->normalizarTexto($producto['nombre'] ?? '');
                $productoLimpio['id_categoria'] = $this->limpiar_cadena($producto['id_categoria'] ?? '');

                // Conversión a números antes de la limpieza si es posible, aunque limpiar_cadena ya elimina tags
                $productoLimpio['cantidad']     = (int) $this->limpiar_cadena($producto['cantidad'] ?? 0);
                $productoLimpio['precio_compra'] = (float) $this->limpiar_cadena($producto['precio_compra'] ?? 0);
                $productoLimpio['precio_venta']  = (float) $this->limpiar_cadena($producto['precio_venta'] ?? 0);

                // Lógica de Color
                if (isset($producto['id_color']) && !empty($producto['id_color'])) {
                    $productoLimpio['tipo_color'] = 'existente';
                    $productoLimpio['id_color']   = $this->limpiar_cadena($producto['id_color']);
                    $productoLimpio['nombre_color'] = null;
                } else if (isset($producto['nombre_color']) && !empty($producto['nombre_color'])) {
                    $productoLimpio['tipo_color'] = 'nuevo';
                    $productoLimpio['id_color']   = null;
                    $productoLimpio['nombre_color'] = $this->normalizarTexto($producto['nombre_color']);
                } else {
                    $productoLimpio['tipo_color'] = 'error';
                }

                // Lógica de Talla
                if (isset($producto['id_talla']) && !empty($producto['id_talla'])) {
                    $productoLimpio['tipo_talla'] = 'existente';
                    $productoLimpio['id_talla']   = $this->limpiar_cadena($producto['id_talla']);
                    $productoLimpio['rango_talla'] = null;
                } else if (isset($producto['rango_talla']) && !empty($producto['rango_talla'])) {
                    $productoLimpio['tipo_talla'] = 'nuevo';
                    $productoLimpio['id_talla']   = null;
                    $productoLimpio['rango_talla'] = $this->normalizarTexto($producto['rango_talla']);
                } else {
                    $productoLimpio['tipo_talla'] = 'error';
                }

                // VALIDACIÓN CRÍTICA DEL PRODUCTO: Descartar si está incompleto o tiene valores no válidos
                if (
                    $productoLimpio['tipo_color'] === 'error' ||
                    $productoLimpio['tipo_talla'] === 'error' ||
                    empty($productoLimpio['nombre']) ||
                    $productoLimpio['cantidad'] <= 0 ||
                    $productoLimpio['precio_compra'] <= 0
                ) {
                    continue; // Pasa al siguiente producto, descartando este
                }

                $productosAdquiridos[] = $productoLimpio;
            }
        }

        // --- 2. VALIDACIÓN DE DATOS MÍNIMA ANTES DE LA TRANSACCIÓN (MEJORADA) ---
        $idsRequeridos = ['id_proveedor', 'id_sucursal', 'id_usuario', 'id_moneda'];
        foreach ($idsRequeridos as $id) {
            // Verifica que los IDs requeridos no estén vacíos y sean mayores a 0
            if (empty($datosCompraPrincipal[$id]) || intval($datosCompraPrincipal[$id]) < 1) {
                return ["error" => "Datos de compra o productos incompletos: Falta seleccionar $id."];
            }
        }

        if (count($productosAdquiridos) === 0) {
            return ["error" => "Datos de compra o productos incompletos: Debe agregar al menos un producto válido."];
        }

        // --- 3. EJECUTAR TRANSACCIÓN EN EL MODELO ---
        $modeloCompra = new compra();
        $resultado = $modeloCompra->registrarTransaccionCompra($datosCompraPrincipal, $productosAdquiridos);

        return $resultado;
    }

    // --- (Métodos auxiliarres normalizarTexto y limpiar_cadena quedan igual) ---
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
