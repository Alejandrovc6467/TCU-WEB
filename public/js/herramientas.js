// Variables globales para controlar el modo de edición, el tipo de archivos y si se están actualizando archivos
let isEditMode = false;
let editNoticiaId = null;
let updateArchivos = false;
let tipoArchivoSeleccionado = null;


// Evento de submit del formulario
document.getElementById('agregarNoticia')
    .addEventListener('submit', function (event) {
        event.preventDefault();

        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            if(updateArchivos){
                actualizarNoticiaConNuevosArchivos();
            }else{
                actualizarNoticiaSinNuevosArchivos();  
            }
        } else {
            agregarNoticia();
        }
    });

// Evento para detectar cambios en el input
document.getElementById("archivosNoticia")
    .addEventListener("change", function (event) {

        if(isEditMode){
            console.log('cambio en iput de archivos en modo edicion');
            updateArchivos = true;
        }else{
            console.log('cambio en iput de archivos en modo agregar');
            updateArchivos = false;
        }
        
        var archivos = event.target.files; // Obtener el archivo seleccionado
        mostrarVistaPreviaDesdeInput(archivos); // Llamar a la función con el archivo
    });


/*** CRUD de noticias ***********************************************************************************/  

// Función para solicitar las noticias y mostrarlas en la vista
function obtenerHerramientas() {
    $.ajax({
        type: "POST",
        url: "?controlador=Herramientas&accion=obtenerHerramientas",
        dataType: "json",
        success: function (response) {
            // Limpiar la tabla antes de agregar nuevos datos
            $("#containertabla tbody").empty();

            // Recorrer la respuesta y agregar los datos a la tabla
            $.each(response, function (index, noticia) {
                var row = $("<tr>");

                // Columna de acciones
                var accionesCell = $("<td>");
                var editarBtn = $("<button>")
                    .html('<span class="material-icons">edit_document</span> ')
                    .addClass("butonEditar")
                    .data("id", noticia.id)
                    .data("nombre", noticia.nombre)
                    .data("descripcion", noticia.descripcion)
                    .data("url_archivos", noticia.archivos.map(archivo => archivo.url))
                    .data("tipo", noticia.tipo)
                    .on("click", function () {
                        editarNoticia(
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
                    .data("id", noticia.id)
                    .on("click", function () {
                        eliminarHerramienta($(this).data("id"));
                    });

                accionesCell.append(editarBtn).append(eliminarBtn);
                row.append(accionesCell);

                // Agregar nombre y descripción
                row.append($("<td>").text(noticia.nombre));
                row.append($("<td>").text(noticia.descripcion));

                // Contenedor para archivos multimedia
                var multimediaCell = $("<td>");
                
                // Procesar los archivos dependiendo del tipo (imagen o video)
                if (noticia.tipo === 'imagen') {
                    // Para imágenes, mostrar miniaturas
                    noticia.archivos.forEach(archivo => {
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
                } else if (noticia.tipo === 'video') {
                    // Para videos, mostrar miniaturas que no se puedan reproducir
                    noticia.archivos.forEach(archivo => {
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
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar las noticias',
                confirmButtonColor: '#088cff'
            });
        }
    });
}

// Función para eliminar una noticia
function eliminarHerramienta(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción marcará la noticia y sus archivos como eliminados.",
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
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: response.mensaje,
                        confirmButtonColor: '#088cff'
                    }).then(() => {
                        obtenerHerramientas(); // Recargar la tabla después de eliminar
                    });
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

// Función para agregar noticias nuevas
function agregarNoticia() {

    console.log('Preaionaste agregar noticia');

    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const archivos = document.getElementById("archivosNoticia").files;

    // para pruebas en consola, borrar despues
    console.log("nombre:", nombre);
    console.log("descripcion:", descripcion);
    console.log("Tipo de archivo seleccionado:", tipoArchivoSeleccionado);
    console.log("Archivos:", archivos);
    
    
    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("nombre", nombre);
    form_data.append("descripcion", descripcion);
    form_data.append("tipo", tipoArchivoSeleccionado);

    // Agregar cada archivo individualmente
    for (let i = 0; i < archivos.length; i++) {
        form_data.append("archivos[]", archivos[i]);
    }
    
    // Enviar la solicitud AJAX para agregar la noticia
    $.ajax({
        url: "?controlador=Herramientas&accion=agregarHerramienta",
        type: "POST",
        data: form_data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

          

            // EN REVISION ****************************************************
            
            if (response[0]["mensaje"] === "Se agregó la herramienta correctamente.") {

                limpiarCamposFormulario();
                Swal.fire({icon: 'success', title: '¡Genial!', text: response[0]["mensaje"], confirmButtonColor: '#088cff' });

            } else { 

                Swal.fire({ icon: 'error', title: 'Oops...', text: response[0]["mensaje"], confirmButtonColor: '#088cff' });

            } 

            // FIN DE EN REVISION *********************************************

            obtenerHerramientas(); // Recargar la tabla

        },
        error: function (xhr, status, error) {

            console.log(error, xhr, status);

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la solicitud',
                confirmButtonColor: '#088cff'
            });
        }
    });
    
    
}

// Funcion para actualizar la noticia sin subir nuevos archivos
function actualizarNoticiaSinNuevosArchivos(){

    console.log('Voy a actualizar sin subir archivos');

    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
   
    console.log("ID de la noticia a editar:", editNoticiaId);
    console.log("nombre:", nombre);
    console.log("descripcion:", descripcion);
    
    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editNoticiaId);
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
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === 'Actualización exitosa.') {
                limpiarCamposFormulario();

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });

                // Salir del modo de edición
                cancelEdit();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response[0]["mensaje"],
                    confirmButtonColor: '#088cff'
                });
            }

            obtenerHerramientas();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // Habilitar el botón en caso de error
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la solicitud',
                confirmButtonColor: '#088cff'
            });
        }
    });
    


}

