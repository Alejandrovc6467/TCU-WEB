<?php
include('public/header.php');
?>


<h1 class="contacto-title">Contacto</h1>
<p class="contacto-title-text">Contáctanos y algún integrante del equipo del TCU se pondrá en contacto contigo lo más pronto posible. ¡Esperamos poder ayudarte pronto!</p>

<div class="container-contacto">
    
    <div class="Subcontainer-contacto">

    <div class="container-contacto-info">

        <h3>Contacta con el equipo</h3>
        <p>Estas listo para contactarnos y darle una mano?</p>


        <h3>Contáctanos al:</h3>
        <p>2511 9228</p>

        <h3>Ubicación</h3>

        <iframe _ngcontent-ng-c2656257193="" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15721.56949926622!2d-83.6719512!3d9.9012413!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fa0d42417bc6851%3A0xd2ae13fcaa9ce072!2sUniversidad%20de%20Costa%20Rica%20(Sede%20del%20Atl%C3%A1ntico)!5e0!3m2!1ses-419!2scr!4v1706824347591!5m2!1ses-419!2scr" width="100%" height="100%" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="border: 0;"></iframe>
        
        <p>Costa Rica/Cartago/Turrialba</p>


        <div class="footer__social-icons">
            <a href="https://www.facebook.com/profile.php?id=100082053260381" target="_blank"> <i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/tc768_gestionando_tu_empresa/" target="_blank"> <i class="bi bi-instagram"></i></a>
        </div>

    </div>

    <div class="container-contacto-form">


    <div class="formulario_container">

        <form id="agregarActividad">

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa el nombre de la actividad:</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-info-circle-fill"  style="color: #a3a3a3;"></i></span>
                    <input type="text" class="form-control" placeholder="" id="nombre" aria-label="nombre"
                        aria-describedby="basic-addon1" maxlength="255" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa la descripción de la actividad:</label>
                <div class="input-group">
                    <label class="input-group-text" for="inputGroupSelect01"><i class="bi bi-info-circle-fill"  style="color: #a3a3a3;"></i></label>
                    <textarea class="form-control" placeholder="" id="descripcion" aria-label="descripcion"
                        aria-describedby="basic-addon1" maxlength="255" required></textarea>
                </div>
            </div>

            <!--div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa imagen de la actividad:</label>
                <div class="input-group">
                    <label class="input-group-text" for="inputGroupSelect01"><i class="fa-solid fa-file"
                            style="color: #a3a3a3;"></i></label>
                    <input class="form-control" type="file" id="imagenActividad" name="imagenActividad" required>
                </div>
            </div-->

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa la imagen de la actividad:</label>
                <div class="input-group">
                    <label class="input-group-text" for="imagenActividad">
                        <i class="bi bi-file-earmark-arrow-up-fill"  style="color: #a3a3a3;"></i>
                    </label>
                    <input class="form-control" type="file" id="imagenActividad" name="imagenActividad" required>
                </div>
                <!-- Contenedor para la vista previa -->
                <div id="vistaPrevia" class="mt-3">
                    <!-- Aquí se cargará la vista previa de la imagen -->
                </div>
            </div>

            <div class="conatainerBotonFormularioModal">
                <button type="submit" value="Registrar" id="buttonRegistrarActividad"
                    class="butonAgregar">Agregar Actividad</button>
            </div>

        </form>

    </div>

    </div>

</div>



<?php
include('public/footer.php');
?>