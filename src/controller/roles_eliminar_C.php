<?php
include_once "model/seguridad_acceso.rol.php";

class roles_eliminar_C extends mainModel
{
    public static function eliminarRol($id_rol)
    {
        if (!isset($id_rol) || $id_rol <= 0) {
            return ["error" => "ID de rol inválido"];
        }

        $id_rol = (int)$id_rol;

        // Verificar si el rol existe
        $rolActual = rol::obtenerPorId($id_rol);
        if (!$rolActual) {
            return ["error" => "El rol no existe"];
        }

        // Verificar si hay usuarios asignados a este rol
        $usuariosAsignados = rol::contarUsuariosPorRol($id_rol);
        if ($usuariosAsignados > 0) {
            return ["error" => "No se puede eliminar el rol porque tiene $usuariosAsignados usuario(s) asignado(s)"];
        }

        $resultado = rol::eliminar($id_rol);

        if (!$resultado) {
            return ["error" => "Error al eliminar el rol"];
        }

        return ["success" => true];
    }
}
