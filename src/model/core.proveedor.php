<?php

/**
 * Modelo Proveedor
 * Interactúa con la tabla core.proveedor
 */
class proveedor extends mainModel
{
    public $nombre;
    public $telefono;
    public $correo;
    public $direccion;

    public function __construct(
        $nombre,
        $telefono = null,
        $correo = null,
        $direccion = null
    ) {
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
    }

    /**
     * Crear un nuevo proveedor
     */
    public function crear()
    {
        $conn = parent::conectar_base_datos();

        $queryName = "insertar_proveedor_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "INSERT INTO core.proveedor (nombre, telefono, correo, direccion, activo) 
             VALUES ($1, $2, $3, $4, true)"
        );

        $resultado = pg_execute($conn, $queryName, [
            $this->nombre,
            $this->telefono,
            $this->correo,
            $this->direccion
        ]);

        if (!$resultado) {
            return false;
        }
        return true;
    }

    /**
     * Editar proveedor existente
     */
    public static function editar($id_proveedor, $nombre, $telefono, $correo, $direccion, $activo)
    {
        $conn = parent::conectar_base_datos();

        $activo_db = (strtolower($activo) === 'true' || $activo === true || $activo === 1 || $activo === '1') ? 't' : 'f';

        $queryName = "actualizar_proveedor_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE core.proveedor SET 
                nombre = $1, 
                telefono = $2, 
                correo = $3, 
                direccion = $4, 
                activo = $5 
            WHERE id_proveedor = $6"
        );

        $resultado = pg_execute($conn, $queryName, [
            $nombre,
            $telefono,
            $correo,
            $direccion,
            $activo_db,
            $id_proveedor
        ]);

        return (bool)$resultado;
    }

    /**
     * Eliminación lógica de proveedor
     */
    public static function eliminar($id_proveedor): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_proveedor <= 0) {
            return false;
        }

        $queryName = "desactivar_proveedor_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE core.proveedor SET activo = 'f' WHERE id_proveedor = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_proveedor]);

        return $resultado !== false && pg_affected_rows($resultado) > 0;
    }

    /**
     * Verificar si existe proveedor por nombre
     */
    public static function existeProveedorPorNombre($nombre)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_proveedor_nombre_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM core.proveedor WHERE LOWER(nombre) = LOWER($1)"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Verificar si existe proveedor por nombre excluyendo un ID específico (para edición)
     */
    public static function existeProveedorPorNombreYIdDiferente($nombre, $id_proveedor)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_proveedor_nombre_update_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM core.proveedor WHERE LOWER(nombre) = LOWER($1) AND id_proveedor != $2"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre, $id_proveedor]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Obtener proveedor por ID
     */
    public static function obtenerPorId($id_proveedor)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "obtener_proveedor_id_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT * FROM core.proveedor WHERE id_proveedor = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_proveedor]);

        if (!$resultado || pg_num_rows($resultado) === 0) {
            return null;
        }

        return pg_fetch_assoc($resultado);
    }
}
