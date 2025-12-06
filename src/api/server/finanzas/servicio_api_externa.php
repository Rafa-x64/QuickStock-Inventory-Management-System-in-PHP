<?php

class ServicioApiExterna
{
    // API Gratuita ExchangeRate-API
    private static $apiUrl = "https://open.er-api.com/v6/latest/USD";

    public static function obtenerTasasExternas()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // Deshabilitar verificado SSL si es en local y da problemas (opcional)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Error cURL API Tasas: " . $error);
            return null;
        }

        $data = json_decode($response, true);

        if (!$data || !isset($data['rates'])) {
            error_log("Error decodificando API Tasas: " . $response);
            return null;
        }

        return $data['rates']; // Retorna array ['EUR' => 0.95, 'VES' => 50.2, ...]
    }
}
