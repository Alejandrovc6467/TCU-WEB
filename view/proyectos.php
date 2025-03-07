<?php
include('public/header.php');
?>

<link rel="stylesheet" href="public/css/proyectos.css">

<!-- Contenido principal de la vista para ingresar proyectos -->
<div class="proyectos_container">

    <h1 class="proyectos_title">Proyectos</h1>

    <!-- Formulario par ingresar proyectos -->
    <div class="formulario_container">

        <form id="agregarProyecto">

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingrese el nombre de la proyecto:</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-info-circle-fill"
                            style="color: #a3a3a3;"></i></span>
                    <input type="text" class="form-control" placeholder="" id="nombre" aria-label="nombre"
                        aria-describedby="basic-addon1" maxlength="255" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingrese la descripción de la proyecto:</label>
                <div class="input-group">
                    <label class="input-group-text" for="inputGroupSelect01"><i class="bi bi-info-circle-fill"
                            style="color: #a3a3a3;"></i></label>
                    <textarea class="form-control" placeholder="" id="descripcion" aria-label="descripcion"
                        aria-describedby="basic-addon1" maxlength="255" required></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingrese imagenes para el proyecto:</label>
                <div class="input-group">
                    <label class="input-group-text" for="imagenProyecto">
                        <i class="bi bi-file-earmark-arrow-up-fill" style="color: #a3a3a3;"></i>
                    </label>
                    <input class="form-control" type="file" id="imagenProyecto" name="imagenProyecto" required>
                </div>
                <!-- Contenedor para la vista previa -->
                <div id="vistaPrevia" class="mt-3">
                    <!-- Aquí se cargará la vista previa de la imagen -->
                </div>
            </div>

            <div class="conatainerBotonFormularioModal">
                <button type="submit" value="Registrar" id="buttonRegistrarProyecto" class="butonAgregar">Agregar
                    Proyecto</button>
            </div>

        </form>

    </div>

    <div class="header_fixed" id="containertabla">
        <table>
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Fecha De Modificacion</th>
                    <th>Imagen</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se llenarán los datos de las proyectos -->
            </tbody>
        </table>
    </div>

</div>

<script src="public/js/Proyectos.js?6"></script>

<?php
include('public/footer.php');
?>