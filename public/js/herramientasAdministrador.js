// Variables globales para controlar el modo de edición, el tipo de archivos y si se están actualizando archivos
let isEditMode = false;
let editHerramientaId = null;
let updateArchivos = false;
let tipoArchivoSeleccionado = null;


// Evento de submit del formulario
document.getElementById('agregarHerramienta')
    .addEventListener('submit', function (event) {
        event.preventDefault();

        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            if (updateArchivos) {
                actualizarHerramientaConNuevosArchivos();
            } else {
                actualizarHerramientaSinNuevosArchivos();
            }
        } else {
            agregarHerramienta();
        }
    });

// Evento para detectar cambios en el input
document.getElementById("archivosHerramienta")
    .addEventListener("change", function (event) {

        if (isEditMode) {
            updateArchivos = true;
        } else {
            updateArchivos = false;
        }

        var archivos = event.target.files; // Obtener el archivo seleccionado
        mostrarVistaPreviaDesdeInput(archivos); // Llamar a la función con el archivo
    });


/*** CRUD de herramientas ***********************************************************************************/

// Función para solicitar las herramientas y mostrarlas en la vista
function obtenerHerramientas() {
    $.ajax({
        type: "POST",
        url: "?controlador=Herramientas&accion=obtenerHerramientas",
        dataType: "json",
        success: function (response) {
            // Limpiar la tabla antes de agregar nuevos datos
            $("#containertabla tbody").empty();

            // Recorrer la respuesta y agregar los datos a la tabla
            $.each(response, function (index, herramienta) {
                var row = $("<tr>");

                // Columna de acciones
                var accionesCell = $("<td>");
                var editarBtn = $("<button>")
                    .html('<span class="material-icons">edit_document</span> ')
                    .addClass("butonEditar")
                    .data("id", herramienta.id)
                    .data("nombre", herramienta.nombre)
                    .data("descripcion", herramienta.descripcion)
                    .data("url_archivos", herramienta.archivos.map(archivo => archivo.url))
                    .data("tipo", herramienta.tipo)
                    .on("click", function () {
                        editarHerramienta(
                            $(this).data("id"),
                            $(this).data("nombre"),
                            $(this).data("descripcion"),
                            $(this).data("url_archivos"),
                            $(this).data("tipo")
                        );
                    });

                var eliminarBtn = $("<button>")
                    .html('<span class="material-icons">cancel</span> ')
                    .addClass("butonDelete")
                    .data("id", herramienta.id)
                    .on("click", function () {
                        eliminarHerramienta($(this).data("id"));
                    });

                accionesCell.append(editarBtn).append(eliminarBtn);
                row.append(accionesCell);

                // Agregar nombre y descripción
                row.append($("<td>").text(herramienta.nombre));
                row.append($("<td>").text(herramienta.descripcion));

                // Contenedor para archivos multimedia
                var multimediaCell = $("<td>");

                // Procesar los archivos dependiendo del tipo (imagen o video)
                if (herramienta.tipo === 'imagen') {
                    // Para imágenes, mostrar miniaturas
                    herramienta.archivos.forEach(archivo => {
                        var img = $("<img>")
                            .attr("src", archivo.url)
                            .attr("alt", "Vista previa de la imagen")
                            .css({
                                "max-width": "90px",
                                "height": "auto",
                                "max-height": "120px",
                            })
                            .addClass("img-thumbnail");
                        multimediaCell.append(img);
                    });
                } else if (herramienta.tipo === 'video') {
                    // Para videos, mostrar miniaturas que no se puedan reproducir
                    herramienta.archivos.forEach(archivo => {
                        var videoThumb = $("<div>")
                            .css({
                                "width": "50px",
                                "height": "60px",
                                "background-image": "url('./public/assets/logo_archivo_video.png')",
                                "background-size": "cover",
                                "display": "inline-block",
                                "margin": "5px",
                                "position": "relative"
                            });


                        multimediaCell.append(videoThumb);
                    });
                }

                row.append(multimediaCell);
                $("#containertabla tbody").append(row);
            });
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
            mostrarMensaje('error', 'Error', 'No se pudieron cargar las herramientas');
        }
    });
}

