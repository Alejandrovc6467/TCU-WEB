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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['rol'])) {
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
        ];

        $this->view->show("proyectos.php", $data);
    }

    /* CRUD ****************************************************************************************/

    public function obtenerProyectos()
    {
        require 'model/ProyectoModel.php';
        $model = new ProyectoModel();

        $proyectos = $model->obtenerProyectos();

        header('Content-Type: application/json');
        echo json_encode($proyectos);
        exit;
    }

    public function eliminarProyecto()
    {
        require 'model/ProyectoModel.php';
        $model = new ProyectoModel();

        $respuesta = $model->eliminarProyecto($_POST['id']);
        header('Content-Type: application/json');
        echo json_encode(['mensaje' => $respuesta]);
        exit;
    }

    public function agregarProyecto()
    {
        if (empty($_FILES['archivos']['name'][0])) {
            echo json_encode([["mensaje" => "No se recibieron imágenes."]]);
            exit;
        }

        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
            echo json_encode([["mensaje" => "Al menos una imagen tiene un formato no permitido."]]);
            exit;
        }

        $imagenes_guardadas = [];
        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);
            if ($this->subirImagen($tmp_name, $rutaDestino)) {
                $imagenes_guardadas[] = $rutaDestino;
            } else {
                echo json_encode([["mensaje" => "Error al subir la imagen: " . $_FILES['archivos']['name'][$key]]]);
                exit;
            }
        }

        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        $id_usuario = $_SESSION['id'];

        require 'model/ProyectoModel.php';
        $model = new ProyectoModel();

        $respuesta = $model->insertarProyecto(
            $_POST['nombre'],
            $_POST['descripcion'],
            $id_usuario,
            $imagenes_guardadas
        );

        header('Content-Type: application/json');
        echo json_encode([['mensaje' => $respuesta]]);
        exit;
    }

    public function actualizarProyectoSinNuevasImagenes()
    {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        require 'model/ProyectoModel.php';
        $model = new ProyectoModel();

        $respuesta = $model->actualizarProyectoSinImagenes(
            $_POST['id'],
            $_POST['nombre'],
            $_POST['descripcion'],
            $_SESSION['id']
        );

        header('Content-Type: application/json');
        echo json_encode([['mensaje' => $respuesta]]);
        exit;
    }

    public function actualizarProyectoConNuevasImagenes()
    {
        if (empty($_FILES['archivos']['name'][0])) {
            echo json_encode([["mensaje" => "No se recibieron imágenes."]]);
            exit;
        }

        $extensiones_permitidas = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

        if (!$this->verificarExtensionesPermitidas($_FILES['archivos'], $extensiones_permitidas)) {
            echo json_encode([["mensaje" => "Al menos una imagen tiene un formato no permitido."]]);
            exit;
        }

        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode([["mensaje" => "Error: Usuario no autenticado."]]);
            exit;
        }

        $imagenes_guardadas = [];
        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            $rutaDestino = $this->definirNombreDeArchivoUnico($_FILES['archivos']['name'][$key]);
            if ($this->subirImagen($tmp_name, $rutaDestino)) {
                $imagenes_guardadas[] = $rutaDestino;
            } else {
                echo json_encode([["mensaje" => "Error al subir la imagen: " . $_FILES['archivos']['name'][$key]]]);
                exit;
            }
        }

        require 'model/ProyectoModel.php';
        $model = new ProyectoModel();

        $respuesta = $model->actualizarProyectoConNuevasImagenes(
            $_POST['id'],
            $_POST['nombre'],
            $_POST['descripcion'],
            $_SESSION['id'],
            $imagenes_guardadas
        );

        header('Content-Type: application/json');
        echo json_encode([['mensaje' => $respuesta]]);
        exit;
    }

    /* FUNCIONES COMPLEMENTARIAS ******************************************************************/

    private function verificarExtensionesPermitidas($archivos, $extensiones_permitidas)
    {
        foreach ($archivos['name'] as $key => $nombreArchivo) {
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            if (!in_array($extension, $extensiones_permitidas)) {
                return false;
            }
        }
        return true;
    }

    private function definirNombreDeArchivoUnico($nombreArchivo)
    {
        $directorioDestino = 'uploads/';
        $nombreBase = pathinfo($nombreArchivo, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

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
        return move_uploaded_file($rutaTemporal, $rutaDestino);
    }
}