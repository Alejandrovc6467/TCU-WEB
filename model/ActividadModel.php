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

    public function insertarActividad($url_archivo, $nombre, $descripcion, $id_usuario): array
    {
        $consulta = $this->db->query('CALL sp_insertarActividad( ?, ?, ?, ? )');
        $consulta->bindParam(1, $url_archivo);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $descripcion);
        $consulta->bindParam(4, $id_usuario);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

}
