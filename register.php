<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username_email'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username_email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Usuario ya registrado"]);
    exit;
}
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (username_email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar"]);
    }

$conn->close();
?>