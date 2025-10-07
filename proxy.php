<?php

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
$response = curl_exec($ch);
curl_close($ch);


header('Content-Type: application/json');
echo $response;
?>
