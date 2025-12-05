<?php
include_once "model/core.proveedor.php";

class proveedores_añadir_C extends mainModel
{
    public static function agregarProveedor($formulario)
    {
        $requeridos = ["nombre_proveedor"];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

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

        // Verificar duplicado de nombre
        if (proveedor::existeProveedorPorNombre($nombre)) {
            return ["error" => "Ya existe un proveedor con ese nombre"];
        }

        $nuevoProveedor = new proveedor(
            $nombre,
            $telefono,
            $correo,
            $direccion
        );

        $resultado = $nuevoProveedor->crear();

        if (!$resultado) {
            return ["error" => "Error al registrar el proveedor"];
        }

        return ["success" => true];
    }
}
