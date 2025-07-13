
// Evento de submit del formulario
document.getElementById('enviarMensajeContacto')
    .addEventListener('submit', function (event) {
        event.preventDefault();
   
    enviarMensaje();
        
});


function enviarMensaje(){
    
    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const mensaje = document.getElementById("mensaje").value.trim();


    // Desactivar el botón mientras se procesa la solicitud AJAX
    document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = true;

    var form_data = {
        nombre: nombre,
        apellido: apellido,
        correo: correo,
        telefono: telefono,
        mensaje: mensaje
    };

    $.ajax({
        type: "POST",
        url: "phpmailer/enviarEmailContacto.php",
        data: form_data,
        dataType: "json",
        success: function (response) {

            // Habilitar el botón después de recibir la respuesta del servidor
            document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = false;

            // Verificar si la respuesta es exitosa
            if (response.status === "success") {

                Swal.fire({
                    icon: 'success',
                    title: '¡Genial!',
                    text: 'Nos pondremos en contacto contigo pronto.',
                    confirmButtonColor: '#088cff'
                });

                //limpiar campos del formulario
                document.getElementById("nombre").value = '';
                document.getElementById("apellido").value = '';
                document.getElementById("correo").value = '';
                document.getElementById("telefono").value = '';
                document.getElementById("mensaje").value = '';

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message || 'Ocurrió un error, intenta nuevamente.',
                    confirmButtonColor: '#088cff'
                });
            }

            
        },
        error: function (xhr, status, error) {

            console.log(xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo conectar con el servidor. Por favor, intenta más tarde.',
                confirmButtonColor: '#088cff'
            });

            // En caso de error, también habilitar el botón nuevamente
            document.getElementById('enviarMensajeContacto').querySelector('button[type="submit"]').disabled = false;
        }
    });
    
}
