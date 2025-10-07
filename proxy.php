<?php
// proxy.php - proxy robusto para desarrollo local
header('Content-Type: application/json; charset=utf-8');
// permitir CORS para pruebas locales (ajusta en producción)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$api = isset($_GET['api_key']) ? $_GET['api_key'] : null;
$codigo = isset($_GET['codigo_barras']) ? $_GET['codigo_barras'] : null;

if (!$api || !$codigo) {
    http_response_code(400);
    echo json_encode(['error'=>true,'mensaje'=>'Faltan parámetros: api_key o codigo_barras']);
    exit;
}

$codigo_enc = urlencode($codigo);
$api_enc = urlencode($api);
$target = "http://62.146.226.238/codigos/codigo_barras.php?api_key={$api_enc}&codigo_barras={$codigo_enc}";

// Init cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true); // necesitamos headers en la respuesta para debug
curl_setopt($ch, CURLOPT_USERAGENT, 'Proxy/1.0 (dev)');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // por si target tiene problemas con SSL (no aplica aqui pero útil)
$response = curl_exec($ch);

if ($response === false) {
    $err = curl_error($ch);
    $errno = curl_errno($ch);
    curl_close($ch);
    http_response_code(502);
    echo json_encode(['error'=>true, 'mensaje'=>'cURL error', 'detalle'=>$err, 'errno'=>$errno]);
    exit;
}

// separar headers y body
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
curl_close($ch);

// Intentar parsear body como JSON
$json_body = null;
$body_is_json = false;
$body_trim = trim($body);
if ($body_trim !== '') {
    $decoded = json_decode($body_trim, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $json_body = $decoded;
        $body_is_json = true;
    }
}

// Devolver un objeto con detalles útiles para debug
http_response_code($http_code);
$out = [
    'error' => ($http_code < 200 || $http_code >= 300) ? true : false,
    'http_code' => $http_code,
    'target' => $target,
    'headers' => $header,
    'body_is_json' => $body_is_json,
];

// si cuerpo JSON, incluirlo directamente
if ($body_is_json) {
    $out['body'] = $json_body;
} else {
    // si no es JSON, incluir el texto raw (limitado a 10KB para no explotar)
    $out['body_text'] = strlen($body) > 10240 ? substr($body,0,10240) . '... (truncated)' : $body;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
