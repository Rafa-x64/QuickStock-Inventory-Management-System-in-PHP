<?php

/**
 * Modelo Rol
 * Interactúa con la tabla seguridad_acceso.rol
 */
class rol extends mainModel
{
    public $nombre_rol;
    public $descripcion;

    public function __construct(
        $nombre_rol,
        $descripcion = null
    ) {
        $this->nombre_rol = $nombre_rol;
        $this->descripcion = $descripcion;
    }

    /**
     * Crear un nuevo rol
     */
    public function crear()
    {
        $conn = parent::conectar_base_datos();

        $queryName = "insertar_rol_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "INSERT INTO seguridad_acceso.rol (nombre_rol, descripcion, activo) 
             VALUES ($1, $2, true)"
        );

        $resultado = pg_execute($conn, $queryName, [
            $this->nombre_rol,
            $this->descripcion
        ]);

        if (!$resultado) {
            return false;
        }
        return true;
    }

    /**
     * Editar rol existente
     */
    public static function editar($id_rol, $nombre_rol, $descripcion, $activo)
    {
        $conn = parent::conectar_base_datos();

        $activo_db = (strtolower($activo) === 'true' || $activo === true || $activo === 1 || $activo === '1') ? 't' : 'f';

        $queryName = "actualizar_rol_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE seguridad_acceso.rol SET 
                nombre_rol = $1, 
                descripcion = $2, 
                activo = $3 
            WHERE id_rol = $4"
        );

        $resultado = pg_execute($conn, $queryName, [
            $nombre_rol,
            $descripcion,
            $activo_db,
            $id_rol
        ]);

        return (bool)$resultado;
    }

    /**
     * Eliminación lógica de rol
     */
    public static function eliminar($id_rol): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_rol <= 0) {
            return false;
        }

        $queryName = "desactivar_rol_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE seguridad_acceso.rol SET activo = 'f' WHERE id_rol = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_rol]);

        return $resultado !== false && pg_affected_rows($resultado) > 0;
    }

    /**
     * Verificar si existe rol por nombre
     */
    public static function existeRolPorNombre($nombre_rol)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_rol_nombre_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM seguridad_acceso.rol WHERE LOWER(nombre_rol) = LOWER($1)"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre_rol]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Verificar si existe rol por nombre excluyendo un ID específico (para edición)
     */
    public static function existeRolPorNombreYIdDiferente($nombre_rol, $id_rol)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_rol_nombre_update_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM seguridad_acceso.rol WHERE LOWER(nombre_rol) = LOWER($1) AND id_rol != $2"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre_rol, $id_rol]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Obtener rol por ID
     */
    public static function obtenerPorId($id_rol)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "obtener_rol_id_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT * FROM seguridad_acceso.rol WHERE id_rol = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_rol]);

        if (!$resultado || pg_num_rows($resultado) === 0) {
            return null;
        }

        return pg_fetch_assoc($resultado);
    }

    /**
     * Contar usuarios asignados a un rol
     */
    public static function contarUsuariosPorRol($id_rol)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "contar_usuarios_rol_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM seguridad_acceso.usuario WHERE id_rol = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_rol]);

        if (!$resultado) {
            return 0;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']);
    }
}
