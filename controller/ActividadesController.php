<?php
class ActividadesController 
{
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    private function verificarAutenticacion()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar si el usuario está logueado
        if (!isset($_SESSION['rol'])) {
            // Si no está logueado, redirigir al login
            header('Location: ?controlador=Index&accion=mostrarlogin');
            exit;
        }
    }

    public function mostrar()
    {
        $this->verificarAutenticacion();

        $data = [
            'nombre' => $_SESSION['nombre'],
            'rol' => $_SESSION['rol']
            // Puedes agregar más datos según necesites
        ];

        $this->view->show("actividades.php", $data);
    }

    /* CRUD *****************************************/

    public function insertarActividad()
    {
        require 'model/ActividadModel.php';
        $actividadModel = new ActividadModel();

        //se extrae la infromacion de la solicitud
        $respuesta = $actividadModel->insertarActividad(
            'url prueba',
            $_POST['nombre'],
            $_POST['descripcion'],
            1
        );

        //se devuelve el contenido de la respuesta como un json
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    private function subirdocumento()
    {
        // Verifica si se ha enviado una solicitud POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $extension_permitida = ['png', 'jpg', 'jpeg', 'svg'];
            
            //se extrae el archivo enviado
            $archivo = $_FILES['archivo'];

            //extraccion de la informacion general del archivo
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
    }

}