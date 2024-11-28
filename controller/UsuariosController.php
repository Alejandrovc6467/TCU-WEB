<?php
class UsuariosController 
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

        // Verificar si el rol es "admin"
        if ($_SESSION['rol'] !== 'admin') {
            // Si no es admin, redirigir a una página de acceso denegado o similar
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
        $this->view->show("usuarios.php", $data);
    }





    //CRUD ******************************************************************

    
    public function obtenerUsuarios()
    {
        require 'model/UsuarioModel.php';
        $usuarioModel = new UsuarioModel();

        $lista = $usuarioModel->obtenerUsuarios();

        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }


    public function insertarUsuario()
    {
        require 'model/UsuarioModel.php';
        $usuarioModel = new UsuarioModel();

        $lista = $usuarioModel->insertarUsuario(
            $_POST['nombre'],
            $_POST['correo'],
            $_POST['contrasena']
        );
        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }


    public function actualizarUsuario()
    {
        require 'model/UsuarioModel.php';
        $usuarioModel = new UsuarioModel();

        $lista = $usuarioModel->actualizarUsuario(
            $_POST['id'],
            $_POST['nombre'],
            $_POST['correo'],
            $_POST['contrasena'],
        );
        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }
    

    public function eliminarUsuario()
    {
        require 'model/UsuarioModel.php';
        $usuarioModel = new UsuarioModel();

        $lista = $usuarioModel->eliminarUsuario(
            $_POST['id']
        );
        header('Content-Type: application/json');
        echo json_encode($lista);
        exit;
    }

    
}