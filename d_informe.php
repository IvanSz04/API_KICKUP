<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "kickup";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Leer JSON de entrada
$data = json_decode(file_get_contents("php://input"), true);

// Validar que haya un ID
if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "Faltan campos obligatorios"]);
    exit;
}

$id = $data['id'];

// Ejecutar DELETE
$sql = "DELETE FROM informes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Consejo eliminado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró el informe con ese ID"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error al ejecutar DELETE: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>