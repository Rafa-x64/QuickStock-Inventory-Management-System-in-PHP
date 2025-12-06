<?php
include_once "model/core.cliente.php";

class clientes_eliminar_C extends mainModel
{
    public static function eliminarCliente($id_cliente)
    {
        if (!isset($id_cliente) || $id_cliente <= 0) {
            return ["error" => "ID de cliente inválido"];
        }

        $id_cliente = (int)$id_cliente;

        // Verificar si el cliente existe
        $clienteActual = cliente::obtenerPorId($id_cliente);
        if (!$clienteActual) {
            return ["error" => "El cliente no existe"];
        }

        $resultado = cliente::eliminar($id_cliente);

        if (!$resultado) {
            return ["error" => "Error al eliminar el cliente"];
        }

        return ["success" => true];
    }
}
