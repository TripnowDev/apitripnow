<?php
require '../config/config.php';
require '../includes/Database.php';
require '../includes/Auth.php';

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

// Validar entrada
if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Correo y contraseña son obligatorios"]);
    exit;
}

// Buscar usuario en la base de datos
$sql = "SELECT id, password FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":email", $data->email);
$stmt->execute();
$user = $stmt->fetch();

if ($user && password_verify($data->password, $user['password'])) {
    $token = Auth::generateToken($user['id']);
    echo json_encode(["token" => $token]);
} else {
    echo json_encode(["error" => "Credenciales inválidas"]);
}
?>
