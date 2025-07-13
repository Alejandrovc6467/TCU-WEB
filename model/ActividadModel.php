<?php

class ActividadModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerActividades()
    {
        $consulta = $this->db->query('SELECT id, url_archivo, nombre, descripcion, fecha FROM Actividad WHERE eliminado = FALSE ORDER BY fecha DESC;');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }

    public function insertarActividad($url_archivo, $nombre, $descripcion, $id_usuario)
    {
        try {
            $this->db->beginTransaction();

            // Verificar si el usuario existe y no está eliminado
            $consulta = $this->db->prepare('SELECT COUNT(*) FROM Usuario WHERE id = ? AND eliminado = FALSE');
            $consulta->execute([$id_usuario]);
            $usuario_existente = $consulta->fetchColumn();

            if ($usuario_existente == 0) {
                return 'Ocurrió un error: el usuario no existe.';
            }

            // Insertar la actividad
            $consulta = $this->db->prepare('INSERT INTO Actividad (url_archivo, nombre, descripcion, fecha, id_usuario, eliminado) VALUES (?, ?, ?, NOW(), ?, FALSE)');
            $consulta->execute([$url_archivo, $nombre, $descripcion, $id_usuario]);

            $this->db->commit();
            return 'Actividad ingresada con éxito.';
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Ocurrió un error al insertar la actividad.';
        }
    }

    public function actualizarActividad($id, $url_archivo, $nombre, $descripcion, $id_usuario)
    {
        try {
            $this->db->beginTransaction();

            // Verificar si el usuario existe
            $consulta = $this->db->prepare('SELECT COUNT(*) FROM Usuario WHERE id = ?');
            $consulta->execute([$id_usuario]);
            $usuario_existente = $consulta->fetchColumn();

            if ($usuario_existente == 0) {
                return [['mensaje' => 'Ocurrió un error: el usuario no existe.']];
            }

            // Actualizar la actividad
            $consulta = $this->db->prepare('UPDATE Actividad SET url_archivo = COALESCE(?, url_archivo), nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$url_archivo, $nombre, $descripcion, $id_usuario, $id]);

            if ($consulta->rowCount() > 0) {
                $this->db->commit();
                return [['mensaje' => 'Actualización exitosa.']];
            } else {
                $this->db->rollBack();
                return [['mensaje' => 'No se realizaron cambios.']];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al actualizar la actividad.']];
        }
    }

    public function eliminarActividad($id)
    {
        try {
            $this->db->beginTransaction();

            // Verificar si la actividad existe y su estado de eliminación
            $consulta = $this->db->prepare('SELECT COUNT(*), eliminado FROM Actividad WHERE id = ?');
            $consulta->execute([$id]);
            $resultado = $consulta->fetch(PDO::FETCH_NUM);

            $actividad_existente = $resultado[0];
            $eliminado_anterior = $resultado[1];

            if ($actividad_existente == 0) {
                return [['mensaje' => 'La actividad no existe.']];
            } elseif ($eliminado_anterior) {
                return [['mensaje' => 'La actividad ya está eliminada.']];
            }

            // Eliminar lógicamente la actividad
            $consulta = $this->db->prepare('UPDATE Actividad SET eliminado = TRUE WHERE id = ?');
            $consulta->execute([$id]);

            $this->db->commit();
            return [['mensaje' => 'Actividad eliminada exitosamente.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al eliminar la actividad.']];
        }
    }

}
