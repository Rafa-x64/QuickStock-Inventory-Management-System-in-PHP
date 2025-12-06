<?php
// Mimic context of src/api/server/index.php
define('__API_SERVER_ROOT__', __DIR__ . '/src/api/server');

// Include functions (needed for connecting to DB)
require_once __API_SERVER_ROOT__ . '/index.functions.php';

// Include the API file we fixed
// This file has the require_once to the model. 
// If relative path is wrong, it will fail here.
require_once __API_SERVER_ROOT__ . '/finanzas/moneda.php';

echo "--- TEST: obtenerTodasMonedas ---\n";
try {
    $res = obtenerTodasMonedas();
    print_r($res);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
