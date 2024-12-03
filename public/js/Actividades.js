
// Variables globales para controlar el modo de edición
let isEditMode = false;
let editActividadId = null;


// Evento de submit del formulario
document.getElementById('agregarActividad')
    .addEventListener('submit', function (event) {
        event.preventDefault();

        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            actualizarActividad();
        } else {
            agregarActividad();
        }
    });

// Evento para detectar cambios en el input
document.getElementById("imagenActividad")
    .addEventListener("change", function (event) {
        var archivo = event.target.files[0]; // Obtener el archivo seleccionado
        mostrarVistaPreviaDesdeInput(archivo); // Llamar a la función con el archivo
    });

// funciona para solicitar las actividades creadas y cargalas en la tabla
function obtenerActividades() {
    $.ajax({
        type: "POST",
        url: "?controlador=Actividades&accion=obtenerActividades",
        dataType: "json",
        success: function (response) {

            // Limpiar la tabla antes de agregar nuevos datos
            $("#containertabla tbody").empty();

            // Recorrer la respuesta y agregar los datos a la tabla
            $.each(response, function (index, actividad) {

                var row = $("<tr>");

                // Columna de acciones
                var accionesCell = $("<td>");
                var editarBtn = $("<button>")
                    .html('<span class="material-icons">edit_document</span> ') 
                    .addClass("butonEditar")
                    .data("id", actividad.id)
                    .data("url_archivo", actividad.url_archivo)
                    .data("nombre", actividad.nombre)
                    .data("descripcion", actividad.descripcion)
                    .on("click", function () {
                        editarActividad(
                            $(this).data("id"),
                            $(this).data("url_archivo"),
                            $(this).data("nombre"),
                            $(this).data("descripcion")
                        );
                    });

                var eliminarBtn = $("<button>")
                    .html('<span class="material-icons">cancel</span> ') 
                    .addClass("butonDelete")
                    .data("id", actividad.id)
                    .on("click", function () {
                        eliminarActividad($(this).data("id"));
                    });

                accionesCell.append(editarBtn).append(eliminarBtn);
                row.append(accionesCell);

                row.append($("<td>").text(actividad.nombre));
                row.append($("<td>").text(actividad.descripcion));
                row.append($("<td>").text(actividad.fecha));
                // Columna de Imagen: Se genera una etiqueta <img> con la URL
                var img = $("<img>")
                    .attr("src", actividad.url_archivo) // Establece la ruta de la imagen
                    .attr("alt", "Imagen de la actividad") // Texto alternativo
                    .css("width", "60px") // Ajusta el tamaño de la imagen
                    .css("height", "auto");
                row.append($("<td>").append(img));

                $("#containertabla tbody").append(row);
            });

        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
        }

    });
}

// funciona para agregar actividades nuevas
function agregarActividad() {
    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const imagen = document.getElementById("imagenActividad").files[0];

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("nombre", nombre); // Agregar el nombre
    form_data.append("descripcion", descripcion); // Agregar la descripción
    form_data.append("archivo", imagen); // Agregar la imagen (archivo)

    console.log(form_data);
    $.ajax({
        url: "?controlador=Actividades&accion=agregarActividad",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false, // No procesar los datos automáticamente
        contentType: false, // Dejar que el navegador configure el tipo de contenido
        success: function (response) {
            console.log(response);

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === "Actividad ingresada con exito.") {
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

            obtenerActividades(); // Recargar la tabla
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;
        }
    });
}

// Función para cargar los datos a editar al formulario
function editarActividad(id, url_archivo, nombre, descripcion) {
    // Enter edit mode
    isEditMode = true;
    editActividadId = id;

    //Eliminar el boton de cancel en caso de que ya exista uno, para agregar el nuevo
    const cancelEditButtonAnterior = document.getElementById('cancelEditButton');
       if (cancelEditButtonAnterior) {
        cancelEditButtonAnterior.remove();
    }

    // Change button text and style
    const submitButton = document.getElementById('buttonRegistrarActividad');
    submitButton.textContent = 'Actualizar Actividad';
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
    document.getElementById('imagenActividad').required = false;
    mostrarVistaPreviaDesdeURL(url_archivo);
}

// función para cancelar la edición
function cancelEdit() {
    // Reset form
    limpiarCamposFormulario();

    // Exit edit mode
    isEditMode = false;
    editActividadId = null;

    // Restore button
    const submitButton = document.getElementById('buttonRegistrarActividad');
    submitButton.textContent = 'Agregar Actividad';
    submitButton.classList.remove('btn-warning');
    submitButton.classList.add('btn-primary');

    // Remover cancel button
    const cancelButton = document.getElementById('cancelEditButton');
    if (cancelButton) {
        cancelButton.remove();
    }

    document.getElementById('imagenActividad').required = true;
};

// Función para actualizar una actividad
function actualizarActividad() {
    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const imagen = document.getElementById("imagenActividad").files[0];

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editActividadId); // Agregar el id de la actividad
    form_data.append("nombre", nombre); // Agregar el nombre
    form_data.append("descripcion", descripcion); // Agregar la descripción
    form_data.append("archivo", imagen); // Agregar la imagen (archivo)

    console.log(form_data);
    $.ajax({
        url: "?controlador=Actividades&accion=modificarActividad",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false, // No procesar los datos automáticamente
        contentType: false, // Dejar que el navegador configure el tipo de contenido
        success: function (response) {
            console.log(response);

            // Habilitar el botón
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;

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
            } else if (response[0]["mensaje"] === 'No se realizaron cambios.') {
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

            obtenerActividades();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
            
            // Habilitar el botón en caso de error
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;

        }
    });
}

// funcion para eliminar una actividad seleccionada
function eliminarActividad(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción marcará la actividad como eliminado.",
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
                url: "?controlador=Actividades&accion=eliminarActividad",
                data: { id: id },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response[0]["mensaje"] === 'Actividad eliminada exitosamente.') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                        obtenerActividades(); // Recargar la tabla
                    } else if (response[0]["mensaje"] === 'La actividad ya está eliminado.') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Información',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                    } else if (response[0]["mensaje"] === 'La actividad no existe.') {
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
    document.getElementById('imagenActividad').value = '';
    document.getElementById("vistaPrevia").innerHTML = '';
};

// funcion auxiliar para cargar una vista previa de las imagenes para una nueva actividad
function mostrarVistaPreviaDesdeInput(archivo) {
    var vistaPrevia = document.getElementById("vistaPrevia"); // Contenedor para la vista previa
    vistaPrevia.innerHTML = ""; // Limpiar cualquier contenido previo

    if (archivo) {
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

        reader.readAsDataURL(archivo); // Leer el archivo como base64
    } else {
        vistaPrevia.innerHTML = "<p class='text-danger'>Selecciona un archivo de imagen válido.</p>";
    }
}

// funcion auxiliar para cargar una vista previa de las imagenes para editar
function mostrarVistaPreviaDesdeURL(url) {
    var vistaPrevia = document.getElementById("vistaPrevia"); // Contenedor para la vista previa
    vistaPrevia.innerHTML = ""; // Limpiar cualquier contenido previo

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
}

// Inicializar la obtención de usuarios para cargar la tabla al ingresar
obtenerActividades();