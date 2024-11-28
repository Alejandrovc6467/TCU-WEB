<?php

class ActividadModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerActividades()
    {
        $consulta = $this->db->query('CALL sp_obtenerActividades()');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }






}
