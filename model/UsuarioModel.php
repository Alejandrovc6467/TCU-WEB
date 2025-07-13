<?php

class UsuarioModel
{

    private $db;

    public function __construct()
    {
        require './libs/SPDO.php';
        $this->db = SPDO::getInstance();
    }

    public function obtenerUsuarios()
    {
        $consulta = $this->db->query('SELECT id, nombre, correo, contrasena, rol FROM usuario WHERE rol != \'admin\' AND eliminado = 0;');
        $resultado = $consulta->fetchAll();
        return $resultado;
    }

    public function insertarUsuario($nombre, $correo, $contrasena)
    {
        try {
            $this->db->beginTransaction();

            $consulta = $this->db->prepare('SELECT COUNT(*) FROM usuario WHERE correo = ?');
            $consulta->execute([$correo]);
            $existe = $consulta->fetchColumn();

            if ($existe > 0) {
                return [['mensaje' => 'Ya existe un usuario con este correo.']];
            }

            $consulta = $this->db->prepare('INSERT INTO usuario (nombre, correo, contrasena, rol, eliminado) VALUES (?, ?, ?, \'usuario\', FALSE)');
            $consulta->execute([$nombre, $correo, $contrasena]);

            $this->db->commit();
            return [['mensaje' => 'Inserción exitosa.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al insertar el usuario.']];
        }
    }

    public function actualizarUsuario($id, $nombre, $correo, $contrasena)
    {
        try {
            $this->db->beginTransaction();

            $consulta = $this->db->prepare('SELECT COUNT(*) FROM usuario WHERE correo = ? AND id != ?');
            $consulta->execute([$correo, $id]);
            $existe = $consulta->fetchColumn();

            if ($existe > 0) {
                return [['mensaje' => 'Ya existe un usuario con este correo.']];
            }

            $consulta = $this->db->prepare('UPDATE usuario SET nombre = ?, correo = ?, contrasena = ? WHERE id = ?');
            $consulta->execute([$nombre, $correo, $contrasena, $id]);

            if ($consulta->rowCount() > 0) {
                $this->db->commit();
                return [['mensaje' => 'Actualización exitosa.']];
            } else {
                $this->db->rollBack();
                return [['mensaje' => 'No se encontró el usuario o no se realizaron cambios.']];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al actualizar el usuario.']];
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $this->db->beginTransaction();

            // Verificar si el usuario existe y su estado de eliminación
            $consulta = $this->db->prepare('SELECT COUNT(*), eliminado FROM usuario WHERE id = ?');
            $consulta->execute([$id]);
            $resultado = $consulta->fetch(PDO::FETCH_NUM);

            $usuarioExistente = $resultado[0];
            $eliminadoAnterior = $resultado[1];

            if ($usuarioExistente == 0) {
                return [['mensaje' => 'El usuario no existe.']];
            } elseif ($eliminadoAnterior) {
                return [['mensaje' => 'El usuario ya está eliminado.']];
            }

            // Eliminar lógicamente el usuario
            $consulta = $this->db->prepare('UPDATE usuario SET eliminado = TRUE WHERE id = ?');
            $consulta->execute([$id]);

            $this->db->commit();
            return [['mensaje' => 'Eliminación exitosa.']];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [['mensaje' => 'Ocurrió un error al eliminar el usuario.']];
        }
    }

}
