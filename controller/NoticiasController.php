<?php
class NoticiasController
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

        $this->view->show("noticiasAdministrador.php", $data);
    }





    /* Todo esto de aqui hacia abajo no me sirve, solo el metodo subir, verlo y json que se envia***************** */
    /* CRUD *****************************************/

    
    public function obtenerNoticias()
    {
        require 'model/NoticiaModel.php';
        $noticiaModel = new NoticiaModel();

        $lista = $noticiaModel->obtenerNoticias();

        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }


    public function eliminarNoticia()
    {
        require 'model/NoticiaModel.php';
        $noticiaModel = new NoticiaModel();
    
        $respuesta = $noticiaModel->eliminarNoticia($_POST['id']);
    
        // Extraer mensaje (asumiendo que es lo único que retorna el SP)
        $mensaje = isset($respuesta[0]['mensaje']) ? $respuesta[0]['mensaje'] : 'Operación finalizada.';
    
        header('Content-Type: application/json');
        echo json_encode(['mensaje' => $mensaje]);
        exit;
    }
    



























    public function obtenerActividad()
    {
        require 'model/ActividadModel.php';
        $actividadModel = new ActividadModel();

        $lista = $actividadModel->obtenerActividad($_POST['id']);

        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }
    public function agregarActividad()
    {
        // se extrae el archivo enviado
        $archivo = $_FILES['archivo'];

        // extenciones permitidas
        $extension_permitida = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        //respuesta en caso de un fromato de extencion invalido
        $respuesta = [
            [
                "mensaje" => 'Ocurrió un error, la extencion de la imagen no es valida, los formatos validos son: ' . implode(', ', $extension_permitida) . ".",
                "0" => 'Ocurrió un error, la extencion de la imagen no es valida, los formatos validos son: ' . implode(', ', $extension_permitida) . "."
            ]
        ];

        if ($this->verificarExtencionesPermitidas($archivo, $extension_permitida)) {

            // se extrae la ruta temporal del archivo
            $rutaTemporal = $archivo['tmp_name'];

            // se define la nueva ruta del archivo 
            $rutaDestino = $this->definirNombreDeArchivoUnico($archivo);

            //verificar que se pueda subir el documento correctamente
            if ($this->subirImagen($rutaTemporal, $rutaDestino)) {

                //verificar si la sescion esta iniciada para tomar el id de usuario
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $id_usuario = $_SESSION['id'];
                if (!isset($_SESSION['id'])) {
                    die("Error: ID de usuario no está configurado en la sesión.");
                }

                $respuesta = $this->insertarActividad(
                    $rutaDestino,
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $id_usuario
                );

            } else {
                $respuesta = [
                    [
                        'message' => 'Ocurrió un error, al enviar el documento, intente de nuevo.',
                        '0' => 'Ocurrió un error, al enviar el documento, intente de nuevo.'
                    ]
                ];
            }

        }

        //se devuelve el contenido de la respuesta como un json
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    public function insertarActividad($url_archivo, $nombre, $descripcion, $id_usuario)
    {
        require 'model/ActividadModel.php';
        $actividadModel = new ActividadModel();

        //se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $actividadModel->insertarActividad(
            $url_archivo,
            $nombre,
            $descripcion,
            $id_usuario
        );

        return $respuesta;
    }

    public function modificarActividad()
    {
        // verifica que exita un archivo para actualizar adjunto en el formulario
        if (isset($_FILES['archivo'])) {

            // se extrae el archivo enviado
            $archivo = $_FILES['archivo'];

            // extenciones permitidas
            $extension_permitida = ['png', 'jpg', 'jpeg', 'svg'];

            //respuesta en caso de un fromato de extencion invalido
            $respuesta = [
                [
                    "mensaje" => 'Ocurrió un error, la extencion de la imagen no es valida, los formatos validos son: ' . implode(', ', $extension_permitida) . ".",
                    "0" => 'Ocurrió un error, la extencion de la imagen no es valida, los formatos validos son: ' . implode(', ', $extension_permitida) . "."
                ]
            ];

            if ($this->verificarExtencionesPermitidas($archivo, $extension_permitida)) {

                // se extrae la ruta temporal del archivo
                $rutaTemporal = $archivo['tmp_name'];

                // se define la nueva ruta del archivo 
                $rutaDestino = $this->definirNombreDeArchivoUnico($archivo);

                //verificar que se pueda subir el documento correctamente
                if ($this->subirImagen($rutaTemporal, $rutaDestino)) {

                    //verificar si la sescion esta iniciada para tomar el id de usuario
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $id_usuario = $_SESSION['id'];
                    if (!isset($_SESSION['id'])) {
                        die("Error: ID de usuario no está configurado en la sesión.");
                    }

                    $respuesta = $this->actualizarActividad(
                        $_POST['id'],
                        $rutaDestino,
                        $_POST['nombre'],
                        $_POST['descripcion'],
                        $id_usuario
                    );

                } else {
                    $respuesta = [
                        [
                            'message' => 'Ocurrió un error, al enviar el documento, intente de nuevo.',
                            '0' => 'Ocurrió un error, al enviar el documento, intente de nuevo.'
                        ]
                    ];
                }

            }

            //se devuelve el contenido de la respuesta como un json
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            exit;

        } else {
            //verificar si la sescion esta iniciada para tomar el id de usuario
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $id_usuario = $_SESSION['id'];
            if (!isset($_SESSION['id'])) {
                die("Error: ID de usuario no está configurado en la sesión.");
            }

            $respuesta = $this->actualizarActividad(
                $_POST['id'],
                null,
                $_POST['nombre'],
                $_POST['descripcion'],
                $id_usuario
            );

            //se devuelve el contenido de la respuesta como un json
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            exit;

        }

    }

    public function actualizarActividad($id, $url_archivo, $nombre, $descripcion, $id_usuario)
    {
        require 'model/ActividadModel.php';
        $actividadModel = new ActividadModel();

        //se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $actividadModel->actualizarActividad(
            $id,
            $url_archivo,
            $nombre,
            $descripcion,
            $id_usuario
        );

        return $respuesta;
    }

    public function eliminarActividad()
    {
        require 'model/ActividadModel.php';
        $actividadModel = new ActividadModel();

        // se ejecuta el metodo para guardar el usuario en base de datos
        $respuesta = $actividadModel->eliminarActividad(
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

    private function definirNombreDeArchivoUnico($archivo)
    {
        // extraccion de datos necesarios para la operacion
        $nombreArchivo = $archivo['name'];
        $directorioDestino = 'uploads/';

        /* 
            varibles necesarias para asegurar que el archivo tenga un nombre unico
            en caso de estar repetido al nombre se le agregar un numero que incrementara 
            por la cantidad de copias del mismo archivo
        */
        $nombreBase = pathinfo($nombreArchivo, PATHINFO_FILENAME);
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nuevoNombre = $nombreBase;
        $contador = 1;

        // este while busca entre los archivos presentes en la direccion definida
        while (file_exists($directorioDestino . $nuevoNombre . '.' . $extension)) {
            $nuevoNombre = $nombreBase . '_' . $contador; //nuevo nombre aumenta su numero en caso de ya existir un archivo repetido
            $contador++;
        }

        // estblece el nombre nuevo del archivo
        $nombreArchivo = $nuevoNombre . '.' . $extension;

        return $directorioDestino . $nombreArchivo;
    }

    private function verificarExtencionesPermitidas($archivo, $extension_permitida)
    {
        // extraccion de la extencion del archivo
        $archivo_info = pathinfo($archivo['name']);
        $archivo_extension = strtolower($archivo_info['extension']);

        return in_array($archivo_extension, $extension_permitida);
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