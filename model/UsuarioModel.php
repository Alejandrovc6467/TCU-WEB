<?php

class UsuarioModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    
    public function obtenerUsuarios()
    {
        $consulta = $this->db->query('CALL sp_obtenerUsuarios()');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }


    public function insertarUsuario($nombre, $correo, $contrasena)
    {
        $consulta = $this->db->prepare('CALL sp_insertarUsuario(?, ?, ?)');
        $consulta->bindParam(1, $nombre);
        $consulta->bindParam(2, $correo);
        $consulta->bindParam(3, $contrasena);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }


    public function actualizarUsuario($id, $nombre, $correo, $contrasena)
    {
        $consulta = $this->db->prepare('CALL sp_actualizarUsuario(?, ?, ?, ?)');
        $consulta->bindParam(1, $id);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $correo);
        $consulta->bindParam(4, $contrasena);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }


    public function eliminarUsuario($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarUsuario(?)');
        $consulta->bindParam(1, $id);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    
}
