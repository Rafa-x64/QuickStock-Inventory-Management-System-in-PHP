<?php

include_once __DIR__ . "/../config/SERVER.php";

class mainModel
{

    //--------------------conexion a la base de datos-------------------------
    protected static function conectar_base_datos()
    {
        $con = pg_connect(PostgreSQL);

        if (!$con) {
            error_log("Error de conexión a la base de datos PostgreSQL.");
            throw new Exception("Error Crítico de Base de Datos: No se pudo conectar a PostgreSQL. Verifique si el servicio está corriendo y las credenciales en SERVER.php");
        }

        return $con;
    }

    //--------------------hacer una consulta-------------------------
    protected static function consulta($sentenciaSQL, $valores = [])
    {
        //Conexión a la base de datos
        $conexion = self::conectar_base_datos();
        if (!$conexion) {
            die("Error de conexión a la base de datos.");
        }

        //Generar un nombre único para la consulta preparada
        $nombreConsulta = "consulta_" . md5($sentenciaSQL);

        //Preparar la consulta
        if (!pg_prepare($conexion, $nombreConsulta, $sentenciaSQL)) {
            die("Error al preparar la consulta.");
        }

        //Ejecutar la consulta con los valores
        $resultado = pg_execute($conexion, $nombreConsulta, $valores);
        if (!$resultado) {
            die("Error al ejecutar la consulta.");
        }

        //Obtener todos los resultados como array asociativo
        $datos = [];
        while ($fila = pg_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }

        //Retornar el array de resultados
        return $datos;
    }

    //------------------desencriptar la matriz sesion---------------------
    protected static function desencriptar_sesion()
    {
        //desencriptar datos de la sesion en el controlador del dashboard para poder mostrar los datos del usuario
        $datos = self::desencriptar_varios_datos($_SESSION);
        return $datos;
    }

    //----------------hashear una contraseña------------------------
    protected static function hashear_contraseña($contraseña): string
    {
        if (!is_string($contraseña)) {
            throw new InvalidArgumentException("el parametro debe ser un string");
        }

        $options = ["cost" => 12];
        $contraseña = password_hash($contraseña, PASSWORD_DEFAULT, $options);

        return $contraseña;
    }

    //--------validar si una contraseña coincide con la de la base de datos----------
    protected static function verificar_contraseña($contraseña, $contraseña_hasheada): bool
    {
        if (!is_string($contraseña)) {
            throw new InvalidArgumentException("el parametro debe ser un string");
        }

        if (password_verify($contraseña, $contraseña_hasheada) == true) {
            return true; //contraseña valida
        } else {
            return false; //contraseña incorrecta
        };
    }

    //----------------encriptar varios datos------------------------
    protected static function encriptar_varios_datos($datos): array
    {
        if (!is_array($datos)) {
            throw new InvalidArgumentException("el parametro debe ser un array asociativo");
        }

        $resultado = [];

        foreach ($datos as $key => $value) {
            $valor_encriptado = self::encriptar_dato($value);
            $resultado[$key] = $valor_encriptado;
        }

        return $resultado;
    }

    //----------------desencriptar varios datos------------------------
    protected static function desencriptar_varios_datos($datos): array
    {
        if (!is_array($datos)) {
            throw new InvalidArgumentException("el parametro debe ser un array asociativo");
        }

        $resultado = [];

        foreach ($datos as $key => $value) {
            $valor_desencriptado = self::desencriptar_dato($value);
            $resultado[$key] = $valor_desencriptado;
        }

        return $resultado;
    }

    //----------------encriptar un dato------------------------
    protected static function encriptar_dato($dato)
    {
        return openssl_encrypt($dato, METHOD, CLAVE, 0, IV);
    }

    //----------------desencriptar un dato------------------------
    public static function desencriptar_dato($dato)
    {
        if (!is_string($dato)) {
            throw new InvalidArgumentException("el parametro debe ser un string");
        }

        return openssl_decrypt($dato, METHOD, CLAVE, 0, IV);
    }
}
