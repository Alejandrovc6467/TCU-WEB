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

   


}