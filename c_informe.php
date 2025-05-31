<?php

// Establece que la respuesta será en formato JSON
header('Content-Type: application/json');

// Incluye el archivo de conexión a la base de datos (debe definir la variable $conn)
include 'db.php';

// Lee y decodifica el JSON recibido en el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Obtiene los valores del JSON recibido o asigna valores por defecto si no existen
$user_id   = $data['user_id'] ?? null;    // ID del usuario (debe ser obligatorio)
$tittle    = $data['tittle'] ?? '';       // Título del informe
$difficulty = $data['difficulty'] ?? '';  // Dificultad del informe
$position   = $data['position'] ?? '';    // Posición
$goals      = $data['goals'] ?? '';       // Objetivos
$csj1       = $data['csj1'] ?? '';        // Campo personalizado 1
$csj2       = $data['csj2'] ?? '';        // Campo personalizado 2
$csj3       = $data['csj3'] ?? '';        // Campo personalizado 3

// Validación: asegura que todos los campos requeridos estén completos
if (
    !$user_id ||
    empty($tittle) || empty($difficulty) || empty($position) ||
    empty($goals) || empty($csj1) || empty($csj2) || empty($csj3)
) {
    echo json_encode(['success' => false, 'message' => 'Campos incompletos']);
    exit;
}

// Prepara la consulta SQL para insertar un nuevo informe
$stmt = $conn->prepare("
    INSERT INTO informes (user_id, tittle, difficulty, position, goals, csj1, csj2, csj3)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

// Enlaza los parámetros con sus tipos (i = entero, s = string)
$stmt->bind_param("isssssss", $user_id, $tittle, $difficulty, $position, $goals, $csj1, $csj2, $csj3);

// Ejecuta la consulta y devuelve la respuesta
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Informe creado con exito']);
} else {
    // ⚠️ Aquí hay dos respuestas seguidas. Solo una debería enviarse.
    echo json_encode(['success' => false, 'message' => 'Campos incompletos']);
    echo json_encode(['success' => false, 'message' => 'Error al crear informe']);
}

// Cierra la sentencia y la conexión
$stmt->close();
$conn->close();
?>