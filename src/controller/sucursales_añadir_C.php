<?php
include_once "model/core.sucursal.php";
class sucursales_añadir_C extends mainModel
{

    public static function agregarSucursal($formulario)
    {
        $rif = strtoupper(trim($formulario['rif_sucursal']));
        $nombre = ucwords(strtolower(trim($formulario['nombre_sucursal'])));

        $direccion = trim($formulario['direccion_sucursal']);
        if (!empty($direccion)) {
            $direccion = ucwords(strtolower($direccion));
        } else {
            $direccion = null;
        }

        // Formatear Teléfono
        $t = preg_replace('/\D/', '', $formulario['telefono_sucursal']);
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
