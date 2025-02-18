<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header("Content-Type: application/json");

// Concexión a la base de datos
$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"), true);

// Validar entrada
if (!isset($data['consecutivo']) || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
    exit;
}

$consecutivo = trim($data['consecutivo']);
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

if (!$email) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "El email proporcionado no es válido"]);
    exit;
}

$sql = "SELECT id FROM reservations WHERE consecutivo = :consecutivo";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":consecutivo", $consecutivo);
$stmt->execute();
$consecutivoResult = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT id FROM customers WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":email", $email);
$stmt->execute();
$emailResult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consecutivoResult && !$emailResult) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "El consecutivo y el email no coinciden"]);
    exit;
} elseif (!$consecutivoResult) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "El consecutivo no existe"]);
    exit;
} elseif (!$emailResult) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "El email no existe"]);
    exit;
}

$sql = "SELECT re.id FROM reservations re 
        JOIN customers cu ON re.customer_id = cu.id
        WHERE re.consecutivo = :consecutivo AND cu.email = :email";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":consecutivo", $consecutivo);
$stmt->bindParam(":email", $email);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "El consecutivo y el email no coinciden"]);
    exit;
}

// Generar token JWT con consecutivo y email
$token = Auth::generateToken([
    "consecutivo" => $consecutivo,
    "email" => $email
]);

http_response_code(200);
echo json_encode(["status" => "success", "token" => $token]);
?>
