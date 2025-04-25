
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



--nuevas tablas para noticias y herramientas

CREATE TABLE noticias(  
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,   
    descripcion VARCHAR(500) NOT NULL,   
    fecha DATETIME NOT NULL,   
    id_usuario INT NOT NULL,   
    eliminado BOOLEAN NOT NULL DEFAULT FALSE,
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('imagen', 'video')),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);


CREATE TABLE herramientas(  
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,   
    descripcion VARCHAR(500) NOT NULL,   
    fecha DATETIME NOT NULL,   
    id_usuario INT NOT NULL,   
    eliminado BOOLEAN NOT NULL DEFAULT FALSE,  
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('imagen', 'video')), 
    FOREIGN KEY (id_usuario) REFERENCES usuario(id) 
);


CREATE TABLE archivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url_archivo VARCHAR(255) NOT NULL,
    eliminado BOOLEAN NOT NULL DEFAULT FALSE
);


CREATE TABLE noticia_archivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_noticia INT NOT NULL REFERENCES noticias(id),
    id_archivo INT NOT NULL REFERENCES archivo(id)
);

CREATE TABLE herramienta_archivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_herramienta INT NOT NULL REFERENCES herramientas(id),
    id_archivo INT NOT NULL REFERENCES archivo(id)
);


--crud noticias ********************************************************************************************************************

DELIMITER $$

CREATE PROCEDURE sp_obtenerNoticias()
BEGIN
    -- Seleccionar noticias no eliminadas junto con sus archivos relacionados no eliminados
    SELECT 
        n.id AS id_noticia,
        n.nombre,
        n.descripcion,
        n.fecha,
        n.tipo,
        a.id AS id_archivo,
        a.url_archivo
    FROM 
        noticias n
    LEFT JOIN 
        noticia_archivo na ON n.id = na.id_noticia
    LEFT JOIN 
        archivo a ON na.id_archivo = a.id AND a.eliminado = FALSE
    WHERE 
        n.eliminado = FALSE
    ORDER BY 
        n.id, a.id;
END $$

DELIMITER ;




DELIMITER $$

