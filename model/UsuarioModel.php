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



























    public function insertarProfesores($cedula, $nombre, $apellido1, $apellido2)
    {
        $consulta = $this->db->prepare('CALL insertarProfesor(?, ?, ?, ?)');
        $consulta->bindParam(1, $cedula);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $apellido1);
        $consulta->bindParam(4, $apellido2);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    public function actualizarProfesor($cedula, $nombre, $apellido1, $apellido2)
    {
        $consulta = $this->db->prepare('CALL actualizarProfesor(?, ?, ?, ?)');
        $consulta->bindParam(1, $cedula);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $apellido1);
        $consulta->bindParam(4, $apellido2);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }


    public function eliminarProfesor($cedula)
    {
        $consulta = $this->db->prepare('CALL eliminarProfesor(?)');
        $consulta->bindParam(1, $cedula);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    public function buscarProfesores($profesorAbuscar)
    {
        $consulta = $this->db->prepare('CALL buscarProfesores(?)');
        $consulta->bindParam(1, $profesorAbuscar);
      
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }
    
}
