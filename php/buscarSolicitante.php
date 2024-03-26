<?php
//variables para la conexión
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

// Ahora puedes acceder a las variables de entorno
$servername = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'];// Asume la existencia de variables para conexión $servername, $username, $password, $dbname
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$rutPasaporte = $_POST['rutPasaporte'];

$stmt = $conn->prepare("SELECT nombres_solicitante, apellidos_solicitante, telefono, email FROM solicitudes WHERE n_documento = ?");
$stmt->bind_param("s", $rutPasaporte);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($nombres, $apellidos, $telefono, $email);
    $stmt->fetch();
    $response = ['encontrado' => true, 'nombres_solicitante' => $nombres, 'apellidos_solicitante' => $apellidos, 'telefono' => $telefono, 'email' => $email];
} else {
    $response = ['encontrado' => false];
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
