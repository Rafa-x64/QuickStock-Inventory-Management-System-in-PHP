<?php

/**
 * Script CLI para probar el envío de correos al sincronizar tasas
 */

$baseDir = __DIR__ . "/../../";
$rootDir = __DIR__ . "/../../../";

require_once $baseDir . "config/SERVER.php";
require_once $baseDir . "model/mainModel.php";
require_once $rootDir . "vendor/autoload.php";
require_once $baseDir . "library/notificador.php";
require_once $baseDir . "model/finanzas.moneda.php";
require_once $baseDir . "model/finanzas.tasa.php";

echo "=== QuickStock - Prueba de Sincronización de Tasas ===\n\n";
echo "Sincronizando tasas desde API externa...\n";

$resultado = TasaCambio::sincronizarTasasApi();

echo "\nResultado de sincronización:\n";
print_r($resultado);

echo "\n=== Fin de la prueba ===\n";
echo "Si hubo actualizaciones, revisa tu correo.\n";
