<?php
class color extends mainModel
{
    public int $id_color;
    public string $nombre;
    public bool $activo;

    public function __construct(int $id_color, string $nombre, bool $activo = true)
    {
        $this->id_color = $id_color;
        if (empty($nombre) || strlen($nombre) > 50) {
            throw new InvalidArgumentException("El nombre no puede estar vacío o exceder 50 caracteres.");
        }
        $this->nombre = $nombre;
        $this->activo = $activo;
    }

    public function crear()
    {
        $conn = parent::conectar_base_datos();

        $stmt = pg_prepare($conn, "buscar_color", "SELECT id_color FROM core.color WHERE nombre = $1 LIMIT 1");
        $result = pg_execute($conn, "buscar_color", [$this->nombre]);

        if ($row = pg_fetch_assoc($result)) return intval($row['id_color']);

        $stmt = pg_prepare($conn, "insertar_color", "INSERT INTO core.color (nombre, activo) VALUES ($1, $2) RETURNING id_color");
        $result = pg_execute($conn, "insertar_color", [$this->nombre, $this->activo]);

        $row = pg_fetch_assoc($result);
        return intval($row['id_color']);
    }

    public static function buscarOCrearPorNombre(string $nombre): ?int
    {
        $conn = parent::conectar_base_datos();

        // Limpiamos y convertimos a minúsculas para una búsqueda consistente
        $nombreLimpio = trim(mb_strtolower($nombre, 'UTF-8'));

        if (empty($nombreLimpio)) {
            return null;
        }

        // 1. Buscar (usando LOWER para búsqueda insensible a mayúsculas)
        $sql_buscar = "SELECT id_color FROM core.color WHERE LOWER(nombre) = $1 LIMIT 1";
        $result = pg_query_params($conn, $sql_buscar, [$nombreLimpio]);

        if ($result && pg_num_rows($result) > 0) {
            // El color existe, retornamos su ID
            return intval(pg_fetch_result($result, 0, 'id_color'));
        }

        // 2. Crear si no existe
        // Usamos ucwords/mb_convert_case para capitalizar la primera letra del nombre a insertar
        $nombreCapitalizado = mb_convert_case($nombreLimpio, MB_CASE_TITLE, 'UTF-8');

        $sql_crear = "INSERT INTO core.color (nombre, activo) VALUES ($1, true) RETURNING id_color";
        $result_crear = pg_query_params($conn, $sql_crear, [$nombreCapitalizado]);

        if ($result_crear && pg_num_rows($result_crear) > 0) {
            // Creado con éxito, retornamos el nuevo ID
            return intval(pg_fetch_result($result_crear, 0, 'id_color'));
        }

        // Fallo al buscar y al crear
        return null;
    }

    public function editar(): bool
    {
        $conn = parent::conectar_base_datos();

        // Convertir boolean a 't' o 'f' para PostgreSQL si es necesario, aunque pg_execute suele manejarlo.
        // Aseguramos que sea boolean primitivo.
        $activoDb = $this->activo ? 't' : 'f';

        $sql = "UPDATE core.color SET nombre = $1, activo = $2 WHERE id_color = $3";
        $params = [$this->nombre, $activoDb, $this->id_color];

        $stmt = pg_prepare($conn, "actualizar_color_" . time(), $sql);
        $result = pg_execute($conn, "actualizar_color_" . time(), $params);

        return $result !== false && pg_affected_rows($result) > 0;
    }

    public static function eliminar(int $id_color): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_color <= 0) {
            return false;
        }

        $sql = "UPDATE core.color SET activo = 'f' WHERE id_color = $1";
        $params = [$id_color];

        $stmt = pg_prepare($conn, "desactivar_color_" . time(), $sql);
        $result = pg_execute($conn, "desactivar_color_" . time(), $params);

        return $result !== false && pg_affected_rows($result) > 0;
    }
}
