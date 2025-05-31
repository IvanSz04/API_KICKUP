<?php
header("Content-Type: application/json");

// CONEXIÓN A TU BASE DE DATOS
include("db.php"); // ← usa esto si ya tienes uno
// O reemplaza por conexión directa así:
/*
$conn = new mysqli("localhost", "usuario_db", "contraseña_db", "nombre_db");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión"]));
}
*/

// RECIBIR DATOS DEL CLIENTE
$data = json_decode(file_get_contents("php://input"));

$username_email = $data->username_email;
$password = $data->password;

// VERIFICAR SI YA EXISTE
$query = "SELECT * FROM usuarios WHERE username_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "El usuario ya existe"]);
} else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO usuarios (username_email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ss", $username_email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario creado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar"]);
    }
};
$conn->close();