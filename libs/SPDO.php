<?php

class SPDO extends PDO
{

    private static $instance = null;

    private function __construct()
    {
        $config = Config::getInstance();
        // cargar datos referentes a la base de datos
        parent::__construct(
            'mysql:host=' . $config->get('dbhost') . ';dbname='
                . $config->get('dbname'),
            $config->get('dbuser'),
            $config->get('dbpass')
        );
    } // constructor

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    } // getInstance

} // fin clase
