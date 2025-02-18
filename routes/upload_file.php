<?php
require '../config/config.php';
require '../includes/Database.php';
require '../includes/Auth.php';

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

// Obtener el `id_reservation` desde el token
$decoded = Auth::verifyToken($token);
$id_reservation = $decoded->id_reserva; 


if (!isset($_FILES['file'])) {
    echo json_encode(["error" => "No se envió ningún archivo"]);
    exit;
}

$file = $_FILES['file'];
$file_name = basename($file['name']);
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_type = $file['type'];

$name_doc = isset($_POST['name_doc']) ? $_POST['name_doc'] : "Documento sin nombre";
$comment_doc = isset($_POST['comment_doc']) ? $_POST['comment_doc'] : "Sin comentarios";

// Validaciones de tipo de archivo
$allowed_types = ["image/jpeg", "image/png", "application/pdf", "video/mp4", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel"];
if (!in_array($file_type, $allowed_types)) {
    echo json_encode(["error" => "Formato no permitido. Solo se aceptan JPG, PNG, PDF, MP4, Excel"]);
    exit;
}

if ($file_size > 10 * 1024 * 1024) { // Límite de 10MB
    echo json_encode(["error" => "El archivo es demasiado grande (máx. 10MB)"]);
    exit;
}

// **Crear la carpeta si no existe**
$upload_dir = "../upload/docreservas/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// **Renombrar el archivo**
$carac_esp = array(" ", "`", "@", "?", ">", "<", ",", "=", "+", "'", "#", "$", "%", "&", "*", "(", ")", "{", "}", "[", "]", "|", "~", "!", "/", ";", ":");
$replace = str_replace($carac_esp, "-", pathinfo($file_name, PATHINFO_FILENAME));
$fechactual = date("Y-m-d_H-i-s");
$random = md5(uniqid($replace . microtime(), true));

// Definir extensión según el tipo de archivo
$ext = pathinfo($file_name, PATHINFO_EXTENSION);
$final_file_name = $random . "_" . $fechactual . "." . $ext;

$target_file = $upload_dir . $final_file_name;

// **Mover el archivo al servidor**
if (!move_uploaded_file($file_tmp, $target_file)) {
    echo json_encode(["error" => "Error al subir el archivo"]);
    exit;
}

// **Guardar en la base de datos**
$db = new Database();
$conn = $db->connect();

$status = 1;
$created_at = date('Y-m-d H:i:s');

$sql = "INSERT INTO reservation_files (file_name, file_type, create_at, id_reservation, status, name_doc_front, coment) 
        VALUES (:file_name, :file_type, :created_at, :id_reservation, :status, :name_doc_front, :comment_doc)";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":file_name", $final_file_name);
$stmt->bindParam(":file_type", $file_type);
$stmt->bindParam(":created_at", $created_at);
$stmt->bindParam(":id_reservation", $id_reservation);
$stmt->bindParam(":status", $status);
$stmt->bindParam(":name_doc_front", $name_doc);
$stmt->bindParam(":comment_doc", $comment_doc);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Archivo subido con éxito", "file_url" => $target_file]);
} else {
    echo json_encode(["error" => "Error al guardar en la base de datos"]);
}
?>
