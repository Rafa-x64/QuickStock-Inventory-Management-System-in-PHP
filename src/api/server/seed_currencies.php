<?php
// Usar rutas ABSOLUTAS para evitar errores
require_once __DIR__ . "/index.functions.php";

$conn = conectar_base_datos();

// 1. Limpiar tabla (opcional, pero para asegurar estado limpio en debug)
// pg_query($conn, "TRUNCATE TABLE finanzas.moneda CASCADE"); // NO TRUNCATE por seguridad, mejor INSERT IF NOT EXISTS

$monedas = [
    [1, 'Dolar Estadounidense', 'USD', '$'],
    [2, 'Euro', 'EUR', '€'],
    [3, 'Bolivar Digital', 'VES', 'Bs.'],
];

echo "Sembrando monedas...\n";
foreach ($monedas as $m) {
    $id = $m[0];
    $nombre = $m[1];
    $codigo = $m[2];
    $simbolo = $m[3];

    // Verificar si existe
    $res = pg_query($conn, "SELECT id_moneda FROM finanzas.moneda WHERE id_moneda = $id");
    if (pg_num_rows($res) == 0) {
        // Insertar
        $sql = "INSERT INTO finanzas.moneda (id_moneda, nombre, codigo, simbolo, activo) VALUES ($id, '$nombre', '$codigo', '$simbolo', true)";
        if (pg_query($conn, $sql)) {
            echo "Moneda insertada: $codigo\n";
        } else {
            echo "Error insertando $codigo: " . pg_last_error($conn) . "\n";
        }
    } else {
        echo "Moneda ya existe: $codigo (Solo se actualiza simbolo si falta)\n";
        // Update por si acaso
        pg_query($conn, "UPDATE finanzas.moneda SET simbolo = '$simbolo' WHERE id_moneda = $id");
    }
}

// 2. Insertar Tasa Inicial para USD (1:1)
echo "Sembrando tasa base USD...\n";
$resTasa = pg_query($conn, "SELECT id_tasa FROM finanzas.tasa_cambio WHERE id_moneda = 1 AND tasa = 1");
if (pg_num_rows($resTasa) == 0) {
    $sqlTasa = "INSERT INTO finanzas.tasa_cambio (id_moneda, tasa, fecha, activo, origen) VALUES (1, 1, CURRENT_DATE, true, 'Inicial')";
    pg_query($conn, $sqlTasa);
    echo "Tasa base USD insertada.\n";
}

echo "Semilla completada.\n";
