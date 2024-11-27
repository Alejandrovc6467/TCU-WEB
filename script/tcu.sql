
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

CREATE PROCEDURE sp_obtenerUsuarios()
BEGIN
    SELECT id, nombre, correo, contrasena, rol
    FROM usuario
    WHERE rol != 'admin'
      AND eliminado = 0;
END$$

DELIMITER ;
