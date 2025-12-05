<?php
include_once "model/core.cliente.php";

class clientes_editar_C extends mainModel
{
    public static function editarCliente($formulario)
    {
        $requeridos = [
            "id_cliente",
            "nombre_cliente",
            "estado_cliente"
        ];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        $id_cliente = (int)$formulario["id_cliente"];
        $nombre = ucwords(trim($formulario["nombre_cliente"]));
        $apellido = isset($formulario["apellido_cliente"]) && trim($formulario["apellido_cliente"]) !== ""
            ? ucwords(trim($formulario["apellido_cliente"]))
            : null;
        $cedula = isset($formulario["cedula_cliente"]) && trim($formulario["cedula_cliente"]) !== ""
            ? strtoupper(trim($formulario["cedula_cliente"]))
            : null;
        $telefono = isset($formulario["telefono_cliente"]) && trim($formulario["telefono_cliente"]) !== ""
            ? trim($formulario["telefono_cliente"])
            : null;
        $correo = isset($formulario["correo_cliente"]) && trim($formulario["correo_cliente"]) !== ""
            ? strtolower(trim($formulario["correo_cliente"]))
            : null;
        $direccion = isset($formulario["direccion_cliente"]) && trim($formulario["direccion_cliente"]) !== ""
            ? trim($formulario["direccion_cliente"])
            : null;
        $activo = $formulario["estado_cliente"] === "true" || $formulario["estado_cliente"] === "1" || $formulario["estado_cliente"] === true;

        // Verificar si el cliente existe
        $clienteActual = cliente::obtenerPorId($id_cliente);
        if (!$clienteActual) {
            return ["error" => "El cliente no existe"];
        }

        // Verificar duplicado de cédula si se proporciona
        if ($cedula !== null && cliente::existeClientePorCedulaYIdDiferente($cedula, $id_cliente)) {
            return ["error" => "Ya existe otro cliente con esa cédula"];
        }

        $resultado = cliente::editar(
            $id_cliente,
            $nombre,
            $apellido,
            $cedula,
            $telefono,
            $correo,
            $direccion,
            $activo
        );

        if (!$resultado) {
            return ["error" => "Error al actualizar el cliente"];
        }

        return ["success" => true];
    }
}
