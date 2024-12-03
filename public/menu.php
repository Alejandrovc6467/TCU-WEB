<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>


<nav class="navbar" id="navbar">
  <div class="navbar-logo">
    <img src="public/assets/logo.webp" alt="Logo">
  </div>
  <div class="navbar-links" id="navbar-links">

    <li class="navbar-item"><a href="?controlador=Index&accion=mostrar">Inicio</a></li>
    <li class="navbar-item"><a href="?controlador=Index&accion=mostrarSobreNosotros">Sobre nosotros</a></li>
    <li class="navbar-item"><a href="?controlador=Index&accion=mostarContacto">Contacto</a></li>
    
    <?php if (!isset($_SESSION['rol'])): ?>
      <li class="navbar-item"><a href="?controlador=Index&accion=mostrarlogin">Login</a></li>
    <?php else: ?>

      <li class="navbar-item">
        <div class="container_configuracion">
          <div class="dropdown">
            <button type="button" class="btn_configuracion" id="dropdownMenuButton"> <i class="bi bi-person-circle"></i> </button>
            <div class="menu_configuraciones" aria-labelledby="dropdownMenuButton">
              <div class="infoMenu_container">
                <i class="bi bi-person-circle"></i>
                <?php
                    $nombre = $_SESSION['nombre'];
                    $correo = $_SESSION['correo'];
                    $rol = ucfirst($_SESSION['rol']);
                    echo "<p class='nombre_infoMenu'>$nombre</p>";
                    echo "<p  class='correo_infoMenu'>$correo</p>";
                ?>
              </div>

              <hr>

              <div class="menu_opciones">

                <a href="?controlador=Actividades&accion=mostrar"> • Actividades</a>
                <a href="?controlador=Proyectos&accion=mostrar"> • Proyectos</a>
                
                <?php if ($_SESSION['rol'] == 'admin'): ?>
                  <a href="?controlador=Usuarios&accion=mostrar"> • Usuarios</a>
                <?php endif; ?>
              </div>

              <hr>
               
              <button class="btnCerrarSesion" onclick="cerrarSesion()"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
            </div>
          </div>
        </div>
      </li>

    <?php endif; ?>

  </div>
  <div class="navbar-hamburger" id="navbar-hamburger">
    <div></div>
    <div></div>
    <div></div>
  </div>
</nav>



<script>
  //ajustes del menu hamburguesa
  document.addEventListener('DOMContentLoaded', function() {

    const navbar = document.getElementById('navbar');
    const navbarLinks = document.getElementById('navbar-links');
    const navbarHamburger = document.getElementById('navbar-hamburger');

    // Toggle navbar on mobile
    navbarHamburger.addEventListener('click', function() {
      navbar.classList.toggle('active-navbar');
    });

    // Close navbar when a link is clicked
    navbarLinks.addEventListener('click', function(event) {
      if (event.target.tagName === 'A') {
        navbar.classList.remove('active-navbar');
      }
    });
        
  });
</script>


<script>
  //abrir/cerrar ventana de configuraciones por medio de la posicion del mouse
  document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const menu_configuraciones = document.querySelector('.menu_configuraciones');

    dropdownButton.addEventListener('mouseenter', function() {
      menu_configuraciones.style.display = 'block';
    });

    document.querySelector('.dropdown').addEventListener('mouseleave', function() {
      menu_configuraciones.style.display = 'none';
    });
  });

  function cerrarSesion() {
      window.location.href = "?controlador=Index&accion=cerrarSesion";
  }
</script>