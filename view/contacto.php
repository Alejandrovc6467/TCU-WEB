
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

                <form id="enviarMensajeContacto">
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
                            <textarea class="form-control" placeholder="" id="mensaje" name="mensaje" aria-label="mensaje" aria-describedby="basic-addon1" maxlength="255" required></textarea>
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




<script>

// Evento de submit del formulario
document.getElementById('enviarMensajeContacto')
    .addEventListener('submit', function (event) {
        event.preventDefault();
   
    enviarMensaje();
        
});


function enviarMensaje(){
    
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const mensaje = document.getElementById("mensaje").value.trim();


    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = true;

    var form_data = {
        nombre: nombre,
        apellido: apellido,
        correo: correo,
        telefono: telefono,
        mensaje: mensaje
    };

    console.log(form_data);

    
    $.ajax({
        type: "POST",
        url: "view/enviarEmailContacto.php",
        data: form_data,
        dataType: "json",
        success: function (response) {

            console.log(response);

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = false;

            // Verificar si la respuesta es exitosa
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: 'Nos pondremos en contacto contigo pronto.',
                    confirmButtonColor: '#088cff'
                });


               //limpiar campos del formulario
                document.getElementById("nombre").value = '';
                document.getElementById("apellido").value = '';
                document.getElementById("correo").value = '';
                document.getElementById("telefono").value = '';
                document.getElementById("mensaje").value = '';


            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message || 'Ocurrió un error, intenta nuevamente.',
                    confirmButtonColor: '#088cff'
                });
            }

            
        },
        error: function (xhr, status, error) {

            console.log(xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo conectar con el servidor. Por favor, intenta más tarde.',
                confirmButtonColor: '#088cff'
            });

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = false;
        }
    });
    
}

</script>






<?php
include('public/footer.php');
?>