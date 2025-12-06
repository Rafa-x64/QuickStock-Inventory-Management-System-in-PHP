<?php
include_once "model/finanzas.moneda.php";
include_once "model/finanzas.tasa.php";

class monedas_tasas_C extends mainModel
{
    /**
     * Obtiene el listado de monedas activas y sus tasas actuales para la vista.
     * Retorna un array con 'monedas' y 'ultima_actualizacion'.
     */
    public static function obtenerResumenTasas()
    {
        $monedas = Moneda::obtenerTodas();
        $resumen = [];

        foreach ($monedas as $m) {
            // Obtener última tasa activa
            $conn = parent::conectar_base_datos();
            $sql = "SELECT tasa, fecha, origen FROM finanzas.tasa_cambio 
                    WHERE id_moneda = $1 AND activo = true 
                    ORDER BY fecha DESC, id_tasa DESC LIMIT 1";

            // Nota: Esto debería ir en el modelo idealmente (obtenerUltimaTasa), 
            // pero lo hago aqui rapido para no re-editar el modelo otra vez si no es necesario.
            // Aunque lo correcto es llamar al modelo.
            // TasaCambio::obtenerHistorial trae todo, podríamos usar limit 1.

            // Mejor usamos una pequeña logica local o agregamos método en modelo si es critico.
            // Por ahora, query directa es segura si se usa pg_prepare.

            $n = "getLastRateC_" . uniqid();
            pg_prepare($conn, $n, $sql);
            $res = pg_execute($conn, $n, [$m['id_moneda']]);
            $row = pg_fetch_assoc($res);

            $m['tasa_actual'] = $row ? $row['tasa'] : (($m['codigo'] == 'USD') ? 1 : 0);
            $m['ultima_fecha'] = $row ? $row['fecha'] : '-';
            $m['origen'] = $row ? $row['origen'] : '-';

            $resumen[] = $m;
        }

        return $resumen;
    }
}
