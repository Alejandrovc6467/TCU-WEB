<?php
class HerramientasController
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

        $this->view->show("herramientasAdministrador.php", $data);
    }

    /* CRUD ****************************************************************************************/

    public function obtenerHerramientas()
    {
        require 'model/HerramientaModel.php';
        $herramientaModel = new HerramientaModel();

        $lista = $herramientaModel->obtenerHerramientas();

        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }

    public function eliminarHerramienta()
    {
        require 'model/HerramientaModel.php';
        $herramientaModel = new HerramientaModel();
    
        $respuesta = $herramientaModel->eliminarHerramienta($_POST['id']);
    
        // Extraer mensaje (asumiendo que es lo único que retorna el SP)
        $mensaje = isset($respuesta[0]['mensaje']) ? $respuesta[0]['mensaje'] : 'Operación finalizada.';
    
        header('Content-Type: application/json');
        echo json_encode(['mensaje' => $mensaje]);
        exit;
    }
    
    public function agregarHerramienta()
    {
        // Verificar si se recibieron archivos
        if (empty($_FILES['archivos']['name'][0])) {
            echo json_encode([["mensaje" => "No se recibieron archivos."]]);
            exit;
        }

        // Extensiones permitidas
        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp', 'mp4', 'avi', 'mkv'];

        // Verificar extensiones antes de procesar archivos
        if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
            echo json_encode([["mensaje" => "Al menos un archivo tiene un formato no permitido."]]);
            exit;
        }

        $archivos_guardados = [];
        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            // Obtener nombre único para el archivo
            $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);

            // Intentar mover el archivo a la ruta de destino
            if ($this->subirImagen($tmp_name, $rutaDestino)) {
                $archivos_guardados[] = $rutaDestino;
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

        // Insertar la herramienta en la base de datos con los archivos subidos
        $respuesta = $this->insertarHerramienta($_POST['nombre'], $_POST['descripcion'], $_POST['tipo'],  $id_usuario, $archivos_guardados);

        $respuesta = [[ 'mensaje' => $respuesta ]];// quitar esto variable de prueba

        //se devuelve el contenido de la respuesta como un json
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    public function insertarHerramienta($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        require 'model/HerramientaModel.php';
        $herramientaModel = new HerramientaModel();

        //se ejecuta el metodo para guardar la herramienta en base de datos
        $respuesta = $herramientaModel->insertarHerramienta(
            $nombre,
            $descripcion,
            $tipo,
            $id_usuario,
            $archivos_guardados
        );

        return $respuesta;
    }

    public function actualizarHerramientaSinNuevosArchivos()
    {
       
        require 'model/HerramientaModel.php';
        $herramientaModel = new HerramientaModel();

        // Verificar sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        $id_usuario = $_SESSION['id'];

        $respuesta = $herramientaModel->actualizarHerramientaSinNuevosArchivos(
            $_POST['id'],
            $_POST['nombre'],
            $_POST['descripcion'],
            $id_usuario 
        );
        
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    public function actualizarHerramientaConNuevosArchivos()
    {

        // Verificar si se recibieron archivos
        if (empty($_FILES['archivos']['name'][0])) {
            echo json_encode([["mensaje" => "No se recibieron archivos."]]);
            exit;
        }

        // Extensiones permitidas
        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp', 'mp4', 'avi', 'mkv'];

        // Verificar extensiones antes de procesar archivos
        if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
            echo json_encode([["mensaje" => "Al menos un archivo tiene un formato no permitido."]]);
            exit;
        }

        // Verificar sesión activa
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        $id_usuario = $_SESSION['id'];

        $archivos_guardados = [];
        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            // Obtener nombre único para el archivo
            $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);

            // Intentar mover el archivo a la ruta de destino
            if ($this->subirImagen($tmp_name, $rutaDestino)) {
                $archivos_guardados[] = $rutaDestino;
            } else {
                echo json_encode([["mensaje" => "Error al subir la imagen: " . $_FILES['archivos']['name'][$key]]]);
                exit;
            }
        }

        // Convertir array de rutas a string separado por comas
        $urls_concatenadas = implode(',', $archivos_guardados);

        // Actualizar la herramienta en la base de datos con los archivos subidos
        $respuesta = $this->insertarActualizacionDeHerramientaConNuevosArchivos($_POST['id'], $_POST['nombre'], $_POST['descripcion'], $id_usuario, $urls_concatenadas);

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    private function insertarActualizacionDeHerramientaConNuevosArchivos($id, $nombre, $descripcion, $id_usuario, $urls_concatenadas)
    {
        require 'model/HerramientaModel.php';
        $herramientaModel = new HerramientaModel();

        //se ejecuta el metodo para actualizar la herramienta en base de datos
        $respuesta = $herramientaModel->actualizarHerramientaConNuevosArchivos(
            $id,
            $nombre,
            $descripcion,
            $id_usuario,
            $urls_concatenadas
        );

        return $respuesta;
    }

    /* Funciones complementarias ******************************************************************/

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

    private function subirImagen($rutaTemporal, $rutaDestino)
    {
        // se sube la imagen a la ruta definida
        return move_uploaded_file($rutaTemporal, $rutaDestino);
    }

}