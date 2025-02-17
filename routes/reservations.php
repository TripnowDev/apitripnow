<?php
require '../config/config.php';
require '../includes/Database.php';
require '../includes/Auth.php';
require '../includes/Model.php';

header("Content-Type: application/json");

// Obtener el token desde los headers
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$decoded = Auth::verifyToken($token);

// Verificar si el token es válido
if (!$decoded) {
    echo json_encode(["error" => "Token inválido"]);
    exit;
}

// Verificar si el token tiene los datos esperados
if (!isset($decoded->consecutivo) || !isset($decoded->email)) {
    echo json_encode(["error" => "Token inválido o incompleto"]);
    exit;
}

$consecutivo = $decoded->consecutivo;
$email = $decoded->email;

// Instanciar el modelo y obtener las reservaciones
$model = new Model();
$reservaciones = $model->obtenerReservaciones($consecutivo, $email);

if (!empty($reservaciones)) {
    echo json_encode(["status" => "success", "data" => $reservaciones]);
} else {
    echo json_encode(["status" => "error", "message" => "No se encontraron reservaciones"]);
}
?>
