<?php
class metodo_pago extends mainModel
{
    public $id_metodo_pago;
    public $nombre;
    public $descripcion;
    public $referencia;
    public $activo;

    public function __construct($nombre = null, $descripcion = null, $referencia = false, $id_metodo_pago = null, $activo = true)
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->referencia = $referencia;
        $this->id_metodo_pago = $id_metodo_pago;
        $this->activo = $activo;
    }

    public function crear()
    {
        $conn = parent::conectar_base_datos();
        $sql = "INSERT INTO finanzas.metodo_pago (nombre, descripcion, referencia, activo) VALUES ($1, $2, $3, $4)";

        $params = [
            $this->nombre,
            $this->descripcion,
            $this->referencia ? 't' : 'f',
            $this->activo ? 't' : 'f'
        ];

        $stmt_name = "crear_metodo_pago_" . uniqid();
        pg_prepare($conn, $stmt_name, $sql);
        $result = pg_execute($conn, $stmt_name, $params);

        return $result ? true : false;
    }

    public static function eliminar($id)
    {
        $conn = parent::conectar_base_datos();
        // Logical deletion
        $sql = "UPDATE finanzas.metodo_pago SET activo = 'f' WHERE id_metodo_pago = $1";

        $stmt_name = "eliminar_metodo_pago_" . uniqid();
        pg_prepare($conn, $stmt_name, $sql);
        $result = pg_execute($conn, $stmt_name, [$id]);

        return $result ? true : false;
    }

    public static function editar($id, $nombre, $descripcion, $referencia, $activo)
    {
        $conn = parent::conectar_base_datos();
        $sql = "UPDATE finanzas.metodo_pago SET nombre = $1, descripcion = $2, referencia = $3, activo = $4 WHERE id_metodo_pago = $5";

        $params = [
            $nombre,
            $descripcion,
            $referencia ? 't' : 'f',
            $activo ? 't' : 'f',
            $id
        ];

        $stmt_name = "editar_metodo_pago_" . uniqid();
        pg_prepare($conn, $stmt_name, $sql);
        $result = pg_execute($conn, $stmt_name, $params);

        return $result ? true : false;
    }

    public static function existeMetodo($nombre)
    {
        $conn = parent::conectar_base_datos();
        $sql = "SELECT COUNT(*) as count FROM finanzas.metodo_pago WHERE LOWER(nombre) = LOWER($1) AND activo = 't'";

        $stmt_name = "existe_metodo_pago_" . uniqid();
        pg_prepare($conn, $stmt_name, $sql);
        $result = pg_execute($conn, $stmt_name, [$nombre]);

        $row = pg_fetch_assoc($result);
        return $row['count'] > 0;
    }
}
