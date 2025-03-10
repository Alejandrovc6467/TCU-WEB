
// Variables globales para controlar el modo de edición
let isEditMode = false;
let editProyectoId = null;


// Evento de submit del formulario
document.getElementById('agregarProyecto')
    .addEventListener('submit', function (event) {
        event.preventDefault();

        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            actualizarProyecto();
        } else {
            agregarProyecto();
        }
    });

// Evento para detectar cambios en el input
document.getElementById("imagenesProyecto")
    .addEventListener("change", function (event) {
        var archivos = event.target.files; // Obtener el archivo seleccionado
        mostrarVistaPreviaDesdeInput(archivos); // Llamar a la función con el archivo
    });

// funciona para solicitar las proyectos creadas y cargalas en la tabla
function obtenerProyectos() {
    $.ajax({
        type: "POST",
        url: "?controlador=Proyectos&accion=obtenerProyectos",
        dataType: "json",
        success: function (response) {

            // Limpiar la tabla antes de agregar nuevos datos
            $("#containertabla tbody").empty();

            // Recorrer la respuesta y agregar los datos a la tabla
            $.each(response, function (index, proyecto) {

                var row = $("<tr>");

                // Columna de acciones
                var accionesCell = $("<td>");
                var editarBtn = $("<button>")
                    .html('<span class="material-icons">edit_document</span> ')
                    .addClass("butonEditar")
                    .data("id", proyecto.id)
                    .data("nombre", proyecto.nombre)
                    .data("descripcion", proyecto.descripcion)
                    .data("url_archivos", proyecto.imagenes)
                    .on("click", function () {
                        editarActividad(
                            $(this).data("id"),
                            $(this).data("nombre"),
                            $(this).data("descripcion"),
                            $(this).data("url_archivos")
                        );
                    });

                var eliminarBtn = $("<button>")
                    .html('<span class="material-icons">cancel</span> ')
                    .addClass("butonDelete")
                    .data("id", proyecto.id)
                    .on("click", function () {
                        eliminarProyecto($(this).data("id"));
                    });

                accionesCell.append(editarBtn).append(eliminarBtn);
                row.append(accionesCell);

                row.append($("<td>").text(proyecto.nombre));
                row.append($("<td>").text(proyecto.descripcion));
                row.append($("<td>").text(proyecto.fecha));

                var imagenes = document.createElement("td");

                proyecto.imagenes.forEach(url_archivo => {
                    // Columna de Imagen: Se genera una etiqueta <img> con la URL
                    var img = document.createElement("img");
                    img.src = url_archivo; // Convertir archivo a base64
                    img.alt = "Vista previa de la imagen";
                    img.style.maxWidth = "90px";
                    img.style.height = "auto";
                    img.className = "img-thumbnail";
                    imagenes.appendChild(img);
                });

                row.append(imagenes);

                $("#containertabla tbody").append(row);
            });

        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
        }

    });
}

// funciona para agregar proyectos nuevas
function agregarProyecto() {
    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const imagenes = document.getElementById("imagenesProyecto").files;

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("nombre", nombre); // Agregar el nombre
    form_data.append("descripcion", descripcion); // Agregar la descripción

    // 📌 Agregar cada archivo individualmente
    for (let i = 0; i < imagenes.length; i++) {
        form_data.append("archivos[]", imagenes[i]);  // El nombre del input debe terminar en []
    }

    console.log([...form_data]); // Para verificar qué datos se están enviando

    $.ajax({
        url: "?controlador=Proyectos&accion=agregarProyecto",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false, // No procesar los datos automáticamente
        contentType: false, // Dejar que el navegador configure el tipo de contenido
        success: function (response) {

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === "Proyecto e imagenes guardadas con exito.") {
                limpiarCamposFormulario();

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });
            } else if (response[0]["mensaje"].includes("Ocurrió un error")) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrió un error, intenta nuevamente',
                    confirmButtonColor: '#088cff'
                });
            }

            obtenerProyectos(); // Recargar la tabla
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = false;
        }
    });
}

// Función para cargar los datos a editar al formulario
function editarActividad(id, nombre, descripcion, url_archivos) {
    // Enter edit mode
    isEditMode = true;
    editProyectoId = id;

    //Eliminar el boton de cancel en caso de que ya exista uno, para agregar el nuevo
    const cancelEditButtonAnterior = document.getElementById('cancelEditButton');
    if (cancelEditButtonAnterior) {
        cancelEditButtonAnterior.remove();
    }

    // Change button text and style
    const submitButton = document.getElementById('buttonRegistrarProyecto');
    submitButton.textContent = 'Actualizar Proyecto';
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
    document.getElementById('imagenesProyecto').required = false;
    mostrarVistaPreviaDesdeURL(url_archivos);
}

