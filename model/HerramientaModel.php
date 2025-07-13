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
        $consulta = $this->db->query('SELECT h.id AS id_herramienta, h.nombre, h.descripcion, h.fecha, h.tipo, a.id AS id_archivo, a.url_archivo FROM herramientas h LEFT JOIN herramienta_archivo ha ON h.id = ha.id_herramienta LEFT JOIN archivo a ON ha.id_archivo = a.id AND a.eliminado = FALSE WHERE h.eliminado = FALSE ORDER BY h.fecha DESC;');

        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $herramientas = [];

        foreach ($resultado as $fila) {
            $idHerramienta = $fila['id_herramienta'];

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

            if ($fila['id_archivo'] !== null) {
                $herramientas[$idHerramienta]['archivos'][] = [
                    'id' => $fila['id_archivo'],
                    'url' => $fila['url_archivo']
                ];
            }
        }

        return array_values($herramientas);
    }

    public function eliminarHerramienta($id)
    {
        try {
            $this->db->beginTransaction();

            // Marcar archivos asociados como eliminados
            $consulta = $this->db->prepare('UPDATE archivo SET eliminado = TRUE WHERE id IN (SELECT id_archivo FROM herramienta_archivo WHERE id_herramienta = ?)');
            $consulta->execute([$id]);

            // Marcar herramienta como eliminada
            $consulta = $this->db->prepare('UPDATE herramientas SET eliminado = TRUE WHERE id = ?');
            $consulta->execute([$id]);

            $this->db->commit();
            return [['mensaje' => 'La herramienta y sus archivos fueron eliminados correctamente.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al eliminar la herramienta.']];
        }
    }

    public function insertarHerramienta($nombre, $descripcion, $tipo, $id_usuario, $archivos_guardados)
    {
        try {
            // Si $archivos_guardados es string, convertirlo a array
            $archivos = is_string($archivos_guardados) ? explode(',', $archivos_guardados) : $archivos_guardados;

            // Validar que sea un array
            if (!is_array($archivos)) {
                return [['mensaje' => 'Formato de archivos inválido.']];
            }

            $this->db->beginTransaction();

            // Insertar la herramienta principal
            $consulta = $this->db->prepare('INSERT INTO herramientas (nombre, descripcion, tipo, fecha, id_usuario) VALUES (?, ?, ?, NOW(), ?)');
            $consulta->execute([$nombre, $descripcion, $tipo, $id_usuario]);

            $id_herramienta = $this->db->lastInsertId();

            // Insertar archivos y sus relaciones
            $consultaArchivo = $this->db->prepare('INSERT INTO archivo (url_archivo) VALUES (?)');
            $consultaRelacion = $this->db->prepare('INSERT INTO herramienta_archivo (id_herramienta, id_archivo) VALUES (?, ?)');

            foreach ($archivos as $url) {
                $url_limpia = trim($url); // Limpiar espacios en blanco
                if (!empty($url_limpia)) {
                    $consultaArchivo->execute([$url_limpia]);
                    $id_archivo = $this->db->lastInsertId();
                    $consultaRelacion->execute([$id_herramienta, $id_archivo]);
                }
            }

            $this->db->commit();
            return [['mensaje' => 'Se agregó la herramienta correctamente.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al insertar la herramienta.']];
        }
    }

    public function actualizarHerramientaSinNuevosArchivos($id, $nombre, $descripcion, $id_usuario)
    {
        try {
            // Verificar si la herramienta existe
            $consulta = $this->db->prepare('SELECT COUNT(*) FROM herramientas WHERE id = ?');
            $consulta->execute([$id]);
            $existe = $consulta->fetchColumn();

            if ($existe == 0) {
                return [['mensaje' => 'Error: La herramienta no existe.']];
            }

            $this->db->beginTransaction();

            // Actualizar la herramienta
            $consulta = $this->db->prepare('UPDATE herramientas SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            $this->db->commit();
            return [['mensaje' => 'Actualización exitosa.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error durante la actualización.']];
        }
    }

    public function actualizarHerramientaConNuevosArchivos($id, $nombre, $descripcion, $id_usuario, $archivos_guardados)
    {
        try {
            // Convertir a array si es string
            $archivos = is_string($archivos_guardados) ? explode(',', $archivos_guardados) : $archivos_guardados;

            if (!is_array($archivos)) {
                return [['mensaje' => 'Formato de archivos inválido.']];
            }

            $this->db->beginTransaction();

            // Actualizar datos básicos de la herramienta
            $consulta = $this->db->prepare('UPDATE herramientas SET nombre = ?, descripcion = ?, id_usuario = ?, fecha = NOW() WHERE id = ?');
            $consulta->execute([$nombre, $descripcion, $id_usuario, $id]);

            // Marcar archivos antiguos como eliminados
            $consulta = $this->db->prepare('UPDATE archivo SET eliminado = TRUE WHERE id IN (SELECT id_archivo FROM herramienta_archivo WHERE id_herramienta = ?)');
            $consulta->execute([$id]);

            // Eliminar relaciones antiguas
            $consulta = $this->db->prepare('DELETE FROM herramienta_archivo WHERE id_herramienta = ?');
            $consulta->execute([$id]);

            // Insertar nuevos archivos y relaciones
            $consultaArchivo = $this->db->prepare('INSERT INTO archivo (url_archivo) VALUES (?)');
            $consultaRelacion = $this->db->prepare('INSERT INTO herramienta_archivo (id_herramienta, id_archivo) VALUES (?, ?)');

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