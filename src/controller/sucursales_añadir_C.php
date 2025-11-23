<?php 
include_once "model/core.sucursal.php";
class sucursales_añadir_C extends mainModel{

    public static function agregarSucursal($formulario)
    {
        $rif = strtoupper(trim($formulario['rif_sucursal']));
        $nombre = ucwords(trim($formulario['nombre_sucursal']));
        $direccion = trim($formulario['direccion_sucursal']);
        if (!empty($direccion)) {
            $direccion = ucwords($direccion);
        } else {
            $direccion = null;
        }
        $telefono = trim($formulario['telefono_sucursal']);
        $fecha_registro = trim($formulario['fecha_registro']);

        if (sucursal::existeSucursalPorNombre($nombre)) {
            return -1;
        }

        $nuevaSucursal = new sucursal(
            $rif,
            $nombre,
            $direccion,
            $telefono,
            $fecha_registro
        );

        $resultado = $nuevaSucursal->crear();

        return $resultado;
    }

}
?>