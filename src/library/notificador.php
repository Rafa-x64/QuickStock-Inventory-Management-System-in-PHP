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
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'alvarezrafaelat@gmail.com';
            $mail->Password   = 'tu_contraseña_de_aplicacion'; // RECORDATORIO: El usuario debe configurar esto
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente y Destinatario
            $mail->setFrom('alvarezrafaelat@gmail.com', 'QuickStock System');
            $mail->addAddress('alvarezrafaelat@gmail.com', 'Rafael Alvarez');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = "Alerta QuickStock: Cambio Tasa $moneda";

            $fecha = date("d-m-Y H:i:s");
            // Estilos CSS inline
            $styleContainer = "padding: 20px; border: 1px solid #ddd; border-top: 5px solid #28a745; font-family: Arial, sans-serif;";
            $styleHeader = "font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px;";
            $styleList = "list-style: none; padding: 0;";
            $styleItem = "padding: 8px 0; border-bottom: 1px solid #eee;";
            $badgeColor = ($origen == 'API') ? '#007bff' : '#ffc107'; // Azul para API, Amarillo para Manual
            $styleBadge = "background-color: $badgeColor; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;";

            $body = "
            <div style='$styleContainer'>
                <div style='$styleHeader'>Actualización de Tasa de Cambio</div>
                <p>Se ha registrado un cambio en el valor de <strong>$moneda</strong>.</p>
                <ul style='$styleList'>
                    <li style='$styleItem'><strong>Fecha:</strong> $fecha</li>
                    <li style='$styleItem'><strong>Valor Anterior:</strong> $viejaTasa</li>
                    <li style='$styleItem'><strong>Valor Nuevo:</strong> $nuevaTasa</li>
                    <li style='$styleItem'><strong>Origen:</strong> <span style='$styleBadge'>$origen</span></li>
                </ul>
                <p style='font-size: 12px; color: #777;'>Mensaje automático del sistema QuickStock.</p>
            </div>
            ";

            $mail->Body    = $body;
            $mail->AltBody = "Cambio en tasa $moneda. Anterior: $viejaTasa, Nuevo: $nuevaTasa. Origen: $origen. Fecha: $fecha";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Loguear error pero no detener la ejecución
            // error_log("Error Notificador: {$mail->ErrorInfo}"); // Comentado para evitar salida en stdout que rompa JSON
            // Podríamos escribir a un archivo si fuera necesario:
            // file_put_contents(__DIR__ . '/../../php_mail_errors.log', date('Y-m-d H:i:s') . " Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            return false;
        }
    }
}
