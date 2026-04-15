<?php
/**
 * proxy.php - Identidad Real
 * Este archivo actúa como intermediario seguro entre la WebApp y Google Gemini.
 * Protege tu API Key para que no sea visible en el navegador.
 */

// --- CONFIGURACIÓN ---
// 1. Genera una NUEVA API Key en: https://aistudio.google.com/app/apikey
// 2. Pégala aquí abajo entre las comillas.
$api_key = "TU_NUEVA_API_KEY_AQUI"; 

// --- LÓGICA DEL PROXY ---
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Recibir datos del frontend
$input_data = json_decode(file_get_contents('php://input'), true);

if (!$input_data || !isset($input_data['contents'])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos de entrada inválidos"]);
    exit;
}

// URL de Google Gemini API
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $api_key;

// Configurar cURL para la petición
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input_data));

// Ejecutar y obtener respuesta
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión con Gemini: " . curl_error($ch)]);
} else {
    http_response_code($http_code);
    echo $response;
}

curl_close($ch);
?>
