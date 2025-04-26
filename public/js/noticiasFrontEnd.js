// Llamado de la funcion para cargar las noticias el frontend de usuario normal
function obtenerNoticiasParaFrontEndUsuario() {
    console.log('Voy a cargar las noticias para el frontend de usuario normal');

    $.ajax({
        type: "POST",
        url: "?controlador=Noticias&accion=obtenerNoticias",
        dataType: "json",
        success: function (response) {
            console.log(response);
            // Limpiar el contenedor de noticias
            $('.noticias-items').empty();
            
            // Recorrer todas las noticias recibidas
            response.forEach(function(noticia) {
                // Crear elemento HTML para la noticia
                let noticiaHTML = '';
                
                // Verificar el tipo de noticia (imagen o video)
                if (noticia.tipo === 'imagen') {
                    // Crear estructura para noticia con imágenes
                    noticiaHTML = crearNoticiaConImagenes(noticia);
                } else if (noticia.tipo === 'video') {
                    // Crear estructura para noticia con video
                    noticiaHTML = crearNoticiaConVideo(noticia);
                }
                
                // Agregar la noticia al contenedor
                $('.noticias-items').append(noticiaHTML);
                
                // Inicializar el carrusel para esta noticia si es de tipo imagen
                if (noticia.tipo === 'imagen' && noticia.archivos.length > 1) {
                    inicializarCarrusel(noticia.id);
                }
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

// Función para crear una noticia con imágenes
function crearNoticiaConImagenes(noticia) {
    let noticiaHTML = `
        <div class="noticia-item" data-noticia-id="${noticia.id}">
            <div class="noticia-item-archivos">
                <div class="galeria-container">
                    <div class="galeria" id="galeria-${noticia.id}">`;
    
    // Crear contenedores para todas las imágenes
    noticia.archivos.forEach((archivo, index) => {
        noticiaHTML += `
            <div class="imagen-container ${index === 0 ? 'activa' : ''}">
                <img src="${archivo.url}" alt="${noticia.nombre}">
            </div>`;
    });
    
    // Agregar botones de navegación si hay más de una imagen
    if (noticia.archivos.length > 1) {
        noticiaHTML += `
            <div class="botones-navegacion">
                <button class="boton anterior" id="anterior-${noticia.id}">
                    <span class="material-icons">chevron_left</span>
                </button>
                <button class="boton siguiente" id="siguiente-${noticia.id}">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
            
            <div class="indicadores" id="indicadores-${noticia.id}">
                <!-- Los indicadores se generarán por JavaScript -->
            </div>`;
    }
    
    noticiaHTML += `
                    </div>
                </div>
            </div>
            <div class="noticia-item-informacion">
                <h1 class="noticia-item-titulo">${noticia.nombre}</h1>
                <p class="noticia-item-descipcion">${noticia.descripcion}</p>
            </div>
        </div>`;
    
    return noticiaHTML;
}

// Función para crear una noticia con video
function crearNoticiaConVideo(noticia) {
    // Asumimos que solo hay un archivo de video
    const videoUrl = noticia.archivos.length > 0 ? noticia.archivos[0].url : '';
    
    let noticiaHTML = `
        <div class="noticia-item" data-noticia-id="${noticia.id}">
            <div class="noticia-item-archivos">
                <div class="noticia-video-container">
                    <video width="250" controls>
                        <source src="${videoUrl}" type="video/mp4">
                        Your browser does not support HTML video.
                    </video>
                </div>
            </div>
            <div class="noticia-item-informacion">
                <h1 class="noticia-item-titulo">${noticia.nombre}</h1>
                <p class="noticia-item-descipcion">${noticia.descripcion}</p>
            </div>
        </div>`;
    
    return noticiaHTML;
}

// Función para inicializar el carrusel para una noticia específica
function inicializarCarrusel(noticiaId) {
    const galeria = document.getElementById(`galeria-${noticiaId}`);
    if (!galeria) return;
    
    const imagenes = galeria.querySelectorAll('.imagen-container');
    const indicadoresContainer = document.getElementById(`indicadores-${noticiaId}`);
    const btnAnterior = document.getElementById(`anterior-${noticiaId}`);
    const btnSiguiente = document.getElementById(`siguiente-${noticiaId}`);
    
    let indiceActual = 0;
    
    // Generar los indicadores
    imagenes.forEach((_, indice) => {
        const indicador = document.createElement('div');
        indicador.classList.add('indicador');
        if (indice === 0) {
            indicador.classList.add('activo');
        }
        indicador.addEventListener('click', () => {
            mostrarImagen(indice);
        });
        indicadoresContainer.appendChild(indicador);
    });
    
    const indicadores = indicadoresContainer.querySelectorAll('.indicador');
    
    function mostrarImagen(indice) {
        // Ocultar todas las imágenes
        imagenes.forEach(imagen => {
            imagen.classList.remove('activa');
        });
        
        // Desactivar todos los indicadores
        indicadores.forEach(indicador => {
            indicador.classList.remove('activo');
        });
        
        // Mostrar la imagen seleccionada
        imagenes[indice].classList.add('activa');
        
        // Activar el indicador seleccionado
        indicadores[indice].classList.add('activo');
        
        // Actualizar el índice actual
        indiceActual = indice;
    }
    
    // Evento para botón anterior
    btnAnterior.addEventListener('click', () => {
        indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
        mostrarImagen(indiceActual);
    });
    
    // Evento para botón siguiente
    btnSiguiente.addEventListener('click', () => {
        indiceActual = (indiceActual + 1) % imagenes.length;
        mostrarImagen(indiceActual);
    });
    
    // También permitir navegación con teclado para esta galería específica
    // (esto solo funciona bien si hay una galería en pantalla a la vez)
    document.addEventListener('keydown', (e) => {
        // Solo procesar si esta noticia está en el viewport
        const noticia = document.querySelector(`[data-noticia-id="${noticiaId}"]`);
        const rect = noticia.getBoundingClientRect();
        const isInViewport = rect.top >= 0 && rect.bottom <= window.innerHeight;
        
        if (isInViewport) {
            if (e.key === 'ArrowLeft') {
                indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
                mostrarImagen(indiceActual);
            } else if (e.key === 'ArrowRight') {
                indiceActual = (indiceActual + 1) % imagenes.length;
                mostrarImagen(indiceActual);
            }
        }
    });
}

// Llamar a la función para cargar las noticias cuando el documento esté listo
$(document).ready(function() {
    obtenerNoticiasParaFrontEndUsuario();
});