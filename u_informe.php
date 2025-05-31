<?php
// Inicia el buffer de salida para capturar todo lo que se envíe al navegador
ob_start(); 

// Muestra todos los errores (útil para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guarda en un archivo de log los datos recibidos por POST y el contenido RAW (JSON)
file_put_contents("debug.log", "POST: " . json_encode($_POST) . "\n", FILE_APPEND);
file_put_contents("debug.log", "RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);

// Define los encabezados para que el navegador sepa cómo interpretar la respuesta
header("Content-Type: application/json");                         
header("Access-Control-Allow-Origin: *");                        
header("Access-Control-Allow-Methods: POST");                    
header("Access-Control-Allow-Headers: Content-Type");           

// Datos de conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "kickup";
$port = 3307;

// Crear la conexión
$conn = new mysqli($host, $user, $password, $database, $port);

// Si falla la conexión, devuelve error 500 y termina
if ($conn->connect_error) {
    http_response_code(500);  // Error interno del servidor
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Obtiene el cuerpo de la solicitud (en formato JSON) y lo convierte en un arreglo PHP
$data = json_decode(file_get_contents("php://input"), true);

// Lista de campos requeridos
$required_fields = ['id', 'tittle', 'difficulty', 'position', 'goals', 'csj1', 'csj2', 'csj3'];

// Revisa que todos los campos requeridos estén presentes
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        echo json_encode(["success" => false, "message" => "Falta el campo: $field"]);
        exit;
    }
}

// Asigna los datos a variables
$id = $data['id'];
$tittle = $data['tittle'];
$difficulty = $data['difficulty'];
$position = $data['position'];
$goals = $data['goals'];
$csj1 = $data['csj1'];
$csj2 = $data['csj2'];
$csj3 = $data['csj3'];

// Sentencia SQL para actualizar un informe existente con los nuevos valores
$sql = "UPDATE informes SET tittle=?, difficulty=?, position=?, goals=?, csj1=?, csj2=?, csj3=? WHERE id=?";
$stmt = $conn->prepare($sql);

// Verifica si la preparación de la sentencia fue exitosa
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en prepare: " . $conn->error]);
    exit;
}

// Enlaza los parámetros a la consulta preparada
$stmt->bind_param("sssssssi", $tittle, $difficulty, $position, $goals, $csj1, $csj2, $csj3, $id);

// Ejecuta la consulta y devuelve el resultado
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

// Cierra la consulta y la conexión
$stmt->close();
$conn->close();

// Guarda la respuesta JSON generada en un archivo de log
file_put_contents("response_debug.log", ob_get_contents(), FILE_APPEND);

// Finaliza el buffer y muestra el contenido en el navegador (opcional)
ob_end_flush();
?>