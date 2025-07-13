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
        $consulta = $this->db->prepare('SELECT id, nombre, correo, rol FROM usuario WHERE correo = ? AND contrasena = ? AND eliminado = 0;');
        $consulta->execute([$correo, $contrasena]);
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

}
