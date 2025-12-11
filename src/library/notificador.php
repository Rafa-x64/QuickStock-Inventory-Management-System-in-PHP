<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir autoload de Composer
require_once __DIR__ . "/../../vendor/autoload.php";

class Notificador
{
    /**
     * Envía una alerta por correo cuando cambia una tasa de cambio.
     */
    public static function enviarAlertaCambioTasa($moneda, $viejaTasa, $nuevaTasa, $origen)
    {
        $mail = new PHPMailer(true); // Argumento true habilita excepciones

        try {
            // Cargar configuración centralizada
            $config = include __DIR__ . "/../api/server/email_config.php";

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = $config['port'];

            // Remitente y Destinatario
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($config['username'], 'Rafael Alvarez');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = "💱 Alerta QuickStock: Cambio Tasa $moneda";

            $fecha = date("d-m-Y H:i:s");

            // Formatear valores para mostrar con Bs.
            // Umbral mínimo: las tasas reales en Bs son altas (40+), valores como 1 o 0.86 son errores
            $umbralMinimo = 10;
            $viejaTasaEsValida = ($viejaTasa !== 'N/A' && $viejaTasa !== null && (float)$viejaTasa >= $umbralMinimo);
            $viejaTasaFormateada = $viejaTasaEsValida
                ? 'Bs. ' . number_format((float)$viejaTasa, 2, ',', '.')
                : 'Sin registro previo válido';
            $nuevaTasaFormateada = 'Bs. ' . number_format((float)$nuevaTasa, 2, ',', '.');

            // Calcular variación porcentual solo si hay tasa anterior válida
            $variacion = '';
            if ($viejaTasaEsValida && (float)$viejaTasa > 0) {
                $porcentaje = (((float)$nuevaTasa - (float)$viejaTasa) / (float)$viejaTasa) * 100;
                $signo = $porcentaje >= 0 ? '+' : '';
                $colorVariacion = $porcentaje >= 0 ? '#dc3545' : '#28a745'; // Rojo si sube, verde si baja
                $variacion = "<li style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Variación:</strong> <span style='color: $colorVariacion; font-weight: bold;'>{$signo}" . number_format($porcentaje, 2, ',', '.') . "%</span></li>";
            }

            // Estilos CSS inline
            $styleContainer = "padding: 20px; border: 1px solid #ddd; border-top: 5px solid #28a745; font-family: Arial, sans-serif;";
            $styleHeader = "font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;";
            $styleList = "list-style: none; padding: 0;";
            $styleItem = "padding: 8px 0; border-bottom: 1px solid #eee;";
            $badgeColor = ($origen == 'API') ? '#007bff' : '#ffc107';
            $styleBadge = "background-color: $badgeColor; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;";

            $body = "
            <div style='$styleContainer'>
                <div style='$styleHeader'>💱 Actualización de Tasa de Cambio</div>
                <p>Se ha registrado un cambio en el valor de <strong>1 $moneda</strong> en bolívares.</p>
                <ul style='$styleList'>
                    <li style='$styleItem'><strong>Fecha:</strong> $fecha</li>
                    <li style='$styleItem'><strong>Valor Anterior:</strong> $viejaTasaFormateada</li>
                    <li style='$styleItem'><strong>Valor Nuevo:</strong> $nuevaTasaFormateada</li>
                    $variacion
                    <li style='$styleItem'><strong>Origen:</strong> <span style='$styleBadge'>$origen</span></li>
                </ul>
                <p style='font-size: 12px; color: #777;'>Mensaje automático del sistema QuickStock.</p>
            </div>
            ";

            $mail->Body    = $body;
            $mail->AltBody = "Cambio en tasa $moneda. Anterior: $viejaTasaFormateada, Nuevo: $nuevaTasaFormateada. Origen: $origen. Fecha: $fecha";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Loguear error en archivo
            file_put_contents(__DIR__ . '/../../logs/mail_errors.log', date('Y-m-d H:i:s') . " Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            return false;
        }
    }

    /**
     * Envía una alerta por correo con la lista de productos con stock bajo.
     * @param array $productos - Array de productos con stock bajo (codigo, producto, cantidad, sucursal)
     */
    public static function enviarAlertaStockBajo($productos)
    {
        if (empty($productos)) {
            return false; // No hay productos con stock bajo
        }

        $mail = new PHPMailer(true);

        try {
            // Cargar configuración centralizada
            $config = include __DIR__ . "/../api/server/email_config.php";

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = $config['port'];

            // Remitente y Destinatario
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($config['username'], 'Rafael Alvarez');

            // Contenido
            $mail->isHTML(true);
            $cantidadProductos = count($productos);
            $mail->Subject = "⚠️ Alerta QuickStock: $cantidadProductos producto(s) con stock bajo";

            $fecha = date("d-m-Y H:i:s");

            // Construir tabla HTML de productos
            $tablaProductos = "";
            foreach ($productos as $prod) {
                $codigo = $prod['codigo'] ?? '-';
                $nombre = $prod['producto'] ?? $prod['nombre'] ?? '-';
                $cantidad = $prod['cantidad'] ?? 0;
                $sucursal = $prod['sucursal_nombre'] ?? $prod['sucursal'] ?? 'General';

                $colorCantidad = ($cantidad == 0) ? '#dc3545' : '#ffc107'; // Rojo si 0, amarillo si bajo
                $tablaProductos .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>$codigo</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>$nombre</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>
                        <span style='background-color: $colorCantidad; color: #fff; padding: 3px 10px; border-radius: 4px; font-weight: bold;'>$cantidad</span>
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>$sucursal</td>
                </tr>";
            }

            $body = "
            <div style='padding: 20px; border: 1px solid #ddd; border-top: 5px solid #dc3545; font-family: Arial, sans-serif;'>
                <div style='font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;'>⚠️ Alerta de Stock Bajo</div>
                <p>Se han detectado <strong>$cantidadProductos producto(s)</strong> con stock bajo o agotado que requieren atención.</p>
                <p style='font-size: 12px; color: #666;'>Fecha del reporte: $fecha</p>
                
                <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                    <thead>
                        <tr style='background-color: #f8f9fa;'>
                            <th style='padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;'>Código</th>
                            <th style='padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;'>Producto</th>
                            <th style='padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;'>Cantidad</th>
                            <th style='padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;'>Sucursal</th>
                        </tr>
                    </thead>
                    <tbody>
                        $tablaProductos
                    </tbody>
                </table>
                
                <p style='margin-top: 20px; padding: 15px; background-color: #fff3cd; border-radius: 5px;'>
                    <strong>Recomendación:</strong> Revise el inventario y realice los pedidos necesarios para reabastecer estos productos.
                </p>
                <p style='font-size: 12px; color: #777; margin-top: 20px;'>Mensaje automático del sistema QuickStock.</p>
            </div>
            ";

            $mail->Body = $body;

            // Texto plano alternativo
            $altBody = "ALERTA DE STOCK BAJO - QuickStock\n\n";
            $altBody .= "Se han detectado $cantidadProductos producto(s) con stock bajo:\n\n";
            foreach ($productos as $prod) {
                $altBody .= "- " . ($prod['producto'] ?? $prod['nombre'] ?? '-') . ": " . ($prod['cantidad'] ?? 0) . " unidades\n";
            }
            $altBody .= "\nFecha: $fecha";
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Loguear error en archivo
            file_put_contents(__DIR__ . '/../../logs/mail_errors.log', date('Y-m-d H:i:s') . " Error Stock Bajo: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            return false;
        }
    }

    /**
     * Envía un correo de prueba para verificar la configuración.
     * @return array - Resultado con status y mensaje
     */
    public static function enviarCorreoPrueba()
    {
        $mail = new PHPMailer(true);

        try {
            // Cargar configuración centralizada
            $config = include __DIR__ . "/../api/server/email_config.php";

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = $config['port'];

            // Remitente y Destinatario
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($config['username'], 'Rafael Alvarez');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = "✅ QuickStock: Correo de Prueba Exitoso";

            $fecha = date("d-m-Y H:i:s");
            $body = "
            <div style='padding: 20px; border: 1px solid #ddd; border-top: 5px solid #28a745; font-family: Arial, sans-serif;'>
                <div style='font-size: 20px; font-weight: bold; color: #28a745; margin-bottom: 15px;'>✅ Configuración de Correo Exitosa</div>
                <p>Este es un correo de prueba del sistema <strong>QuickStock</strong>.</p>
                <p>Si estás recibiendo este mensaje, significa que la configuración de correos está funcionando correctamente.</p>
                <ul style='list-style: none; padding: 0;'>
                    <li style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Servidor SMTP:</strong> {$config['host']}</li>
                    <li style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Puerto:</strong> {$config['port']}</li>
                    <li style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Fecha de prueba:</strong> $fecha</li>
                </ul>
                <p style='font-size: 12px; color: #777; margin-top: 20px;'>Mensaje automático del sistema QuickStock.</p>
            </div>
            ";

            $mail->Body    = $body;
            $mail->AltBody = "Correo de prueba QuickStock. Fecha: $fecha. La configuración de correos funciona correctamente.";

            $mail->send();
            return ["status" => "success", "message" => "Correo de prueba enviado correctamente a {$config['username']}"];
        } catch (Exception $e) {
            // Loguear error en archivo
            $errorInfo = $mail->ErrorInfo ?? 'Error desconocido';
            file_put_contents(__DIR__ . '/../../logs/mail_errors.log', date('Y-m-d H:i:s') . " Error Prueba: " . $errorInfo . "\n", FILE_APPEND);
            return ["status" => "error", "message" => "Error al enviar correo: " . $errorInfo];
        }
    }
}
