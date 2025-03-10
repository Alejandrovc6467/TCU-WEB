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
        // Obtener todos los proyectos
        $consulta = $this->db->query('CALL sp_obtenerProyectos()');
        $proyectos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta->closeCursor();

        // Recorrer cada proyecto para obtener sus imágenes
        foreach ($proyectos as &$proyecto) {
            $consultaImagenes = $this->db->prepare('CALL sp_obtenerImagenesProyectos(?)');
            $consultaImagenes->bindParam(1, $proyecto['id'], PDO::PARAM_INT);
            $consultaImagenes->execute();

            // Obtener todas las imágenes del proyecto actual
            $imagenes = $consultaImagenes->fetchAll(PDO::FETCH_ASSOC);
            $consultaImagenes->closeCursor();

            // Agregar las imágenes al proyecto
            $proyecto['imagenes'] = array_column($imagenes, 'url_archivo'); // Extraer solo las URLs
        }

        return $proyectos;
    }

    public function insertarProyecto($nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        try {
            // Iniciar la transacción
            $this->db->beginTransaction();

            // Insertar el proyecto
            $consultaProyecto = $this->db->prepare('CALL sp_insertarProyecto(?, ?, ?)');
            $consultaProyecto->bindParam(1, $nombre);
            $consultaProyecto->bindParam(2, $descripcion);
            $consultaProyecto->bindParam(3, $id_usuario);
            $consultaProyecto->execute();

            $resultadoProyecto = $consultaProyecto->fetch(PDO::FETCH_ASSOC);
            $consultaProyecto->closeCursor();

            // Verificar si hubo un error en el SP de proyecto
            if (!$resultadoProyecto || $resultadoProyecto['mensaje'] !== 'Proyecto ingresado con exito.') {
                throw new Exception($resultadoProyecto['mensaje'] ?? 'Desconocido');
            }

            // Obtener el ID del proyecto recién insertado
            $id_proyecto = $resultadoProyecto['id'];

            // Insertar imágenes asociadas al proyecto
            $consultaImagen = $this->db->prepare('CALL sp_insertarImagenProyecto(?, ?)');

            foreach ($imagenes_guardadas as $imagen) {
                $consultaImagen->bindParam(1, $imagen);
                $consultaImagen->bindParam(2, $id_proyecto);
                $consultaImagen->execute();
                $consultaImagen->closeCursor();
            }

            // Confirmar la transacción
            $this->db->commit();

            return "Proyecto e imagenes guardadas con exito.";

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->db->rollBack();
            return "Error en la transaccion: " . $e->getMessage();
        }
    }

    public function actualizarProyecto($id, $nombre, $descripcion, $id_usuario, $imagenes_guardadas)
    {
        try {
            // Iniciar la transacción
            $this->db->beginTransaction();

            // Llamar al procedimiento almacenado para actualizar el proyecto
            $consulta = $this->db->prepare('CALL sp_actualizarProyecto(?, ?, ?, ?)');
            $consulta->bindParam(1, $id, PDO::PARAM_INT);
            $consulta->bindParam(2, $nombre, PDO::PARAM_STR);
            $consulta->bindParam(3, $descripcion, PDO::PARAM_STR);
            $consulta->bindParam(4, $id_usuario, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            $consulta->closeCursor();

            // Verificar si hubo un error en la actualización
            if (!$resultado || ($resultado['mensaje'] !== 'Actualización exitosa.' && $resultado['mensaje'] !== 'No se realizaron cambios en nombre y descripcion.')) {
                throw new Exception($resultado['mensaje'] ?? 'Error desconocido.');
            }

            // Si hay imágenes nuevas, insertarlas en la BD
            if (!empty($imagenes_guardadas)) {
                $consultaImagen = $this->db->prepare('CALL sp_insertarImagenProyecto(?, ?)');

                foreach ($imagenes_guardadas as $imagen) {
                    $consultaImagen->bindParam(1, $imagen, PDO::PARAM_STR);
                    $consultaImagen->bindParam(2, $id, PDO::PARAM_INT);
                    $consultaImagen->execute();
                    $consultaImagen->closeCursor();
                }
            }

            // Confirmar la transacción
            $this->db->commit();
            return $resultado['mensaje'];

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->db->rollBack();
            return "Error en la transacción: " . $e->getMessage();
        }
    }

    public function eliminarProyecto($id)
    {
        $consulta = $this->db->prepare('CALL sp_eliminarProyecto( ? )');
        $consulta->bindParam(1, $id);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        $consulta->closeCursor();
        return $resultado;
    }

}
