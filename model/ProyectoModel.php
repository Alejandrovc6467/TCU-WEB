<?php

class ProyectoModel
{
    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerProyectos()
    {
        $consulta = $this->db->query('CALL sp_obtenerProyectos()');
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $proyectos = [];

        foreach ($resultado as $fila) {
            $idProyecto = $fila['id_proyecto'];

            if (!isset($proyectos[$idProyecto])) {
                $proyectos[$idProyecto] = [
                    'id' => $idProyecto,
                    'nombre' => $fila['nombre'],
                    'descripcion' => $fila['descripcion'],
                    'fecha' => $fila['fecha'],
                    'id_usuario' => $fila['id_usuario'],
                    'imagenes' => []
                ];
            }

            if ($fila['id_imagen'] !== null) {
                $proyectos[$idProyecto]['imagenes'][] = [
                    'id' => $fila['id_imagen'],
                    'url' => $fila['url_archivo']
                ];
            }
        }

        return array_values($proyectos);
    }

    public function insertarProyecto($nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        try {
            $urls_concatenadas = implode(',', $imagenes_guardadas);

            $consulta = $this->db->prepare('CALL sp_insertarProyecto(?, ?, ?, ?)');
            $consulta->bindParam(1, $nombre);
            $consulta->bindParam(2, $descripcion);
            $consulta->bindParam(3, $id_usuario);
            $consulta->bindParam(4, $urls_concatenadas);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            $consulta->closeCursor();

            if (!$resultado || strpos($resultado['mensaje'], 'éxito') === false) {
                throw new Exception($resultado['mensaje'] ?? 'Error desconocido.');
            }

            return $resultado['mensaje'];

        } catch (Exception $e) {
            return "Error en la transacción: " . $e->getMessage();
        }
    }

    public function actualizarProyectoConNuevasImagenes($id, $nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        try {
            $urls_concatenadas = implode(',', $imagenes_guardadas);

            $consulta = $this->db->prepare('CALL sp_actualizarProyectoConImagenes(?, ?, ?, ?, ?)');
            $consulta->bindParam(1, $id);
            $consulta->bindParam(2, $nombre);
            $consulta->bindParam(3, $descripcion);
            $consulta->bindParam(4, $id_usuario);
            $consulta->bindParam(5, $urls_concatenadas);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            $consulta->closeCursor();

            return $resultado['mensaje'];

        } catch (Exception $e) {
            return "Error en la transacción: " . $e->getMessage();
        }
    }

    public function actualizarProyectoSinImagenes($id, $nombre, $descripcion, $id_usuario)
    {
        try {
            $consulta = $this->db->prepare('CALL sp_actualizarProyectoSinImagenes(?, ?, ?, ?)');
            $consulta->bindParam(1, $id);
            $consulta->bindParam(2, $nombre);
            $consulta->bindParam(3, $descripcion);
            $consulta->bindParam(4, $id_usuario);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            $consulta->closeCursor();

            return $resultado['mensaje'];

        } catch (Exception $e) {
            return "Error en la transacción: " . $e->getMessage();
        }
    }

    public function eliminarProyecto($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarProyecto(?)');
        $consulta->bindParam(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        $consulta->closeCursor();
        return $resultado['mensaje'] ?? 'Resultado desconocido.';
    }
}