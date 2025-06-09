<?php include('public/header.php'); ?>

<link rel="stylesheet" href="public/css/proyectos.css">

<div class="proyectos_container">
    <h1 class="proyectos_title">Proyectos</h1>

    <!-- Formulario para agregar/editar proyectos -->
    <div class="formulario_container">
        <form id="agregarProyecto" enctype="multipart/form-data">
            <!-- Nombre del proyecto -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del proyecto:</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-info-circle-fill" style="color: #a3a3a3;"></i>
                    </span>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" required>
                </div>
            </div>

            <!-- Descripción del proyecto -->
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del proyecto:</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-info-circle-fill" style="color: #a3a3a3;"></i>
                    </span>
                    <textarea class="form-control" id="descripcion" name="descripcion" maxlength="255"
                        required></textarea>
                </div>
            </div>

            <!-- Imágenes del proyecto -->
            <div class="mb-3">
                <label for="imagenesProyecto" class="form-label">Imágenes del proyecto:</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-file-earmark-arrow-up-fill" style="color: #a3a3a3;"></i>
                    </span>
                    <input type="file" class="form-control" id="imagenesProyecto" name="archivos[]" multiple required>
                </div>

                <!-- Vista previa de imágenes -->
                <div id="vistaPrevia" class="mt-3">
                    <!-- Aquí se mostrarán las vistas previas -->
                </div>
            </div>

            <!-- Botón principal -->
            <div class="conatainerBotonFormularioModal">
                <button type="submit" id="buttonRegistrarProyecto" class="butonAgregar">
                    Agregar Proyecto
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla para mostrar proyectos -->
    <div class="header_fixed" id="containertabla">
        <table>
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Última Modificación</th>
                    <th>Imágenes</th>
                </tr>
            </thead>
            <tbody>
                <!-- Se llena dinámicamente por JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript del módulo de proyectos -->
<script src="public/js/Proyectos.js?7"></script>

<?php include('public/footer.php'); ?>