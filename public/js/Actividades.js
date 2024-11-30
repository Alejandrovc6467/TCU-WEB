
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

    $(document).ready(function () {
        $("#formsubirdocumento").on("submit", function (e) {
            e.preventDefault(); // Evita el comportamiento predeterminado de enviar el formulario.
    
            // Recopila los datos del formulario.
            var formData = new FormData(this);
    
    
            // Obtén el valor del campo de entrada con ID "nombre".
            var temadocumento = $("#temadocumento").val();
            var email_user = $("#email_user").val();
    
    
            // Agrega el valor a formData con una clave específica.
            formData.append("temadocumento", temadocumento);
            formData.append("email_user", email_user);
    
    
            // Realiza la solicitud AJAX para procesar el formulario.
            $.ajax({
                url: "?controlador=Index&accion=subirdocumento", // Ruta al archivo PHP que maneja el formulario.
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    // Se ejecuta antes de enviar la solicitud AJAX.
                    $('.loader-container').css('display', 'block');
                },
                success: function (response) {
    
                    console.log(response);
    
                    if (response.message === "1") {
    
                        $('#inputfile').val(''); //si todo sale bien reseteo el input del file
    
                        Swal.fire({
                            icon: 'success',
                            title: '¡Genial!',
                            text: 'Documento subido correctamente',
                            confirmButtonColor: '#2980B9',
                        });
    
    
                        let serviceID = 'default_service';
                        let templateID = 'template_3v6j8dt';
    
    
                        emailjs.sendForm(serviceID, templateID, document.getElementById('formsubirdocumento'))
                            .then(() => {
                                console.log('Se envio');
                                //alert('Sent!');
                            }, (err) => {
    
                                console.log('No se envio');
                                //alert(JSON.stringify(err));
                            });
    
    
    
                        //  aqui 
    
    
                    } else if (response.message === "2") {
    
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'El archivo no es de un tipo permitido como los siguientes (.pdf, .doc, .docx, .odt)',
                            confirmButtonColor: '#2980B9',
                        });
    
    
                    } else {
    
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Ha ocurrido un error, intenta de nuevo.',
                            confirmButtonColor: '#2980B9',
                        });
    
                    }
    
    
                },
                complete: function () {
                    // Se ejecuta después de que se completa la solicitud AJAX.
                    $('.loader-container').css('display', 'none');
                }
    
            });
        });
    });
    
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
            console.log(response);

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