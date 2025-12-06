<?php
require_once "src/config/conexion.php";

$conn = conectar_base_datos();
$result = pg_query($conn, "SELECT * FROM finanzas.moneda");
$rows = pg_fetch_all($result);

echo "Total Monedas: " . ($rows ? count($rows) : 0) . "\n";
if ($rows) {
    print_r($rows);
} else {
    echo "La tabla finanzas.moneda esta vacia.\n";
}
