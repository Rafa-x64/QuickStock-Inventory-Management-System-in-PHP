<?php

function obtenerMetodosPago($filtro = null, $referencia = null, $estado = null)
{
    $conn = conectar_base_datos();
    $sql = "SELECT * FROM finanzas.metodo_pago";
    $params = [];
    $whereClauses = [];
    $paramIndex = 1;

    // 1. FILTRO POR NOMBRE
    if (!empty($filtro)) {
        // Uso de LIKE y LOWER para búsqueda sin distinción de mayúsculas/minúsculas
        $whereClauses[] = "(LOWER(nombre) LIKE LOWER($" . $paramIndex++ . "))";
        $params[] = "%$filtro%";
    }

    // 2. FILTRO POR REFERENCIA (Booleano 't' o 'f')
    if ($referencia === 't' || $referencia === 'f') {
        // En PostgreSQL, comparas directamente el valor booleano
        $whereClauses[] = "referencia = $" . $paramIndex++ . "";
        $params[] = $referencia;
    }

    // 3. FILTRO POR ESTADO (Booleano 't' o 'f')
    if ($estado === 't' || $estado === 'f') {
        $whereClauses[] = "activo = $" . $paramIndex++ . "";
        $params[] = $estado;
    }

    // Construir la cláusula WHERE
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