// Función para eliminar una herramienta
function eliminarHerramienta(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción marcará la herramienta y sus archivos como eliminados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "?controlador=Herramientas&accion=eliminarHerramienta",
                data: { id: id },
                dataType: "json",
                success: function (response) {
                    mostrarMensaje('success', 'Eliminado', response.mensaje);
                    obtenerHerramientas(); // Recargar la tabla después de eliminar
                },
                error: function (xhr, status, error) {
                    console.log(error, xhr, status);
                    mostrarMensaje('error', 'Error', 'No se pudo eliminar la herramienta');
                }
            });
        }
    });
}

// Función para agregar herramientas nuevas
function agregarHerramienta() {

    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const archivos = document.getElementById("archivosHerramienta").files;

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("nombre", nombre);
    form_data.append("descripcion", descripcion);
    form_data.append("tipo", tipoArchivoSeleccionado);

    // Agregar cada archivo individualmente
    for (let i = 0; i < archivos.length; i++) {
        form_data.append("archivos[]", archivos[i]);
    }

    // Enviar la solicitud AJAX para agregar la herramienta
    $.ajax({
        url: "?controlador=Herramientas&accion=agregarHerramienta",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === "Se agregó la herramienta correctamente.") {

                limpiarCamposFormulario();
                mostrarMensaje('success', '¡Genial!', response[0]["mensaje"]);

            } else {

                mostrarMensaje('error', 'Oops...', response[0]["mensaje"]);
            }

            obtenerHerramientas(); // Recargar la tabla

        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;
            mostrarMensaje('error', 'Error', 'Error al procesar la solicitud');
        }
    });
}

// Funcion para actualizar la herramienta sin subir nuevos archivos
function actualizarHerramientaSinNuevosArchivos() {

    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editHerramientaId);
    form_data.append("nombre", nombre);
    form_data.append("descripcion", descripcion);

    $.ajax({
        url: "?controlador=Herramientas&accion=actualizarHerramientaSinNuevosArchivos",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            // Habilitar el botón
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === 'Actualización exitosa.') {
                limpiarCamposFormulario();
                mostrarMensaje('success', '¡Genial!', response[0]["mensaje"]);
                // Salir del modo de edición
                cancelEdit();
            } else {
                mostrarMensaje('error', 'Oops...', response[0]["mensaje"]);
            }

            obtenerHerramientas();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // Habilitar el botón en caso de error
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;
            mostrarMensaje('error', 'Error', 'Error al procesar la solicitud');
        }
    });
}

// Funcion para actualizar la herramienta y subir nuevos archivos
function actualizarHerramientaConNuevosArchivos() {

    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const archivos = document.getElementById("archivosHerramienta").files;

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editHerramientaId);
    form_data.append("nombre", nombre);
    form_data.append("descripcion", descripcion);

    // Agregar cada archivo individualmente si hay nuevos
    for (let i = 0; i < archivos.length; i++) {
        form_data.append("archivos[]", archivos[i]);
    }

    $.ajax({
        url: "?controlador=Herramientas&accion=actualizarHerramientaConNuevosArchivos",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            // Habilitar el botón
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === 'Actualización exitosa.') {
                limpiarCamposFormulario();
                mostrarMensaje('success', '¡Genial!', response[0]["mensaje"]);
                // Salir del modo de edición
                cancelEdit();
            } else {
                mostrarMensaje('error', 'Oops...', response[0]["mensaje"]);
            }

            obtenerHerramientas();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // Habilitar el botón en caso de error
            document.getElementById('agregarHerramienta').querySelector('button[type="submit"]').disabled = false;
            mostrarMensaje('error', 'Error', 'Error al procesar la solicitud');
        }
    });
}

