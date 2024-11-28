<?php

class userModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function login($correo, $contrasena)
    {
        $consulta = $this->db->prepare('call sp_loginUsuario(?, ?)');
        $consulta->bindParam(1, $correo);
        $consulta->bindParam(2, $contrasena);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

   
   


}
