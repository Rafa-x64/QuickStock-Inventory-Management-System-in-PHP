<?php
include_once "model/core.sucursal.php";
class sucursales_editar_C extends mainModel
{

    public static function editarSucursal($formulario)
    {
        // 1. Limpieza y Normalización de Datos
        $id_sucursal = (int)trim($formulario['id_sucursal'] ?? 0);
        $rif = strtoupper(trim($formulario['rif_sucursal'] ?? ''));
        $nombre = ucwords(strtolower(trim($formulario['nombre_sucursal'] ?? '')));
        $activo = trim($formulario['activo'] ?? 'false');

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

        $direccion = trim($formulario['direccion_sucursal'] ?? '');
        if (!empty($direccion)) {
            $direccion = ucwords(strtolower($direccion));
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
