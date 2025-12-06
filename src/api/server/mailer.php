<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

function enviarCorreoAlerta($producto, $stock_actual, $minimo, $sucursal_nombre)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor (Placeholder - Ajustar con credenciales reales si existen)
        // Por ahora usaremos mail() nativo de PHP si no hay SMTP configurado, 
        // o configuramos un SMTP de prueba si el usuario lo provee.
        // Asumiremos que el servidor local puede enviar correos o que se configurará luego.

        // $mail->isSMTP(); 
        // $mail->Host       = 'smtp.gmail.com';
        // $mail->SMTPAuth   = true;
        // $mail->Username   = 'tu_correo@gmail.com';
        // $mail->Password   = 'tu_contraseña_de_aplicacion';
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->Port       = 465;

        // Configuración básica para envío local (o SMTP si se descomenta arriba)
        $mail->setFrom('no-reply@quickstock.com', 'QuickStock');
        $mail->addAddress('alvarezrafaelat@gmail.com', 'Administrador');

        $mail->isHTML(true);
        $mail->Subject = "ALERTA: Stock Bajo - $producto";

        $body = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #d9534f;'>Alerta de Stock Bajo</h2>
            <p>El siguiente producto ha alcanzado su nivel mínimo de inventario:</p>
            <ul>
                <li><strong>Producto:</strong> $producto</li>
                <li><strong>Sucursal:</strong> $sucursal_nombre</li>
                <li><strong>Stock Actual:</strong> <span style='color: red; font-weight: bold;'>$stock_actual</span></li>
                <li><strong>Mínimo Permitido:</strong> $minimo</li>
            </ul>
            <p>Por favor, gestione la reposición de este artículo lo antes posible.</p>
            <hr>
            <small>QuickStock - Sistema de Gestión de Inventario</small>
        </div>
        ";

        $mail->Body = $body;
        $mail->AltBody = "Alerta de Stock Bajo: El producto $producto (Sucursal: $sucursal_nombre) tiene un stock actual de $stock_actual (Mínimo: $minimo).";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Loguear error pero no detener la ejecución del sistema
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
