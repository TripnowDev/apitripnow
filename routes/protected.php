<?php
require '../config/config.php';
require '../includes/Auth.php';

header("Content-Type: application/json");

// Obtener el token del header
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token requerido"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$decoded = Auth::verifyToken($token);

if (!$decoded) {
    echo json_encode(["error" => "Token inválido o expirado"]);
    exit;
}

echo json_encode(["message" => "Acceso concedido", "user_id" => $decoded->sub]);
?>
