<?php
include('public/header.php');
?>



<div class="usuarios_container">

  <h1 class="usuarios_title">Usuarios</h1>


  <div class="formulario_container">

    <form id="agregarUsuario">

     
      <div class="mb-3">
          <label for="basic-url" class="form-label">Ingresa el nombre:</label>
          <div class="input-group">
              <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill"  style="color: #a3a3a3;"></i></span>
              <input type="text" class="form-control" placeholder="" id="nombre" aria-label="nombre" aria-describedby="basic-addon1" required>
          </div>
      </div>


      <div class="mb-3">
          <label for="basic-url" class="form-label">Ingresa el correo:</label>
          <div class="input-group">
              <span class="input-group-text" id="basic-addon1"> <i class="bi bi-envelope-fill"  style="color: #a3a3a3;"></i> </span>
              <input type="email" class="form-control" placeholder="" id="correo" aria-label="correo" aria-describedby="basic-addon1" required>
          </div>
      </div>

      <div class="mb-3">
          <label for="basic-url" class="form-label">Ingresa la contraseña:</label>
          <div class="input-group">
              <span class="input-group-text" id="basic-addon1"><i class="bi bi-file-earmark-lock-fill" style="color: #a3a3a3;"></i></span>
              <input type="text" class="form-control" placeholder="" id="contrasena" aria-label="contrasena" aria-describedby="basic-addon1" required>
          </div>
      </div>


      <div class="conatainerBotonFormularioModal">
          <button type="submit" value="Registrar" id="buttonRegistrarUsuario" class="butonAgregar">Agregar usuario</button>
      </div>


    </form>

  </div>

  
  <div class="header_fixed" id="containertabla">
    <table>
      <thead>
        <tr>
          <th>Acciones</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Contraseña</th>
        </tr>
      </thead>
      <tbody>
        <!-- Aquí se llenarán los datos de los usuarios -->
      </tbody>
    </table>
  </div>


  
</div>




<script src="public/js/Usuarios.js?14"></script>



<?php
include('public/footer.php');
?>