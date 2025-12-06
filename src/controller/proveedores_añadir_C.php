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

        $nombre = ucwords(strtolower(trim($formulario["nombre_proveedor"])));

        $telefono = null;
        if (isset($formulario["telefono_proveedor"]) && trim($formulario["telefono_proveedor"]) !== "") {
            $t = preg_replace('/\D/', '', $formulario["telefono_proveedor"]);
            if (strpos($t, '0') === 0) {
                $t = '58' . substr($t, 1);
            } elseif (strpos($t, '58') !== 0) {
                $t = '58' . $t;
            }
            if (strlen($t) >= 12) {
                $telefono = "+" . substr($t, 0, 2) . " " . substr($t, 2, 3) . "-" . substr($t, 5, 3) . "-" . substr($t, 8, 2) . "-" . substr($t, 10);
            } else {
                $telefono = "+" . $t;
            }
        }

        $correo = isset($formulario["correo_proveedor"]) && trim($formulario["correo_proveedor"]) !== ""
            ? strtolower(trim($formulario["correo_proveedor"]))
            : null;

        $direccion = null;
        if (isset($formulario["direccion_proveedor"]) && trim($formulario["direccion_proveedor"]) !== "") {
            $direccion = ucwords(strtolower(trim($formulario["direccion_proveedor"])));
        }

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
