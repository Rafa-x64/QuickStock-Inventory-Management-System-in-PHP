<?php
// Rutas absolutas para evitar errores de inclusión
require_once __DIR__ . "/../model/mainModel.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Mpdf\Mpdf;



class reportes_C extends mainModel
{
    // Inicializar la conexión
    public static $conn;

    public static function conectar()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Establecer conexión si no existe
        if (!isset(self::$conn) || self::$conn === false) {
            self::$conn = parent::conectar_base_datos();
        }
    }

    public static function generarReporte($params)
    {
        self::conectar();

        $tipo = $params['tipo_reporte'] ?? '';
        $fecha_inicio = $params['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $params['fecha_fin'] ?? date('Y-m-d');

        // Lógica de Sucursal: Prioridad a la sesión
        // Usamos null coalescing operator de forma segura
        $id_sucursal = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;

        if (empty($id_sucursal)) {
            $id_sucursal = $params['id_sucursal'] ?? null;
        }

        // Asegurar que las fechas incluyan la hora completa
        $fecha_inicio_sql = $fecha_inicio . " 00:00:00";
        $fecha_fin_sql = $fecha_fin . " 23:59:59";

        // Verificar si es solicitud de PDF
        if (($params['accion'] ?? '') === 'imprimir_pdf') {
            self::generarPDF($tipo, $fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
            return; // Detener ejecución tras generar PDF
        }

        $out = '';
        switch ($tipo) {
            case 'rotacion':
                $out = self::rotacion($fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
                break;
            case 'ventas':
                $out = self::ventas($fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
                break;
            case 'inventario':
                $out = self::inventario($id_sucursal);
                break;
            case 'financiero':
                $out = self::financiero($fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
                break;
            default:
                $out = '<div class="alert alert-warning">Seleccione un tipo de reporte válido.</div>';
                break;
        }
        return $out;
    }

    public static function generarPDF($tipo, $inicio, $fin, $id_sucursal)
    {
        // Limpiar cualquier salida previa (HTML del header, sidebar, errores, etc.)
        while (ob_get_level()) {
            ob_end_clean();
        }

        $html_content = '';
        $titulo = '';

        switch ($tipo) {
            case 'rotacion':
                $html_content = self::rotacion($inicio, $fin, $id_sucursal);
                $titulo = 'Reporte de Rotación';
                break;
            case 'ventas':
                $html_content = self::ventas($inicio, $fin, $id_sucursal);
                $titulo = 'Reporte de Ventas';
                break;
            case 'inventario':
                $html_content = self::inventario($id_sucursal);
                $titulo = 'Reporte de Inventario';
                break;
            case 'financiero':
                $html_content = self::financiero($inicio, $fin, $id_sucursal);
                $titulo = 'Reporte Financiero';
                break;
            default:
                echo "Tipo de reporte no válido para PDF";
                exit;
        }

        // Estilos básicos para el PDF
        $stylesheet = '
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #333; color: #fff; padding: 8px; text-align: left; }
            td { border-bottom: 1px solid #ddd; padding: 8px; }
            h1 { color: #333; text-align: center; }
            .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
            .alert { padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        ';

        $html = '
            <html>
            <head><style>' . $stylesheet . '</style></head>
            <body>
                <h1>' . $titulo . '</h1>
                <p><strong>Fecha de Emisión:</strong> ' . date('d/m/Y H:i A') . '</p>
                ' . ($tipo !== 'inventario' ? '<p><strong>Rango:</strong> ' . date('d/m/Y', strtotime($inicio)) . ' al ' . date('d/m/Y', strtotime($fin)) . '</p>' : '') . '
                ' . $html_content . '
                <div class="footer">Generado por QuickStock</div>
            </body>
            </html>
        ';

        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
            $mpdf->WriteHTML($html);
            $mpdf->Output($titulo . '_' . date('YmdHis') . '.pdf', 'D'); // 'D' para descargar
            exit;
        } catch (\Mpdf\MpdfException $e) {
            echo "Error al generar PDF: " . $e->getMessage();
            exit;
        }
    }

    public static function rotacion($inicio, $fin, $id_sucursal)
    {
        if (!self::$conn) self::conectar();

        $consulta = "rotacion_" . uniqid();
        $params = [$inicio, $fin];
        $sql = "SELECT 
                    p.nombre as producto,
                    p.codigo_barra,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.cantidad * dv.precio_unitario) as total_ingresos
                FROM ventas.detalle_venta dv
                JOIN inventario.producto p ON dv.id_producto = p.id_producto
                JOIN ventas.venta v ON dv.id_venta = v.id_venta
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true";

        if (!empty($id_sucursal)) {
            $sql .= " AND v.id_usuario IN (SELECT id_usuario FROM seguridad_acceso.usuario WHERE id_sucursal = $3)";
            $params[] = $id_sucursal;
        }

        $sql .= " GROUP BY p.nombre, p.codigo_barra ORDER BY total_vendido DESC";

        $prepare = pg_prepare(self::$conn, $consulta, $sql);
        if (!$prepare) {
            return '<div class="alert alert-danger">Error al preparar consulta rotación: ' . pg_last_error(self::$conn) . '</div>';
        }

        $resultado = pg_execute(self::$conn, $consulta, $params);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al ejecutar consulta rotación.</div>';
        }
        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No hay datos de rotación.</div>';
        }

        return self::generarTabla(
            ['Producto', 'Código', 'Unidades Vendidas', 'Ingresos Generados'],
            $datos,
            ['producto', 'codigo_barra', 'total_vendido', 'total_ingresos'],
            true
        );
    }

    public static function ventas($inicio, $fin, $id_sucursal)
    {
        if (!self::$conn) self::conectar();

        $consulta = "ventas_" . uniqid();
        $params = [$inicio, $fin];
        $sql = "SELECT 
                    v.id_venta,
                    TO_CHAR(v.fecha, 'DD/MM/YYYY HH12:MI AM') as fecha_hora,
                    COALESCE(c.nombre || ' ' || c.apellido, 'Cliente Genérico') as cliente,
                    u.nombre || ' ' || u.apellido as vendedor,
                    COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) as total
                FROM ventas.venta v
                LEFT JOIN core.cliente c ON v.id_cliente = c.id_cliente
                JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                LEFT JOIN ventas.detalle_venta dv ON v.id_venta = dv.id_venta
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true";

        if (!empty($id_sucursal)) {
            $sql .= " AND u.id_sucursal = $3";
            $params[] = $id_sucursal;
        }

        $sql .= " GROUP BY v.id_venta, v.fecha, c.nombre, c.apellido, u.nombre, u.apellido ORDER BY v.fecha DESC";

        $prepare = pg_prepare(self::$conn, $consulta, $sql);
        if (!$prepare) {
            return '<div class="alert alert-danger">Error al preparar consulta ventas: ' . pg_last_error(self::$conn) . '</div>';
        }

        $resultado = pg_execute(self::$conn, $consulta, $params);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al ejecutar consulta ventas.</div>';
        }
        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No se encontraron ventas.</div>';
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
        if (!self::$conn) self::conectar();

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

        $prepare = pg_prepare(self::$conn, $consulta, $sql);
        if (!$prepare) {
            return '<div class="alert alert-danger">Error al preparar consulta inventario: ' . pg_last_error(self::$conn) . '</div>';
        }

        $resultado = pg_execute(self::$conn, $consulta, $params);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al ejecutar consulta inventario.</div>';
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

    public static function financiero($inicio, $fin, $id_sucursal)
    {
        if (!self::$conn) self::conectar();

        $consulta = "financiero_" . uniqid();
        $params = [$inicio, $fin];
        $sql = "SELECT 
                    mp.nombre as metodo,
                    m.codigo as moneda,
                    COUNT(pv.id_pago) as transacciones,
                    SUM(pv.monto) as total_declarado
                FROM ventas.pago_venta pv
                JOIN finanzas.metodo_pago mp ON pv.id_metodo_pago = mp.id_metodo_pago
                JOIN finanzas.moneda m ON pv.id_moneda = m.id_moneda
                JOIN ventas.venta v ON pv.id_venta = v.id_venta
                JOIN seguridad_acceso.usuario u ON v.id_usuario = u.id_usuario
                WHERE v.fecha BETWEEN $1 AND $2
                AND v.activo = true";

        if (!empty($id_sucursal)) {
            $sql .= " AND u.id_sucursal = $3";
            $params[] = $id_sucursal;
        }

        $sql .= " GROUP BY mp.nombre, m.codigo ORDER BY total_declarado DESC";

        $prepare = pg_prepare(self::$conn, $consulta, $sql);
        if (!$prepare) {
            return '<div class="alert alert-danger">Error al preparar consulta financiero: ' . pg_last_error(self::$conn) . '</div>';
        }

        $resultado = pg_execute(self::$conn, $consulta, $params);

        if (!$resultado) {
            return '<div class="alert alert-danger">Error al ejecutar consulta financiero.</div>';
        }
        $datos = pg_fetch_all($resultado);
        if (!$datos) {
            return '<div class="alert alert-info">No hay movimientos financieros.</div>';
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
