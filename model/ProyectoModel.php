<?php

class ProyectoModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerProyectos()
    {
        $consulta = $this->db->query('CALL sp_obtenerProyectos()');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }






}
