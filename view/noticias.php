<?php
  include('public/header.php');
?>

<div class="container-noticias">
  <h1 class="noticias-title">Noticias</h1>
  <p class="noticias-title-text">Informate de las noticias más recientes sobre nuestros proyectos y más.</p>

  <div class="subcontainer-noticias">
    <!-- Contenedor donde se cargarán dinámicamente las noticias -->
    <div class="noticias-items">
      <!-- El contenido se añadirá dinámicamente desde JavaScript -->
    </div>
  </div>
</div>

<script src="public/js/noticiasFrontEnd.js"></script>

<?php
include('public/footer.php');
?>