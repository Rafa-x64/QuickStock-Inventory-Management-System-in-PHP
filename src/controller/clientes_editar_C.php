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
        $nombre = ucwords(strtolower(trim($formulario["nombre_cliente"])));

        $apellido = null;
        if (isset($formulario["apellido_cliente"]) && trim($formulario["apellido_cliente"]) !== "") {
            $apellido = ucwords(strtolower(trim($formulario["apellido_cliente"])));
        }

        $cedula = null;
        if (isset($formulario["cedula_cliente"]) && trim($formulario["cedula_cliente"]) !== "") {
            $c = trim($formulario["cedula_cliente"]);
            // Limpiar caracteres no alfanuméricos básicos para procesar
            $clean_c = preg_replace('/[^a-zA-Z0-9]/', '', $c);
            // Extraer letra y números
            if (preg_match('/^([VEve])(\d+)$/', $clean_c, $matches)) {
                $letra = strtoupper($matches[1]);
                $numeros = number_format((int)$matches[2], 0, '', '.');
                $cedula = "$letra-$numeros";
            } else {
                $cedula = strtoupper($c);
            }
        }

        $telefono = null;
        if (isset($formulario["telefono_cliente"]) && trim($formulario["telefono_cliente"]) !== "") {
            // Eliminar todo lo que no sea dígito
            $t = preg_replace('/\D/', '', $formulario["telefono_cliente"]);

            // Normalizar a formato internacional 58...
            if (strpos($t, '0') === 0) {
                $t = '58' . substr($t, 1);
            } elseif (strpos($t, '58') !== 0) {
                $t = '58' . $t;
            }

            // Aplicar formato +58 XXX-XXX-XX-XX
            if (strlen($t) >= 12) {
                $telefono = "+" . substr($t, 0, 2) . " " . substr($t, 2, 3) . "-" . substr($t, 5, 3) . "-" . substr($t, 8, 2) . "-" . substr($t, 10);
            } else {
                $telefono = "+" . $t;
            }
        }

        $correo = isset($formulario["correo_cliente"]) && trim($formulario["correo_cliente"]) !== ""
            ? strtolower(trim($formulario["correo_cliente"]))
            : null;

        $direccion = null;
        if (isset($formulario["direccion_cliente"]) && trim($formulario["direccion_cliente"]) !== "") {
            $direccion = ucwords(strtolower(trim($formulario["direccion_cliente"])));
        }
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
