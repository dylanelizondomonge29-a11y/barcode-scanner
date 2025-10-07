<?php
// proxy.php

// Verifica que se hayan pasado los parámetros
if(!isset($_GET['codigo_barras']) || !isset($_GET['api_key'])){
    http_response_code(400);
    echo json_encode(['error' => true, 'mensaje' => 'Faltan parámetros']);
    exit;
}

// Obtiene los parámetros
$codigo = urlencode($_GET['codigo_barras']);
$apiKey = urlencode($_GET['api_key']);

// URL de la API original
$url = "http://62.146.226.238/codigos/codigo_barras.php?api_key=$apiKey&codigo_barras=$codigo";

// Llamada a la API con cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Devuelve la respuesta JSON tal cual
header('Content-Type: application/json');
http_response_code($httpCode);
echo $result;
