<?php
class talla extends mainModel
{
    public int $id_talla;
    public string $rango_talla;
    public bool $activo;

    public function __construct(int $id_talla, string $rango_talla, bool $activo = true)
    {
        $this->id_talla = $id_talla;
        // La validación simple se realiza aquí, en el constructor
        if (empty($rango_talla) || strlen($rango_talla) > 20) {
            throw new InvalidArgumentException("El rango de talla no puede estar vacío o exceder 20 caracteres.");
        }
        $this->rango_talla = $rango_talla;
        $this->activo = $activo;
    }

    public function crear()
    {
        $conn = parent::conectar_base_datos();

        $stmt = pg_prepare($conn, "buscar_talla", "SELECT id_talla FROM core.talla WHERE rango_talla = $1 LIMIT 1");
        $result = pg_execute($conn, "buscar_talla", [$this->rango_talla]);

        if ($row = pg_fetch_assoc($result)) return intval($row['id_talla']);

        $stmt = pg_prepare($conn, "insertar_talla", "INSERT INTO core.talla (rango_talla, activo) VALUES ($1, $2) RETURNING id_talla");
        $result = pg_execute($conn, "insertar_talla", [$this->rango_talla, $this->activo]);

        $row = pg_fetch_assoc($result);
        return intval($row['id_talla']);
    }

    public static function buscarOCrearPorRango(string $rango): ?int
    {
        $conn = parent::conectar_base_datos();

        // Limpiamos y convertimos a minúsculas para una búsqueda consistente
        $rangoLimpio = trim(mb_strtolower($rango, 'UTF-8'));

        if (empty($rangoLimpio)) {
            return null;
        }

        // 1. Buscar (usando LOWER para búsqueda insensible a mayúsculas)
        $sql_buscar = "SELECT id_talla FROM core.talla WHERE LOWER(rango_talla) = $1 LIMIT 1";
        $result = pg_query_params($conn, $sql_buscar, [$rangoLimpio]);

        if ($result && pg_num_rows($result) > 0) {
            // La talla existe, retornamos su ID
            return intval(pg_fetch_result($result, 0, 'id_talla'));
        }

        // 2. Crear si no existe
        // Usamos mb_strtoupper o simplemente el rango limpio para la inserción, 
        // dependiendo del estándar de formato que uses para los rangos de talla (e.g., S, M, L).
        $rangoNormalizado = mb_strtoupper($rangoLimpio, 'UTF-8');

        $sql_crear = "INSERT INTO core.talla (rango_talla, activo) VALUES ($1, true) RETURNING id_talla";
        $result_crear = pg_query_params($conn, $sql_crear, [$rangoNormalizado]);

        if ($result_crear && pg_num_rows($result_crear) > 0) {
            // Creado con éxito, retornamos el nuevo ID
            return intval(pg_fetch_result($result_crear, 0, 'id_talla'));
        }

        // Fallo al buscar y al crear
        return null;
    }
}