/***  Funciones complementarias  *********************************************************/

// Método para manejar el cambio de selección de tipo de archivo
document.querySelectorAll('input[name="tipoArchivo"]').forEach((radio) => {
    radio.addEventListener('change', handleTipoArchivoChange);
});

function handleTipoArchivoChange(event) {

    tipoArchivoSeleccionado = event.target.value;

    // Limpiar la vista previa anterior y el input de archivos, para evitar conflictos
    var vistaPrevia = document.getElementById("vistaPrevia");
    vistaPrevia.innerHTML = "";
    document.getElementById('archivosHerramienta').value = '';

    // Manejo de cambios en la UI dependiendo del tipo
    if (tipoArchivoSeleccionado === 'imagen') {
        document.getElementById('archivosHerramienta').accept = 'image/*';
        document.getElementById('archivosHerramienta').multiple = true;
    } else if (tipoArchivoSeleccionado === 'video') {
        document.getElementById('archivosHerramienta').accept = 'video/*';
        document.getElementById('archivosHerramienta').multiple = false;
    }
}

// Ejecutar una vez al cargar para aplicar el tipo por defecto
handleTipoArchivoChange({ target: document.querySelector('input[name="tipoArchivo"]:checked') });

// Función para cargar los datos a editar al formulario
function editarHerramienta(id, nombre, descripcion, url_archivos, tipo) {
    // Enter edit mode
    isEditMode = true;
    editHerramientaId = id;

    // Eliminar el botón de cancel en caso de que ya exista uno, para agregar el nuevo
    const cancelEditButtonAnterior = document.getElementById('cancelEditButton');
    if (cancelEditButtonAnterior) {
        cancelEditButtonAnterior.remove();
    }

    // Change button text and style
    const submitButton = document.getElementById('buttonRegistrarHerramienta');
    submitButton.textContent = 'Actualizar Herramienta';
    submitButton.classList.remove('btn-primary');
    submitButton.classList.add('btn-warning');

    // Create and add cancel button
    const cancelButton = document.createElement('button');
    cancelButton.textContent = 'Cancelar';
    cancelButton.type = 'button';
    cancelButton.classList.add('botonCancelar');
    cancelButton.id = 'cancelEditButton';
    cancelButton.addEventListener('click', cancelEdit);

    // Insert cancel button next to submit button
    submitButton.parentNode.insertBefore(cancelButton, submitButton.nextSibling);

    // Fill form fields
    document.getElementById('nombre').value = nombre;
    document.getElementById('descripcion').value = descripcion;
    document.getElementById('archivosHerramienta').required = false;

    // Seleccionar el radio según el tipo recibido y cambiar el tipo de archivo aceptados en el input archivosHerramienta
    if (tipo === 'imagen') {
        document.getElementById('radioImagenes').checked = true;
        document.getElementById('archivosHerramienta').accept = 'image/*';
    } else {
        document.getElementById('radioVideo').checked = true;
        document.getElementById('archivosHerramienta').accept = 'video/*';
    }

    // Desactivar los radios de tipo de archivo
    document.getElementById('radioImagenes').disabled = true;
    document.getElementById('radioVideo').disabled = true;


    // Mostrar vista previa según el tipo de archivo
    mostrarVistaPreviaDesdeURL(url_archivos, tipo);
}

// Función para cancelar la edición
function cancelEdit() {
    // Reset form
    limpiarCamposFormulario();

    // Exit edit mode
    isEditMode = false;
    editHerramientaId = null;

    // Restore button
    const submitButton = document.getElementById('buttonRegistrarHerramienta');
    submitButton.textContent = 'Agregar Herramienta';
    submitButton.classList.remove('btn-warning');
    submitButton.classList.add('btn-primary');

    // Remover cancel button
    const cancelButton = document.getElementById('cancelEditButton');
    if (cancelButton) {
        cancelButton.remove();
    }

    // Activar los radios de tipo de archivo
    document.getElementById('radioImagenes').disabled = false;
    document.getElementById('radioVideo').disabled = false;

    document.getElementById('archivosHerramienta').required = true;
}

