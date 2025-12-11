<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

function enviarCorreoAlerta($producto, $stock_actual, $minimo, $sucursal_nombre)
{
    $config = include __DIR__ . "/email_config.php";
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port       = $config['port'];

        // Configuración básica
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($config['username']); // Enviar al mismo admin por defecto

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
