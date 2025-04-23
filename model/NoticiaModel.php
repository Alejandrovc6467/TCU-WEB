<?php

class NoticiaModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerNoticiasOriginal()
    {
        $consulta = $this->db->query('CALL obtenerNoticias()');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }


    public function obtenerNoticias()
    {
        $consulta = $this->db->query('CALL sp_obtenerNoticias()');
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        
        // Estructura para agrupar noticias con sus archivos
        $noticias = [];
        
        foreach ($resultado as $fila) {
            $idNoticia = $fila['id_noticia'];
            
            // Si la noticia no existe en el array, la agregamos
            if (!isset($noticias[$idNoticia])) {
                $noticias[$idNoticia] = [
                    'id' => $idNoticia,
                    'nombre' => $fila['nombre'],
                    'descripcion' => $fila['descripcion'],
                    'fecha' => $fila['fecha'],
                    'tipo' => $fila['tipo'],
                    'archivos' => []
                ];
            }
            
            // Si hay un archivo asociado (id_archivo no es NULL), lo agregamos
            if ($fila['id_archivo'] !== null) {
                $noticias[$idNoticia]['archivos'][] = [
                    'id' => $fila['id_archivo'],
                    'url' => $fila['url_archivo']
                ];
            }
        }
        
        // Convertir a array indexado para el JSON
        return array_values($noticias);
    }




    public function eliminarNoticia($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarNoticia(?)');
        $consulta->bindParam(1, $id);
        $consulta->execute();
        $resultado = $consulta->fetchAll(); // Aquí se captura el mensaje del SP
        $consulta->closeCursor();
        return $resultado;
    }













    //revisar todo esto para ver que me sirve
    /*
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
    */    

}