// Función para limpiar campos del formulario
const limpiarCamposFormulario = () => {
    document.getElementById('nombre').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('archivosHerramienta').value = '';
    document.getElementById("vistaPrevia").innerHTML = '';
};

// Función auxiliar para cargar una vista previa de los archivos para una nueva herramienta
function mostrarVistaPreviaDesdeInput(archivos) {

    var vistaPrevia = document.getElementById("vistaPrevia");
    vistaPrevia.innerHTML = "";

    if (archivos["length"] > 0) {
        // Determinar el tipo del primer archivo
        const primerArchivo = archivos[0];
        const esVideo = primerArchivo.type.startsWith('video/');

        for (let i = 0; i < archivos["length"]; i++) {
            const archivo = archivos[i];
            var reader = new FileReader();

            reader.onload = function (e) {
                if (archivo.type.startsWith('image/')) {
                    // Mostrar vista previa de imagen
                    var img = document.createElement("img");
                    img.src = e.target.result;
                    img.alt = "Vista previa de la imagen";
                    img.style.maxWidth = "200px";
                    img.style.height = "auto";
                    img.className = "img-thumbnail";
                    vistaPrevia.appendChild(img);
                } else if (archivo.type.startsWith('video/')) {
                    // Mostrar vista previa de video
                    var videoContainer = document.createElement("div");
                    videoContainer.className = "video-preview";
                    videoContainer.style.display = "inline-block";
                    videoContainer.style.margin = "5px";

                    var video = document.createElement("video");
                    video.src = e.target.result;
                    video.controls = true;
                    video.muted = true;
                    video.style.maxWidth = "200px";
                    video.style.height = "auto";

                    videoContainer.appendChild(video);
                    vistaPrevia.appendChild(videoContainer);
                }
            };

            reader.readAsDataURL(archivo);
        }
    } else {
        vistaPrevia.innerHTML = "<p class='text-danger'>Selecciona archivos válidos.</p>";
    }
}

// Función auxiliar para cargar una vista previa de los archivos para editar
function mostrarVistaPreviaDesdeURL(url_archivos, tipo) {
    var vistaPrevia = document.getElementById("vistaPrevia");
    vistaPrevia.innerHTML = "";

    if (tipo === 'imagen') {
        // Mostrar imágenes
        url_archivos.forEach(url => {
            if (url.trim() !== "") {
                var img = document.createElement("img");
                img.src = url;
                img.alt = "Vista previa de la imagen";
                img.style.maxWidth = "200px";
                img.style.height = "auto";
                img.className = "img-thumbnail";
                vistaPrevia.appendChild(img);
            }
        });
    } else if (tipo === 'video') {
        // Mostrar videos
        url_archivos.forEach(url => {
            if (url.trim() !== "") {
                var videoContainer = document.createElement("div");
                videoContainer.className = "video-preview";
                videoContainer.style.display = "inline-block";
                videoContainer.style.margin = "5px";

                var video = document.createElement("video");
                video.src = url;
                video.controls = true;
                video.muted = true;
                video.style.maxWidth = "200px";
                video.style.height = "auto";

                videoContainer.appendChild(video);
                vistaPrevia.appendChild(video);
            }
        });
    }

    if (vistaPrevia.children.length === 0) {
        vistaPrevia.innerHTML = "<p class='text-danger'>No hay archivos disponibles.</p>";
    }
}

//Funcion axiliar para mostrar un mensajes
const mostrarMensaje = (icon, title, text) => {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#088cff'
    });
}

// Llamar a la función para cargar las herramientas al cargar la página
obtenerHerramientas();