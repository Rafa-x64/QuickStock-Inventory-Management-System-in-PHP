<?php
include_once "model/core.sucursal.php";
class sucursales_editar_C extends mainModel {

    public static function editarSucursal($formulario)
    {
        // 1. Limpieza y Normalización de Datos
        $id_sucursal = (int)trim($formulario['id_sucursal'] ?? 0);
        $rif = strtoupper(trim($formulario['rif_sucursal'] ?? ''));
        $nombre = ucwords(trim($formulario['nombre_sucursal'] ?? ''));
        $direccion = trim($formulario['direccion_sucursal'] ?? '');
        $activo = trim($formulario['activo'] ?? 'false');

        // La fecha de registro no se usa en la actualización UPDATE
        $telefono = trim($formulario['telefono_sucursal']);

        if (!empty($direccion)) {
            $direccion = ucwords($direccion);
        } else {
            $direccion = null;
        }

        // 2. Validación Previa (ID necesario)
        if ($id_sucursal <= 0) {
            return false;
        }

        // 3. Validación de unicidad del nombre (Excluyendo la sucursal actual)
        if (sucursal::existeSucursalPorNombreYIdDiferente($nombre, $id_sucursal)) {
            return -1; // Nombre duplicado
        }

        // 4. Ejecución del método de actualización ESTÁTICO del Modelo
        $resultado = sucursal::editar(
            $id_sucursal,
            $nombre,
            $rif,
            $direccion,
            $telefono,
            $activo
        );

        return $resultado;
    }
}
?>