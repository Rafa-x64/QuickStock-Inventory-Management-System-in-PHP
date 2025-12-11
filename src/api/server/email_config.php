<?php
// Configuración centralizada de correos
// IMPORTANTE: El usuario debe generar una "Contraseña de Aplicación" en Google Account > Security > 2-Step Verification > App Passwords
// y colcoarla aquí abajo.

return [
    'host' => 'smtp.gmail.com',
    'username' => 'alvarezrafaelat@gmail.com',
    'password' => 'lcqn jara eltr dvrk', // <--- PEGAR CONTRASEÑA AQUI
    'port' => 465,
    'smtp_secure' => 'ssl', // o PHPMailer::ENCRYPTION_SMTPS
    'from_email' => 'alvarezrafaelat@gmail.com',
    'from_name' => 'QuickStock System'
];
