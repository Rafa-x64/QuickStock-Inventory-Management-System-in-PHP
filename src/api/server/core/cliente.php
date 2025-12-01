<?php
require_once __DIR__ . "/../index.functions.php";

function obtenerClientePorCedula($cedula)
{
    $conn = conectar_base_datos();
    $sql = "SELECT * FROM core.cliente WHERE cedula = $1 AND activo = true";
    pg_prepare($conn, "get_cliente_cedula", $sql);
    $result = pg_execute($conn, "get_cliente_cedula", [$cedula]);

    if ($result && pg_num_rows($result) > 0) {
        return ["status" => true, "cliente" => pg_fetch_assoc($result)];
    } else {
        return ["status" => false, "mensaje" => "Cliente no encontrado"];
    }
}
