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
$username_email = $data["username_email"] ?? "";

if (empty($username_email)) {
    echo json_encode(["success" => false, "message" => "Correo vacío"]);
    exit;
}

// Verificar si ya existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username_email = ?");
$stmt->bind_param("s", $username_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Ya existe
    $user = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "Login exitoso (existente)",
        "id" => $user["id"],
        "username_email" => $username_email
    ]);
} else {
    // Crear nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (username_email, password) VALUES (?, '')");
    $stmt->bind_param("s", $username_email);
    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "Usuario creado",
            "id" => $newId,
            "username_email" => $username_email
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al crear usuario"]);
    }
}

$conn->close();