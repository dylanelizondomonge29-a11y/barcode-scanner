<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

if (!isset($_GET['codigo'])) {
    echo json_encode(['error' => true, 'mensaje' => 'No se recibió código']);
    exit;
}

$apiKey = '123456';
$codigo = urlencode($_GET['codigo']);
$url = "http://62.146.226.238/codigos/codigo_barras.php?api_key=$apiKey&codigo_barras=$codigo";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);
curl_setopt($ch, CURLOPT_USERAGENT, 'SimpleProxy/1.0');

$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => true, 'mensaje' => 'Error al conectar con la API remota', 'detalle' => $err]);
    exit;
}

http_response_code($httpCode);
echo $response;
?>
