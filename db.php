<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "kickup";
$port = 3307; 

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>