CREATE PROCEDURE sp_eliminarNoticia(IN p_id_noticia INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Ocurrió un error al eliminar la noticia.' AS mensaje;
    END;

    START TRANSACTION;

    -- Marcar los archivos asociados como eliminados
    UPDATE archivo 
    SET eliminado = TRUE
    WHERE id IN (
        SELECT id_archivo 
        FROM noticia_archivo 
        WHERE id_noticia = p_id_noticia
    );

    -- Marcar la noticia como eliminada
    UPDATE noticias 
    SET eliminado = TRUE 
    WHERE id = p_id_noticia;

    COMMIT;
    SELECT 'La noticia y sus archivos fueron eliminados correctamente.' AS mensaje;
END$$

DELIMITER ;





DELIMITER $$

CREATE PROCEDURE sp_ingresarNoticia (
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_tipo VARCHAR(10),
    IN p_id_usuario INT,
    IN p_urls TEXT -- URLs separadas por comas
)
BEGIN
    DECLARE v_id_noticia INT;
    DECLARE v_pos_inicio INT DEFAULT 1;
    DECLARE v_pos_coma INT;
    DECLARE v_url_actual VARCHAR(255);
    DECLARE v_longitud INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Ocurrió un error al insertar la noticia.' AS mensaje;
    END;

    START TRANSACTION;

    -- Insertar la noticia
    INSERT INTO noticias (nombre, descripcion, tipo, fecha, id_usuario)
    VALUES (p_nombre, p_descripcion, p_tipo, NOW(), p_id_usuario);

    SET v_id_noticia = LAST_INSERT_ID();
    SET v_longitud = CHAR_LENGTH(p_urls);

    WHILE v_pos_inicio <= v_longitud DO
        SET v_pos_coma = LOCATE(',', p_urls, v_pos_inicio);

        IF v_pos_coma = 0 THEN
            SET v_url_actual = TRIM(SUBSTRING(p_urls, v_pos_inicio));
            SET v_pos_inicio = v_longitud + 1;
        ELSE
            SET v_url_actual = TRIM(SUBSTRING(p_urls, v_pos_inicio, v_pos_coma - v_pos_inicio));
            SET v_pos_inicio = v_pos_coma + 1;
        END IF;

        -- Insertar archivo
        INSERT INTO archivo (url_archivo) VALUES (v_url_actual);
        SET @id_archivo = LAST_INSERT_ID();

        -- Insertar relación noticia_archivo
        INSERT INTO noticia_archivo (id_noticia, id_archivo) VALUES (v_id_noticia, @id_archivo);
    END WHILE;

    COMMIT;
    SELECT 'Se agregó la noticia correctamente.' AS mensaje;
END$$

DELIMITER ;



DELIMITER $$

CREATE PROCEDURE sp_actualizarNoticiaSinNuevosArchivos (
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_id_usuario INT
)
BEGIN
    

    DECLARE existe INT;
    SELECT COUNT(*) INTO existe FROM noticias WHERE id = p_id;

    IF existe = 0 THEN
        SELECT 'Error: La noticia no existe.' AS mensaje;
    ELSE
        UPDATE noticias
        SET
            nombre = p_nombre,
            descripcion = p_descripcion,
            id_usuario = p_id_usuario,
            fecha = NOW()
        WHERE id = p_id;

        SELECT 'Actualización exitosa.' AS mensaje;
    END IF;

END$$

DELIMITER ;



DELIMITER $$

CREATE PROCEDURE sp_actualizarNoticiaConNuevosArchivos (
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_id_usuario INT,
    IN p_urls TEXT -- URLs separadas por comas
)
BEGIN
    DECLARE v_pos_inicio INT DEFAULT 1;
    DECLARE v_pos_coma INT;
    DECLARE v_url_actual VARCHAR(255);
    DECLARE v_longitud INT;
    DECLARE v_id_archivo INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Ocurrió un error durante la actualización.' AS mensaje;
    END;

    START TRANSACTION;

    -- Actualizar los datos básicos de la noticia
    UPDATE noticias 
    SET nombre = p_nombre, descripcion = p_descripcion, id_usuario = p_id_usuario 
    WHERE id = p_id;

    -- Obtener todos los id_archivo relacionados a esta noticia y marcarlos como eliminados
    UPDATE archivo 
    SET eliminado = TRUE
    WHERE id IN (
        SELECT id_archivo FROM noticia_archivo WHERE id_noticia = p_id
    );

    -- Eliminar relaciones anteriores de la tabla noticia_archivo
    DELETE FROM noticia_archivo WHERE id_noticia = p_id;

    -- Insertar los nuevos archivos
    SET v_longitud = CHAR_LENGTH(p_urls);

    WHILE v_pos_inicio <= v_longitud DO
        SET v_pos_coma = LOCATE(',', p_urls, v_pos_inicio);

        IF v_pos_coma = 0 THEN
            SET v_url_actual = TRIM(SUBSTRING(p_urls, v_pos_inicio));
            SET v_pos_inicio = v_longitud + 1;
        ELSE
            SET v_url_actual = TRIM(SUBSTRING(p_urls, v_pos_inicio, v_pos_coma - v_pos_inicio));
            SET v_pos_inicio = v_pos_coma + 1;
        END IF;

        INSERT INTO archivo (url_archivo) VALUES (v_url_actual);
        SET v_id_archivo = LAST_INSERT_ID();

        INSERT INTO noticia_archivo (id_noticia, id_archivo) VALUES (p_id, v_id_archivo);
    END WHILE;

    COMMIT;
    SELECT 'Actualización exitosa.' AS mensaje;
END$$

DELIMITER ;




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


DELIMITER $$

DROP PROCEDURE IF EXISTS sp_obtenerActividades$$

CREATE PROCEDURE sp_obtenerActividades()
BEGIN

    SELECT 
        id,
        url_archivo,
        nombre,
        descripcion,
        fecha
    FROM Actividad
    WHERE eliminado = FALSE;

END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_insertarActividad$$

CREATE PROCEDURE sp_insertarActividad(
    IN p_url_archivo VARCHAR(255),
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_id_usuario INT
)
BEGIN
    DECLARE usuario_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- verificar que el usuario a ingresar exista en la tabla de usuarios, filtrando por id de dicho usuario
    SELECT COUNT(*) INTO usuario_existente FROM Usuario WHERE id = p_id_usuario AND eliminado = TRUE;

    IF  usuario_existente > 0 THEN
        -- mensaje de error si el usuario no existe
        SELECT 'Ocurrió un error el usuario no existe.' AS mensaje;
    ELSE
        BEGIN
            -- en caso de que todo sea correcto se insertan los datos
            INSERT INTO Actividad ( url_archivo, nombre, descripcion, fecha, id_usuario, eliminado ) 
            VALUES ( p_url_archivo, p_nombre, p_descripcion, NOW(), p_id_usuario, FALSE );
            SELECT 'Actividad ingresada con exito.' AS mensaje;
        END;
    END IF;
    
    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al insertar la actividad.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;

DELIMITER $$
-- eliminar sp en caso de que exista para actualizarlo 
DROP PROCEDURE IF EXISTS sp_actualizarActividad$$

CREATE PROCEDURE sp_actualizarActividad(
    IN p_id INT,
    IN p_url_archivo VARCHAR(255),
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_id_usuario INT
)
BEGIN
    DECLARE usuario_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- verificar que el usuario a ingresar exista en la tabla de usuarios, filtrando por id de dicho usuario
    SELECT COUNT(*) INTO usuario_existente FROM Usuario WHERE id = p_id_usuario;

    IF  usuario_existente < 0 THEN
        -- mensaje de error si el usuario no existe
        SELECT 'Ocurrió un error el usuario no existe.' AS mensaje;
    ELSE
        BEGIN
            -- actualiza los datos de la actividad sobre-escribiendo la informacion
            UPDATE Actividad
            SET url_archivo = COALESCE(p_url_archivo, url_archivo), -- verifica si se actualiza la url
                nombre = p_nombre,
                descripcion = p_descripcion,
                id_usuario = p_id_usuario
            WHERE id = p_id;

            -- Verificar si la actualización afectó filas
            IF ROW_COUNT() > 0 THEN
                UPDATE Actividad
                SET fecha = NOW()
                WHERE id = p_id;

                SELECT 'Actualización exitosa.' AS mensaje;
            ELSE
                SELECT 'No se realizaron cambios.' AS mensaje;
            END IF;

        END;
    END IF;
    
    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al actualizar la actividad.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;

DELIMITER $$
-- eliminar sp en caso de que exista para actualizarlo 
DROP PROCEDURE IF EXISTS sp_eliminarActividad$$

CREATE PROCEDURE sp_eliminarActividad(
    IN p_id INT
)
BEGIN
    DECLARE actividad_existente INT;
    DECLARE eliminado_anterior BOOLEAN;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- verificar que la actividad no este ya eliminada
    SELECT COUNT(*), eliminado INTO actividad_existente, eliminado_anterior FROM Actividad WHERE id = p_id;

    IF  actividad_existente < 0 THEN
        -- mensaje de error si la actividad no existe
        SELECT 'La actividad no existe.' AS mensaje;
    ELSEIF eliminado_anterior = TRUE THEN
        -- mensaje si la actividad ya fue eliminada
        SELECT 'La actividad ya está eliminado.' AS mensaje;
    ELSE
        BEGIN
            -- actualiza los datos de la actividad sobre-escribiendo la informacion del valor borrado
            UPDATE Actividad
            SET eliminado = TRUE
            WHERE id = p_id;

            -- Verificar si la actualización afectó correctamente la columna eliminado
            SELECT 'Actividad eliminada exitosamente.' AS mensaje;

        END;
    END IF;
    
    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al eliminar la actividad.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;


-- crud proyectos y imagenes ********************************************************************************************************************


DELIMITER $$

-- Obtener todos los proyectos no eliminados
DROP PROCEDURE IF EXISTS sp_obtenerProyectos$$

CREATE PROCEDURE sp_obtenerProyectos()
BEGIN

    SELECT 
        id, 
        nombre, 
        descripcion, 
        fecha, 
        id_usuario
    FROM proyecto
    WHERE eliminado = FALSE;

END$$

DELIMITER ;

DELIMITER $$

-- Obtener todas las imagenes de un proyecto no eliminado
DROP PROCEDURE IF EXISTS sp_obtenerImagenesProyectos$$

CREATE PROCEDURE sp_obtenerImagenesProyectos(
    IN p_id INT
)
BEGIN

    SELECT 
        url_archivo
    FROM imagen_proyecto
    WHERE id_proyecto = p_id AND eliminado = FALSE;

END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_insertarProyecto$$

CREATE PROCEDURE sp_insertarProyecto(
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_id_usuario INT
)
BEGIN
    DECLARE usuario_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- verificar que el usuario a ingresar exista en la tabla de usuarios, filtrando por id de dicho usuario
    SELECT COUNT(*) INTO usuario_existente FROM Usuario WHERE id = p_id_usuario AND eliminado = TRUE;

    IF  usuario_existente > 0 THEN
        -- mensaje de error si el usuario no existe
        SELECT 'Ocurrió un error el usuario no existe.' AS mensaje;
    ELSE
        BEGIN
            -- en caso de que todo sea correcto se insertan los datos
            INSERT INTO Proyecto ( nombre, descripcion, fecha, id_usuario, eliminado ) 
            VALUES ( p_nombre, p_descripcion, NOW(), p_id_usuario, FALSE );
            SELECT 'Proyecto ingresado con exito.' AS mensaje, LAST_INSERT_ID() AS id;
        END;
    END IF;
    
    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al insertar el proyecto.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;

DELIMITER $$

-- Insertar una nueva imagen en un proyecto
DROP PROCEDURE IF EXISTS sp_insertarImagenProyecto$$

CREATE PROCEDURE sp_insertarImagenProyecto(
    IN p_url_archivo VARCHAR(255),
    IN p_id_proyecto INT
)
BEGIN
    DECLARE proyecto_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- Verificar si el proyecto existe
    SELECT COUNT(*) INTO proyecto_existente FROM proyecto WHERE id = p_id_proyecto;

    IF proyecto_existente = 0 THEN
        SELECT 'Error: El proyecto no existe.' AS mensaje;
    ELSE
        BEGIN
            INSERT INTO imagen_proyecto (url_archivo, id_proyecto, eliminado)
            VALUES (p_url_archivo, p_id_proyecto, FALSE);
            SELECT 'Imagen agregada con éxito.' AS mensaje;
        END;
    END IF;

    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al insertar la imagen.' AS mensaje;
    END IF;

END$$

DELIMITER ;

DELIMITER $$

-- Actualizar un proyecto existente
DROP PROCEDURE IF EXISTS sp_actualizarProyecto$$

CREATE PROCEDURE sp_actualizarProyecto(
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(500),
    IN p_id_usuario INT
)
BEGIN
    DECLARE proyecto_existente INT;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- Verificar si el proyecto existe
    SELECT COUNT(*) INTO proyecto_existente FROM proyecto WHERE id = p_id;

    IF proyecto_existente = 0 THEN
        SELECT 'El proyecto no existe.' AS mensaje;
    ELSE
        UPDATE Proyecto
        SET nombre = p_nombre,
            descripcion = p_descripcion,
            id_usuario = p_id_usuario
        WHERE id = p_id;

        -- Verificar si la actualización afectó filas
        IF ROW_COUNT() > 0 THEN
            UPDATE Proyecto
            SET fecha = NOW()
            WHERE id = p_id;

            SELECT 'Actualización exitosa.' AS mensaje;
        ELSE
            SELECT 'No se realizaron cambios en nombre y descripcion.' AS mensaje;
        END IF;
    
    END IF;

    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al actualizar el proyecto.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;

DELIMITER $$

-- Eliminar (desactivar) un proyecto
DROP PROCEDURE IF EXISTS sp_eliminarProyecto$$

CREATE PROCEDURE sp_eliminarProyecto(
    IN p_id INT
)
BEGIN
    DECLARE proyecto_existente INT;
    DECLARE eliminado_anterior BOOLEAN;
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;

    -- Verificar si el proyecto existe y si ya está eliminado
    SELECT COUNT(*), eliminado INTO proyecto_existente, eliminado_anterior 
    FROM proyecto WHERE id = p_id;

    START TRANSACTION;-- se utilizan transacciones para que la eliminacion del proyecto y sus imagenes sea un todo

    IF proyecto_existente = 0 THEN
        -- Mensaje de error si el proyecto no existe
        SELECT 'El proyecto no existe.' AS mensaje;
    ELSEIF eliminado_anterior = TRUE THEN
        -- Mensaje si el proyecto ya fue eliminado
        SELECT 'El proyecto ya está eliminado.' AS mensaje;
    ELSE
        BEGIN 
            -- Marcar el proyecto como eliminado
            UPDATE proyecto
            SET eliminado = TRUE
            WHERE id = p_id;

            UPDATE imagen_proyecto
            SET eliminado = TRUE
            WHERE id_proyecto = p_id;

            SELECT 'Proyecto eliminado exitosamente.' AS mensaje;
            COMMIT;
        END;
    END IF;
    
    -- Manejo de excepciones
    IF error_occurred THEN
        SELECT 'Ocurrió un error al eliminar el proyecto.' AS mensaje;
        ROLLBACK;
    END IF;

END$$

DELIMITER ;