// Funcion para actualizar la noticia y subir nuevos archivos
function actualizarNoticiaConNuevosArchivos(){

    console.log('Voy a actualizar y subir archivos nuevos');
    
    // Se obtienen los valores introducidos en el formulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const archivos = document.getElementById("archivosNoticia").files;

    console.log("nombre:", nombre);
    console.log("descripcion:", descripcion);
    console.log("Archivos:", archivos);
    console.log("ID de la noticia a editar:", editNoticiaId);

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = true;

    // Crear un objeto FormData para enviar archivos y datos
    var form_data = new FormData();
    form_data.append("id", editNoticiaId);
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
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

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
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrió un error, intenta nuevamente',
                    confirmButtonColor: '#088cff'
                });
            }

            obtenerHerramientas();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // Habilitar el botón en caso de error
            document.getElementById('agregarNoticia').querySelector('button[type="submit"]').disabled = false;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la solicitud',
                confirmButtonColor: '#088cff'
            });
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
    console.log("Tipo de archivo seleccionado:", tipoArchivoSeleccionado);

    // Limpiar la vista previa anterior y el input de archivos, para evitar conflictos
    var vistaPrevia = document.getElementById("vistaPrevia");
    vistaPrevia.innerHTML = "";
    document.getElementById('archivosNoticia').value = '';
  

    // Manejo de cambios en la UI dependiendo del tipo
    if (tipoArchivoSeleccionado === 'imagen') {
      document.getElementById('archivosNoticia').accept = 'image/*';
      document.getElementById('archivosNoticia').multiple = true;
    } else if (tipoArchivoSeleccionado === 'video') {
      document.getElementById('archivosNoticia').accept = 'video/*';
      document.getElementById('archivosNoticia').multiple = false;
    }
}

// Ejecutar una vez al cargar para aplicar el tipo por defecto
handleTipoArchivoChange({ target: document.querySelector('input[name="tipoArchivo"]:checked') });

// Función para cargar los datos a editar al formulario
function editarNoticia(id, nombre, descripcion, url_archivos, tipo) {
    // Enter edit mode
    isEditMode = true;
    editNoticiaId = id;

    // Eliminar el botón de cancel en caso de que ya exista uno, para agregar el nuevo
    const cancelEditButtonAnterior = document.getElementById('cancelEditButton');
    if (cancelEditButtonAnterior) {
        cancelEditButtonAnterior.remove();
    }

    // Change button text and style
    const submitButton = document.getElementById('buttonRegistrarNoticia');
    submitButton.textContent = 'Actualizar Noticia';
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
    document.getElementById('archivosNoticia').required = false;

    // Seleccionar el radio según el tipo recibido y cambiar el tipo de archivo aceptados en el input archivosNoticia
    if (tipo === 'imagen') {
        document.getElementById('radioImagenes').checked = true;
        document.getElementById('archivosNoticia').accept = 'image/*';
    } else{
        document.getElementById('radioVideo').checked = true;
        document.getElementById('archivosNoticia').accept = 'video/*';
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
    editNoticiaId = null;

    // Restore button
    const submitButton = document.getElementById('buttonRegistrarNoticia');
    submitButton.textContent = 'Agregar Noticia';
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

    document.getElementById('archivosNoticia').required = true;
}

// Función para limpiar campos del formulario
const limpiarCamposFormulario = () => {
    document.getElementById('nombre').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('archivosNoticia').value = '';
    document.getElementById("vistaPrevia").innerHTML = '';
};

// Función auxiliar para cargar una vista previa de los archivos para una nueva noticia
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

// Llamar a la función para cargar las noticias al cargar la página
obtenerHerramientas();