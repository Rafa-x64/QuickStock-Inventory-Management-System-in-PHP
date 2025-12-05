<?php
include_once "model/core.proveedor.php";

class proveedores_eliminar_C extends mainModel
{
    public static function eliminarProveedor($id_proveedor)
    {
        if (!isset($id_proveedor) || $id_proveedor <= 0) {
            return ["error" => "ID de proveedor inválido"];
        }

        $id_proveedor = (int)$id_proveedor;

        // Verificar si el proveedor existe
        $proveedorActual = proveedor::obtenerPorId($id_proveedor);
        if (!$proveedorActual) {
            return ["error" => "El proveedor no existe"];
        }

        $resultado = proveedor::eliminar($id_proveedor);

        if (!$resultado) {
            return ["error" => "Error al eliminar el proveedor"];
        }

        return ["success" => true];
    }
}
