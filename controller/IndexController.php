<?php
class IndexController
{
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }


    public function mostrar()
    {
        $this->view->show("indexView.php");
    }

    public function mostrarSobreNosotros()
    {
        $this->view->show("sobreNosotros.php");
    }

    public function mostarContacto()
    {
        $this->view->show("contacto.php");
    }

    public function mostrarlogin()
    {
        // Verificar si ya hay una sesión activa
        session_start();
        if (isset($_SESSION['rol'])) {
            // Si ya está logueado, redirigir según su rol
            if ($_SESSION['rol'] == "Admin") {
                return $this->view->show("usuarios.php");
            } else {
                return $this->view->show("actividades.php");
            }
        }
        // Si no hay sesión, mostrar login
        $this->view->show("login.php");
    }



    public function login()
    {

        if (isset($_POST['correo'])  &&  isset($_POST['contrasena'])) {

            require 'model/userModel.php';
            $userModel = new userModel();

            $roles = $userModel->login(
                $_POST['correo'],
                $_POST['contrasena']
            );

            if (empty($roles)) {
                $data['mensaje'] = 'Credenciales incorrectas';
                $this->view->show("login.php", $data);
                return;
            } else {

                session_start(); 

                foreach ($roles as $value) {
                    $nombre = $value[0];
                    $correo = $value[1];
                    $rol = $value[2];
                   
                }

                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['rol'] = $rol;
              

                if ($rol == "admin") {
                    return $this->view->show("usuarios.php");
                } else {
                    return  $this->view->show("actividades.php");
                }

            }

        } else {
            $data['mensaje'] = 'Inicia sesion desde el login para ingresar correctamente';
            return  $this->view->show("login.php", $data);
        }

    }



    public function cerrarSesion(){

        // Inicias la sesión si no está iniciada
        session_start();
        
        // Elimina todas las variables de sesión
        session_unset();

        // Destruye la sesión
        session_destroy();

        // Redirige al usuario a la página de inicio de sesión
        //return $this->view->show("login.php");
        header('Location: ?controlador=Index&accion=mostrarlogin');
        exit;

    }



   











    //todo esto de aqui hacia abajo borrarlo y ponerlo donde va, en cada parte respectiva


    public function subirdocumento()
    {
        // Verifica si se ha enviado una solicitud POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        


            
            //nuevo
            $extension_permitida = ['pdf', 'doc', 'docx', 'odt'];

            $archivo = $_FILES['archivo'];

            $archivo_info = pathinfo($archivo['name']);
            $archivo_extension = strtolower($archivo_info['extension']);

            if (in_array($archivo_extension, $extension_permitida)) {
               
                    
                //realizar operaciones de guardado, validación u otras acciones aquí.

                $nombreArchivo = $_FILES['archivo']['name'];
                $rutaTemporal = $_FILES['archivo']['tmp_name'];
                $directorioDestino = 'uploads/';
                    
                
                $nombreBase = pathinfo($nombreArchivo, PATHINFO_FILENAME);
                $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
                $nuevoNombre = $nombreBase;
                $contador = 1;
                    
                while (file_exists($directorioDestino . $nuevoNombre . '.' . $extension)) {
                    $nuevoNombre = $nombreBase . '_' . $contador;
                    $contador++;
                }
                    
                $nombreArchivo = $nuevoNombre . '.' . $extension;
                

            
                $rutaDestino = $directorioDestino . $nombreArchivo;
            
                if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                    

                    //una vez subido al servidor hago la insercion a la bd
                
                    //////////////////// guaradado en la base de datos
                    require 'model/userModel.php';
                    $userModel = new userModel();

                    $resultado = $userModel->guardarDocumentoBD(
                        $rutaDestino,
                        $_POST['email_user'],
                        $_POST['temadocumento']
                    );
                    //////////////////////// FIN guaradado en la base de datos

                    $mensaje = "1";
                
                } else {
                    $mensaje = "0";
                }



                // Despues de hacer todo el proceso de guardado prepararo una respuesta en formato JSON para enviarla al "subirdocumento.js" y el muestre la ventana de sweet Alert con el mensaje
               



                //echo 'El archivo se ha cargado exitosamente.';
            } else {
                $mensaje = "2";
                //echo 'El archivo no es de un tipo permitido (PDF, DOC o DOCX).';
            }
            //fin nuevo

            $response = array(
                'status' => 'success',
                'message' => $mensaje
            );
        
    
            // Envía la respuesta en formato JSON.
            header('Content-Type: application/json');
            echo json_encode($response);



        } else {
            // Manejo de solicitud no válida.
            $response = array(
                'status' => 'error',
                'message' => 'Ocurrio un error al enviar el documento, intente de nuevo'
            );
        
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        
      
    }// subirdocumento


  
  

} // fin clase
