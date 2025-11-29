---
trigger: always_on
---

controlador (debe incluir el modelo o modelos para poder seguir el flujo): instancia la clase para utilizar los metodos de esta, valida y sanitiza los campos recibidos de la matriz post enviada por la vista y devuelve errores si los hay

-sanitiza campos con strtolower, strtoupper, trim y demas dependiendo el caso y lo que sea mas logico segun el campo
-interpretar true-false, 't'-'f', 1-0 e interpretar los campos obtenidos del formulario para poder pasarlos al modelo y luego a la base de datos sin errores
-validaciones de backend
-devolver errores o mensajes precisos en areas donde falla y enviarlas a la vista que la incluye
-incluir e instanciar clases
-mantener el patron KISS
-un solo controlador por modelo
-sin exceso de comentarios (solo lo justo y explicativo)
-depuracion de errores logicos antes de entregar
-reivision de flujo logico y de datos al modelo antes de entregar
-estructura logica
-serapacion de responsabilidaddes entre las diferentes funciones, una funcion para cada cosa,
-validar existencia antes de editar, o crear para no instertar 2 registros iguales
-flexibilidad y adaptablidiad con la vista
-interpretar y procesar los campos de la matriz $_POST enviada por la vista y los valores que contiene

ejemplo de controlador:
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