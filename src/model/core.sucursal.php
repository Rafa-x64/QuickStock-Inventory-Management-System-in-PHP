<?php
class sucursal extends mainModel
{
    public $rif;
    public $nombre;
    public $direccion;
    public $telefono;
    public $fecha_registro; // Asegúrate de que este atributo existe

    public function __construct($rif, $nombre, $direccion = null, $telefono, $fecha_registro)
    {
        $this->rif = $rif;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->fecha_registro = $fecha_registro ?? date('Y-m-d');
    }


    public function crear()
    {
        $conn = parent::conectar_base_datos();

        pg_prepare(
            $conn,
            "agregar_sucursal",
            "INSERT INTO core.sucursal (nombre, direccion, telefono, rif, activo, fecha_registro) 
            VALUES ($1, $2, $3, $4, 't', $5)"
        );

        $resultado = pg_execute($conn, "agregar_sucursal", [
            $this->nombre,
            $this->direccion,
            $this->telefono,
            $this->rif,
            $this->fecha_registro
        ]);

        if (!$resultado) {
            return false;
        }
        return true;
    }

    public static function editar($id_sucursal, $nombre, $rif, $direccion, $telefono, $activo)
    {
        $conn = parent::conectar_base_datos();

        // Convertir el valor de 'activo' de PHP a un booleano de PostgreSQL ('t' o 'f')
        $activo_db = (strtolower($activo) === 'true' || $activo === true || $activo === 1) ? 't' : 'f';

        pg_prepare(
            $conn,
            "actualizar_sucursal_estatica",
            "UPDATE core.sucursal SET 
                nombre = $1, 
                direccion = $2, 
                telefono = $3, 
                rif = $4, 
                activo = $5 
            WHERE id_sucursal = $6"
        );

        $resultado = pg_execute($conn, "actualizar_sucursal_estatica", [
            $nombre,
            $direccion,
            $telefono,
            $rif,
            $activo_db,
            $id_sucursal // Clave para la edición
        ]);

        return (bool)$resultado;
    }

    public static function eliminar($id_sucursal): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_sucursal <= 0) {
            return false;
        }

        $sql = "UPDATE core.sucursal 
            SET activo = 'f' 
            WHERE id_sucursal = $1";

        $params = [$id_sucursal];

        $statement_name = "desactivar_sucursal_" . time();
        $stmt = pg_prepare($conn, $statement_name, $sql);
        $result = pg_execute($conn, $statement_name, $params);

        return $result !== false && pg_affected_rows($result) > 0;
    }

    public static function existeSucursalPorNombre($nombre)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_sucursal_nombre_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(nombre) FROM core.sucursal WHERE nombre = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    public static function existeSucursalPorNombreYIdDiferente($nombre, $id_sucursal)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_sucursal_nombre_update_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(nombre) FROM core.sucursal WHERE nombre = $1 AND id_sucursal != $2"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre, $id_sucursal]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }
}
