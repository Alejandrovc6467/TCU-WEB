<?php

class Config
{

    private $vars;
    private static $instance;

    private function __construct()
    {
        $this->vars = array();
    } // constructor

    public function set($nombreAtributo, $valor)
    {
        if (!isset($this->vars[$nombreAtributo])) {
            $this->vars[$nombreAtributo] = $valor;
        }
    } // set generico

    public function get($nombreAtributo)
    {
        if (isset($this->vars[$nombreAtributo]))
            return $this->vars[$nombreAtributo];
    } // get generico

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $tmpClass = __CLASS__;
            self::$instance = new $tmpClass;
        }
        return self::$instance;
    } // getInstance

} // fin clase
