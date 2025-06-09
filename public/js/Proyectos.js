let isEditMode = false;
let editProyectoId = null;
let updateArchivos = false;

document.getElementById('agregarProyecto').addEventListener('submit', function (event) {
    event.preventDefault();

    if (isEditMode) {
        if (updateArchivos) {
            actualizarProyectoConNuevasImagenes();
        } else {
            actualizarProyectoSinImagenes();
        }
    } else {
        agregarProyecto();
    }
});

document.getElementById("imagenesProyecto").addEventListener("change", function (event) {
    if (isEditMode) {
        updateArchivos = true;
    }
    const archivos = event.target.files;
    mostrarVistaPreviaDesdeInput(archivos);
});

function obtenerProyectos() {
    $.ajax({
        type: "POST",
        url: "?controlador=Proyectos&accion=obtenerProyectos",
        dataType: "json",
        success: function (response) {
            $("#containertabla tbody").empty();

            $.each(response, function (index, proyecto) {
                let row = $("<tr>");
                let accionesCell = $("<td>");
                let editarBtn = $("<button>")
                    .html('<span class="material-icons">edit_document</span>')
                    .addClass("butonEditar")
                    .data("id", proyecto.id)
                    .data("nombre", proyecto.nombre)
                    .data("descripcion", proyecto.descripcion)
                    .data("imagenes", proyecto.imagenes)
                    .on("click", function () {
                        editarProyecto(
                            $(this).data("id"),
                            $(this).data("nombre"),
                            $(this).data("descripcion"),
                            $(this).data("imagenes")
                        );
                    });

                let eliminarBtn = $("<button>")
                    .html('<span class="material-icons">cancel</span>')
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

                let imagenesCell = $("<td>");
                proyecto.imagenes.forEach(imagen => {
                    let img = $("<img>")
                        .attr("src", imagen.url)
                        .addClass("img-thumbnail")
                        .css({ maxWidth: "90px", height: "auto" });
                    imagenesCell.append(img);
                });

                row.append(imagenesCell);
                $("#containertabla tbody").append(row);
            });
        },
        error: function () {
            mostrarMensaje('error', 'Error', 'No se pudieron cargar los proyectos.');
        }
    });
}

function agregarProyecto() {
    const nombre = $("#nombre").val().trim();
    const descripcion = $("#descripcion").val().trim();
    const archivos = $("#imagenesProyecto")[0].files;

    const formData = new FormData();
    formData.append("nombre", nombre);
    formData.append("descripcion", descripcion);
    for (let i = 0; i < archivos.length; i++) {
        formData.append("archivos[]", archivos[i]);
    }

    $.ajax({
        url: "?controlador=Proyectos&accion=agregarProyecto",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            if (response[0].mensaje === "Proyecto ingresado con éxito.") {
                limpiarCamposFormulario();
                mostrarMensaje('success', '¡Genial!', response[0].mensaje);
            } else {
                mostrarMensaje('error', 'Oops...', response[0].mensaje);
            }
            obtenerProyectos();
        },
        error: function () {
            mostrarMensaje('error', 'Error', 'No se pudo agregar el proyecto.');
        }
    });
}

function actualizarProyectoSinImagenes() {
    const formData = new FormData();
    formData.append("id", editProyectoId);
    formData.append("nombre", $("#nombre").val().trim());
    formData.append("descripcion", $("#descripcion").val().trim());

    $.ajax({
        url: "?controlador=Proyectos&accion=actualizarProyectoSinNuevasImagenes",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            mostrarMensaje('success', 'Actualizado', response[0].mensaje);
            limpiarCamposFormulario();
            cancelEdit();
            obtenerProyectos();
        },
        error: function () {
            mostrarMensaje('error', 'Error', 'No se pudo actualizar.');
        }
    });
}

function actualizarProyectoConNuevasImagenes() {
    const formData = new FormData();
    const archivos = $("#imagenesProyecto")[0].files;

    formData.append("id", editProyectoId);
    formData.append("nombre", $("#nombre").val().trim());
    formData.append("descripcion", $("#descripcion").val().trim());

    for (let i = 0; i < archivos.length; i++) {
        formData.append("archivos[]", archivos[i]);
    }

    $.ajax({
        url: "?controlador=Proyectos&accion=actualizarProyectoConNuevasImagenes",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            mostrarMensaje('success', 'Actualizado', response[0].mensaje);
            limpiarCamposFormulario();
            cancelEdit();
            obtenerProyectos();
        },
        error: function () {
            mostrarMensaje('error', 'Error', 'No se pudo actualizar el proyecto.');
        }
    });
}

function eliminarProyecto(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esto marcará el proyecto y sus imágenes como eliminados.",
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
                    mostrarMensaje('success', 'Eliminado', response.mensaje);
                    obtenerProyectos();
                },
                error: function () {
                    mostrarMensaje('error', 'Error', 'No se pudo eliminar.');
                }
            });
        }
    });
}

function editarProyecto(id, nombre, descripcion, imagenes) {
    isEditMode = true;
    updateArchivos = false;
    editProyectoId = id;

    $("#nombre").val(nombre);
    $("#descripcion").val(descripcion);
    $("#imagenesProyecto").prop("required", false);

    const submitButton = $("#buttonRegistrarProyecto");
    submitButton.text("Actualizar Proyecto").removeClass("btn-primary").addClass("btn-warning");

    const cancelButton = $("<button>")
        .attr("type", "button")
        .attr("id", "cancelEditButton")
        .addClass("botonCancelar")
        .text("Cancelar")
        .on("click", cancelEdit);

    submitButton.after(cancelButton);

    mostrarVistaPreviaDesdeURL(imagenes);
}

function cancelEdit() {
    isEditMode = false;
    updateArchivos = false;
    editProyectoId = null;

    $("#nombre").val("");
    $("#descripcion").val("");
    $("#imagenesProyecto").val("");
    $("#vistaPrevia").empty();

    const submitButton = $("#buttonRegistrarProyecto");
    submitButton.text("Agregar Proyecto").removeClass("btn-warning").addClass("btn-primary");

    $("#cancelEditButton").remove();
    $("#imagenesProyecto").prop("required", true);
}

function mostrarVistaPreviaDesdeInput(archivos) {
    const contenedor = document.getElementById("vistaPrevia");
    contenedor.innerHTML = "";

    Array.from(archivos).forEach(archivo => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            img.alt = "Vista previa";
            img.className = "img-thumbnail";
            img.style.maxWidth = "200px";
            img.style.height = "auto";
            contenedor.appendChild(img);
        };
        reader.readAsDataURL(archivo);
    });
}

function mostrarVistaPreviaDesdeURL(imagenes) {
    const contenedor = document.getElementById("vistaPrevia");
    contenedor.innerHTML = "";

    imagenes.forEach(imagen => {
        const url = typeof imagen === "object" ? imagen.url : imagen;
        if (typeof url === "string" && url.trim() !== "") {
            const img = document.createElement("img");
            img.src = url;
            img.alt = "Vista previa";
            img.className = "img-thumbnail";
            img.style.maxWidth = "200px";
            img.style.height = "auto";
            contenedor.appendChild(img);
        }
    });
}

function limpiarCamposFormulario() {
    $("#nombre").val('');
    $("#descripcion").val('');
    $("#imagenesProyecto").val('');
    $("#vistaPrevia").empty();
}

function mostrarMensaje(icon, title, text) {
    Swal.fire({ icon, title, text, confirmButtonColor: '#088cff' });
}

obtenerProyectos();