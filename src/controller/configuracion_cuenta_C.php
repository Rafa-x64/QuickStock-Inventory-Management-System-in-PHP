<?php
include_once "model/seguridad_acceso.usuario.php";

class configuracion_cuenta_C extends mainModel
{
    public static function actualizarPerfil($formulario)
    {
        // Verificar sesión
        if (!isset($_SESSION['sesion_usuario'])) {
            return ["error" => "Sesión no válida"];
        }

        $id_usuario = $_SESSION['sesion_usuario']['usuario']['id_usuario'];

        $requeridos = ["nombre", "apellido", "email", "telefono"];
        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        $nombre = ucwords(strtolower(trim($formulario["nombre"])));
        $apellido = ucwords(strtolower(trim($formulario["apellido"])));
        $email = strtolower(trim($formulario["email"]));

        // Formatear Teléfono
        $t = preg_replace('/\D/', '', $formulario["telefono"]);
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

        $direccion = !empty($formulario["direccion"]) ? ucwords(strtolower(trim($formulario["direccion"]))) : null;

        // TODO: Validar si el email ya existe en otro usuario (pendiente implementar en modelo si es necesario)
        // Por ahora asumimos que el modelo o la BD lanzará error si hay duplicado unique

        $resultado = usuario::actualizarPerfil(
            $id_usuario,
            $nombre,
            $apellido,
            $telefono,
            $direccion,
            $email
        );

        if ($resultado) {
            // Actualizar sesión con nuevos datos
            $_SESSION['sesion_usuario']['usuario']['nombre'] = $nombre;
            $_SESSION['sesion_usuario']['usuario']['apellido'] = $apellido;
            $_SESSION['sesion_usuario']['usuario']['email'] = $email;
            $_SESSION['sesion_usuario']['usuario']['telefono'] = $telefono;
            // Dirección no suele estar en el array principal de sesión, pero si estuviera, actualizarla.
            // Según estructura-sesion.md: [usuario] => [id_usuario, nombre, apellido, cedula, email, telefono, activo]

            return ["success" => true, "mensaje" => "Perfil actualizado correctamente"];
        } else {
            return ["error" => "Error al actualizar el perfil"];
        }
    }

    public static function cambiarContrasena($formulario)
    {
        if (!isset($_SESSION['sesion_usuario'])) {
            return ["error" => "Sesión no válida"];
        }

        $id_usuario = $_SESSION['sesion_usuario']['usuario']['id_usuario'];

        $pass_actual = $formulario["pass_actual"] ?? "";
        $pass_nueva = $formulario["pass_nueva"] ?? "";
        $pass_confirm = $formulario["pass_confirm"] ?? "";

        if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirm)) {
            return ["error" => "Todos los campos de contraseña son obligatorios"];
        }

        if ($pass_nueva !== $pass_confirm) {
            return ["error" => "Las nuevas contraseñas no coinciden"];
        }

        // Verificar contraseña actual
        if (!usuario::verificarContrasena($id_usuario, $pass_actual)) {
            return ["error" => "La contraseña actual es incorrecta"];
        }

        $resultado = usuario::actualizarContrasena($id_usuario, $pass_nueva);

        if ($resultado) {
            return ["success" => true, "mensaje" => "Contraseña actualizada correctamente"];
        } else {
            return ["error" => "Error al actualizar la contraseña"];
        }
    }
}
