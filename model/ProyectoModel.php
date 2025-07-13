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
        $consulta = $this->db->query('SELECT p.id AS id_proyecto, p.nombre, p.descripcion, p.fecha, p.id_usuario, ip.id AS id_imagen, ip.url_archivo FROM proyecto p LEFT JOIN imagen_proyecto ip ON p.id = ip.id_proyecto AND ip.eliminado = FALSE WHERE p.eliminado = FALSE ORDER BY p.fecha DESC;');

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
            $this->db->beginTransaction();

            // Insertar el proyecto principal
            $consulta = $this->db->prepare('INSERT INTO proyecto (nombre, descripcion, fecha, id_usuario, eliminado) VALUES (?, ?, NOW(), ?, FALSE)');
            $consulta->execute([$nombre, $descripcion, $id_usuario]);

            $id_proyecto = $this->db->lastInsertId();

            // Insertar las imágenes
            $consulta = $this->db->prepare('INSERT INTO imagen_proyecto (url_archivo, id_proyecto) VALUES (?, ?)');
            foreach ($imagenes_guardadas as $url) {
                $consulta->execute([$url, $id_proyecto]);
            }

            $this->db->commit();
            return 'Proyecto ingresado con éxito.';
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Ocurrió un error al insertar el proyecto.';
        }
    }

    public function actualizarProyectoConNuevasImagenes($id, $nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        try {
            $this->db->beginTransaction();

            // Actualizar el proyecto principal
            $consulta = $this->db->prepare('UPDATE proyecto SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            // Marcar como eliminadas las imágenes antiguas
            $consulta = $this->db->prepare('UPDATE imagen_proyecto SET eliminado = TRUE WHERE id_proyecto = ?');
            $consulta->execute([$id]);

            // Insertar las nuevas imágenes
            $consulta = $this->db->prepare('INSERT INTO imagen_proyecto (url_archivo, id_proyecto) VALUES (?, ?)');
            foreach ($imagenes_guardadas as $url) {
                $consulta->execute([$url, $id]);
            }

            $this->db->commit();
            return 'Actualización exitosa.';
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Ocurrió un error durante la actualización.';
        }
    }

    public function actualizarProyectoSinImagenes($id, $nombre, $descripcion, $id_usuario)
    {
        try {
            // Verificar si el proyecto existe
            $consulta = $this->db->prepare('SELECT COUNT(*) FROM proyecto WHERE id = ? AND eliminado = FALSE');
            $consulta->execute([$id]);
            $existe = $consulta->fetchColumn();

            if ($existe == 0) {
                return [['mensaje' => 'Error: El proyecto no existe o está eliminado.']];
            }

            $this->db->beginTransaction();

            // Actualizar el proyecto
            $consulta = $this->db->prepare('UPDATE proyecto SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            $this->db->commit();
            return 'Actualización exitosa.';
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Ocurrió un error durante la actualización del proyecto.';
        }
    }

    public function eliminarProyecto($id)
    {
        try {
            $this->db->beginTransaction();

            // Eliminar el proyecto (lógicamente)
            $consulta = $this->db->prepare('UPDATE proyecto SET eliminado = TRUE WHERE id = ?');
            $consulta->execute([$id]);

            // Eliminar las imágenes asociadas (lógicamente)
            $consulta = $this->db->prepare('UPDATE imagen_proyecto SET eliminado = TRUE WHERE id_proyecto = ?');
            $consulta->execute([$id]);

            $this->db->commit();
            return 'Proyecto y sus imágenes eliminados correctamente.';
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Ocurrió un error al eliminar el proyecto.';
        }
    }

}