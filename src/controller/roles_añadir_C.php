<?php
include_once "model/seguridad_acceso.rol.php";

class roles_añadir_C extends mainModel
{
    public static function agregarRol($formulario)
    {
        $requeridos = ["nombre_rol"];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        $nombre_rol = ucwords(strtolower(trim($formulario["nombre_rol"])));
        // Descripción opcional
        $descripcion = !empty($formulario['descripcion_rol']) ? ucfirst(trim($formulario['descripcion_rol'])) : null;

        // Verificar duplicado de nombre
        if (rol::existeRolPorNombre($nombre_rol)) {
            return ["error" => "Ya existe un rol con ese nombre"];
        }

        $nuevoRol = new rol(
            $nombre_rol,
            $descripcion
        );

        $resultado = $nuevoRol->crear();

        if (!$resultado) {
            return ["error" => "Error al registrar el rol"];
        }

        return ["success" => true];
    }
}
