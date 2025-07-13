
// Variables globales para controlar el modo de edición
let isEditMode = false;
let editUserId = null;


// Evento de submit del formulario
document.getElementById('agregarUsuario')
    .addEventListener('submit', function (event) {
        event.preventDefault();
   
        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            actualizarUsuario();
        } else {
            agregarUsuario();
        }
});
   
   

// Función para obtener usuarios
function obtenerUsuarios() {
    $.ajax({
        type: "POST",
        url: "?controlador=Usuarios&accion=obtenerUsuarios",
        dataType: "json",
        success: function(response) {

            // Limpiar la tabla antes de agregar nuevos datos
            $("#containertabla tbody").empty();
                                   
            // Recorrer la respuesta y agregar los datos a la tabla
            $.each(response, function(index, usuario) {
                if (usuario.rol !== "admin") {

                    var row = $("<tr>");
   
                    // Columna de acciones
                    var accionesCell = $("<td>");
                    var editarBtn = $("<button>")
                        .html('<span class="material-icons">edit_document</span> ') 
                        .addClass("butonEditar")
                        .data("id", usuario.id)
                        .data("nombre", usuario.nombre)
                        .data("correo", usuario.correo)
                        .data("contrasena", usuario.contrasena)
                        .on("click", function() {
                            editarUsuario(
                                $(this).data("id"),
                                $(this).data("nombre"),
                                $(this).data("correo"),
                                $(this).data("contrasena")
                        );
                    });
                                               
                    var eliminarBtn = $("<button>")
                        .html('<span class="material-icons">cancel</span> ') 
                        .addClass("butonDelete")
                        .data("id", usuario.id)
                        .on("click", function() {
                            eliminarUsuario($(this).data("id"));
                        });
                                               
                    accionesCell.append(editarBtn).append(eliminarBtn);
                    row.append(accionesCell);
   
                    row.append($("<td>").text(usuario.nombre));
                    row.append($("<td>").text(usuario.correo));
                    row.append($("<td>").text('********'));
   
                    $("#containertabla tbody").append(row);
                }
            });

        },
           error: function(xhr, status, error) {
           console.log(error, xhr, status);
        }

    });
}



//funciona para agregar usuarios
function agregarUsuario(){
    // Mantener la lógica original de agregar usuario
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contrasena = document.getElementById("contrasena").value.trim();

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarUsuario').querySelector('button[type="submit"]').disabled = true;

    var form_data = {
        nombre: nombre,
        correo: correo,
        contrasena: contrasena
    };

    $.ajax({
        type: "POST",
        url: "?controlador=Usuarios&accion=insertarUsuario",
        data: form_data,
        dataType: "json",
        success: function (response) {

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarUsuario').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === "Inserción exitosa.") {
                limpiarCamposFormulario();

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: 'Usuario registrado correctamente',
                    confirmButtonColor: '#088cff'
                });
            } else if (response[0]["mensaje"] === "Ya existe un usuario con este correo.") {
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

            obtenerUsuarios();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarUsuario').querySelector('button[type="submit"]').disabled = false;
        }
    });
}



// Función para cargar los datos a editar al formulario
function editarUsuario(id, nombre, correo, contrasena) {
    // Enter edit mode
    isEditMode = true;
    editUserId = id;

    //Eliminar el boton de cancel en caso de que ya exista uno, para agregar el nuevo
    const cancelEditButtonAnterior = document.getElementById('cancelEditButton');
       if (cancelEditButtonAnterior) {
        cancelEditButtonAnterior.remove();
    }
   
    // Change button text and style
    const submitButton = document.getElementById('buttonRegistrarUsuario');
    submitButton.textContent = 'Actualizar usuario';
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
    document.getElementById("nombre").value = nombre;
    document.getElementById("correo").value = correo;
    document.getElementById("contrasena").value = contrasena;
}
 


// Función para actualizar usuario
function actualizarUsuario() {
       const nombre = document.getElementById("nombre").value.trim();
       const correo = document.getElementById("correo").value.trim();
       const contrasena = document.getElementById("contrasena").value.trim();
   
       // Desactivar el botón mientras se procesa la solicitud
       const submitButton = document.getElementById('buttonRegistrarUsuario');
       submitButton.disabled = true;
   
       var form_data = {
           id: editUserId,
           nombre: nombre,
           correo: correo,
           contrasena: contrasena
       };
   
       $.ajax({
           type: "POST",
           url: "?controlador=Usuarios&accion=actualizarUsuario",
           data: form_data,
           dataType: "json",
           success: function (response) {
               // Habilitar el botón
               submitButton.disabled = false;
   
               if (response[0]["mensaje"] === "Actualización exitosa.") {
                   limpiarCamposFormulario();
   
                   Swal.fire({
                       icon: 'success',
                       title: '¡Genial!',
                       text: 'Usuario actualizado correctamente',
                       confirmButtonColor: '#088cff'
                   });
   
                   // Salir del modo de edición
                   cancelEdit();
               } else if (response[0]["mensaje"] === "Ya existe un usuario con este correo.") {
                   Swal.fire({
                       icon: 'error',
                       title: 'Oops...',
                       text: response[0]["mensaje"],
                       confirmButtonColor: '#088cff'
                   });
               } else if (response[0]["mensaje"] === "No se encontró el usuario o no se realizaron cambios.") {
                   Swal.fire({
                       icon: 'error',
                       title: 'Oops...',
                       text: 'No se realizaron cambios',
                       confirmButtonColor: '#088cff'
                   });
               }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Ocurrió un error, intenta nuevamente',
                        confirmButtonColor: '#088cff'
                    });
               }
   
               obtenerUsuarios();
           },
           error: function (xhr, status, error) {
               console.log(error, xhr, status);
               
               // Habilitar el botón en caso de error
               submitButton.disabled = false;
   
               Swal.fire({
                   icon: 'error',
                   title: 'Error',
                   text: 'Hubo un problema al actualizar el usuario',
                   confirmButtonColor: '#088cff'
               });
           }
       });
}
   


// Función para cancelar la edición
function cancelEdit() {
       // Reset form
       limpiarCamposFormulario();
   
       // Exit edit mode
       isEditMode = false;
       editUserId = null;
   
       // Restore button
       const submitButton = document.getElementById('buttonRegistrarUsuario');
       submitButton.textContent = 'Agregar usuario';
       submitButton.classList.remove('btn-warning');
       submitButton.classList.add('btn-primary');
   
       // Remover cancel button
       const cancelButton = document.getElementById('cancelEditButton');
       if (cancelButton) {
           cancelButton.remove();
       }
}
   


// Función para eliminar usuario
function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción marcará al usuario como eliminado.",
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
                url: "?controlador=Usuarios&accion=eliminarUsuario",
                data: { id: id },
                dataType: "json",
                success: function(response) {
                    if (response[0]["mensaje"] === 'Eliminación exitosa.') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                        obtenerUsuarios(); // Recargar la tabla
                    } else if (response[0]["mensaje"] === 'El usuario ya está eliminado.') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Información',
                            text: response[0]["mensaje"],
                            confirmButtonColor: '#088cff'
                        });
                    } else if (response[0]["mensaje"] === 'El usuario no existe.') {
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
                error: function(xhr, status, error) {
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



// Función para limpiar campos del formulario
const limpiarCamposFormulario = () => {
    document.getElementById("nombre").value = '';
    document.getElementById("correo").value = '';
    document.getElementById("contrasena").value = '';
};
   


// Inicializar la obtención de usuarios para cargar la tabla al ingresar
obtenerUsuarios();