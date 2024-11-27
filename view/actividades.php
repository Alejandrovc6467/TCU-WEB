<?php
include('public/header.php');
?>



<h1>Actividades</h1>


<?php

if (isset($_SESSION['rol'])) { // Comprobar si la variable de sesión existe

   
  $nombre =  strtoupper($_SESSION['nombre']);
  $correo = $_SESSION['correo'];
  $rol = $_SESSION['rol'];

  echo "<h5 class=\"username\"><i class=\"fa-solid fa-circle-user\"></i> $nombre</h5>";
  echo "<p class=\"useremail\">$correo</p>";
  echo "<p class=\"useremail\">$rol</p>";
  
}

?>







<?php
include('public/footer.php');
?>