<?php

class HerramientaModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerHerramientas()
    {
        $consulta = $this->db->query('CALL sp_obtenerHerramientas()');
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        
        // Estructura para agrupar herramientas con sus archivos
        $herramientas = [];
        
        foreach ($resultado as $fila) {
            $idHerramienta = $fila['id_herramienta'];
            
            // Si la herramienta no existe en el array, la agregamos
            if (!isset($herramientas[$idHerramienta])) {
                $herramientas[$idHerramienta] = [
                    'id' => $idHerramienta,
                    'nombre' => $fila['nombre'],
                    'descripcion' => $fila['descripcion'],
                    'fecha' => $fila['fecha'],
                    'tipo' => $fila['tipo'],
                    'archivos' => []
                ];
            }
            
            // Si hay un archivo asociado (id_archivo no es NULL), lo agregamos
            if ($fila['id_archivo'] !== null) {
                $herramientas[$idHerramienta]['archivos'][] = [
                    'id' => $fila['id_archivo'],
                    'url' => $fila['url_archivo']
                ];
            }
        }
        
        // Convertir a array indexado para el JSON
        return array_values($herramientas);
    }

    public function eliminarHerramienta($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarHerramienta(?)');
        $consulta->bindParam(1, $id);
        $consulta->execute();
        $resultado = $consulta->fetchAll(); // Aquí se captura el mensaje del SP
        $consulta->closeCursor();
        return $resultado;
    }

    public function insertarHerramienta($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        // Convertir array de rutas a string separado por comas
        $urls_concatenadas = implode(',', $archivos_guardados);

        $consulta = $this->db->prepare('CALL sp_ingresarHerramienta(?, ?, ?, ?, ?)');
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

    public function actualizarHerramientaSinNuevosArchivos($id, $nombre, $descripcion, $id_usuario)
    {
        $consulta = $this->db->prepare('CALL sp_actualizarHerramientaSinNuevosArchivos(?, ?, ?, ?)');
        $consulta->bindParam(1, $id);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $descripcion);
        $consulta->bindParam(4, $id_usuario);
        $consulta->execute();

        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

    public function actualizarHerramientaConNuevosArchivos($id, $nombre, $descripcion, $id_usuario, $urls_concatenadas)
    {
        $consulta = $this->db->prepare('CALL sp_actualizarHerramientaConNuevosArchivos(?, ?, ?, ?, ?)');
        $consulta->bindParam(1, $id);
        $consulta->bindParam(2, $nombre);
        $consulta->bindParam(3, $descripcion);
        $consulta->bindParam(4, $id_usuario);
        $consulta->bindParam(5, $urls_concatenadas);
        $consulta->execute();

        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

}