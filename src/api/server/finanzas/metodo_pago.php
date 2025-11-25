<?php

function obtenerMetodosPago($filtro = null, $referencia = null, $estado = null)
{
    $conn = conectar_base_datos();
    $sql = "SELECT * FROM finanzas.metodo_pago";
    $params = [];
    $whereClauses = [];
    $paramIndex = 1; // Contador para los parámetros posicionales ($1, $2, etc.)

    // 2. Lógica para FILTRO POR NOMBRE
    if (!empty($filtro)) {
        $whereClauses[] = "(LOWER(nombre) LIKE LOWER($" . $paramIndex++ . "))";
        $params[] = "%$filtro%";
    }

    // 3. Lógica para FILTRO POR REFERENCIA ('t' o 'f')
    if ($referencia === 't' || $referencia === 'f') {
        $whereClauses[] = "referencia = $" . $paramIndex++ . "";
        // Se usa 'TRUE' o 'FALSE' en mayúsculas para mejor compatibilidad con booleanos de PostgreSQL
        $params[] = ($referencia === 't' ? 'TRUE' : 'FALSE');
    }

    // 4. Lógica para FILTRO POR ESTADO ('t' o 'f')
    if ($estado === 't' || $estado === 'f') {
        $whereClauses[] = "activo = $" . $paramIndex++ . "";
        $params[] = ($estado === 't' ? 'TRUE' : 'FALSE');
    }

    // 5. Construir la cláusula WHERE
    if (count($whereClauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    $sql .= " ORDER BY id_metodo_pago ASC";

    // Ejecutar la consulta
    $stmt_name = "obtener_metodos_pago_" . uniqid();
    pg_prepare($conn, $stmt_name, $sql);
    $result = pg_execute($conn, $stmt_name, $params);

    $metodo = pg_fetch_all($result) ?: [];
    return ["data" => $metodo];
}
function obtenerMetodoPagoPorId($id)
{
    $conn = conectar_base_datos();
    $sql = "SELECT * FROM finanzas.metodo_pago WHERE id_metodo_pago = $1";

    $stmt_name = "obtener_metodo_pago_id_" . uniqid();
    pg_prepare($conn, $stmt_name, $sql);
    $result = pg_execute($conn, $stmt_name, [$id]);

    $metodo = pg_fetch_assoc($result) ?: null;
    return ["data" => $metodo];
}
