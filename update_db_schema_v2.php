<?php
require_once __DIR__ . "/src/model/mainModel.php";

class SchemaUpdater extends mainModel
{
    public static function update()
    {
        echo "Iniciando actualización de esquema...\n";
        $conn = parent::conectar_base_datos();

        if (!$conn) {
            die("Error al conectar a la base de datos.");
        }

        // 1. Agregar columna 'simbolo' a 'finanzas.moneda'
        $sql1 = "ALTER TABLE finanzas.moneda ADD COLUMN IF NOT EXISTS simbolo VARCHAR(5) DEFAULT '$'";
        $res1 = pg_query($conn, $sql1);
        echo $res1 ? "[OK] Columna 'simbolo' verificada en 'finanzas.moneda'.\n" : "[ERROR] Falló al agregar 'simbolo'.\n";

        // 2. Agregar columna 'origen' a 'finanzas.tasa_cambio'
        $sql2 = "ALTER TABLE finanzas.tasa_cambio ADD COLUMN IF NOT EXISTS origen VARCHAR(20) DEFAULT 'Manual'";
        $res2 = pg_query($conn, $sql2);
        echo $res2 ? "[OK] Columna 'origen' verificada en 'finanzas.tasa_cambio'.\n" : "[ERROR] Falló al agregar 'origen'.\n";

        echo "Actualización completada.\n";
    }
}

SchemaUpdater::update();
