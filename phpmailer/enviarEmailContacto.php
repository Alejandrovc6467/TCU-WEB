<?php
ob_start(); // Start output buffering
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Capture form data
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

// Basic validation
$response = ['status' => 'error', 'message' => 'Por favor complete todos los campos requeridos.'];

if(!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($mensaje)) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alejandrovc177@gmail.com';
        $mail->Password = 'izcb kowq obmi iuuu';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('alejandrovc177@gmail.com');
        $mail->addAddress('alejandrovc6467@gmail.com');
        $mail->isHTML(true);
        $mail->Subject = 'Sitio web de TCU TC-768';

        $mail->Body = '
        <html>
        <head>
            <title>Alguien quiere obtener más información</title>
        </head>
        <body>
            <h2>Detalles del mensaje:</h2>
            <p><strong>Nombre:</strong> ' . $nombre . '</p>
            <p><strong>Apellidos:</strong> ' . $apellido . '</p>
            <p><strong>Correo:</strong> ' . $correo . '</p>
            <p><strong>Teléfono:</strong> ' . $telefono . '</p>
            <p><strong>Mensaje:</strong></p>
            <p>' . $mensaje . '</p>
        </body>
        </html>';

        $mail->send();
        $response = ['status' => 'success', 'message' => 'Prueba exitosa'];

    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo];
    }
}

$json_response = json_encode($response);
ob_end_clean(); // Clear output buffer
echo $json_response;
exit;
?>