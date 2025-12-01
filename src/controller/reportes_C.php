<?php
require_once "model/mainModel.php";
require_once "../vendor/autoload.php";

use Mpdf\Mpdf;

class reportes_C extends mainModel
{

    // Inicializar la conexión
    public static $conn;

    public static function conectar()
    {
        self::$conn = parent::conectar_base_datos();
    }

    public static function generarReporte($params)
    {
        self::conectar();
        $tipo = $params['tipo_reporte'] ?? '';
        $fecha_inicio = $params['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $params['fecha_fin'] ?? date('Y-m-d');
        $id_sucursal = $params['id_sucursal'] ?? null;

        // Asegurar que las fechas incluyan la hora completa para cubrir todo el día final
        $fecha_inicio_sql = $fecha_inicio . " 00:00:00";
        $fecha_fin_sql = $fecha_fin . " 23:59:59";

        $out = '';
        switch ($tipo) {
            case 'rotacion':
                $out = self::rotacion($fecha_inicio_sql, $fecha_fin_sql);
                break;
            case 'ventas':
                $out = self::ventas($fecha_inicio_sql, $fecha_fin_sql);
                break;
            case 'inventario':
                $out = self::inventario($id_sucursal);
                break;
            case 'financiero':
                $out = self::financiero($fecha_inicio_sql, $fecha_fin_sql);
                break;
            default:
                $out = '<div class="alert alert-warning">Seleccione un tipo de reporte válido.</div>';
                break;
        }
        return $out;
    }

    public static function rotacion($inicio, $fin)
    {
        $consulta = "rotacion_" . uniqid();
        $sql = "SELECT 
                    p.nombre as producto,
                    p.codigo_barra,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.subtotal) as total_ingresos
                FROM ventas.detalle_venta dv
                JOIN inventario.producto p ON dv.id_producto = p.id_producto
                JOIN ventas.venta v ON dv.id_venta = v.id_venta
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true
                GROUP BY p.nombre, p.codigo_barra
                ORDER BY total_vendido DESC";

        pg_prepare(self::$conn, $consulta, $sql);
        $resultado = pg_execute(self::$conn, $consulta, [$inicio, $fin]);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al consultar rotación.</div>';
        }

        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No hay datos de rotación para este rango de fechas.</div>';
        }

        return self::generarTabla(
            ['Producto', 'Código', 'Unidades Vendidas', 'Ingresos Generados'],
            $datos,
            ['producto', 'codigo_barra', 'total_vendido', 'total_ingresos'],
            true // Formatear moneda en la última columna
        );
    }

    public static function ventas($inicio, $fin)
    {
        $consulta = "ventas_" . uniqid();
        $sql = "SELECT 
                    v.id_venta,
                    TO_CHAR(v.fecha, 'DD/MM/YYYY HH12:MI AM') as fecha_hora,
                    COALESCE(c.nombre || ' ' || c.apellido, 'Cliente Genérico') as cliente,
                    u.nombre || ' ' || u.apellido as vendedor,
                    v.total
                FROM ventas.venta v
                LEFT JOIN core.cliente c ON v.id_cliente = c.id_cliente
                JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true
                ORDER BY v.fecha DESC";

        pg_prepare(self::$conn, $consulta, $sql);
        $resultado = pg_execute(self::$conn, $consulta, [$inicio, $fin]);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al consultar ventas.</div>';
        }

        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No se encontraron ventas en este rango.</div>';
        }

        return self::generarTabla(
            ['# Venta', 'Fecha', 'Cliente', 'Vendedor', 'Total'],
            $datos,
            ['id_venta', 'fecha_hora', 'cliente', 'vendedor', 'total'],
            true
        );
    }

    public static function inventario($id_sucursal)
    {
        $consulta = "inventario_" . uniqid();
        $params = [];
        $sql = "SELECT 
                    p.codigo_barra,
                    p.nombre as producto,
                    c.nombre as categoria,
                    s.nombre as sucursal,
                    i.cantidad,
                    i.minimo
                FROM inventario.inventario i
                JOIN inventario.producto p ON i.id_producto = p.id_producto
                LEFT JOIN core.categoria c ON p.id_categoria = c.id_categoria
                JOIN core.sucursal s ON i.id_sucursal = s.id_sucursal
                WHERE i.activo = true AND p.activo = true";

        if (!empty($id_sucursal)) {
            $sql .= " AND i.id_sucursal = $1";
            $params[] = $id_sucursal;
        }

        $sql .= " ORDER BY p.nombre ASC";

        pg_prepare(self::$conn, $consulta, $sql);
        $resultado = pg_execute(self::$conn, $consulta, $params);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al consultar inventario.</div>';
        }

        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No hay inventario registrado.</div>';
        }

        return self::generarTabla(
            ['Código', 'Producto', 'Categoría', 'Sucursal', 'Stock', 'Mínimo'],
            $datos,
            ['codigo_barra', 'producto', 'categoria', 'sucursal', 'cantidad', 'minimo']
        );
    }

    public static function financiero($inicio, $fin)
    {
        $consulta = "financiero_" . uniqid();
        $sql = "SELECT 
                    mp.nombre as metodo,
                    m.codigo as moneda,
                    COUNT(pv.id_pago) as transacciones,
                    SUM(pv.monto) as total_declarado
                FROM ventas.pago_venta pv
                JOIN finanzas.metodo_pago mp ON pv.id_metodo_pago = mp.id_metodo_pago
                JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
                JOIN ventas.venta v ON pv.id_venta = v.id_venta
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true
                GROUP BY mp.nombre, m.codigo
                ORDER BY total_declarado DESC";

        pg_prepare(self::$conn, $consulta, $sql);
        $resultado = pg_execute(self::$conn, $consulta, [$inicio, $fin]);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al consultar reporte financiero.</div>';
        }

        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No hay movimientos financieros en este rango.</div>';
        }

        return self::generarTabla(
            ['Método de Pago', 'Moneda', 'Transacciones', 'Total'],
            $datos,
            ['metodo', 'moneda', 'transacciones', 'total_declarado'],
            true
        );
    }

    private static function generarTabla($headers, $data, $keys, $isMoney = false)
    {
        $html = '<div class="table-responsive Quick-card p-3 rounded shadow-sm">';
        $html .= '<table class="table table-striped table-hover align-middle mb-0">';
        $html .= '<thead class="table-dark">';
        $html .= '<tr>';
        foreach ($headers as $header) {
            $html .= '<th scope="col" class="py-3">' . $header . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($keys as $key) {
                $valor = $row[$key] ?? '-';
                // Si es la última columna y se indicó formato moneda (simple heurística)
                if ($isMoney && $key === end($keys) && is_numeric($valor)) {
                    $valor = number_format($valor, 2);
                }
                $html .= '<td class="py-2">' . $valor . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }
}
