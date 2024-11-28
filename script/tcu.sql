
CREATE DATABASE tcu;


USE tcu;


DELIMITER $$

CREATE PROCEDURE sp_loginUsuario(
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255)
)
BEGIN
    SELECT id, nombre, correo, rol
    FROM usuario
    WHERE correo = p_correo 
      AND contrasena = p_contrasena 
      AND eliminado = 0;
END$$

DELIMITER ;





-- Tabla Usuario
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(100) NOT NULL,      
    correo VARCHAR(150) NOT NULL UNIQUE, 
    contrasena VARCHAR(255) NOT NULL,  
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    eliminado BOOLEAN NOT NULL DEFAULT FALSE
);

-- Tabla Actividad
CREATE TABLE actividad  (
  id INT AUTO_INCREMENT PRIMARY KEY, 
  url_archivo VARCHAR(255) NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  descripcion VARCHAR(500) NOT NULL,
  fecha DATETIME NOT NULL,
  id_usuario INT NOT NULL,
  eliminado BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);

-- Tabla Proyecto
CREATE TABLE proyecto  (
  id INT AUTO_INCREMENT PRIMARY KEY, 
  nombre VARCHAR(255) NOT NULL,
  descripcion VARCHAR(500) NOT NULL,
  fecha DATETIME NOT NULL,
  id_usuario INT NOT NULL,
  eliminado BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);

-- Tabla ImagenProyecto
CREATE TABLE imagen_proyecto  (
  id INT AUTO_INCREMENT PRIMARY KEY, 
  url_archivo VARCHAR(255) NOT NULL,
  id_proyecto INT NOT NULL,
  eliminado BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_proyecto) REFERENCES proyecto(id)
);







-- crud usuarios ********************************************************************************************************************


DELIMITER $$

CREATE PROCEDURE sp_obtenerUsuarios()
BEGIN
    SELECT id, nombre, correo, contrasena, rol
    FROM usuario
    WHERE rol != 'admin'
      AND eliminado = 0;
END$$

DELIMITER ;





DELIMITER $$

CREATE PROCEDURE sp_insertarUsuario(
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255)
)
BEGIN
    DECLARE correo_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- Verificar si el correo ya existe
    SELECT COUNT(*) INTO correo_existente FROM usuario WHERE correo = p_correo;

    IF correo_existente > 0 THEN
        -- Si el correo ya existe, mostrar un mensaje
        SELECT 'Ya existe un usuario con este correo.' AS mensaje;
    ELSE
        BEGIN
            -- Intentar la inserción, incluyendo la columna eliminado con valor FALSE por defecto
            INSERT INTO usuario (nombre, correo, contrasena, rol, eliminado)
            VALUES (p_nombre, p_correo, p_contrasena, 'usuario', FALSE);
            SELECT 'Inserción exitosa.' AS mensaje;
        END;
    END IF;

    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al insertar el usuario.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;





DELIMITER $$

CREATE PROCEDURE sp_actualizarUsuario(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255)
)
BEGIN
    DECLARE correo_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- Verificar si el correo ya pertenece a otro usuario
    SELECT COUNT(*) INTO correo_existente 
    FROM usuario 
    WHERE correo = p_correo AND id != p_id;

    IF correo_existente > 0 THEN
        -- Si el correo ya pertenece a otro usuario, mostrar un mensaje
        SELECT 'Ya existe un usuario con este correo.' AS mensaje;
    ELSE
        BEGIN
            -- Intentar la actualización del usuario
            UPDATE usuario
            SET nombre = p_nombre,
                correo = p_correo,
                contrasena = p_contrasena
            WHERE id = p_id;

            -- Verificar si la actualización afectó filas
            IF ROW_COUNT() > 0 THEN
                SELECT 'Actualización exitosa.' AS mensaje;
            ELSE
                SELECT 'No se encontró el usuario o no se realizaron cambios.' AS mensaje;
            END IF;
        END;
    END IF;

    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al actualizar el usuario.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;





DELIMITER $$

CREATE PROCEDURE sp_eliminarUsuario(
    IN p_id INT
)
BEGIN
    DECLARE usuario_existente INT;
    DECLARE eliminado_anterior BOOLEAN;

    -- Verificar si el usuario existe y no está ya eliminado
    SELECT COUNT(*), eliminado 
    INTO usuario_existente, eliminado_anterior 
    FROM usuario 
    WHERE id = p_id;

    IF usuario_existente = 0 THEN
        -- Si el usuario no existe, mostrar un mensaje
        SELECT 'El usuario no existe.' AS mensaje;
    ELSEIF eliminado_anterior = TRUE THEN
        -- Si el usuario ya está eliminado, mostrar un mensaje
        SELECT 'El usuario ya está eliminado.' AS mensaje;
    ELSE
        -- Actualizar el campo eliminado a TRUE
        UPDATE usuario 
        SET eliminado = TRUE 
        WHERE id = p_id;

        SELECT 'Eliminación exitosa.' AS mensaje;
    END IF;

END$$

DELIMITER ;








-- crud actividades ********************************************************************************************************************





-- crud proyectos ********************************************************************************************************************


