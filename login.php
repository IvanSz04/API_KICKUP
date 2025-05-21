<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "kickup";
$port = 3307; 

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión"]));
}

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username_email"] ?? "";
$password = $data["password"] ?? "";

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE username_email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        echo json_encode(["success" => true, "message" => "Login exitoso"]);
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
}

$conn->close();
