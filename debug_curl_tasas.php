<?php
$url = 'http://localhost/DEV/PHP/QuickStock/src/api/server/index.php';
$data = ['accion' => 'obtener_tasas_cambio'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
// Skip SSL verification for localhost if needed
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

if ($response === false) {
    echo "Curl error: " . curl_error($ch);
} else {
    echo "Response: " . $response . "\n";
    echo "Decoded: ";
    print_r(json_decode($response, true));
}
curl_close($ch);
