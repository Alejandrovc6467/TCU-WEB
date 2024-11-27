-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2024 at 01:26 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `congresedimentos`
--
CREATE DATABASE IF NOT EXISTS `congresedimentos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `congresedimentos`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarAprobacionDocumento` (IN `userEmail` VARCHAR(255), IN `newAprobacionDocumento` TINYINT)   BEGIN
    UPDATE usuario
    SET aprobacionDocumento = newAprobacionDocumento
    WHERE email = userEmail;

    SELECT 1 AS resultado;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarDocumentoEstado` (IN `iddocumento` INT(11), IN `nuevoestado` TINYINT(1))   BEGIN
    UPDATE documento
    SET aprobado = nuevoestado
    WHERE id = iddocumento;

    select 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarPagoDocumento` (IN `userEmail` VARCHAR(255), IN `newPagoDocumento` TINYINT)   BEGIN
    UPDATE usuario
    SET pagoDocumento = newPagoDocumento
    WHERE email = userEmail;

    SELECT 1 AS resultado;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarParticipacionPremio` (IN `user_email` VARCHAR(255), IN `newEstadoParticipacion` TINYINT(1))   BEGIN
    
    UPDATE usuario
    SET participacionPremio = newEstadoParticipacion
    WHERE email = user_email;

    SELECT 1 AS resultado;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarRolUsuario` (IN `p_email` VARCHAR(255), IN `p_rol` VARCHAR(255))   BEGIN
    UPDATE usuarios
    SET rol = p_rol
    WHERE email = p_email;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_tema_documento` (IN `id_documento` INT, IN `nuevo_tema` VARCHAR(255))   BEGIN
    UPDATE documento
    SET temadocumento = nuevo_tema
    WHERE id = id_documento;

    SELECT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarDocumento` (IN `p_url_documento` VARCHAR(255), IN `p_email_user` VARCHAR(255), IN `p_temadocumento` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Error en la inserción
        SELECT 0;
    END;
    
    -- Inserción de datos
    INSERT INTO documento (url_documento, email_user, temadocumento, delete_flag)
    VALUES (p_url_documento, p_email_user, p_temadocumento, 0);
    
    -- Inserción exitosa
    SELECT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarNuevoUsuario` (IN `p_email` VARCHAR(255), IN `p_contrasenia` VARCHAR(255), IN `p_nombre` VARCHAR(255), IN `p_apellidos` VARCHAR(255), IN `p_institucion` VARCHAR(255), IN `p_pais` VARCHAR(255))   BEGIN

    IF (SELECT COUNT(*) FROM usuario WHERE p_email = email) = 0 THEN

      INSERT INTO usuario (email, contrasenia, nombre, apellidos, institucion, activo, rol, pais)
      VALUES (p_email, p_contrasenia, p_nombre, p_apellidos, p_institucion, FALSE, "usuario", p_pais, FALSE, FALSE);


      SELECT 1 as resultado;

    ELSE
        SELECT 0 as resultado;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerInformacionDocumento` (IN `temaDocumentoParam` VARCHAR(255))   BEGIN
    SELECT d.id, u.email, u.nombre, u.apellidos, u.institucion, d.url_documento, d.aprobado
    FROM usuario u
    JOIN documento d ON u.email = d.email_user
    WHERE d.temadocumento = temaDocumentoParam;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerParticipacionPremio` (IN `p_email` VARCHAR(255))   BEGIN
    DECLARE participacion INT;
    
    SELECT participacionPremio INTO participacion
    FROM usuario
    WHERE email = p_email;
    
    IF participacion IS NOT NULL THEN
        select participacion;
    ELSE
        SELECT participacion = -1; -- Indicador de que el correo no fue encontrado
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerUsuarios` ()   BEGIN
    SELECT *  FROM usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_activar_cuenta` (IN `email_param` VARCHAR(255))   BEGIN
    DECLARE cuenta_id VARCHAR(255);

    SELECT email INTO cuenta_id FROM usuario WHERE email = email_param;

    IF cuenta_id IS NOT NULL THEN
        UPDATE usuario SET activo = TRUE WHERE email = cuenta_id;

        SELECT 1 as resultado;
    ELSE
        SELECT 0 as resultado;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login` (IN `p_nombre` VARCHAR(255), IN `p_contrasena` VARCHAR(255))   BEGIN
    select rol, email, nombre, apellidos, pagoDocumento, aprobacionDocumento
    FROM usuario
    WHERE email = p_nombre && contrasenia = p_contrasena && activo = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUserRole` (IN `userEmail` VARCHAR(255), IN `newRole` VARCHAR(255))   BEGIN
    UPDATE usuario
    SET rol = newRole
    WHERE email = userEmail;

    select 1 as resultado;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `url_documento` varchar(255) DEFAULT NULL,
  `email_user` varchar(255) DEFAULT NULL,
  `temadocumento` varchar(255) DEFAULT NULL,
  `delete_flag` tinyint(1) DEFAULT NULL,
  `aprobado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documento`
--

INSERT INTO `documento` (`id`, `url_documento`, `email_user`, `temadocumento`, `delete_flag`, `aprobado`) VALUES
(83, 'uploads/Plantilla_resumen_IV_CISE.IMTA.REBECA.1.pdf', 'rebeca_gonzalez@tlaloc.imta.mx', 'GESTIÓN DE CUENCAS', 0, 0),
(87, 'uploads/Todas las sesiones editados_comentarioWWatler (2).docx', 'dangelo.sandoval@gmail.com', 'PREVENCIÓN DE DESASTRES', 0, 0),
(88, 'uploads/Resumen AMH1.docx', 'ahansen@tlaloc.imta.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(91, 'uploads/Resumen_IV_CISE_Leroy_Santana.docx', 'lsantanam2300@alumno.ipn.mx', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(93, 'uploads/Resumen Hansen et al. IV CISE_1.docx', 'ahansen@tlaloc.imta.mx', 'MANEJO Y REHABILITACIÓN DE SEDIMENTOS CONTAMINADOS', 0, 0),
(101, 'uploads/Resumen_IV_CISE_NVCV_1.docx', 'nadiav.cruzv@gmail.com', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(103, 'uploads/Influencia de los sólidos en suspensión_XVBY_MCMR_1.docx', 'xochbell2708@gmail.com', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(104, 'uploads/RESUMEN_CISE_2024_XVBY_MCMR.pdf', 'xochbell2708@gmail.com', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(105, 'uploads/Defeo CISE 2024 Resumen.docx', 'sdefeo@ucmerced.edu', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(106, 'uploads/Resumen CISE Henrique.docx', 'chaveshml@gmail.com', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(107, 'uploads/Plantilla_resumen_IV_CISE.docx', 'fr19.martinez@gmail.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(111, 'uploads/Resumen CICE.docx', 'pablogarciach@gmail.com', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(112, 'uploads/RESUMEN_KLGA_IV_CISE.pdf', 'kgineza1601@alumno.ipn.mx', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(116, 'uploads/RodalMoralesND_resumen_IV_CISE_2.docx', 'nrodalmorales@ucmerced.edu', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(122, 'uploads/Plantilla_resumen_IV_CISE Vetiver 03.10.24_5.docx', 'etra.julie@gmail.com', 'SOLUCIONES BASADAS EN LA NATURALEZA', 0, 0),
(124, 'uploads/Resumen_IV_CISE_JIES_I_Vanessa_Moreno_1.docx', 'vanessag.moay@gmail.com', 'MANEJO Y REHABILITACIÓN DE SEDIMENTOS CONTAMINADOS', 0, 0),
(125, 'uploads/Resumen_IV_CISE Centeno et al.docx', 'alfaroca@uaslp.mx', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(131, 'uploads/Resumen_IV_CISE_Almacenamiento de carbono en sedimentos_5.docx', 'ddominguez1800@alumno.ipn.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(133, 'uploads/Abalo_CLR (IV CISE Abstract)_1.pdf', 'cabalo@ucmerced.edu', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(136, 'uploads/resumen_IVCISE RodriguezSalemi.pdf', 'vrodriguezsalemi@gmail.com', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(142, 'uploads/Monitoreo ambiental para la sostenibilidad de los recursos hídricos en Ciudad Universitaria, UNAM, México.pdf', 'calidad.pumagua@gmail.com', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(144, 'uploads/Plantilla_resumen_IV_CISE[Carlos Vences].pdf', 'carlos_vences@comunidad.unam.mx', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(145, 'uploads/Plantilla_resumen_IV_CISE[Mayumy Cabrera].pdf', 'mayari77@yahoo.com.mx', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(146, 'uploads/Resumen_IV_CISE_Flores_Ronces.docx', 'ron_alf23@hotmail.com', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(147, 'uploads/Plantilla_resumen_IV_CISE[Andrés Salinas][OK].docx', 'andru.omassi@gmail.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(148, 'uploads/VARIACIÓN ESPACIAL Y TEMPORAL DEL TRANSPORTE DE SEDIMENTOS EN EL RIO MAGDALENA .pdf', 'jufochoa@unal.edu.co', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(149, 'uploads/EL RIO MAGDALENA EN SU VALLE MEDIO. EL ULTIMO ENTRE LOS GRANDES RIOS.pdf', 'jufochoa@unal.edu.co', 'MORFOLOGÍA EN RÍOS Y CUERPOS DE AGUA', 0, 0),
(150, 'uploads/Plantilla_resumen_IV_CISE[Luis Garduño].pdf', 'luis.castro@ingenieria.unam.edu', 'MORFOLOGÍA EN RÍOS Y CUERPOS DE AGUA', 0, 0),
(151, 'uploads/Resumen_CISE.docx', 'cordoba.olga@colpos.mx', 'GESTIÓN DE CUENCAS', 0, 0),
(152, 'uploads/Resumen_CISE_1.docx', 'cordoba.olga@colpos.mx', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(153, 'uploads/RESUMEN_CISE.pdf', 'basilio_ed@hotmail.com', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(154, 'uploads/Resumen_IV_CISE_Jojoa.pdf', 'odjojoaa@unal.edu.co', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(155, 'uploads/Resumen_IV_CISE_Esteban Hernandez Medina.pdf', 'estebanhm21@gmail.com', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(156, 'uploads/Resumen_IV_CISE_Modelo RioMagdalena.pdf', 'franklintorres@correo.unicordoba.edu.co', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(157, 'uploads/Resumen_IV_CISE Erosion_MaizCafé.pdf', 'j.baumann@cgiar.org', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(158, 'uploads/Resumen_IV_CISE Erosion_MaízCafé2.docx', 'j.baumann@cgiar.org', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(159, 'uploads/Resumen_IV_CISE_Lluvias Erosividad2.pdf', 'j.baumann@cgiar.org', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(160, 'uploads/Resumen_IV_CISE_Lluvias Erosividad2.docx', 'j.baumann@cgiar.org', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(161, 'uploads/Resumen_IV_CISE_Gestión_de_Cuencas_MAHH.pdf', 'malbher@igeofisica.unam.mx', 'GESTIÓN DE CUENCAS', 0, 0),
(162, 'uploads/resumen_obras de autoliberación de peces_IV_CISE.docx', 'emerson221@gmail.com', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(163, 'uploads/erosion APFFLT _IV_CISE_JIES_I.pdf', 'gflores@pampano.unacar.mx', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(164, 'uploads/Resumen_IV_CISE_ Modelo SWAT Microcuenca Quiejel Guatemala - Pedro Rivera IMTA.docx', 'privera@tlaloc.imta.mx', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(165, 'uploads/Resumen_IV_CISE_SolucionesBS_MHH&GHZ.pdf', 'malbher@igeofisica.unam.mx', 'SOLUCIONES BASADAS EN LA NATURALEZA', 0, 0),
(167, 'uploads/Resumen_IV CISE_Elías-García et al._1.docx', 'vigeligar@gmail.com', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(168, 'uploads/Plantilla_resumen_IV_CISE.pdf', 'afzunigac@gmail.com', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(172, 'uploads/Plantilla_resumen_IV_CISECaballeroM_1.docx', 'maga@igeofisica.unam.mx', 'FECHADO Y RECONSTRUCCIÓN HISTÓRICA DE PROCESOS EN SEDIMENTOS', 0, 0),
(173, 'uploads/Resumen_IV_CISE Spalletti.docx', 'pspalletti@gmail.com', 'MORFOLOGÍA EN RÍOS Y CUERPOS DE AGUA', 0, 0),
(174, 'uploads/Beutel_resumen_IV_CISE .pdf', 'mwbeutel201@gmail.com', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(175, 'uploads/DISEÑO DE UNA CENTRAL DE COGENERACIÓN PARA LA PRODUCCIÓN DE ENERGÍA ELÉCTRICA USANDO BAGAZO DE AGAVE COMO BIOCOMBUSTIBLE SÓLIDO.pdf', 'ferrerangel098@gmail.com', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(176, 'uploads/Plantilla_resumen_IV_CISE Quevedo Olga.pdf', 'olga.quevedop@ug.edu.ec', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(177, 'uploads/Resumen_CISE_Producciondesedimentos.pdf', 'wadiazu@unal.edu.co', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(178, 'uploads/Resumen_CISE_DiseñoApartado.pdf', 'aemunozo@unal.edu.co', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(179, 'uploads/IV_CISE_MEEHL.pdf', 'j.m.martin@cgiar.org', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(180, 'uploads/IV_CISE_ClimaLoCa.pdf', 'j.m.martin@cgiar.org', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(181, 'uploads/Resumen_IV_CISE_oficial _DiseñoApartado&Carepa.pdf', 'aemunozo@unal.edu.co', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(182, 'uploads/Abstract_CISEIV_Valencia_Rúa.pdf', 'ijruar@unal.edu.co', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(183, 'uploads/Resumen_Perpiñan & Posada 2024.pdf', 'aaperpinang@unal.edu.co', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(184, 'uploads/Resumen_IV_CISE_Salazar-RemigioL.docx', 'lausr.93@hotmail.com', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(185, 'uploads/Resumen_Iván et al 2024.pdf', 'aaperpinang@unal.edu.co', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(186, 'uploads/Resumen_Perpiñan & Posada 2 2024.pdf', 'aaperpinang@unal.edu.co', 'PREVENCIÓN DE DESASTRES', 0, 0),
(187, 'uploads/Ruiz_Valverde_2024_resumen_IV_CISE.docx', 'paulo.ruizcubillo@ucr.ac.cr', 'MORFOLOGÍA EN RÍOS Y CUERPOS DE AGUA', 0, 0),
(188, 'uploads/IP0577-Keller-S Climate Resiliency Measures .pdf', 'gordonrkeller@gmail.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(189, 'uploads/IV_CISE MASARE final.pdf', 'rbianchi@mi.unc.edu.ar', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(190, 'uploads/IV_CISE Modelo final.pdf', 'rbianchi@mi.unc.edu.ar', 'GESTIÓN DE CUENCAS', 0, 0),
(191, 'uploads/Abalo_abstract_IV_CISE.pdf', 'cabalo@ucmerced.edu', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(192, 'uploads/Abalo_abstract_IV_CISE_1.pdf', 'cabalo@ucmerced.edu', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(193, 'uploads/Plantilla_resumen_IV_CISE.RPizarro.Chile.docx', 'rpizarro@utalca.cl', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(194, 'uploads/Vega, Raimat, Corroyer y Boyer 2024.pdf', 'arabela.vega@gmail.com', 'SOLUCIONES BASADAS EN LA NATURALEZA', 0, 0),
(195, 'uploads/Resumen_P1_IV_CISE_2024.pdf', 'clfton@uni.pe', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(196, 'uploads/Resumen_P2_IV_CISE_2024.pdf', 'clfton@uni.pe', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(197, 'uploads/Marianela_Alfaro_resumen_IV_CISE.pdf', 'marianela.alfaro@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(198, 'uploads/Resumen_IV_CISE JLG &PRL.pdf', 'priggioni@ice.go.cr', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(199, 'uploads/Plantilla_resumen_IV_CISE_5.docx', 'cribas@fcien.edu.uy', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(200, 'uploads/Resumen_IV_CISE_Carolina Ribas.pdf', 'cribas@fcien.edu.uy', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(201, 'uploads/Mello K_resumen_IV_CISE.pdf', 'kmello@fcien.edu.uy', 'GESTIÓN DE CUENCAS', 0, 0),
(202, 'uploads/Ribas C_Resumen_IV_CISE.pdf', 'cribas@fcien.edu.uy', 'GESTIÓN DE CUENCAS', 0, 0),
(203, 'uploads/Ribas C_Resumen_IV_CISE.docx', 'cribas@fcien.edu.uy', 'GESTIÓN DE CUENCAS', 0, 0),
(204, 'uploads/Abstract_IV_CISE_Wong & Rojas.docx', 'anayansi.wong@ucr.ac.cr', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(205, 'uploads/Erosion Gerardo Mora.docx', 'enriquemora270402@estudiantec.cr', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(206, 'uploads/Plantilla_resumen_IV_CISE_CC.docx', 'chreties@fing.edu.uy', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(207, 'uploads/Sosa et al.docx', 'beatriz@fcien.edu.uy', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(208, 'uploads/Sosa et al_1.docx', 'beatriz@fcien.edu.uy', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(209, 'uploads/Resumen_IV_CISE_Castellón Avalos Marinelys_RevBlancaE_RevBárbaraG.docx', 'marinelys.castellon89@gmail.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(210, 'uploads/Resumen_IV_CISE_Castellón Avalos Marinelys_RevBlancaE_RevBárbaraG.pdf', 'marinelys.castellon89@gmail.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(211, 'uploads/Plantilla_resumen_IV_CISE-MatiasGomez-Chile.docx', 'matias.gomez@utalca.cl', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(212, 'uploads/Sandoval y Hansen.docx', 'dangelo.sandoval@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(213, 'uploads/EASC_resumen_IV_CISE.docx', 'esanchez@tlaloc.imta.mx', 'OBRAS HIDRÁULICAS E INTERACCIÓN AGUA-SEDIMENTO', 0, 0),
(214, 'uploads/Plantilla_resumen_IV_CISE_DS.docx', 'vanessag.moay@gmail.com', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(215, 'uploads/Resumen_IV_CISE_FIMA.pdf', 'fima@itaipu.gov.py', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(216, 'uploads/Resumen_IV_CISE_FIMA.docx', 'fima@itaipu.gov.py', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(217, 'uploads/Resumen_IV_CISE_Sykora.pdf', 'veronica.sykora@ambiente.gub.uy', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(218, 'uploads/Congreso Costa Rica 2024.pdf', 'mperezb@fagro.edu.uy', 'TRANSPORTE DE SEDIMENTOS', 0, 0),
(219, 'uploads/Resumen_IV_CISE_LUZPAOLA.docx', 'luzpaola@itaipu.gov.py', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(220, 'uploads/Laiz, O_resumen_IV_CISE.docx', 'orla.laiz.46@gmail.com', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(221, 'uploads/IV CISE_2024_Mayén et al.docx', 'carlosduran_88@ciencias.unam.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(222, 'uploads/Resumen_FBalocchi_etal.docx', 'francisco.balocchi@arauco.com', 'EROSIÓN, SEDIMENTOS Y CAMBIO CLIMÁTICO', 0, 0),
(223, 'uploads/HugoLC_Resumen_IV_CISE.pdf', 'hugolopezcamarillo4.6@gmail.com', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(224, 'uploads/Sandoval et al.docx', 'dangelo.sandoval@ucr.ac.cr', 'MANEJO Y REUSO DE SEDIMENTOS', 0, 0),
(225, 'uploads/resumen  cise 2024 Alonso et al.pdf', 'pteroestigma@gmail.com', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(226, 'uploads/Resumen_IV_CISE.pdf', 'maria.riveravillalobos@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(227, 'uploads/Resumen proyecto C2089.docx', 'CARLOS.SANCHEZROMERO@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(228, 'uploads/Resumen proyecto C2089_1.docx', 'CARLOS.SANCHEZROMERO@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(229, 'uploads/Resumen-Aporte del sector agropecuario a la carga externa de carbono en el embalse La Angostura.pdf', 'fiorella.biolley@ucr.ac.cr', 'SEDIMENTOS COMO VECTORES DE TRANSPORTE DE CONTAMINANTES', 0, 0),
(230, 'uploads/KarenPA_Resumen_IV_CISE.pdf', 'karen.pinon@uaem.edu.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(231, 'uploads/Resumen_congreso_Dahayana.pdf', 'gamboadaya@gmail.com', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(232, 'uploads/Agustín Solano Arguedas - Resumen IV CISE.pdf', 'agustin.solano@ucr.ac.cr', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(233, 'uploads/Agustín Solano Arguedas - Resumen IV CISE.docx', 'agustin.solano@ucr.ac.cr', 'MONITOREO Y CARACTERIZACIÓN DE SEDIMENTOS', 0, 0),
(234, 'uploads/RESUMEN FINAL CONGRESO.pdf', 'ana.fallasalvarez@ucr.ac.cr', 'PERSPECTIVA AMBIENTAL, SOCIAL Y LEGISLATIVA', 0, 0),
(235, 'uploads/Resumen_IV_CISE_1.pdf', 'maria.menesesgomez@ucr.ac.cr', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(236, 'uploads/KarenPA_Resumen_IV_CISE_1.pdf', 'karen.pinon@uaem.edu.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0),
(237, 'uploads/KarenPA_Resumen_IV_CISE_2.pdf', 'karen.pinon@uaem.edu.mx', 'EROSIÓN, SEDIMENTOS, ECOLOGÍA Y CALIDAD DEL AGUA', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `email` varchar(255) NOT NULL,
  `contrasenia` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `institucion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  `rol` varchar(255) DEFAULT NULL,
  `pais` varchar(255) DEFAULT NULL,
  `pagoDocumento` tinyint(4) DEFAULT NULL,
  `aprobacionDocumento` tinyint(4) DEFAULT NULL,
  `participacionPremio` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`email`, `contrasenia`, `nombre`, `apellidos`, `institucion`, `activo`, `rol`, `pais`, `pagoDocumento`, `aprobacionDocumento`, `participacionPremio`) VALUES
