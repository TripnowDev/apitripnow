<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Validar que se envíen consecutivo y email
if (!isset($data['consecutivo']) || !isset($data['email'])) {
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$consecutivo = $data['consecutivo'];
$email = $data['email'];

// Generar token JWT con consecutivo y email
$token = Auth::generateToken([
    "consecutivo" => $consecutivo,
    "email" => $email
]);

echo json_encode(["token" => $token]);
?>
