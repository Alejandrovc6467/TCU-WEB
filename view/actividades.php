<?php
include('public/header.php');
?>

<link rel="stylesheet" href="public/css/actividades.css">

<!-- Contenido principal de la vista para ingresar actividades -->
<div class="actividades_container">

    <h1 class="actividades_title">Actividades</h1>

    <!-- Formulario par ingresar actividades -->
    <div class="formulario_container">

        <form method="POST" id="agregarActividad" action="?controlador=Tesina&accion=subirdocumento"
            enctype="multipart/form-data" accept=".pdf, .doc, .docx, .odt">

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingrese el de la actividad:</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-circle-info"
                            style="color: #a3a3a3;"></i></span>
                    <input type="text" class="form-control" placeholder="" id="nombre" aria-label="nombre"
                        aria-describedby="basic-addon1" maxlength="255" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa descripcion de la actividad:</label>
                <div class="input-group">
                    <label class="input-group-text" for="inputGroupSelect01"><i class="fa-solid fa-circle-info"
                            style="color: #a3a3a3;"></i></label>
                    <textarea class="form-control" placeholder="" id="descripcion" aria-label="descripcion"
                        aria-describedby="basic-addon1" maxlength="255" required></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="basic-url" class="form-label">Ingresa imagen de la actividad:</label>
                <div class="input-group">
                    <label class="input-group-text" for="inputGroupSelect01"><i class="fa-solid fa-file"
                            style="color: #a3a3a3;"></i></label>
                    <input class="form-control" type="file" id="archivo" name="archivo" required>
                </div>
            </div>

            <div class="conatainerBotonFormularioModal">
                <button type="submit" value="Registrar" id="buttonRegistrarActividad"
                    class="btn btn-primary butonAgregarForm"><i class="fa-solid fa-square-plus"
                        style="color: #ffffff;"></i> Agregar Actividad</button>
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
                    <th>Imagen</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se llenarán los datos de los usuarios -->
            </tbody>
        </table>
    </div>

</div>

<script src="public/js/Actividades.js?1"></script>

<?php
include('public/footer.php');
?>