// función para cancelar la edición
function cancelEdit() {
    // Reset form
    limpiarCamposFormulario();

    // Exit edit mode
    isEditMode = false;
    editProyectoId = null;

    // Restore button
    const submitButton = document.getElementById('buttonRegistrarProyecto');
    submitButton.textContent = 'Agregar Proyecto';
    submitButton.classList.remove('btn-warning');
    submitButton.classList.add('btn-primary');

    // Remover cancel button
    const cancelButton = document.getElementById('cancelEditButton');
    if (cancelButton) {
        cancelButton.remove();
    }

    document.getElementById('imagenesProyecto').required = true;
};

// Función para actualizar una actividad
function actualizarProyecto() {
    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const imagenes = document.getElementById("imagenesProyecto").files;

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editProyectoId); // Agregar el id de la actividad
    form_data.append("nombre", nombre); // Agregar el nombre
    form_data.append("descripcion", descripcion); // Agregar la descripción

    // 📌 Agregar cada archivo individualmente
    for (let i = 0; i < imagenes.length; i++) {
        form_data.append("archivos[]", imagenes[i]);  // El nombre del input debe terminar en []
    }

    console.log(form_data);
    $.ajax({
        url: "?controlador=Proyectos&accion=modificarProyecto",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false, // No procesar los datos automáticamente
        contentType: false, // Dejar que el navegador configure el tipo de contenido
        success: function (response) {
            console.log(response);

            // Habilitar el botón
            document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === 'Actualización exitosa.') {
                limpiarCamposFormulario();

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: 'Actualización exitosa.',
                    confirmButtonColor: '#088cff'
                });

                // Salir del modo de edición
                cancelEdit();
            } else if (response[0]["mensaje"] === 'No se realizaron cambios en nombre y descripcion.') {
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });
            } else if (response[0]["mensaje"].includes("Ocurrió un error")) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrió un error, intenta nuevamente',
                    confirmButtonColor: '#088cff'
                });
            }

            obtenerProyectos();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // Habilitar el botón en caso de error
            document.getElementById('agregarProyecto').querySelector('button[type="submit"]').disabled = false;

        }
    });
}

// funcion para eliminar una actividad seleccionada
function eliminarProyecto(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción marcará el proyecto y sus imagenes como eliminado.",
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
                url: "?controlador=Proyectos&accion=eliminarProyecto",
                data: { id: id },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response[0]["mensaje"] === 'Proyecto eliminado exitosamente.') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                        obtenerProyectos(); // Recargar la tabla
                    } else if (response[0]["mensaje"] === 'El proyecto ya está eliminado.') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Información',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                    } else if (response[0]["mensaje"] === 'El proyecto no existe.') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ocurrió un error inesperado.',
                            confirmButtonColor: '#088cff'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error al procesar la solicitud. Intenta nuevamente.',
                        confirmButtonColor: '#088cff'
                    });
                }
            });
        }
    });
}

// función para limpiar campos del formulario
const limpiarCamposFormulario = () => {
    document.getElementById('nombre').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('imagenesProyecto').value = '';
    document.getElementById("vistaPrevia").innerHTML = '';
};

// funcion auxiliar para cargar una vista previa de las imagenes para una nueva actividad
function mostrarVistaPreviaDesdeInput(archivos) {
    var vistaPrevia = document.getElementById("vistaPrevia"); // Contenedor para la vista previa
    if (!isEditMode) vistaPrevia.innerHTML = ""; // Limpiar cualquier contenido previo si no esta en modo de edicion

    if (archivos["length"] > 0) {

        for (let i = 0; i < archivos["length"]; i++) {

            var reader = new FileReader();

            // Cuando el archivo esté listo, mostrarlo
            reader.onload = function (e) {
                var img = document.createElement("img");
                img.src = e.target.result; // Convertir archivo a base64
                img.alt = "Vista previa de la imagen";
                img.style.maxWidth = "200px";
                img.style.height = "auto";
                img.className = "img-thumbnail";
                vistaPrevia.appendChild(img);
            };

            reader.readAsDataURL(archivos[i]); // Leer el archivo como base64   
        }

    } else {
        vistaPrevia.innerHTML = "<p class='text-danger'>Selecciona un archivo de imagen válido.</p>";
    }
}

// funcion auxiliar para cargar una vista previa de las imagenes para editar
function mostrarVistaPreviaDesdeURL(url_archivos) {
    var vistaPrevia = document.getElementById("vistaPrevia"); // Contenedor para la vista previa
    if (!isEditMode) vistaPrevia.innerHTML = ""; // Limpiar cualquier contenido previo

    url_archivos.forEach(url => {
        if (url.trim() !== "") {
            var img = document.createElement("img");
            img.src = url; // Usar la URL proporcionada
            img.alt = "Vista previa de la imagen";
            img.style.maxWidth = "200px";
            img.style.height = "auto";
            img.className = "img-thumbnail";
            vistaPrevia.appendChild(img);
        } else {
            vistaPrevia.innerHTML = "<p class='text-danger'>Proporciona una URL válida.</p>";
        }
    });

}

// Inicializar la obtención de usuarios para cargar la tabla al ingresar
obtenerProyectos();