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

    public function mostrarNoticias()
    {
        $this->view->show("noticias.php");
    }

    public function mostrarHerramientas()
    {
        $this->view->show("herramientas.php");
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

            require 'model/LoginModel.php';
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
                    $id = $value[0];
                    $nombre = $value[1];
                    $correo = $value[2];
                    $rol = $value[3];
                   
                }

                $_SESSION['id'] = $id;
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



  

} // fin clase
