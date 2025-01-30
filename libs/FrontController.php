<?php

class FrontController
{

    public static function main()
    {
        require 'libs/View.php';
        require 'libs/configuration.php';

        // Obtener la ruta actual
        $request_uri = $_SERVER['REQUEST_URI'];
        $base_path = '/TCU-WEB/';
        
        // Verificar si la URL termina en 'admin'
        if (strpos($request_uri, $base_path . 'admin') !== false) {
            header('Location: ' . $base_path . '?controlador=Index&accion=mostrarlogin');
            exit;
        }


        if (!empty($_GET['controlador']))
            $controllerName = $_GET['controlador'] . 'Controller';
        else
            $controllerName = 'IndexController';

        if (!empty($_GET['accion']))
            $nombreAccion = $_GET['accion'];
        else
            $nombreAccion = 'mostrar';

        $rutaContralador = $config->get('controllerFolder') . $controllerName . '.php';

        if (is_file($rutaContralador))
            require $rutaContralador;
        else
            die('Controlador no encontrado - 404 not found');

        if (!is_callable(array($controllerName, $nombreAccion)) == FALSE) { // cabia el signo si es devserver y ya 
            trigger_error($controllerName . '-' . $nombreAccion . ' no existe', E_USER_NOTICE);
            return FALSE;
        }

        $controller = new $controllerName();
        $controller->$nombreAccion();
    } // main

} // fin clase
