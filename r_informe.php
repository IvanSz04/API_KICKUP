<?php
header('Content-Type: application/json');
include 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$query = isset($input['query']) ? $input['query'] : '';

$sql = "SELECT * FROM informes WHERE tittle LIKE ?";
$stmt = $conn->prepare($sql);

$searchTerm = "%$query%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);

$conn->close();
?>