// Llamado de la funcion para cargar las herramientas el frontend de usuario normal
function obtenerHerramientasParaFrontEndUsuario() {

    $.ajax({
        type: "POST",
        url: "?controlador=Herramientas&accion=obtenerHerramientas",
        dataType: "json",
        success: function (response) {
            // Limpiar el contenedor de herramientas
            $('.herramientas-items').empty();

            // Recorrer todas las herramientas recibidas
            response.forEach(function (herramienta) {
                // Crear elemento HTML para la herramienta
                let herramientaHTML = '';

                // Verificar el tipo de herramienta (imagen o video)
                if (herramienta.tipo === 'imagen') {
                    // Crear estructura para herramienta con imágenes
                    herramientaHTML = crearHerramientaConImagenes(herramienta);
                } else if (herramienta.tipo === 'video') {
                    // Crear estructura para herramienta con video
                    herramientaHTML = crearHerramientaConVideo(herramienta);
                }

                // Agregar la herramienta al contenedor
                $('.herramientas-items').append(herramientaHTML);

                // Inicializar el carrusel para esta herramienta si es de tipo imagen
                if (herramienta.tipo === 'imagen' && herramienta.archivos.length > 1) {
                    inicializarCarrusel(herramienta.id);
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar las herramientas',
                confirmButtonColor: '#088cff'
            });
        }
    });
}

// Función para crear una herramienta con imágenes
function crearHerramientaConImagenes(herramienta) {
    let herramientaHTML = `
        <div class="herramienta-item" data-herramienta-id="${herramienta.id}">
            <div class="herramienta-item-archivos">
                <div class="galeria-container">
                    <div class="galeria" id="galeria-${herramienta.id}">`;

    // Crear contenedores para todas las imágenes
    herramienta.archivos.forEach((archivo, index) => {
        herramientaHTML += `
            <div class="imagen-container ${index === 0 ? 'activa' : ''}">
                <img src="${archivo.url}" alt="${herramienta.nombre}">
            </div>`;
    });

    // Agregar botones de navegación si hay más de una imagen
    if (herramienta.archivos.length > 1) {
        herramientaHTML += `
            <div class="botones-navegacion">
                <button class="boton anterior" id="anterior-${herramienta.id}">
                    <span class="material-icons">chevron_left</span>
                </button>
                <button class="boton siguiente" id="siguiente-${herramienta.id}">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
            
            <div class="indicadores" id="indicadores-${herramienta.id}">
                <!-- Los indicadores se generarán por JavaScript -->
            </div>`;
    }

    herramientaHTML += `
                    </div>
                </div>
            </div>
            <div class="herramienta-item-informacion">
                <h1 class="herramienta-item-titulo">${herramienta.nombre}</h1>
                <p class="herramienta-item-descipcion">${herramienta.descripcion}</p>
            </div>
        </div>`;

    return herramientaHTML;
}

// Función para crear una herramienta con video
function crearHerramientaConVideo(herramienta) {
    // Asumimos que solo hay un archivo de video
    const videoUrl = herramienta.archivos.length > 0 ? herramienta.archivos[0].url : '';

    let herramientaHTML = `
        <div class="herramienta-item" data-herramienta-id="${herramienta.id}">
            <div class="herramienta-item-archivos">
                <div class="herramienta-video-container">
                    <video width="250" controls preload="metadata">
                        <source src="${videoUrl}" type="video/mp4">
                        Your browser does not support HTML video.
                    </video>
                </div>
            </div>
            <div class="herramienta-item-informacion">
                <h1 class="herramienta-item-titulo">${herramienta.nombre}</h1>
                <p class="herramienta-item-descipcion">${herramienta.descripcion}</p>
            </div>
        </div>`;

    return herramientaHTML;
}

// Función para inicializar el carrusel para una herramienta específica
function inicializarCarrusel(herramientaId) {
    const galeria = document.getElementById(`galeria-${herramientaId}`);
    if (!galeria) return;

    const imagenes = galeria.querySelectorAll('.imagen-container');
    const indicadoresContainer = document.getElementById(`indicadores-${herramientaId}`);
    const btnAnterior = document.getElementById(`anterior-${herramientaId}`);
    const btnSiguiente = document.getElementById(`siguiente-${herramientaId}`);

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
        // Solo procesar si esta herramienta está en el viewport
        const herramienta = document.querySelector(`[data-herramienta-id="${herramientaId}"]`);
        const rect = herramienta.getBoundingClientRect();
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

// Llamar a la función para cargar las herramientas cuando el documento esté listo
$(document).ready(function () {
    obtenerHerramientasParaFrontEndUsuario();
});