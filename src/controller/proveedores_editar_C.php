<?php
include_once "model/core.proveedor.php";

class proveedores_editar_C extends mainModel
{
    public static function editarProveedor($formulario)
    {
        $requeridos = [
            "id_proveedor",
            "nombre_proveedor",
            "estado_proveedor"
        ];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        $id_proveedor = (int)$formulario["id_proveedor"];
        $nombre = ucwords(trim($formulario["nombre_proveedor"]));
        $telefono = isset($formulario["telefono_proveedor"]) && trim($formulario["telefono_proveedor"]) !== ""
            ? trim($formulario["telefono_proveedor"])
            : null;
        $correo = isset($formulario["correo_proveedor"]) && trim($formulario["correo_proveedor"]) !== ""
            ? strtolower(trim($formulario["correo_proveedor"]))
            : null;
        $direccion = isset($formulario["direccion_proveedor"]) && trim($formulario["direccion_proveedor"]) !== ""
            ? trim($formulario["direccion_proveedor"])
            : null;
        $activo = $formulario["estado_proveedor"] === "true" || $formulario["estado_proveedor"] === "1" || $formulario["estado_proveedor"] === true;

        // Verificar si el proveedor existe
        $proveedorActual = proveedor::obtenerPorId($id_proveedor);
        if (!$proveedorActual) {
            return ["error" => "El proveedor no existe"];
        }

        // Verificar duplicado de nombre
        if (proveedor::existeProveedorPorNombreYIdDiferente($nombre, $id_proveedor)) {
            return ["error" => "Ya existe otro proveedor con ese nombre"];
        }

        $resultado = proveedor::editar(
            $id_proveedor,
            $nombre,
            $telefono,
            $correo,
            $direccion,
            $activo
        );

        if (!$resultado) {
            return ["error" => "Error al actualizar el proveedor"];
        }

        return ["success" => true];
    }
}
