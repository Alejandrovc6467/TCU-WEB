<?php
class ProyectosController
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

        // Mostrar la vista de actividades
        $this->view->show("proyectos.php", $data);
    }

    /* CRUD *****************************************/

    public function obtenerProyectos()
    {
        require 'model/ProyectoModel.php';
        $proyectoModel = new ProyectoModel();

        $lista = $proyectoModel->obtenerProyectos();

        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }

    public function agregarProyecto()
    {
        // Verificar si se recibieron archivos
        if (empty($_FILES['archivos']['name'][0])) {
            echo json_encode([["mensaje" => "No se recibieron imágenes."]]);
            exit;
        }

        // Extensiones permitidas
        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        // Verificar extensiones antes de procesar archivos
        if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
            echo json_encode([["mensaje" => "Al menos una imagen tiene un formato no permitido."]]);
            exit;
        }

        $imagenes_guardadas = [];
        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            // Obtener nombre único para el archivo
            $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);

            // Intentar mover el archivo a la ruta de destino
            if ($this->subirImagen($tmp_name, $rutaDestino)) {
                $imagenes_guardadas[] = $rutaDestino;
            } else {
                echo json_encode([["mensaje" => "Error al subir la imagen: " . $_FILES['archivos']['name'][$key]]]);
                exit;
            }
        }

        // Verificar sesión activa
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        $id_usuario = $_SESSION['id'];

        // Insertar el proyecto en la base de datos con imágenes
        $respuesta = $this->insertarProyecto($_POST['nombre'], $_POST['descripcion'], $id_usuario, $imagenes_guardadas);

        $respuesta = [
            [
                'mensaje' => $respuesta,
                '0' => $respuesta
            ]
        ];

        //se devuelve el contenido de la respuesta como un json
        header('Content-Type: application/json');
        // Responder con éxito o error
        echo json_encode($respuesta);
        exit;
    }

    public function insertarProyecto($nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        require 'model/ProyectoModel.php';
        $proyectoModel = new ProyectoModel();

        //se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $proyectoModel->insertarProyecto(
            $nombre,
            $descripcion,
            $id_usuario,
            $imagenes_guardadas
        );

        return $respuesta;
    }

    public function modificarProyecto()
    {
        // Verificar si la sesión está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Usuario no autenticado."]]);
            exit;
        }
        $id_usuario = $_SESSION['id'];

        // Verificar si se recibió el ID del proyecto
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode([["mensaje" => "ID del proyecto no especificado."]]);
            exit;
        }
        $id_proyecto = $_POST['id'];

        // Obtener nombre y descripción
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        // Extensiones permitidas para imágenes
        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        // Procesar imágenes si se enviaron
        $imagenes_guardadas = [];
        if (!empty($_FILES['archivos']['name'][0])) {
            if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
                echo json_encode([["mensaje" => "Al menos una imagen tiene un formato no permitido."]]);
                exit;
            }

            foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
                // Obtener nombre único para el archivo
                $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);

                // Intentar mover el archivo a la ruta de destino
                if ($this->subirImagen($tmp_name, $rutaDestino)) {
                    $imagenes_guardadas[] = $rutaDestino;
                } else {
                    echo json_encode([["mensaje" => "Error al subir la imagen: " . $_FILES['archivos']['name'][$key]]]);
                    exit;
                }
            }
        }

        // Llamar al método del modelo para actualizar el proyecto
        $respuesta = $this->actualizarProyecto($id_proyecto, $nombre, $descripcion, $id_usuario, $imagenes_guardadas);

        $respuesta = [
            [
                'mensaje' => $respuesta,
                '0' => $respuesta
            ]
        ];

        //se devuelve el contenido de la respuesta como un json
        header('Content-Type: application/json');
        // Responder con éxito o error
        echo json_encode($respuesta);
        exit;
    }

    public function actualizarProyecto($id, $nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        require 'model/ProyectoModel.php';
        $proyectoModel = new ProyectoModel();

        //se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $proyectoModel->actualizarProyecto(
            $id,
            $nombre,
            $descripcion,
            $id_usuario,
            $imagenes_guardadas
        );

        return $respuesta;
    }

    public function eliminarProyecto()
    {
        require 'model/ProyectoModel.php';
        $proyectoModel = new ProyectoModel();

        // se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $proyectoModel->eliminarProyecto(
            $_POST['id']
        );

        // se retorna el resultado de la operacion
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    /* METODOS COMPLEMENTARIOS *****************************************/

    private function subirImagen($rutaTemporal, $rutaDestino)
    {
        // se sube la imagen a la ruta definida
        return move_uploaded_file($rutaTemporal, $rutaDestino);
    }

    private function definirNombreDeArchivoUnico($nombreArchivo)
    {
        $directorioDestino = 'uploads/';

        // Extraer información del archivo
        $nombreBase = pathinfo($nombreArchivo, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        // Generar un nombre único
        $nuevoNombre = $nombreBase;
        $contador = 1;

        while (file_exists($directorioDestino . $nuevoNombre . '.' . $extension)) {
            $nuevoNombre = $nombreBase . '_' . $contador;
            $contador++;
        }

        return $directorioDestino . $nuevoNombre . '.' . $extension;
    }

    private function verificarExtensionesPermitidas($archivos, $extensiones_permitidas)
    {
        foreach ($archivos['name'] as $key => $nombreArchivo) {
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            if (!in_array($extension, $extensiones_permitidas)) {
                return false; // Retorna falso si alguna extensión no está permitida
            }
        }
        return true; // Si todas las imágenes son válidas, retorna true
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