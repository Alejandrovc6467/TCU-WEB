
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
      <li class="navbar-item"><a href="?controlador=Actividades&accion=mostrar">Actividades</a></li>
      <li class="navbar-item"><a href="?controlador=Proyectos&accion=mostrar">Proyectos</a></li>
      
      <?php if ($_SESSION['rol'] == 'admin'): ?>
        <li class="navbar-item"><a href="?controlador=Usuarios&accion=mostrar">Usuarios</a></li>
      <?php endif; ?>
      
      <li class="navbar-item"><a href="?controlador=Index&accion=cerrarSesion">Cerrar Sesión</a></li>
    <?php endif; ?>
  </div>
  <div class="navbar-hamburger" id="navbar-hamburger">
    <div></div>
    <div></div>
    <div></div>
  </div>
</nav>




<script>
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