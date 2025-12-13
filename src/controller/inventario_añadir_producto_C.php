<?php
include_once "model/inventario.producto.php";
include_once "model/core.categoria.php";
include_once "model/core.color.php";
include_once "model/core.talla.php";
class inventario_añadir_producto_C extends mainModel
{
    public static function agregarProducto($formulario)
    {
        try {
            $codigo_barra   = trim($formulario['codigo_barra']);
            $nombre         = ucwords(trim($formulario['nombre']));
            $descripcion    = trim($formulario['descripcion']);
            $precio_compra  = floatval($formulario['precio_compra']);
            $precio         = floatval($formulario['precio']);

            $id_proveedor   = isset($formulario['id_proveedor']) && intval($formulario['id_proveedor']) > 0 ? intval($formulario['id_proveedor']) : null;
            $id_sucursal    = intval($formulario['id_sucursal']);
            $cantidad       = intval($formulario['cantidad']);
            $minimo         = intval($formulario['minimo']);

            $nombre_categoria = !empty($formulario['nombre_categoria']) ? ucwords(trim($formulario['nombre_categoria'])) : null;
            $id_categoria     = !empty($formulario['id_categoria']) ? intval($formulario['id_categoria']) : null;

            $nombre_color = !empty($formulario['nombre_color']) ? ucwords(trim($formulario['nombre_color'])) : null;
            $id_color     = !empty($formulario['id_color']) ? intval($formulario['id_color']) : null;

            $rango_talla = !empty($formulario['rango_talla']) ? trim($formulario['rango_talla']) : null;
            $id_talla    = !empty($formulario['id_talla']) ? intval($formulario['id_talla']) : null;

            if (empty($codigo_barra)) return "Código de barras obligatorio";
            if (empty($nombre)) return "Nombre obligatorio";
            // if (empty($descripcion)) return "Descripción obligatoria"; // Opcional
            if ($precio_compra < 0.01) return "Precio de compra mínimo 0.01";
            if ($precio < 0.01) return "Precio de venta mínimo 0.01";
            if ($precio_compra > $precio) return "El precio de venta no puede ser menor al precio de compra.";
            if ($cantidad < 0) return "Stock inicial no puede ser negativo";
            if ($minimo < 1) return "Stock mínimo debe ser ≥ 1";
            if (!$id_sucursal) return "Debe seleccionar una sucursal";

            if ($nombre_categoria) {
                if (strlen($nombre_categoria) < 4) return "Nombre de categoría mínimo 4 letras";
                $categoria = new categoria(0, $nombre_categoria);
                $id_categoria = $categoria->crear();
            } elseif (!$id_categoria) {
                return "Debe seleccionar o agregar una categoría";
            }

            if ($nombre_color) {
                if (strlen($nombre_color) < 3) return "Color mínimo 3 letras";
                $color = new color(0, $nombre_color);
                $id_color = $color->crear();
            } elseif (!$id_color) {
                return "Debe seleccionar o agregar un color";
            }

            if ($rango_talla) {
                if (strlen($rango_talla) < 1) return 'Formato de talla inválido (no puede estar vacío)';
                $talla = new talla(0, $rango_talla);
                $id_talla = $talla->crear();
            } elseif (!$id_talla) {
                return "Debe seleccionar o agregar una talla";
            }

            $productoExistente = producto::buscarPorNombreOCodigo($nombre, $codigo_barra);
            if ($productoExistente) return "El producto ya está registrado";

            $producto = new producto(
                0,
                $nombre,
                $descripcion,
                $id_categoria,
                $id_color,
                $id_talla,
                $precio,
                $id_proveedor,
                true,
                $codigo_barra,
                $precio_compra
            );

            $id_producto = $producto->crear();
            if (!$id_producto) return "Error al crear el producto";

            $producto->agregarInventario($id_producto, $id_sucursal, $cantidad, $minimo);

            return "Producto agregado correctamente";
        } catch (Exception $e) {
            return "Error al agregar producto: " . $e->getMessage();
        }
    }
}
