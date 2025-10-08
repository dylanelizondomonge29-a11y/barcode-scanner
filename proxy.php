<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_GET['codigo'])) {
  echo json_encode(["error" => true, "mensaje" => "Falta el parámetro 'codigo'"]);
  exit;
}

$codigo = $_GET['codigo'];
$apiUrl = "http://62.146.226.238/codigos/codigo_barras.php?api_key=123456&codigo_barras=" . urlencode($codigo);

$response = @file_get_contents($apiUrl);

if ($response === FALSE) {
  echo json_encode(["error" => true, "mensaje" => "No se pudo conectar con la API."]);
} else {
  echo $response;
}
?>
