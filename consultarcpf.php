<?php
// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // Cache for 24 hours

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit;
}

$cpf = $_GET['cpf'] ?? '';

if (!$cpf) {
    echo json_encode(['success' => false, 'error' => 'CPF não informado']);
    exit;
}

$cpf = preg_replace('/\D/', '', $cpf);

$url = 'https://api.amnesiatecnologia.rocks/?token=261207b9-0ec2-468a-ac04-f9d38a51da88&cpf=' . $cpf;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['success' => false, 'error' => 'Erro ao consultar CPF']);
    exit;
}

$data = json_decode($response, true);

if (!isset($data['DADOS'])) {
    echo json_encode(['success' => false, 'error' => 'CPF não encontrado']);
    exit;
}

// Retornar no formato esperado pelo frontend (com DADOS em maiúsculo)
echo json_encode($data);
