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
        $consulta = $this->db->query('SELECT n.id AS id_noticia, n.nombre, n.descripcion, n.fecha, n.tipo, a.id AS id_archivo, a.url_archivo FROM noticias n LEFT JOIN noticia_archivo na ON n.id = na.id_noticia LEFT JOIN archivo a ON na.id_archivo = a.id AND a.eliminado = FALSE WHERE n.eliminado = FALSE ORDER BY n.fecha DESC;');

        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $noticias = [];

        foreach ($resultado as $fila) {
            $idNoticia = $fila['id_noticia'];

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

            if ($fila['id_archivo'] !== null) {
                $noticias[$idNoticia]['archivos'][] = [
                    'id' => $fila['id_archivo'],
                    'url' => $fila['url_archivo']
                ];
            }
        }

        return array_values($noticias);
    }

    public function eliminarNoticia($id)
    {
        try {
            $this->db->beginTransaction();

            // Marcar archivos asociados como eliminados
            $consulta = $this->db->prepare('UPDATE archivo SET eliminado = TRUE WHERE id IN (SELECT id_archivo FROM noticia_archivo WHERE id_noticia = ?)');
            $consulta->execute([$id]);

            // Marcar noticia como eliminada
            $consulta = $this->db->prepare('UPDATE noticias SET eliminado = TRUE WHERE id = ?');
            $consulta->execute([$id]);

            $this->db->commit();
            return [['mensaje' => 'La noticia y sus archivos fueron eliminados correctamente.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al eliminar la noticia.']];
        }
    }

    public function insertarNoticia($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        try {
            // Convertir a array si es string
            $archivos = is_string($archivos_guardados) ? explode(',', $archivos_guardados) : $archivos_guardados;

            if (!is_array($archivos)) {
                return [['mensaje' => 'Formato de archivos inválido.']];
            }

            $this->db->beginTransaction();

            // Insertar la noticia principal
            $consulta = $this->db->prepare('INSERT INTO noticias (nombre, descripcion, tipo, fecha, id_usuario) VALUES (?, ?, ?, NOW(), ?)');
            $consulta->execute([$nombre, $descripcion, $tipo, $id_usuario]);

            $id_noticia = $this->db->lastInsertId();

            // Insertar archivos y sus relaciones
            $consultaArchivo = $this->db->prepare('INSERT INTO archivo (url_archivo) VALUES (?)');
            $consultaRelacion = $this->db->prepare('INSERT INTO noticia_archivo (id_noticia, id_archivo) VALUES (?, ?)');

            foreach ($archivos as $url) {
                $url_limpia = trim($url);
                if (!empty($url_limpia)) {
                    $consultaArchivo->execute([$url_limpia]);
                    $id_archivo = $this->db->lastInsertId();
                    $consultaRelacion->execute([$id_noticia, $id_archivo]);
                }
            }

            $this->db->commit();
            return [['mensaje' => 'Se agregó la noticia correctamente.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al insertar la noticia.']];
        }
    }

    public function actualizarNoticiaSinNuevosArchivos($id, $nombre, $descripcion, $id_usuario)
    {
        try {
            // Verificar si la noticia existe
            $consulta = $this->db->prepare('SELECT COUNT(*) FROM noticias WHERE id = ?');
            $consulta->execute([$id]);
            $existe = $consulta->fetchColumn();

            if ($existe == 0) {
                return [['mensaje' => 'Error: La noticia no existe.']];
            }

            $this->db->beginTransaction();

            // Actualizar la noticia
            $consulta = $this->db->prepare('UPDATE noticias SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            $this->db->commit();
            return [['mensaje' => 'Actualización exitosa.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error durante la actualización.']];
        }
    }

    public function actualizarNoticiaConNuevosArchivos($id, $nombre, $descripcion, $id_usuario, $archivos_guardados)
    {
        try {
            // Convertir a array si es string
            $archivos = is_string($archivos_guardados) ? explode(',', $archivos_guardados) : $archivos_guardados;

            if (!is_array($archivos)) {
                return [['mensaje' => 'Formato de archivos inválido.']];
            }

            $this->db->beginTransaction();

            // Actualizar datos básicos de la noticia
            $consulta = $this->db->prepare('UPDATE noticias SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            // Marcar archivos antiguos como eliminados
            $consulta = $this->db->prepare('UPDATE archivo SET eliminado = TRUE WHERE id IN (SELECT id_archivo FROM noticia_archivo WHERE id_noticia = ?)');
            $consulta->execute([$id]);

            // Eliminar relaciones antiguas
            $consulta = $this->db->prepare('DELETE FROM noticia_archivo WHERE id_noticia = ?');
            $consulta->execute([$id]);

            // Insertar nuevos archivos y relaciones
            $consultaArchivo = $this->db->prepare('INSERT INTO archivo (url_archivo) VALUES (?)');
            $consultaRelacion = $this->db->prepare('INSERT INTO noticia_archivo (id_noticia, id_archivo) VALUES (?, ?)');

            foreach ($archivos as $url) {
                $url_limpia = trim($url);
                if (!empty($url_limpia)) {
                    $consultaArchivo->execute([$url_limpia]);
                    $id_archivo = $this->db->lastInsertId();
                    $consultaRelacion->execute([$id, $id_archivo]);
                }
            }

            $this->db->commit();
            return [['mensaje' => 'Actualización exitosa.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error durante la actualización.']];
        }
    }

}
