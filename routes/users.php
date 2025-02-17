<?php
require '../config/config.php';
require '../includes/Database.php';

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

// Recibir JSON
$data = json_decode(file_get_contents("php://input"));

// Validación básica
if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}

// Encriptar contraseña
$password_hash = password_hash($data->password, PASSWORD_BCRYPT);

$sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":email", $data->email);
$stmt->bindParam(":password", $password_hash);

if ($stmt->execute()) {
    echo json_encode(["message" => "Usuario registrado con éxito"]);
} else {
    echo json_encode(["error" => "Error al registrar usuario"]);
}
?>
