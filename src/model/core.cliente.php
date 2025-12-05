<?php

/**
 * Modelo Cliente
 * Interactúa con la tabla core.cliente
 */
class cliente extends mainModel
{
    public $nombre;
    public $apellido;
    public $cedula;
    public $telefono;
    public $correo;
    public $direccion;

    public function __construct(
        $nombre,
        $apellido = null,
        $cedula = null,
        $telefono = null,
        $correo = null,
        $direccion = null
    ) {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cedula = $cedula;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
    }

    /**
     * Crear un nuevo cliente
     */
    public function crear()
    {
        $conn = parent::conectar_base_datos();

        $queryName = "insertar_cliente_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "INSERT INTO core.cliente (nombre, apellido, cedula, telefono, correo, direccion, activo) 
             VALUES ($1, $2, $3, $4, $5, $6, true)"
        );

        $resultado = pg_execute($conn, $queryName, [
            $this->nombre,
            $this->apellido,
            $this->cedula,
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
     * Editar cliente existente
     */
    public static function editar($id_cliente, $nombre, $apellido, $cedula, $telefono, $correo, $direccion, $activo)
    {
        $conn = parent::conectar_base_datos();

        $activo_db = (strtolower($activo) === 'true' || $activo === true || $activo === 1 || $activo === '1') ? 't' : 'f';

        $queryName = "actualizar_cliente_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE core.cliente SET 
                nombre = $1, 
                apellido = $2, 
                cedula = $3, 
                telefono = $4, 
                correo = $5, 
                direccion = $6, 
                activo = $7 
            WHERE id_cliente = $8"
        );

        $resultado = pg_execute($conn, $queryName, [
            $nombre,
            $apellido,
            $cedula,
            $telefono,
            $correo,
            $direccion,
            $activo_db,
            $id_cliente
        ]);

        return (bool)$resultado;
    }

    /**
     * Eliminación lógica de cliente
     */
    public static function eliminar($id_cliente): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_cliente <= 0) {
            return false;
        }

        $queryName = "desactivar_cliente_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "UPDATE core.cliente SET activo = 'f' WHERE id_cliente = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_cliente]);

        return $resultado !== false && pg_affected_rows($resultado) > 0;
    }

    /**
     * Verificar si existe cliente por cédula
     */
    public static function existeClientePorCedula($cedula)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_cliente_cedula_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM core.cliente WHERE cedula = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$cedula]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Verificar si existe cliente por cédula excluyendo un ID específico (para edición)
     */
    public static function existeClientePorCedulaYIdDiferente($cedula, $id_cliente)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_cliente_cedula_update_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(*) FROM core.cliente WHERE cedula = $1 AND id_cliente != $2"
        );

        $resultado = pg_execute($conn, $queryName, [$cedula, $id_cliente]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    /**
     * Obtener cliente por ID
     */
    public static function obtenerPorId($id_cliente)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "obtener_cliente_id_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT * FROM core.cliente WHERE id_cliente = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$id_cliente]);

        if (!$resultado || pg_num_rows($resultado) === 0) {
            return null;
        }

        return pg_fetch_assoc($resultado);
    }
}
