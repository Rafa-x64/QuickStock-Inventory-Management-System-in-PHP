<?php
include_once __DIR__ . "/mainModel.php";

class Moneda extends mainModel
{
    /**
     * Obtiene todas las monedas registradas.
     */
    public static function obtenerTodas()
    {
        $conn = parent::conectar_base_datos();
        $sql = "SELECT * FROM finanzas.moneda ORDER BY id_moneda ASC";

        // Preparar
        $stmtName = "get_all_monedas_" . uniqid();
        pg_prepare($conn, $stmtName, $sql);

        $result = pg_execute($conn, $stmtName, []);

        if ($result) {
            return pg_fetch_all($result) ?: [];
        }
        return [];
    }

    /**
     * Crea una nueva moneda.
     */
    public static function crear($nombre, $codigo, $simbolo, $activo)
    {
        $conn = parent::conectar_base_datos(); // Usar método del padre

        $sql = "INSERT INTO finanzas.moneda (nombre, codigo, simbolo, activo) VALUES ($1, $2, $3, $4) RETURNING id_moneda";

        $stmtName = "create_moneda_" . uniqid();
        pg_prepare($conn, $stmtName, $sql);

        $activoDb = ($activo === true || $activo === 'true' || $activo === 't') ? 't' : 'f';

        $result = pg_execute($conn, $stmtName, [$nombre, $codigo, $simbolo, $activoDb]);

        if ($result) {
            $row = pg_fetch_assoc($result);
            return $row['id_moneda'];
        }
        return false;
    }

    /**
     * Edita una moneda existente.
     */
    public static function editar($id_moneda, $nombre, $codigo, $simbolo, $activo)
    {
        $conn = parent::conectar_base_datos();

        $sql = "UPDATE finanzas.moneda SET nombre = $1, codigo = $2, simbolo = $3, activo = $4 WHERE id_moneda = $5";

        $stmtName = "update_moneda_" . uniqid();
        pg_prepare($conn, $stmtName, $sql);

        $activoDb = ($activo === true || $activo === 'true' || $activo === 't') ? 't' : 'f';

        $result = pg_execute($conn, $stmtName, [$nombre, $codigo, $simbolo, $activoDb, $id_moneda]);

        return (bool)$result;
    }
}
