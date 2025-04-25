
// Llamado de la funcion para cargar las noticias el frontend de usuario normal
function obtenerNoticiasParaFrontEndUsuario() {

    console.log('Voy a cargar las noticias para el frontend de usuario normal');

    $.ajax({
        type: "POST",
        url: "?controlador=Noticias&accion=obtenerNoticias",
        dataType: "json",
        success: function (response) {
           console.log(response);
           //verificar el tipo, si es de tipo imagen o video para mostrarlo en el frontend
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


obtenerNoticiasParaFrontEndUsuario();