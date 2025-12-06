<?php
require_once __DIR__ . '/src/api/server/index.functions.php';
require_once __DIR__ . '/src/model/finanzas.moneda.php';
require_once __DIR__ . '/src/model/finanzas.tasa.php';
require_once __DIR__ . '/src/api/server/finanzas/servicio_api_externa.php';

// Turn off display_errors just in case, but my fix was in the code logging
ini_set('display_errors', 0);

echo "--- SINCRONIZANDO (Directo) ---\n";
// This calls Notificador if rate changes. 
// Since rate is ALREADY 257.something, it might NOT trigger notification if value matches.
// I will temporarily force a dummy value to DB to trigger notification attempt?
// No, that risks breaking it.
// I will simply run it. If it updates (e.g. slight variation), it triggers.
// Or I can delete the rate first? No.

$res = TasaCambio::sincronizarTasasApi();
print_r($res);

echo "\n--- FIN ---\n";
