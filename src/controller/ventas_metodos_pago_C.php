<?php
include_once "model/finanzas.metodo_pago.php";

class ventas_metodos_pago_C extends mainModel
{
    public static function agregarMetodoPago($formulario)
    {
        $nombre = ucwords(trim($formulario['nombre_metodo']));
        $descripcion = trim($formulario['descripcion_metodo'] ?? '');
        $referencia = isset($formulario['referencia_metodo']); // Checkbox

        if (empty($nombre)) {
            return ["error" => "El nombre es obligatorio."];
        }

        if (metodo_pago::existeMetodo($nombre)) {
            return ["error" => "El método de pago ya existe."];
        }

        $nuevoMetodo = new metodo_pago($nombre, $descripcion, $referencia);
        $resultado = $nuevoMetodo->crear();

        if ($resultado) {
            return ["success" => "Método de pago creado correctamente."];
        } else {
            return ["error" => "Error al crear el método de pago."];
        }
    }

    public static function eliminarMetodoPago($id)
    {
        if (empty($id)) {
            return ["error" => "ID no válido."];
        }

        $resultado = metodo_pago::eliminar($id);

        if ($resultado) {
            return ["success" => "Método de pago eliminado correctamente."];
        } else {
            return ["error" => "Error al eliminar el método de pago."];
        }
    }

    public static function editarMetodoPago($formulario)
    {
        $id = $formulario['id_metodo_pago'] ?? null;
        $nombre = ucwords(trim($formulario['nombre_metodo']));
        $descripcion = trim($formulario['descripcion_metodo'] ?? '');
        $referencia = isset($formulario['referencia_metodo']); // Checkbox
        $activo = isset($formulario['activo_metodo']); // Checkbox

        if (empty($id)) {
            return ["error" => "ID no válido."];
        }

        if (empty($nombre)) {
            return ["error" => "El nombre es obligatorio."];
        }

        $resultado = metodo_pago::editar($id, $nombre, $descripcion, $referencia, $activo);

        if ($resultado) {
            return ["success" => "Método de pago actualizado correctamente."];
        } else {
            return ["error" => "Error al actualizar el método de pago."];
        }
    }
}
