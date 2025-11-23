<?php 
include_once "model/core.sucursal.php";
class sucursales_eliminar_C extends mainModel{
    public static function eliminarSucursal($formulario){
        $id_sucursal = $formulario["id_sucursal"] ?? null;

        if(!isset($id_sucursal) || empty($id_sucursal) || $id_sucursal == "" || $id_sucursal == null){
            return [
                "estado" => "error",
                "mensaje" => "'ID de sucursal' es obligatorio."
            ];
        }

        $resultado = sucursal::eliminar($id_sucursal);

        if(!$resultado){
            return [
                "estado" => "error",
                "mensaje" => "'Sucursal' no se pudo eliminar."
            ];
        }

        return [
            "estado" => "exito",
            "mensaje" => "'Sucursal' eliminada exitosamente."
        ];
    }
}
?>