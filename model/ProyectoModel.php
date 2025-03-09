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
