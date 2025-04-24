<?php

class NoticiaModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
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

    public function insertarNoticia($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        // Convertir array de rutas a string separado por comas
        $urls_concatenadas = implode(',', $archivos_guardados);

        $consulta = $this->db->prepare('CALL sp_ingresarNoticia(?, ?, ?, ?, ?)');
        $consulta->bindParam(1, $nombre);
        $consulta->bindParam(2, $descripcion);
        $consulta->bindParam(3, $tipo);
        $consulta->bindParam(4, $id_usuario);
        $consulta->bindParam(5, $urls_concatenadas);
        $consulta->execute();

        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado[0]['mensaje'];
    }


    public function actualizarNoticiaSinNuevosArchivos($id, $nombre, $descripcion, $id_usuario)
    {
        $consulta = $this->db->prepare('CALL sp_actualizarNoticiaSinNuevosArchivos(?, ?, ?, ?)');
        $consulta->bindParam(1, $id);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $descripcion);
        $consulta->bindParam(4, $id_usuario);
        $consulta->execute();

        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado[0]['mensaje'];
    }













    public function actualizarNoticia($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        // Convertir array de rutas a string separado por comas
        $urls_concatenadas = implode(',', $archivos_guardados);

        $consulta = $this->db->prepare('CALL sp_ingresarNoticia(?, ?, ?, ?, ?)');
        $consulta->bindParam(1, $nombre);
        $consulta->bindParam(2, $descripcion);
        $consulta->bindParam(3, $tipo);
        $consulta->bindParam(4, $id_usuario);
        $consulta->bindParam(5, $urls_concatenadas);
        $consulta->execute();

        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado[0]['mensaje']; // devolver mensaje limpio
    }





}
