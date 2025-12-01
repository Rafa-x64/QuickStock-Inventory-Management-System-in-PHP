<?php

function conectar_base_datos()
{
    date_default_timezone_set('America/Caracas');
    $con = pg_connect("host= localhost port=5432 dbname=QuickStock user=postgres password=postgres");

    if (!$con) {
        error_log("Error de conexión a la base de datos PostgreSQL.");
        exit();
    }

    return $con;
}

//obligatorio retorno de array para interpretar con js
function obtenerNombreSucursal()
{
    return ["nombre_sucursal" => $_SESSION["sesion_usuario"]["sucursal"]["nombre_sucursal"]];
}
