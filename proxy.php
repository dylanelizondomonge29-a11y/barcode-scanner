<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$api_key = "123456";
$codigo = $_GET['codigo_barras'] ?? '';

if (!$codigo) {
    echo json_encode(["error" => true, "mensaje" => "Código no recibido"]);
    exit;
}

$url = "http://62.146.226.238/codigos/codigo_barras.php?api_key={$api_key}&codigo_barras={$codigo}";
$response = @file_get_contents($url);

if ($response === FALSE) {
    echo json_encode(["error" => true, "mensaje" => "No se pudo conectar a la API"]);
} else {
    echo $response;
}
?>
