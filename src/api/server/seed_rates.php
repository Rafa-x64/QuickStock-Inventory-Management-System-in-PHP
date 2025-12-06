<?php
require_once __DIR__ . "/index.functions.php";

$conn = conectar_base_datos();

// 1. Obtener IDs
$resUS = pg_fetch_assoc(pg_query($conn, "SELECT id_moneda FROM finanzas.moneda WHERE codigo = 'USD'"));
$resVES = pg_fetch_assoc(pg_query($conn, "SELECT id_moneda FROM finanzas.moneda WHERE codigo = 'VES'"));
$resEUR = pg_fetch_assoc(pg_query($conn, "SELECT id_moneda FROM finanzas.moneda WHERE codigo = 'EUR'"));

$idUSD = $resUS['id_moneda'] ?? 1;
$idVES = $resVES['id_moneda'] ?? 3;
$idEUR = $resEUR['id_moneda'] ?? 2;

// 2. Insertar Tasas Dummy (si no existen) para que el sistema "arranque"
// USD = 1
pg_query($conn, "INSERT INTO finanzas.tasa_cambio (id_moneda, tasa, fecha, activo, origen) VALUES ($idUSD, 1, CURRENT_DATE, true, 'Inicial') ON CONFLICT DO NOTHING");

// VES = 64.73 (Ejemplo instruccion)
$sqlCheckVES = "SELECT id_tasa FROM finanzas.tasa_cambio WHERE id_moneda = $idVES AND activo = true";
if (pg_num_rows(pg_query($conn, $sqlCheckVES)) == 0) {
    pg_query($conn, "INSERT INTO finanzas.tasa_cambio (id_moneda, tasa, fecha, activo, origen) VALUES ($idVES, 50.00, CURRENT_DATE, true, 'Manual')");
    echo "Tasa VES insertada (50.00)\n";
} else {
    echo "Tasa VES ya existe.\n";
}

// EUR = 0.95 (Ejemplo)
$sqlCheckEUR = "SELECT id_tasa FROM finanzas.tasa_cambio WHERE id_moneda = $idEUR AND activo = true";
if (pg_num_rows(pg_query($conn, $sqlCheckEUR)) == 0) {
    pg_query($conn, "INSERT INTO finanzas.tasa_cambio (id_moneda, tasa, fecha, activo, origen) VALUES ($idEUR, 0.95, CURRENT_DATE, true, 'Manual')");
    echo "Tasa EUR insertada (0.95)\n";
} else {
    echo "Tasa EUR ya existe.\n";
}

echo "Tasas iniciales verificadas.\n";
