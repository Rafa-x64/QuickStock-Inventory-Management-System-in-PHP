<?php
include_once "model/seguridad_acceso.rol.php";

class roles_editar_C extends mainModel
{
    public static function editarRol($formulario)
    {
        $requeridos = [
            "id_rol",
            "nombre_rol",
            "estado_rol"
        ];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        $id_rol = (int)$formulario["id_rol"];
        $nombre_rol = ucwords(trim($formulario["nombre_rol"]));
        $descripcion = isset($formulario["descripcion_rol"]) && trim($formulario["descripcion_rol"]) !== ""
            ? trim($formulario["descripcion_rol"])
            : null;
        $activo = $formulario["estado_rol"] === "true" || $formulario["estado_rol"] === "1" || $formulario["estado_rol"] === true;

        // Verificar si el rol existe
        $rolActual = rol::obtenerPorId($id_rol);
        if (!$rolActual) {
            return ["error" => "El rol no existe"];
        }

        // Verificar duplicado de nombre
        if (rol::existeRolPorNombreYIdDiferente($nombre_rol, $id_rol)) {
            return ["error" => "Ya existe otro rol con ese nombre"];
        }

        $resultado = rol::editar(
            $id_rol,
            $nombre_rol,
            $descripcion,
            $activo
        );

        if (!$resultado) {
            return ["error" => "Error al actualizar el rol"];
        }

        return ["success" => true];
    }
}
