<?php
  include('public/header.php');
?>

<div class="noticiasAdmin-container">

  <h1 class="noticiasAdmin_title">Noticias</h1>
 

  <div class="formulario_container">

    <form id="agregarNoticia">

      <div class="mb-3">
        <label for="basic-url" class="form-label">Ingrese el nombre de la noticia:</label>
        <div class="input-group">
          <span class="input-group-text" id="basic-addon1"><i class="bi bi-info-circle-fill" style="color: #a3a3a3;"></i></span>
          <input type="text" class="form-control" placeholder="" id="nombre" aria-label="nombre" aria-describedby="basic-addon1" maxlength="255" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="basic-url" class="form-label">Ingrese la descripción de la noticia:</label>
        <div class="input-group">
          <label class="input-group-text" for="inputGroupSelect01"><i class="bi bi-info-circle-fill" style="color: #a3a3a3;"></i></label>
          <textarea class="form-control" placeholder="" id="descripcion" aria-label="descripcion" aria-describedby="basic-addon1" maxlength="255" required></textarea>
        </div>
      </div>


      <div class="mb-3">
        <label class="form-label">Seleccione el tipo de archivo:</label>
        <div class="d-flex gap-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipoArchivo" id="radioImagenes" value="imagen" checked>
            <label class="form-check-label" for="radioImagenes">Imágenes</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipoArchivo" id="radioVideo" value="video">
            <label class="form-check-label" for="radioVideo">Video</label>
          </div>
        </div>
      </div>

      <div class="mb-3">
            <label for="basic-url" class="form-label">Ingrese los archivos de la noticia:  <p class="formatos-permitodos-text">Formatos permitidos: png, jpg, jpeg, svg, webp, mp4, avi, mkv</p></label>
            <div class="input-group">
                <label class="input-group-text" for="archivosNoticia">
                    <i class="bi bi-file-earmark-arrow-up-fill" style="color: #a3a3a3;"></i>
                </label>
                <input class="form-control" type="file" id="archivosNoticia" name="archivosNoticia" multiple required>
            </div>
           
            <!-- Contenedor para la vista previa -->
            <div id="vistaPrevia" class="mt-3">
                <!-- Aquí se cargará la vista previa de la imagen -->
            </div>
      </div>

        <div class="conatainerBotonFormularioModal">
            <button type="submit" value="Registrar" id="buttonRegistrarNoticia" class="butonAgregar">Agregar
                Noticia</button>
        </div>

    </form>

  </div>

  <div class="header_fixed" id="containertabla">
    <table>
        <thead>
            <tr>
                <th>Acciones</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Archivos multimedia</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aquí se llenarán los datos de las proyectos -->
        </tbody>
    </table>
  </div>
  

</div>



<script src="public/js/noticias.js?7"></script>

<?php
include('public/footer.php');
?>