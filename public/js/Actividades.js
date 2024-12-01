
// Variables globales para controlar el modo de edición
let isEditMode = false;
let editUserId = null;


// Evento de submit del formulario
document.getElementById('agregarActividad')
    .addEventListener('submit', function (event) {
        event.preventDefault();

        // llamar a la funcion dependiendo del estado
        if (isEditMode) {
            actualizarUsuario();
        } else {
            agregarActividad();
        }
    });


//funciona para agregar actividades nuevas
function agregarActividad() {
    // se optienen los valores introducidos en el fromulario
    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const imagen = document.getElementById("imagenActividad").files[0];

    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = true;

    // se crea el objeto que contiene la informacion a enviar del fromulario
    var form_data = {
        nombre: nombre,
        descripcion: descripcion
    };
    console.log(form_data);
    $.ajax({
        url: "?controlador=Actividades&accion=insertarActividad",
        type: "POST",
        data: form_data,
        dataType: "json",
        success: function (response) {
            console.log(response);

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;

            if (response[0]["mensaje"] === "Actividad ingresada con exito.") {
                limpiarCamposFormulario();

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: 'Usuario registrado correctamente',
                    confirmButtonColor: '#088cff'
                });
            } else if (response[0]["mensaje"] === "Ocurrió un error el usuario no existe.") {
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

            //obtenerUsuarios();
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('agregarActividad').querySelector('button[type="submit"]').disabled = false;
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
    submitButton.textContent = 'Agregar Actividad';
    submitButton.classList.remove('btn-warning');
    submitButton.classList.add('btn-primary');

    // Remover cancel button
    const cancelButton = document.getElementById('cancelEditButton');
    if (cancelButton) {
        cancelButton.remove();
    }
};

// Función para limpiar campos del formulario
const limpiarCamposFormulario = () => {
    document.getElementById('nombre').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('imagenActividad').value = '';
};