('aaperpinang@unal.edu.co', 'Ap19852020*', 'Adrian Augusto ', 'PERPIÑAN GUERRA', 'UNIVERSIDAD NACIONAL DE COLOMBIA', 1, 'Usuario', 'Colombia', 0, 0, 0),
('aemunozo@unal.edu.co', 'unalmed9710', 'Adrian Esteban ', 'Muñoz Osorio', 'UNIVERSIDAD NACIONAL DE COLOMBIA', 1, 'Usuario', 'Colombia', 0, 0, 0),
('afzunigac@gmail.com', 'Serfeliz123', 'ANDRES FELIPE', 'ZUÑIGA CABEZAS', 'CIIEMAD-IPN', 1, 'Usuario', 'México', 0, 0, 0),
('agustin.solano@ucr.ac.cr', 'Charlatan6.CISE', 'Agustín', 'Solano Arguedas', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('ahansen@tlaloc.imta.mx', 'Xochitl81', 'Anne M.', 'Hansen', 'Instituto Mexicano de Tecnología del Agua', 1, 'Administrador de Contenidos Documentales', 'México', 0, 0, 0),
('alejandrovc177@gmail.com', 'alejandrovc177@', 'Alejandro', 'Vasquez Cordero', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 1, 1, 1),
('alfaroca@uaslp.mx', 'ChMj63_C', 'Ma. Catalina', 'Alfaro de la Torre', 'Facultad de Ciencias Químicas, Universidad Autónoma de San Luis Potosí', 1, 'Usuario', 'México', 0, 0, 0),
('ana.fallasalvarez@ucr.ac.cr', 'Ana_2801881', 'Ana Lucía ', 'Fallas Álvarez', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('anayansi.wong@ucr.ac.cr', 'jonathan7287.', 'Anayansi', 'Wong Monge', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('andru.omassi@gmail.com', 'puerto84', 'Andrés', 'Salinas Omassi', 'Posgrado en Ciencias del Mar y Limnología', 1, 'Usuario', 'México', 0, 0, 0),
('angulobcs@gmail.com', 'lombriz', 'Beatriz', 'ANGULO ', 'IVIC ', 1, 'Usuario', 'Venezuela', 0, 0, 0),
('arabela.vega@gmail.com', 'Maracas123.', 'Arabela Sofía', 'Vega Aguilar', 'Independiente', 1, 'Usuario', 'Costa Rica', 1, 0, 0),
('basilio_ed@hotmail.com', 'Edson3043', 'Edson Eduardo', 'López Basilio', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('beatriz@fcien.edu.uy', 'NarutoT1', 'Beatriz Marcela', 'Sosa Calleja', 'Universidad de la República', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('blanca_prado@yahoo.com.mx', '825267', 'BLANCA LUCIA', 'PRADO PANO', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('c.mata@fucore.org', 'pamela0708', 'Christian', 'Mata Bonilla', 'Fundación Costarricense para la Restauración Ecológica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('cabalo@ucmerced.edu', '@Christineabalo123', 'Christine Leah', 'Abalo', 'University of California, Merced', 1, 'Usuario', 'México', 1, 0, 0),
('calidad.pumagua@gmail.com', 'PUMAGUA322+', 'Nallely ', 'Vázquez Salvador', 'Instituto de Ingeniería UNAM', 1, 'Usuario', 'México', 0, 0, 0),
('cantellano.eliseo@gmail.com', 'Care571960co', 'Eliseo', 'Cantellano de Rosas', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('CARLOS.SANCHEZROMERO@ucr.ac.cr', 'Carlos.081193', 'CARLOS', 'SÁNCHEZ ROMERO', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('carlosduran_88@ciencias.unam.mx', 'ciliadosbentonicos145', 'Carlos Alberto', 'Durán Ramírez', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('carlos_vences@comunidad.unam.mx', '1024Mayumy', 'Carlos', 'Vences Espinosa', 'Facultad de Ingeniería-UNAM', 1, 'Usuario', 'México', 0, 0, 0),
('Castillo.denisse.c@gmail.com', 'RVDKCeli', 'Denisse', 'Castillo', 'Ikiam', 1, 'Usuario', 'Ecuador', 0, 0, 0),
('castillojosepablo@gmail.com', 'J.A2422', 'José', 'Castillo Pablo', 'BIOFOREST', 1, 'Usuario', 'Panamá', 0, 0, 0),
('cesar.espiritu.limay1@gmail.com', 'Soydelauni1984', 'Cesar', 'Espiritu', 'Universidad Nacional Agraria La Molina', 1, 'Usuario', 'Perú', 0, 0, 0),
('chaveshml@gmail.com', 'Caceri07', 'Henrique ', 'Chaves', 'Universidade de Brasília', 1, 'Usuario', 'Brasil', 0, 0, 0),
('chreties@fing.edu.uy', 'Curi$160', 'Christian', 'Chreties', 'IMFIA-Facultad de Ingeniería-Universidad de la República', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('clfton@uni.pe', '01Zap$0138_1', 'Clifton', 'Paucar Y Montenegro', 'Universidad Nacional de Ingenieria', 1, 'Usuario', 'Perú', 0, 0, 0),
('cordoba.olga@colpos.mx', 'Vanecongreso', 'Olga Vanessa', 'Córdoba Sandoval', 'Colegio de Postgraduados', 1, 'Usuario', 'Nicaragua', 0, 0, 0),
('cribas@fcien.edu.uy', 'crf250594', 'Carolina', 'Ribas Fros', 'Universidad de la República', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('dangelo.sandoval@gmail.com', 'Bloque100', 'DAngelo', 'Sandoval', 'UCR', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('dangelo.sandoval@ucr.ac.cr', 'Turri2023', 'DAngelo', 'Sandoval', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('dangelo.ucr@gmail.com', 'Prueba', 'Dangelo', 'Sandoval', 'Universidad de Costa Rica', 1, 'SOLUCIONES BASADAS EN LA NATURALEZA', 'Costa Rica', 0, 0, 0),
('daniandre200126@gmail.com', '04141978', 'Daniela ', 'André campos ', 'UCR', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('ddominguez1800@alumno.ipn.mx', 'Lasker1$', 'Diego', 'Dominguez Solis', 'Instituto Politécnico Nacional', 1, 'Usuario', 'México', 0, 0, 0),
('denisse.castillo@est.ikiam.edu.ec', 'RVDKCeli2001', 'Denisse', 'Castillo', 'Ikiam', 1, 'Usuario', 'Ecuador', 0, 0, 0),
('emerson221@gmail.com', 'Parra221ya$', 'Emerson', 'Parra', 'Universidad Nacional de Colombia', 1, 'Usuario', 'Colombia', 0, 0, 0),
('enriquemora270402@estudiantec.cr', 'Estatica_2021', 'Gerardo', 'Mora Pérez', 'Instituto Tecnológico de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('esanchez@tlaloc.imta.mx', 'hqCN10v', 'Enrique A', 'Sánchez', 'Instituto Mexicano de Tecnología del Agua', 1, 'Usuario', 'México', 0, 0, 0),
('estebanhm21@gmail.com', 'pedePEPA12', 'Esteban', 'Hernández Medina', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('etra.julie@gmail.com', '110Lupines', 'Julie', 'Etra', 'Western Botanical Services, Inc. ', 1, 'Usuario', 'México', 0, 0, 0),
('ferrerangel098@gmail.com', 'FeRRer066', 'Miguel Ángel ', 'Ferrer Hernández ', 'Tecnológico Nacional de México, Campus Morelia', 1, 'Usuario', 'México', 1, 0, 0),
('fima@itaipu.gov.py', 'fimaciseccp1912', 'Franklin', 'Molinas', 'Itaipu Binacional ', 1, 'Usuario', 'Paraguay', 0, 0, 0),
('fiorella.biolley@ucr.ac.cr', 'Fio.2019', 'Fiorella', 'Biolley Solano', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('fr19.martinez@gmail.com', 'Celeste_49536930', 'Felix Rocael', 'Martínez Gómez', 'FAUSAC', 1, 'Usuario', 'Guatemala', 1, 0, 0),
('francisco.balocchi@arauco.com', '18131427', 'Francisco', 'Balocchi', 'Bioforest', 1, 'Usuario', 'Chile', 0, 0, 0),
('francisco.urueta@soudermiller.com', 'faBioliTa28', 'Francisco X', 'Urueta', 'SMA', 1, 'Usuario', 'México', 0, 0, 0),
('franklintorres@correo.unicordoba.edu.co', 'Fmtb_2024', 'Franklin', 'Torres Bejarano', 'Universidad de Córdoba', 1, 'Usuario', 'Colombia', 0, 0, 0),
('gamboadaya@gmail.com', 'Christopher.99', 'Dahayana Alexandra', 'Gamboa Monge', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('gflores@pampano.unacar.mx', 'jgft5687', 'Juan Gabriel', 'Flores Trujillo', 'Universidad Autónoma del Carmen', 1, 'Usuario', 'México', 0, 0, 0),
('go.3692580147@gmail.com', 'Historia123', 'Gonzalo', 'Contreras Pfäfflin', 'Santaoyála Consultores y Asociados ', 1, 'Usuario', 'Bolivia', 0, 0, 0),
('gordonrkeller@gmail.com', 'PuraVida2024!!', 'GORDON', 'KELLER', 'UNITED STATES--GENESEE GEOTECHNICAL', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('graciela.perez@ucr.ac.cr', 'araya', 'Graciela', 'Pérez Araya', 'UCR', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('hugolopezcamarillo4.6@gmail.com', 'Eukaryahlc13', 'Hugo', 'López Camarillo', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('ijruar@unal.edu.co', 'Sedimentario', 'Iván De Jesús', 'Rúa Ramírez', 'Universidad Nacional', 1, 'Usuario', 'Colombia', 0, 0, 0),
('ipn_mac@yahoo.com.mx', 'enero1945$', 'MIGUEL', 'ALVARADO CARDONA', 'INSTITUTO POLITÉCNICO NACIONAL', 1, 'Usuario', 'México', 0, 0, 0),
('j.baumann@cgiar.org', 'Tail.DRAg57', 'Jurgen', 'Baumann', 'Alianza Bioversity International y CIAT', 1, 'Usuario', 'Honduras', 0, 0, 0),
('j.m.martin@cgiar.org', 'CIAT2021_3', 'JAVIER', 'MARTIN', 'Alliance Bioversity-CIAT', 1, 'Usuario', 'Colombia', 0, 0, 0),
('jagarciaa@uaemex.mx', 'Cise4518', 'Juan Antonio', 'Garcia Aragon', 'IITCA-Univ. Aut. estado de Mexico', 1, 'Usuario', 'México', 0, 0, 0),
('jeison.rojas812@gmail.com', 'TxTCarTago123', 'Jeison ', 'Rojas Valverde ', 'Universidad de Costa Rica ', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('josesamueltejeda@gmail.com', 'Josesamuel7i', 'SAMUEL', 'TEJEDA', 'INSTITUTO NACIONAL DE INVESTIGACIONES NUCLEARES', 1, 'Usuario', 'México', 0, 0, 0),
('jufochoa@unal.edu.co', 'Jacobo0508', 'juan felipe', 'ochoa', 'universidad nacional de colombia', 1, 'Usuario', 'Colombia', 1, 0, 0),
('jzunigam@ice.go.cr', 'Jozucise', 'Jose', 'Zuniga', 'ICE', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('karen.pinon@uaem.edu.mx', 'termodinamica', 'Karen', 'Piñón Acosta', 'Universidad Autónoma del Estado de Morelos', 1, 'Usuario', 'México', 0, 0, 0),
('Kendall.rodriguezmontero@ucr.ac.cr', 'K3ndall.2334', 'Kendall', 'Rodriguez', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('kgineza1601@alumno.ipn.mx', 'Sami1234', 'Karla Lizbeth ', 'Ginez Aldana', 'Instituto Politécnico Nacional', 1, 'Usuario', 'México', 0, 0, 0),
('kmello@fcien.edu.uy', 'Febrero25', 'Karolain Sibeli', 'Mello Macedo', 'Universidad de la República', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('lausr.93@hotmail.com', 'Lauris_28', 'Laura', 'Salazar Remigio', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('lorepbruno@gmail.com', '31228077l', 'Lorena Portilho Bruno', 'Lorena', 'Universidade de Brasília', 1, 'Usuario', 'Brasil', 0, 0, 0),
('lsantanam2300@alumno.ipn.mx', 'J91SM5ago*', 'Leroy Agustín', 'Santana Martí', 'Instituto Politécnico Nacional (IPN)', 1, 'Usuario', 'México', 1, 0, 0),
('luis.castro@ingenieria.unam.edu', 'haMqyd-caxqy3-fohwew', 'Luis Bruno', 'Garduño Castro', 'Universidad Autónoma Metropolitana', 1, 'Usuario', 'México', 1, 0, 0),
('luzpaola@itaipu.gov.py', 'flordelotopaola', 'Luz Paola', 'Inchausti S.', 'Itaipu Binacional', 1, 'Usuario', 'Paraguay', 0, 0, 0),
('maga@igeofisica.unam.mx', 'YaqueEramigato!', 'Margarita ', 'Caballero', 'Laboratorio de Paleolimnología, Instituto de Geofísica, Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 1, 0, 0),
('malbher@igeofisica.unam.mx', 'finanzas911', 'Mario Alberto', 'Hernández Hernández', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 1, 0, 0),
('maria.menesesgomez@ucr.ac.cr', 'Majolabam.1902', 'María José ', 'Meneses Gómez ', 'Universidad de Costa Rica ', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('maria.riveravillalobos@ucr.ac.cr', 'mariaD0120', 'María Daniela ', 'Rivera Villalobos', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('marianela.alfaro@ucr.ac.cr', 'Manina.79*', 'Marianela', 'Alfaro Santamaría', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('marinelys.castellon89@gmail.com', 'Congresomari', 'Marinelys', 'Castellón Avalos', 'Centro Interdisciplinario de Ciencias Marinas-Instituto Politécnico Nacional (CICIMAR-IPN)', 1, 'Usuario', 'México', 0, 0, 0),
('matias.gomez@utalca.cl', '32484323mg', 'Matías', 'Gómez Canto', 'Universidad de Talca', 1, 'Usuario', 'Chile', 0, 0, 0),
('mayari77@yahoo.com.mx', '1024Mayaven', 'Mayumy Amparo', 'Cabrera Ramírez', 'Facultad de Ingeniería-UNAM', 1, 'Usuario', 'México', 0, 0, 0),
('mbeutel@ucmerced.edu', 'G9yx0r#4qAmbEV$w', 'Marc', 'Beutel', 'University of California, Merced', 1, 'Usuario', 'México', 1, 0, 0),
('mcueva@iahidroclic.com', 'LosPunto2', 'Michel Huber', 'Cueva Portal', 'Hidroclic', 1, 'Usuario', 'Perú', 0, 0, 0),
('mperezb@fagro.edu.uy', 'Diciembre1969', 'Mario', 'Pérez Bidegain', 'Facultad de Agronomia', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('mwbeutel201@gmail.com', '123456789', 'Marc', 'Beutel', 'University of California Merced', 1, 'Usuario', 'México', 0, 0, 0),
('nadiav.cruzv@gmail.com', 'congresoCR24', 'Nadia Viridiana', 'Cruz Vivar', 'Instituto Mexicano de Tecnología del Agua', 1, 'Usuario', 'México', 1, 0, 0),
('nevenca15@gmail.com', 'CHOLAN15', 'NEVENCA', 'CHOLAN RODRIGUEZ', 'Universidad Nacional Agraria La Molina', 1, 'Usuario', 'Perú', 0, 0, 0),
('nrodalmorales@ucmerced.edu', 'Narnia33', 'NAIVY DENNISE', 'RODAL MORALES', 'University of California Merced', 1, 'Usuario', 'México', 1, 0, 0),
('odjojoaa@unal.edu.co', 'Omitar_9817', 'Omar David', 'Jojoa Ávila', 'Universidad Nacional de Colombia', 1, 'Usuario', 'Colombia', 0, 0, 0),
('odjojoaa@unal.edu.oc', 'Omitar_9817', 'Omar David', 'Jojoa Ávila', 'Universidad Nacional de Colombia', 1, 'Usuario', 'Colombia', 0, 0, 0),
('olga.quevedop@ug.edu.ec', 'Odocoileus34', 'Olga ', 'Quevedo ', 'Universidad de Guayaquil ', 1, 'Usuario', 'Ecuador', 0, 0, 0),
('orla.laiz.46@gmail.com', '?xpVdkh,-fF\"RQ7', 'Orlando Rolando', 'Laiz Averhoff', 'Empresa de Aprovechamiento Hidraulico deLa Habana', 1, 'Usuario', 'Cuba', 0, 0, 0),
('pablogarciach@gmail.com', 'jyktap-maVxav-8rojro', 'Pablo', 'Garcia-Chevesich', 'Colorado School of Mines', 1, 'Usuario', 'Chile', 0, 0, 0),
('paochoa@utpl.edu.ec', 'Paoc2XX21', 'Pablo', 'Ochoa', 'Universidad Técnica Particular de Loja', 1, 'Usuario', 'Ecuador', 0, 0, 0),
('paolasuarez792@gmail.com', 'rivers351', 'Paola Alejandra', 'Suárez', 'Universidad Nacional del Nordeste', 1, 'Usuario', 'Argentina', 0, 0, 0),
('paulo.ruizcubillo@ucr.ac.cr', 'CISE#1203', 'Paulo', 'Ruiz Cubillo', 'Universidad de Costa Rica', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('pchang@unal.edu.co', 'Antho123', 'Philippe', 'Chang', 'UNAL', 1, 'Usuario', 'Colombia', 0, 0, 0),
('priggioni@ice.go.cr', 'Pablo2018', 'Priscilla', 'Riggioni Leonhardes', 'ICE', 1, 'Usuario', 'Costa Rica', 0, 0, 0),
('privera@tlaloc.imta.mx', 'Amairani1912', 'PEDRO', 'RIVERA RUIZ', 'INSTITUTO MEXICANO DE TECNOLOGIA DEL AGUA', 1, 'Usuario', 'México', 0, 0, 0),
('pspalletti@gmail.com', 'Meh0212', 'Pablo Daniel', 'SPALLETTI', 'Instituto Nacional del Agua', 1, 'Usuario', 'Argentina', 0, 0, 0),
('pteroestigma@gmail.com', 'Neba64San', 'Perla', 'Alonso EguíaLis', 'Instituto Mexicano de Tecnología del Agua', 1, 'Usuario', 'México', 0, 0, 0),
('rbianchi@mi.unc.edu.ar', 'Rb40246879', 'Rocio', 'Bianchi', 'IDIT CONICET', 1, 'Usuario', 'Argentina', 0, 0, 0),
('rebeca_gonzalez@tlaloc.imta.mx', '0Nutrigrain', 'Rebeca', 'Gonzalez Villela', 'Instituto Mexicano de Tecnología del Agua', 1, 'Usuario', 'México', 0, 0, 0),
('registro.ivcise@gmail.com', 'registro.ivcise@1', 'Usuario', 'Administrador', 'UCR', 1, 'admin', 'Costa Rica', 0, 0, 0),
('registro.ivcise@sa.ucr.ac.cr', 'Comite', 'Comité', 'Científico', 'UCR', 1, 'SOLUCIONES BASADAS EN LA NATURALEZA', 'Costa Rica', 0, 0, 0),
('rfuentes@icc.org.gt', 'Iccguatemala7872', 'Rodolfo Eduardo', 'Fuentes Gómez', 'Instituto Privado de Investigación sobre Cambio Climático (ICC)', 1, 'Usuario', 'Guatemala', 0, 0, 0),
('ron_alf23@hotmail.com', 'tierra08', 'Jose Alfredo ', 'Flores Ronces ', 'Universidad Autónoma de Guerrero ', 1, 'Usuario', 'México', 1, 0, 0),
('rpizarro@utalca.cl', 'ctha2023', 'Roberto', 'Pizarro', 'Universidad de Talca', 1, 'Usuario', 'Chile', 0, 0, 0),
('samuelg100158@gmail.com', 'samuel1234', 'Ángel Samuel', 'Reséndiz González', 'CECyT No. 3 \"Estanislao Ramírez Ruíz\"', 1, 'Usuario', 'México', 0, 0, 0),
('sdefeo@ucmerced.edu', 'JustConference!1', 'Shelby', 'Defeo', 'University of California, Merced', 1, 'Usuario', 'Puerto Rico', 1, 0, 0),
('vanessag.moay@gmail.com', 'cise2024', 'Vanessa ', 'Moreno Ayala', 'Universidad Nacional Autónoma de México', 1, 'Usuario', 'México', 0, 0, 0),
('veronica.sykora@ambiente.gub.uy', 'Piso8', 'Verónica Denise', 'Sykora', 'Ministerio de Ambiente Uruguay', 1, 'Usuario', 'Uruguay', 0, 0, 0),
('vigeligar@gmail.com', 'VICglee98', 'Víctor Gabriel ', 'Elías García', 'Universidad Nacional Autónoma de México, UNAM', 1, 'Usuario', 'México', 0, 0, 0),
('vrodriguezsalemi@gmail.com', 'folklore', 'Valeria', 'Rodriguez Salemi', 'Instituto Nacional del Agua', 1, 'Usuario', 'Argentina', 1, 0, 0),
('wadiazu@unal.edu.co', '28978414Arbey', 'Wilson Arbey', 'Diaz Urueña', 'Universidad Nacional de Colombia sede Medellin', 1, 'Usuario', 'Colombia', 0, 0, 0),
('xochbell2708@gmail.com', '123tolkien3', 'XOCHITL V', 'BELLO YAÑEZ', 'CIIEMAD- INSTITUTO POLITECNICO NACIONAL', 1, 'Usuario', 'México', 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_user` (`email_user`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`email_user`) REFERENCES `usuario` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
