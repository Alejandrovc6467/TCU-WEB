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

    public function insertarActividad($url_archivo, $nombre, $descripcion, $id_usuario)
    {
        $consulta = $this->db->prepare('CALL sp_insertarActividad( ?, ?, ?, ? )');
        $consulta->bindParam(1, $url_archivo);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $descripcion);
        $consulta->bindParam(4, $id_usuario);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    public function actualizarActividad($id, $url_archivo, $nombre, $descripcion, $id_usuario)
    {
        $consulta = $this->db->prepare('CALL sp_actualizarActividad( ?, ?, ?, ?, ? )');
        $consulta->bindParam(1, $id);
        $consulta->bindParam(2, $url_archivo, $url_archivo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $consulta->bindParam(3, $nombre);
        $consulta->bindParam(4, var: $descripcion);
        $consulta->bindParam(5, $id_usuario);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    public function eliminarActividad($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarActividad( ? )');
        $consulta->bindParam(1, $id);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

}
