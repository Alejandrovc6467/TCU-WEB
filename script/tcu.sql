
CREATE DATABASE tcu;


USE tcu;




DELIMITER $$

CREATE PROCEDURE sp_loginUsuario(
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255)
)
BEGIN
    SELECT nombre, correo, rol
    FROM usuario
    WHERE correo = p_correo 
      AND contrasena = p_contrasena 
      AND eliminado = 0;
END$$

DELIMITER ;







CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(100) NOT NULL,      
    correo VARCHAR(150) NOT NULL UNIQUE, 
    contrasena VARCHAR(255) NOT NULL,  
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    eliminado BOOLEAN NOT NULL DEFAULT FALSE
);


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


CALL sp_insertarUsuario('Alejandro Vasquez', 'alejandrovc177@gmail.com', '123456');




DELIMITER $$

CREATE PROCEDURE sp_obtenerUsuarios()
BEGIN
    SELECT id, nombre, correo, contrasena, rol
    FROM usuario
    WHERE rol != 'admin'
      AND eliminado = 0;
END$$

DELIMITER ;
