<?php
class inicioSesionC extends mainModel
{
    public static function validarAccesso($formulario)
    {
        $correo = trim($formulario["usuario_correo"]);
        $contraseña = trim($formulario["usuario_contraseña"]);

        if (!self::validarCorreo($correo)) {
            return "error de correo";
        }

        if (!self::validarContraseña($correo, $contraseña)) {
            return "error de contraseña";
        }

        if (!self::iniciarSesion($correo, $contraseña)) {
            return "error al iniciar sesion";
        }

        // Retornar el nombre del rol para redirigir al dashboard correcto
        $nombre_rol = $_SESSION["sesion_usuario"]["rol"]["nombre_rol"] ?? "";
        return "exito:" . $nombre_rol;
    }

    public static function validarCorreo($correo)
    {
        $conn = parent::conectar_base_datos();
        pg_prepare($conn, "validar_correo", "
            SELECT * FROM seguridad_acceso.usuario WHERE email = $1
        ");
        $resultado = pg_execute($conn, "validar_correo", [$correo]);
        $fila = pg_fetch_assoc($resultado);

        if (!$fila) {
            return false;
        }
        return true;
    }

    public static function validarContraseña($correo, $contraseña)
    {
        $conn = parent::conectar_base_datos();
        pg_prepare($conn, "validar_contraseña", "
            SELECT * FROM seguridad_acceso.usuario WHERE email = $1
        ");
        $resultado = pg_execute($conn, "validar_contraseña", [$correo]);
        $fila = pg_fetch_assoc($resultado);

        if (!$fila) {
            return false;
        }

        if (!parent::verificar_contraseña($contraseña, $fila["contraseña"])) {
            return false;
        }

        return true;
    }

    public static function iniciarSesion($correo, $contraseña)
    {
        $conn = parent::conectar_base_datos();

        pg_prepare($conn, "iniciar_sesion", "
                SELECT 
                U.id_usuario,
                U.nombre,
                U.apellido,
                U.cedula,
                U.email,
                U.telefono AS telefono_usuario,
                U.activo,

                R.id_rol,
                R.nombre_rol,
                R.descripcion AS descripcion_rol,

                S.id_sucursal,
                S.nombre AS nombre_sucursal,
                S.direccion,
                S.telefono AS telefono_sucursal,
                S.rif

            FROM seguridad_acceso.usuario U
            LEFT JOIN seguridad_acceso.rol R ON U.id_rol = R.id_rol
            LEFT JOIN core.sucursal S ON U.id_sucursal = S.id_sucursal
            WHERE U.email = $1
        ");

        $resultado = pg_execute($conn, "iniciar_sesion", [$correo]);
        $fila = pg_fetch_assoc($resultado);

        if (!$fila) {
            return false;
        }

        $_SESSION["sesion_usuario"] =
            [
                "usuario" => [
                    "id_usuario" => $fila["id_usuario"],
                    "nombre" => $fila["nombre"],
                    "apellido" => $fila["apellido"],
                    "cedula" => $fila["cedula"],
                    "email" => $fila["email"],
                    "telefono" => $fila["telefono_usuario"],
                    "activo" => $fila["activo"]
                ],
                "rol" => [
                    "id_rol" => $fila["id_rol"],
                    "nombre_rol" => $fila["nombre_rol"],
                    "descripcion" => $fila["descripcion_rol"]
                ],
                "sucursal" => [
                    "id_sucursal" => $fila["id_sucursal"],
                    "nombre_sucursal" => $fila["nombre_sucursal"],
                    "direccion" => $fila["direccion"],
                    "telefono_sucursal" => $fila["telefono_sucursal"],
                    "rif" => $fila["rif"]
                ]
            ];

        return true;
    }
}
