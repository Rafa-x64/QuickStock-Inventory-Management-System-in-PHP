<?php
//se incluye el modelo para instanciarlo
include_once "model/seguridad_acceso.usuario.php";
class empleados_añadir_C extends mainModel
{
    public static function agregarEmpleado($formulario)
    {

        //sanitiza los campos
        //sanitiza los campos
        $nombre = ucwords(strtolower(trim($formulario["nombre_empleado"])));
        $apellido = ucwords(strtolower(trim($formulario["apellido_empleado"])));

        // Formatear Cédula
        $c = trim($formulario["cedula_empleado"]);
        $clean_c = preg_replace('/[^a-zA-Z0-9]/', '', $c);
        if (preg_match('/^([VEve])(\d+)$/', $clean_c, $matches)) {
            $cedula = strtoupper($matches[1]) . "-" . number_format((int)$matches[2], 0, '', '.');
        } else {
            $cedula = strtoupper($c);
        }

        // Formatear Teléfono
        $t = preg_replace('/\D/', '', $formulario["telefono_empleado"]);
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

        $id_rol = $formulario["id_rol"];
        $email = strtolower(trim($formulario["email_empleado"]));
        $contraseña = parent::hashear_contraseña($formulario["contrasena_empleado"]);
        $direccion = !empty($formulario["direccion_empleado"]) ? ucwords(strtolower(trim($formulario["direccion_empleado"]))) : null;
        $id_sucursal = $formulario["id_sucursal"];
        $fecha_registro = $formulario["fecha_registro"];

        //validaciones
        if (!self::validarCedula($cedula)) {
            return "Esta cedula ya existe";
        }

        if (!self::validarCorreo($email)) {
            return "Este correo ya existe";
        }

        //instacia la clase usuario para usar sus metodos (no obligatorio)
        $empleado = new usuario($nombre, $apellido, $cedula, $telefono, $id_rol, $email, $contraseña, $direccion, $id_sucursal, $fecha_registro);

        //ultima validacion
        if (!$empleado->crearEmpleado()) {
            return "Error al registrar el empleado";
        }

        //si todo sale bien 
        return "sisa mano";
    }

    public static function validarCedula($cedula)
    {
        $conn = parent::conectar_base_datos();
        pg_prepare($conn, "validar_cedula", "
        SELECT cedula FROM seguridad_acceso.usuario WHERE cedula = $1
    ");
        $resultado = pg_execute($conn, "validar_cedula", [$cedula]);
        $fila = pg_fetch_assoc($resultado);

        if ($fila) {
            return false; // ya existe
        }
        return true; // no existe, válido
    }

    public static function validarCorreo($correo)
    {
        $conn = parent::conectar_base_datos();
        pg_prepare($conn, "validar_correo", "
        SELECT email FROM seguridad_acceso.usuario WHERE email = $1
    ");
        $resultado = pg_execute($conn, "validar_correo", [$correo]);
        $fila = pg_fetch_assoc($resultado);

        if ($fila) {
            return false; // ya existe
        }
        return true; // no existe
    }
}
