<?php

use PHPMailer\PHPMailer\PHPMailer;

//variables para la conexión
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

// Ahora puedes acceder a las variables de entorno
$servername = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'];

$mail_host = $_ENV['MAIL_HOST'];
$mail_port = $_ENV['MAIL_PORT'];
$mail_username = $_ENV['MAIL_USERNAME'];
$mail_pass = $_ENV['MAIL_PASSWORD'];
$mail_encryption = $_ENV['MAIL_ENCRYPTION'];
$mail_from = $_ENV['MAIL_FROM_ADDRESS'];


// Asume la existencia de variables para conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger los datos del formulario y limpiarlos
$n_documento = $_POST['n_documento'];
$nombres_solicitante = $_POST['nombres_solicitante'];
$apellidos_solicitante = $_POST['apellidos_solicitante'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$tipo_intencion = $_POST['tipo_intencion'];
$nombre_intencion = $_POST['nombre_intencion'];
$parroquia = $_POST['parroquia'];
$fecha_fallecimiento = $_POST['fecha_fallecimiento'];

$stmt = $conn->prepare("INSERT INTO solicitudes (n_documento, nombres_solicitante, apellidos_solicitante, telefono, email, tipo_intencion, nombre_intencion, parroquia, fecha_fallecimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $n_documento, $nombres_solicitante, $apellidos_solicitante, $telefono, $email, $tipo_intencion, $nombre_intencion
, $parroquia, $fecha_fallecimiento);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Nuevo registro creado exitosamente.";
    // Preparar el correo electrónico para enviar
    require_once '../vendor/autoload.php'; // Asegúrate de ajustar la ruta según tu estructura de directorios

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $mail_host;  // Especifica tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = $mail_username;  // Tu email SMTP
    $mail->Password = $mail_pass;  // Tu contraseña SMTP
    $mail->SMTPSecure = $mail_encryption;
    $mail->Port = $mail_port;

    $mail->setFrom($mail_from, 'Intenciones');
    $mail->addAddress($email);     // Agregar al destinatario

    $mail->isHTML(true);  // Establecer el formato del email a HTML

    $mail->Subject = 'Intencion Recibida';
    $htmlContent = file_get_contents('../body-email.html'); // Asegúrate de que la ruta al archivo sea correcta.
    $mail->Body = $htmlContent;

    if (!$mail->send()) {
        echo 'El mensaje no pudo ser enviado.';
        echo 'Error de correo: ' . $mail->ErrorInfo;
    } else {
        // Redireccionar a la página de confirmación
        header('Location: ../confirmacion.html');
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
