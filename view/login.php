<?php
  include('public/header.php');
?>


<div class="login">

  <div class="login_form">



	<div class="alerta">
        <?php if (isset($vars['mensaje'])) : ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: "Error",
                        text: "<?php echo $vars['mensaje']; ?>",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
            </script>
        <?php endif; ?>
  </div>

  <h3>Ingresa tus datos</h3>


  <form method="post" action="?controlador=Index&accion=login">
    <div class="txt_field">
      <input name="correo" id="correo" type="email" required>
      <span></span>
      <label>Correo</label>
    </div>
    <div class="txt_field">
      <input name="contrasena" id="contrasena" type="password">
      <span></span>
      <label>Contraseña</label>
    </div>
    
    <input type="submit" value="Ingresar">
    <div class="signup_link"> Comunícate con el profesor si tienes algún problema </div>
  </form>

  </div>

</div>



<?php
  include('public/footer.php');
?>