
<?php
include('public/header.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

?>


<h1 class="contacto-title">Contacto</h1>
<p class="contacto-title-text">Contáctanos y algún integrante del equipo del TCU se pondrá en contacto contigo lo más pronto posible. ¡Esperamos poder ayudarte pronto!</p>

<div class="container-contacto">
    
    <div class="subcontainer-contacto">

        <div class="container-contacto-informacion">

            <h4>Contacta con el equipo</h4>
            <p>Estas listo para contactarnos y darle una mano?</p>


            <h4>Contáctanos al:</h4>
            <p>  <i class="bi bi-telephone"></i> 2511 9228</p>

            <h4>Ubicación:</h4>

            <iframe _ngcontent-ng-c2656257193="" 
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15721.56949926622!2d-83.6719512!3d9.9012413!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fa0d42417bc6851%3A0xd2ae13fcaa9ce072!2sUniversidad%20de%20Costa%20Rica%20(Sede%20del%20Atl%C3%A1ntico)!5e0!3m2!1ses-419!2scr!4v1706824347591!5m2!1ses-419!2scr" 
                width="100%" 
                height="33%" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade" 
                style="border: 0; border-radius: 10px;">
            </iframe>

            <p>Costa Rica/Cartago/Turrialba</p>


            <div class="footer__social-icons">
                <a href="https://www.facebook.com/profile.php?id=100082053260381" target="_blank"> <i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/tc768_gestionando_tu_empresa/" target="_blank"> <i class="bi bi-instagram"></i></a>
            </div>

        </div>

        <div class="container-contacto-form">


            <div class="formulario_container_contacto">

                <form id="enviarMensajeContacto" method="POST">
                    <div class="box-input-doble">
                        <div class="mb-3 inputFormSingle">
                            <label for="basic-url" class="form-label">Nombre:</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill" style="color: #a3a3a3;"></i></span>
                                <input type="text" class="form-control" placeholder="" id="nombre" name="nombre" aria-label="nombre" aria-describedby="basic-addon1" required>
                            </div>
                        </div>
                        <div class="mb-3 inputFormSingle">
                            <label for="basic-url" class="form-label">Apellido:</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill" style="color: #a3a3a3;"></i></span>
                                <input type="text" class="form-control" placeholder="" id="apellido" name="apellido" aria-label="apellido" aria-describedby="basic-addon1" required>
                            </div>
                        </div>
                    </div>
                    <div class="box-input-doble">
                        <div class="mb-3 inputFormSingle">
                            <label for="basic-url" class="form-label">Correo:</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope-fill" style="color: #a3a3a3;"></i></span>
                                <input type="email" class="form-control" placeholder="" id="correo" name="correo" aria-label="correo" aria-describedby="basic-addon1" required>
                            </div>
                        </div>
                        <div class="mb-3 inputFormSingle">
                            <label for="basic-url" class="form-label">Teléfono:</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="bi bi-telephone-fill" style="color: #a3a3a3;"></i></span>
                                <input type="text" class="form-control" placeholder="" id="telefono" name="telefono" aria-label="telefono" aria-describedby="basic-addon1" maxlength="255" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="basic-url" class="form-label">Mensaje:</label>
                        <div class="input-group">
                            <label class="input-group-text"><i class="bi bi-chat-left-text-fill" style="color: #a3a3a3;"></i></label>
                            <textarea class="form-control" placeholder="" id="descripcion" name="descripcion" aria-label="descripcion" aria-describedby="basic-addon1" maxlength="255" required></textarea>
                        </div>
                    </div>
                    <div class="conatainerBotonFormularioModal">
                        <button type="submit" name="submit_contact" value="Contactanos" id="buttonEnviarMensajeContacto" class="butonContactanos">Contáctanos</button>
                    </div>
                </form>


            </div>

        </div>

    </div>

</div>







<?php

if(isset($_POST['submit_contact'])) {
    // Capture form data
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $mensaje = $_POST['descripcion'] ?? '';

    // Basic validation
    if(empty($nombre) || empty($apellido) || empty($correo) || empty($mensaje)) {
        echo "<script>alert('Por favor complete todos los campos requeridos.');</script>";
    } else {


        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alejandrovc177@gmail.com';
        $mail->Password = 'izcb kowq obmi omai';
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

    }
}

?>



<?php
include('public/footer.php');
?>