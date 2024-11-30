
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
            agregarUsuario();
        }
    });

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
    document.getElementById("nombre").value = '';
    document.getElementById("descripcion").value = '';
    document.getElementById("archivo").value = '';
};