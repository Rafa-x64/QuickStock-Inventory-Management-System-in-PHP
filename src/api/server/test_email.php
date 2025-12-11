<?php
// test_email.php
require_once __DIR__ . "/../../../vendor/autoload.php";
$config = include __DIR__ . "/email_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Config loaded. Host: " . $config['host'] . ", User: " . $config['username'] . "\n";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['username'];
    $mail->Password = $config['password'];
    $mail->SMTPSecure = 'ssl';
    $mail->Port = $config['port'];

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['username']); // Send to self

    $mail->isHTML(true);
    $mail->Subject = 'QuickStock SMTP Test';
    $mail->Body = 'Success! Email configuration is working.';

    $mail->send();
    echo "Email sent successfully.\n";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}\n";
}
