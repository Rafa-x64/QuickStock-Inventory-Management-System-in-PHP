<?php
define('__API_SERVER_ROOT__', __DIR__ . '/src/api/server');
require_once __API_SERVER_ROOT__ . '/index.functions.php';

$conn = conectar_base_datos();

// Fix VES
pg_query($conn, "UPDATE finanzas.moneda SET simbolo = 'Bs.' WHERE codigo = 'VES'");
// Fix USD
pg_query($conn, "UPDATE finanzas.moneda SET simbolo = '$' WHERE codigo = 'USD'");
// Fix EUR
pg_query($conn, "UPDATE finanzas.moneda SET simbolo = '€' WHERE codigo = 'EUR'");

echo "Simbolos actualizados.\n";
