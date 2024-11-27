<?php
include('public/header.php');
?>



<?php

  if (isset($_SESSION['rol'])) { // Comprobar si la variable de sesión existe

   
    $nombre =  strtoupper($_SESSION['nombre']);
    $correo = $_SESSION['correo'];
    $rol = $_SESSION['rol'];
 
    echo "<h5 class=\"username\"><i class=\"fa-solid fa-circle-user\"></i> $nombre</h5>";
    echo "<p class=\"useremail\">$correo</p>";
    echo "<p class=\"useremail\">$rol</p>";
    
  }

?>




<div class="usuarios_container">

  <h1>Usuarios</h1>



  <div class="formulario-container">
    <p>formulario aqui</p>
  </div>

  
  <div class="header_fixed" id="containertabla">
    <table>
      <thead>
        <tr>
          <th>Acciones</th>
          <th>Nombre completo</th>
          <th>Correo</th>
          <th>Contraseña</th>
        </tr>
      </thead>
      <tbody>
        <!-- Aquí se llenarán los datos de los usuarios -->
      </tbody>
    </table>
  </div>

</div>











<script>
               
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
                                      .text("Editar")
                                      .addClass("btn btn-editar")
                                      .data("id", usuario.id)
                                      .on("click", function() {
                                      editarUsuario($(this).data("id"));
                                    });
                                      
                                    var eliminarBtn = $("<button>")
                                      .text("Eliminar")
                                      .addClass("btn btn-eliminar")
                                      .data("id", usuario.id)
                                      .on("click", function() {
                                      eliminarUsuario($(this).data("id"));
                                    });
                                      
                                      accionesCell.append(editarBtn).append(eliminarBtn);
                                      row.append(accionesCell);

                                    row.append($("<td>").text(usuario.nombre));
                                    row.append($("<td>").text(usuario.correo));
                                    row.append($("<td>").text(usuario.contrasena));

                                    $("#containertabla tbody").append(row);
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(error, xhr, status);
                        }
                    });
                }
                








  // probar todo esto, de aqui hacia abajo

  function editarUsuario(id) {

    console.log(id);

    /*
      $.ajax({
          type: "POST",
          url: "?controlador=AdministradorUsuarios&accion=obtenerUsuarioPorId",
          data: { id: id },
          dataType: "json",
          success: function(usuario) {
              // Abrir un modal o formulario para editar
              $("#nombreEditar").val(usuario.nombre);
              $("#correoEditar").val(usuario.correo);
              $("#idUsuario").val(usuario.id);
              $("#modalEditar").modal("show");
          },
          error: function(xhr, status, error) {
              console.log(error);
              alert("Error al obtener los datos del usuario");
          }
      });
      */
  }

  function guardarEdicion() {
      $.ajax({
          type: "POST",
          url: "?controlador=AdministradorUsuarios&accion=actualizarUsuario",
          data: {
              id: $("#idUsuario").val(),
              nombre: $("#nombreEditar").val(),
              correo: $("#correoEditar").val()
          },
          success: function(response) {
              $("#modalEditar").modal("hide");
              obtenerUsuarios(); // Recargar la tabla
              alert("Usuario actualizado exitosamente");
          },
          error: function(xhr, status, error) {
              console.log(error);
              alert("Error al actualizar el usuario");
          }
      });
  }

  function eliminarUsuario(id) {

    console.log(id);


    /*
      if (confirm("¿Estás seguro de que quieres eliminar este usuario?")) {
          $.ajax({
              type: "POST",
              url: "?controlador=AdministradorUsuarios&accion=eliminarUsuario",
              data: { id: id },
              success: function(response) {
                  obtenerUsuarios(); // Recargar la tabla
                  alert("Usuario eliminado exitosamente");
              },
              error: function(xhr, status, error) {
                  console.log(error);
                  alert("Error al eliminar el usuario");
              }
          });
      }
          */
  }


                


  

  obtenerUsuarios();

</script>





<?php
include('public/footer.php');
?>