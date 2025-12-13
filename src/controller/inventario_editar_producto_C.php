<?php
include_once "model/inventario.producto.php";
include_once "model/core.color.php";
include_once "model/core.talla.php";

class inventario_editar_producto_C extends mainModel
{
    public static function editarProducto($formulario): array
    {
        // 1. Validar campos obligatorios (excluyendo descripcion e id_proveedor)
        $requeridos = [
            "id_producto",
            "codigo_barra",
            "nombre",
            "id_categoria",
            "precio_compra",
            "precio",
            "cantidad",
            "minimo",
            "id_sucursal",
            "activo" // <-- AÑADIDO: Ahora es obligatorio
        ];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo obligatorio faltante o vacío: " . $campo];
            }
        }

        // Validación de precios
        $precio_compra = (float)$formulario["precio_compra"];
        $precio_venta  = (float)$formulario["precio"];

        if ($precio_compra < 0.01) return ["error" => "Precio de compra mínimo 0.01"];
        if ($precio_venta < 0.01) return ["error" => "Precio de venta mínimo 0.01"];
        if ($precio_compra > $precio_venta) return ["error" => "El precio de venta no puede ser menor al precio de compra."];

        $data = $formulario;
        $id_color_final = null;
        $id_talla_final = null;

        // --- 2. Manejo de Color (Creación condicional) ---
        if (!empty($data["nombre_color"])) {
            try {
                $nuevo_color = new color(0, trim($data["nombre_color"]));
                $id_color_final = $nuevo_color->crear();
            } catch (InvalidArgumentException $e) {
                return ["error" => "Color inválido: " . $e->getMessage()];
            }
        } elseif (!empty($data["id_color"])) {
            $id_color_final = (int)$data["id_color"];
        } else {
            return ["error" => "Debe seleccionar o añadir un Color."];
        }

        // --- 3. Manejo de Talla (Creación condicional) ---
        if (!empty($data["rango_talla"])) {
            try {
                $nueva_talla = new talla(0, trim($data["rango_talla"]));
                $id_talla_final = $nueva_talla->crear();
            } catch (InvalidArgumentException $e) {
                return ["error" => "Talla inválida: " . $e->getMessage()];
            }
        } elseif (!empty($data["id_talla"])) {
            $id_talla_final = (int)$data["id_talla"];
        } else {
            return ["error" => "Debe seleccionar o añadir una Talla."];
        }

        // 4. Preparar datos finales para el Modelo de Producto

        // Acomodar campos NULLEABLES:
        $id_proveedor_final = !empty($data["id_proveedor"]) ? (int)$data["id_proveedor"] : null;
        $descripcion_final = trim($data["descripcion"]) !== "" ? trim($data["descripcion"]) : null;

        // Convertir el string "true"/"false" del formulario a booleano PHP
        $activo_final = filter_var($data["activo"], FILTER_VALIDATE_BOOLEAN);

        $producto_data = [
            "id_producto"   => (int)$data["id_producto"],
            "codigo_barra"  => trim($data["codigo_barra"]),
            "nombre"        => trim($data["nombre"]),
            "descripcion"   => $descripcion_final,
            "id_categoria"  => (int)$data["id_categoria"],
            "id_proveedor"  => $id_proveedor_final,
            "id_color"      => $id_color_final,
            "id_talla"      => $id_talla_final,
            "precio_compra" => (float)$data["precio_compra"],
            "precio"        => (float)$data["precio"],
            "activo"        => $activo_final, // <-- YA CONVERTIDO A BOOLEANO
            "id_sucursal"   => (int)$data["id_sucursal"],
            "cantidad"      => (int)$data["cantidad"],
            "minimo"        => (int)$data["minimo"],
        ];

        // 5. Delegar al Modelo
        return producto::editar($producto_data);
    }
}
