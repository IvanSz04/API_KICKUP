<?php
// Establece el encabezado para que la respuesta sea en formato JSON
header('Content-Type: application/json');

// Incluye el archivo de conexión a la base de datos (se espera que defina $conn)
include 'db.php';

// Lee y decodifica los datos enviados en formato JSON (cuerpo de la solicitud)
$input = json_decode(file_get_contents('php://input'), true);

// Obtiene el valor del campo 'query' si existe, o asigna una cadena vacía
$query = isset($input['query']) ? $input['query'] : '';

// Prepara la consulta SQL para buscar informes cuyo título coincida parcialmente con el texto ingresado
$sql = "SELECT * FROM informes WHERE tittle LIKE ?";
$stmt = $conn->prepare($sql);

// Usa comodines % para la búsqueda parcial (ej. "%fútbol%" buscará cualquier título que contenga "fútbol")
$searchTerm = "%$query%";
$stmt->bind_param("s", $searchTerm);

// Ejecuta la consulta
$stmt->execute();

// Obtiene el resultado
$result = $stmt->get_result();

// Inicializa un arreglo para almacenar los resultados
$data = [];

// Itera sobre los resultados y los guarda como arreglos asociativos
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Comprueba si se encontraron resultados y responde en formato JSON
if ($result->num_rows > 0) {
    // (Nota: este segundo while es innecesario porque ya se recorrieron todos los resultados arriba)
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
} else {
    // Si no se encontraron resultados, devuelve una lista vacía
    echo json_encode([
        "success" => true,
        "data" => []
    ]);
}

// Cierra la conexión a la base de datos
$conn->close();
?>