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

   
   
    
    
    public function obtenerUsuarios()
    {
        $consulta = $this->db->query('CALL ObtenerUsuarios()');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }
    

    public function actualizarRolUsuario($email, $rol)
    {
        $consulta = $this->db->prepare('CALL ActualizarRolUsuario(?, ?)');
        $consulta->bindParam(1, $email);
        $consulta->bindParam(2, $rol);
        $consulta->execute();
        $consulta->closeCursor();
    }

    public function actualizarRol($email, $nuevoRol)
    {
        $consulta = $this->db->prepare('CALL UpdateUserRole(?, ?)');
        $consulta->bindParam(1, $email, PDO::PARAM_STR);
        $consulta->bindParam(2, $nuevoRol, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }

  

    public function actualizarPago($email, $nuevoEstadoPago)
    {
        $consulta = $this->db->prepare('CALL actualizarPagoDocumento(?, ?)');
        $consulta->bindParam(1, $email, PDO::PARAM_STR);
        $consulta->bindParam(2, $nuevoEstadoPago, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }


    public function actualizarAprobacion($email, $nuevoEstadoAprobacion)
    {
        $consulta = $this->db->prepare('CALL actualizarAprobacionDocumento(?, ?)');
        $consulta->bindParam(1, $email, PDO::PARAM_STR);
        $consulta->bindParam(2, $nuevoEstadoAprobacion, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }




    
    public function guardarDocumentoBD($url_documento, $email_user, $temadocumento)
    {
        $consulta = $this->db->prepare('call InsertarDocumento(?,?,?)');
        $consulta->bindParam(1, $url_documento);
        $consulta->bindParam(2, $email_user);
        $consulta->bindParam(3, $temadocumento);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    

    public function ObtenerInformacionDocumento($temadocumento)
    {
        $consulta = $this->db->prepare('CALL ObtenerInformacionDocumento(?)');
        $consulta->bindParam(1, $temadocumento, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }


    

    public function actualizarDocumentoEstado($id, $aprobado)
    {
        $consulta = $this->db->prepare('CALL actualizarDocumentoEstado(?, ?)');
        $consulta->bindParam(1, $id, PDO::PARAM_STR);
        $consulta->bindParam(2, $aprobado, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }

    public function actualizarTemaDocumento($id_documento, $nuevo_tema)
    {
        $consulta = $this->db->prepare('CALL actualizar_tema_documento(?, ?)');
        $consulta->bindParam(1, $id_documento, PDO::PARAM_STR);
        $consulta->bindParam(2, $nuevo_tema, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }
 


 

    public function obtenerParticipacionPremio($user_email)
    {
        $consulta = $this->db->prepare('CALL obtenerParticipacionPremio(?)');
        $consulta->bindParam(1, $user_email, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }




    public function actualizarParticipacionPremio($email, $nuevoEstadoPremio)
    {
        $consulta = $this->db->prepare('CALL actualizarParticipacionPremio(?, ?)');
        $consulta->bindParam(1, $email, PDO::PARAM_STR);
        $consulta->bindParam(2, $nuevoEstadoPremio, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Cerrar la consulta
        $consulta->closeCursor();
    }


}
