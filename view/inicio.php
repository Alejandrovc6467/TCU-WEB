
<div class="inicio">

  <div class="inicio-title-section">
    <h1>Bienvenidos al TC-768</h1>
    <p>Apoyo y Fortalecimiento del sector empresarial del cantón de Turrialba</p>
    <p>Gestionando tu empresa</p>
  </div>

  <img src="public/assets/home.webp" alt="TC Image">


  
  <h1 class="title-actividades">Actividades</h1>
  <p class="title-actividades-texto">Acontinuación se presenta actividades realizadas en el TCU</p>


  <div class="actividades">
    
    <!-- Esta actividad nunca se muestra la dejo aqui para guiarme para los estilos css -->
    <div class="image-container">
      <img src="public/assets/home.webp" alt="TC Image">
      <div class="image-overlay">
        <h3 class="image-title">Nombre</h3>
      </div>
      <button class="toggle-description-btn">Ver más</button>
      <div class="image-description" hidden>
        <h3>Nombre</h3>
        <p>Descripción aqui.</p>
      </div>
    </div> 

  </div>


  <div class="fullscreen-overlay">
    <div class="fullscreen-content">
      <button class="close-btn">X</button>
      <div class="contenedorImagenDescripcion">
        <img class="fullscreen-image" src="" alt="Fullscreen Image">
        <div class="fullscreen-description"></div>
      </div>
    </div>
  </div>



</div>





<script>
 
function obtenerActividadesParaInicio() {
    $.ajax({
        type: "POST",
        url: "?controlador=Actividades&accion=obtenerActividades",
        dataType: "json",
        success: function (response) {
            // Limpiar el contenedor de actividades
            $(".actividades").empty();

            // Recorrer la respuesta y agregar los datos al contenedor
            $.each(response, function (index, actividad) {
                // Crear el contenedor de imagen
                var imageContainer = $("<div>").addClass("image-container");
                
                // Crear la imagen
                var img = $("<img>")
                    .attr("src", actividad.url_archivo)
                    .attr("alt", "Actividad Image");
                
                // Crear la superposición de imagen
                var imageOverlay = $("<div>").addClass("image-overlay")
                    .append($("<h3>").addClass("image-title").text(actividad.nombre));
                
                // Crear botón de ver más
                var toggleBtn = $("<button>")
                    .addClass("toggle-description-btn")
                    .text("Ver más")
                    .on("click", function() {
                        // Find the closest image and description
                        const container = $(this).closest('.image-container');
                        const image = container.find('img');
                        const originalDescription = container.find('.image-description');
                        
                        // Set fullscreen elements
                        const fullscreenOverlay = $('.fullscreen-overlay');
                        const fullscreenImage = fullscreenOverlay.find('.fullscreen-image');
                        const fullscreenDescription = fullscreenOverlay.find('.fullscreen-description');

                        // Populate fullscreen elements
                        fullscreenImage.attr('src', image.attr('src'));
                        fullscreenImage.attr('alt', image.attr('alt'));
                        fullscreenDescription.html(originalDescription.html());

                        // Show fullscreen overlay
                        fullscreenOverlay.css('display', 'flex');
                    });
                
                // Crear descripción
                var imageDescription = $("<div>")
                    .addClass("image-description")
                    .attr("hidden", true)
                    .append($("<h3>").text(actividad.nombre))
                    .append($("<p>").text(actividad.descripcion));
                
                // Ensamblar todos los elementos
                imageContainer
                    .append(img)
                    .append(imageOverlay)
                    .append(toggleBtn)
                    .append(imageDescription);
                
                // Agregar al contenedor de actividades
                $(".actividades").append(imageContainer);
            });
        },
        error: function (xhr, status, error) {
            console.log(error, xhr, status);
        }
    });
}

// Añadir event listeners globales para el overlay
$(document).ready(function() {
    // Close button functionality
    $('.close-btn').on('click', function() {
        $('.fullscreen-overlay').css('display', 'none');
    });

    // Close overlay if clicked outside the image
    $('.fullscreen-overlay').on('click', function(event) {
        if (event.target === this) {
            $(this).css('display', 'none');
        }
    });
});

obtenerActividadesParaInicio();


</script>