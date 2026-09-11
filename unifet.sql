
CREATE DATABASE IF NOT EXISTS `unifet` ;
USE `unifet`;

-- estructura para tabla unifet.asignacion_docente
CREATE TABLE IF NOT EXISTS `asignacion_docente` (
  `id_asignacion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_periodo` int(10) unsigned NOT NULL,
  `id_docente` int(10) unsigned NOT NULL,
  `id_asignatura` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `uq_asignacion` (`id_periodo`,`id_docente`,`id_asignatura`,`id_programa`),
  KEY `fk_asig_docente` (`id_docente`),
  KEY `fk_asig_asignatura` (`id_asignatura`),
  KEY `fk_asig_programa` (`id_programa`),
  CONSTRAINT `fk_asig_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  CONSTRAINT `fk_asig_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  CONSTRAINT `fk_asig_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  CONSTRAINT `fk_asig_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- datos para la tabla unifet.asignacion_docente: ~0 rows (aproximadamente)

--  estructura para tabla unifet.asignatura
CREATE TABLE IF NOT EXISTS `asignatura` (
  `id_asignatura` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `creditos` tinyint(3) unsigned NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_asignatura`),
  UNIQUE KEY `uq_asignatura_codigo` (`id_institucion`,`codigo`),
  CONSTRAINT `fk_asignatura_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.asignatura: ~0 rows (aproximadamente)

--  estructura para tabla unifet.asignatura_programa
CREATE TABLE IF NOT EXISTS `asignatura_programa` (
  `id_asignatura_programa` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_asignatura` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_asignatura_programa`),
  UNIQUE KEY `uq_asignatura_programa` (`id_asignatura`,`id_programa`),
  KEY `fk_ap_programa` (`id_programa`),
  CONSTRAINT `fk_ap_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE,
  CONSTRAINT `fk_ap_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.asignatura_programa: ~0 rows (aproximadamente)

--  estructura para tabla unifet.auditoria_clase
CREATE TABLE IF NOT EXISTS `auditoria_clase` (
  `id_auditoria` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_horario` int(10) unsigned NOT NULL COMMENT 'Siempre apunta al horario definitivo, nunca a una propuesta FET',
  `fecha_auditoria` date NOT NULL,
  `hora_verificacion` time DEFAULT NULL,
  `id_usuario_auditor` int(10) unsigned NOT NULL,
  `resultado` enum('PENDIENTE','VERIFICADA','NO_REALIZADA') NOT NULL DEFAULT 'PENDIENTE',
  `observacion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`),
  KEY `fk_auditoria_horario` (`id_horario`),
  KEY `fk_auditoria_usuario` (`id_usuario_auditor`),
  KEY `idx_auditoria_fecha` (`fecha_auditoria`,`resultado`),
  CONSTRAINT `fk_auditoria_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE,
  CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario_auditor`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.auditoria_clase: ~0 rows (aproximadamente)

--  estructura para tabla unifet.bloque
CREATE TABLE IF NOT EXISTS `bloque` (
  `id_bloque` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sede` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_bloque`),
  UNIQUE KEY `uq_bloque_codigo` (`id_sede`,`codigo`),
  CONSTRAINT `fk_bloque_sede` FOREIGN KEY (`id_sede`) REFERENCES `sede` (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.bloque: ~0 rows (aproximadamente)

--  estructura para tabla unifet.docente
CREATE TABLE IF NOT EXISTS `docente` (
  `id_docente` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned DEFAULT NULL COMMENT 'NULL si el docente no tiene cuenta en el sistema',
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_docente`),
  UNIQUE KEY `uq_docente_codigo` (`id_institucion`,`codigo`),
  KEY `fk_docente_usuario` (`id_usuario`),
  CONSTRAINT `fk_docente_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  CONSTRAINT `fk_docente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.docente: ~0 rows (aproximadamente)

--  estructura para tabla unifet.docente_asignatura
CREATE TABLE IF NOT EXISTS `docente_asignatura` (
  `id_docente_asignatura` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_docente` int(10) unsigned NOT NULL,
  `id_asignatura` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_docente_asignatura`),
  UNIQUE KEY `uq_docente_asignatura` (`id_docente`,`id_asignatura`),
  KEY `fk_da_asignatura` (`id_asignatura`),
  CONSTRAINT `fk_da_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE,
  CONSTRAINT `fk_da_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.docente_asignatura: ~0 rows (aproximadamente)

--  estructura para tabla unifet.docente_programa
CREATE TABLE IF NOT EXISTS `docente_programa` (
  `id_docente_programa` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_docente` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_docente_programa`),
  UNIQUE KEY `uq_docente_programa` (`id_docente`,`id_programa`),
  KEY `fk_dp_programa` (`id_programa`),
  CONSTRAINT `fk_dp_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE,
  CONSTRAINT `fk_dp_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.docente_programa: ~0 rows (aproximadamente)

--  estructura para tabla unifet.espacio
CREATE TABLE IF NOT EXISTS `espacio` (
  `id_espacio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_bloque` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('AULA','LABORATORIO','SALA','AUDITORIO','OTRO') NOT NULL,
  `capacidad` int(10) unsigned NOT NULL DEFAULT 0,
  `caracteristicas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'ej: proyector, aire acondicionado, PCs' CHECK (json_valid(`caracteristicas`)),
  `estado` enum('DISPONIBLE','MANTENIMIENTO','INACTIVO') NOT NULL DEFAULT 'DISPONIBLE',
  PRIMARY KEY (`id_espacio`),
  UNIQUE KEY `uq_espacio_codigo` (`id_bloque`,`codigo`),
  CONSTRAINT `fk_espacio_bloque` FOREIGN KEY (`id_bloque`) REFERENCES `bloque` (`id_bloque`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.espacio: ~0 rows (aproximadamente)

--  estructura para tabla unifet.facultad
CREATE TABLE IF NOT EXISTS `facultad` (
  `id_facultad` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_facultad`),
  UNIQUE KEY `uq_facultad_codigo` (`id_institucion`,`codigo`),
  CONSTRAINT `fk_facultad_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.facultad: ~0 rows (aproximadamente)

--  estructura para tabla unifet.horario
CREATE TABLE IF NOT EXISTS `horario` (
  `id_horario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_periodo` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  `id_asignatura` int(10) unsigned NOT NULL,
  `id_docente` int(10) unsigned NOT NULL,
  `id_espacio` int(10) unsigned NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('BORRADOR','PROPUESTA','CONFIRMADO','CERRADO','MODIFICADO','ELIMINADO') NOT NULL DEFAULT 'BORRADOR',
  `origen` enum('MANUAL','FET','HIBRIDO') NOT NULL DEFAULT 'MANUAL',
  `id_usuario_creador` int(10) unsigned NOT NULL,
  `id_usuario_modificador` int(10) unsigned DEFAULT NULL,
  `motivo_modificacion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_horario`),
  UNIQUE KEY `uq_horario_espacio_slot` (`id_periodo`,`id_espacio`,`dia_semana`,`hora_inicio`),
  UNIQUE KEY `uq_horario_docente_slot` (`id_periodo`,`id_docente`,`dia_semana`,`hora_inicio`),
  KEY `fk_horario_programa` (`id_programa`),
  KEY `fk_horario_asignatura` (`id_asignatura`),
  KEY `fk_horario_usuario_creador` (`id_usuario_creador`),
  KEY `fk_horario_usuario_modificador` (`id_usuario_modificador`),
  KEY `idx_horario_filtros` (`id_periodo`,`id_programa`,`dia_semana`,`estado`),
  KEY `idx_horario_espacio` (`id_espacio`,`dia_semana`),
  KEY `idx_horario_docente` (`id_docente`,`dia_semana`),
  CONSTRAINT `fk_horario_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  CONSTRAINT `fk_horario_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  CONSTRAINT `fk_horario_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`),
  CONSTRAINT `fk_horario_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  CONSTRAINT `fk_horario_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`),
  CONSTRAINT `fk_horario_usuario_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `fk_horario_usuario_modificador` FOREIGN KEY (`id_usuario_modificador`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `chk_horario_horas` CHECK (`hora_fin` > `hora_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.horario: ~0 rows (aproximadamente)

--  estructura para tabla unifet.horario_historial
CREATE TABLE IF NOT EXISTS `horario_historial` (
  `id_historial` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_horario` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `accion` enum('CREACION','MODIFICACION','CIERRE','REAPERTURA','ELIMINACION') NOT NULL,
  `valor_anterior` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valor_anterior`)),
  `valor_nuevo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valor_nuevo`)),
  `motivo` varchar(255) DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_historial`),
  KEY `fk_hh_horario` (`id_horario`),
  KEY `fk_hh_usuario` (`id_usuario`),
  CONSTRAINT `fk_hh_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE,
  CONSTRAINT `fk_hh_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.horario_historial: ~0 rows (aproximadamente)

--  estructura para tabla unifet.importacion_excel
CREATE TABLE IF NOT EXISTS `importacion_excel` (
  `id_importacion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `tipo_entidad` enum('DOCENTE','ASIGNATURA','ESPACIO','PROGRAMA','OTRO') NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `total_filas` int(10) unsigned NOT NULL DEFAULT 0,
  `filas_exitosas` int(10) unsigned NOT NULL DEFAULT 0,
  `filas_error` int(10) unsigned NOT NULL DEFAULT 0,
  `estado` enum('PENDIENTE','PROCESADO','PROCESADO_CON_ERRORES','FALLIDO') NOT NULL DEFAULT 'PENDIENTE',
  `detalle_errores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalle_errores`)),
  `fecha_importacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_importacion`),
  KEY `fk_import_institucion` (`id_institucion`),
  KEY `fk_import_usuario` (`id_usuario`),
  CONSTRAINT `fk_import_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  CONSTRAINT `fk_import_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.importacion_excel: ~0 rows (aproximadamente)

--  estructura para tabla unifet.institucion
CREATE TABLE IF NOT EXISTS `institucion` (
  `id_institucion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `nombre_sistema` varchar(150) DEFAULT NULL,
  `lema` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `logo_documentos_url` varchar(255) DEFAULT NULL,
  `favicon_url` varchar(255) DEFAULT NULL,
  `color_principal` varchar(7) DEFAULT NULL COMMENT 'HEX #RRGGBB',
  `color_secundario` varchar(7) DEFAULT NULL COMMENT 'HEX #RRGGBB',
  `color_acento` varchar(7) DEFAULT NULL COMMENT 'HEX #RRGGBB',
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_institucion`),
  UNIQUE KEY `uq_institucion_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.institucion: ~0 rows (aproximadamente)

--  estructura para tabla unifet.oferta_academica
CREATE TABLE IF NOT EXISTS `oferta_academica` (
  `id_oferta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_periodo` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  `id_asignatura` int(10) unsigned NOT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (`id_oferta`),
  UNIQUE KEY `uq_oferta` (`id_periodo`,`id_programa`,`id_asignatura`),
  KEY `fk_oferta_programa` (`id_programa`),
  KEY `fk_oferta_asignatura` (`id_asignatura`),
  CONSTRAINT `fk_oferta_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  CONSTRAINT `fk_oferta_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  CONSTRAINT `fk_oferta_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.oferta_academica: ~0 rows (aproximadamente)

--  estructura para tabla unifet.periodo_academico
CREATE TABLE IF NOT EXISTS `periodo_academico` (
  `id_periodo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `id_periodo_base` int(10) unsigned DEFAULT NULL COMMENT 'Periodo del cual se reutilizó información',
  `codigo` varchar(20) NOT NULL COMMENT 'ej: 2026-1, 2026-2',
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('PLANIFICACION','ACTIVO','CERRADO','HISTORICO') NOT NULL DEFAULT 'PLANIFICACION',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_periodo`),
  UNIQUE KEY `uq_periodo_codigo` (`id_institucion`,`codigo`),
  KEY `fk_periodo_base` (`id_periodo_base`),
  CONSTRAINT `fk_periodo_base` FOREIGN KEY (`id_periodo_base`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE SET NULL,
  CONSTRAINT `fk_periodo_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.periodo_academico: ~0 rows (aproximadamente)

--  estructura para tabla unifet.programa
CREATE TABLE IF NOT EXISTS `programa` (
  `id_programa` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_facultad` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_programa`),
  UNIQUE KEY `uq_programa_codigo` (`id_facultad`,`codigo`),
  CONSTRAINT `fk_programa_facultad` FOREIGN KEY (`id_facultad`) REFERENCES `facultad` (`id_facultad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.programa: ~0 rows (aproximadamente)

--  estructura para tabla unifet.programa_lider
CREATE TABLE IF NOT EXISTS `programa_lider` (
  `id_programa_lider` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_programa` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_programa_lider`),
  UNIQUE KEY `uq_programa_lider` (`id_programa`,`id_usuario`),
  KEY `fk_pl_usuario` (`id_usuario`),
  CONSTRAINT `fk_pl_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE,
  CONSTRAINT `fk_pl_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.programa_lider: ~0 rows (aproximadamente)

--  estructura para tabla unifet.propuesta_fet
CREATE TABLE IF NOT EXISTS `propuesta_fet` (
  `id_propuesta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_periodo` int(10) unsigned NOT NULL,
  `id_programa` int(10) unsigned NOT NULL,
  `id_usuario_solicitante` int(10) unsigned NOT NULL,
  `parametros_entrada` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Restricciones/config enviadas a FET' CHECK (json_valid(`parametros_entrada`)),
  `estado` enum('GENERADA','ACEPTADA','MODIFICADA','DESCARTADA') NOT NULL DEFAULT 'GENERADA',
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL COMMENT 'Cuándo se aceptó/modificó/descartó',
  PRIMARY KEY (`id_propuesta`),
  KEY `fk_pf_periodo` (`id_periodo`),
  KEY `fk_pf_programa` (`id_programa`),
  KEY `fk_pf_usuario` (`id_usuario_solicitante`),
  CONSTRAINT `fk_pf_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  CONSTRAINT `fk_pf_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`),
  CONSTRAINT `fk_pf_usuario` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.propuesta_fet: ~0 rows (aproximadamente)

--  estructura para tabla unifet.propuesta_fet_detalle
CREATE TABLE IF NOT EXISTS `propuesta_fet_detalle` (
  `id_detalle` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_propuesta` int(10) unsigned NOT NULL,
  `id_asignatura` int(10) unsigned NOT NULL,
  `id_docente` int(10) unsigned NOT NULL,
  `id_espacio` int(10) unsigned NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `id_horario_resultante` int(10) unsigned DEFAULT NULL COMMENT 'Se llena cuando el líder acepta esta línea y se crea el horario definitivo',
  PRIMARY KEY (`id_detalle`),
  KEY `fk_pfd_propuesta` (`id_propuesta`),
  KEY `fk_pfd_asignatura` (`id_asignatura`),
  KEY `fk_pfd_docente` (`id_docente`),
  KEY `fk_pfd_espacio` (`id_espacio`),
  KEY `fk_pfd_horario` (`id_horario_resultante`),
  CONSTRAINT `fk_pfd_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  CONSTRAINT `fk_pfd_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  CONSTRAINT `fk_pfd_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`),
  CONSTRAINT `fk_pfd_horario` FOREIGN KEY (`id_horario_resultante`) REFERENCES `horario` (`id_horario`) ON DELETE SET NULL,
  CONSTRAINT `fk_pfd_propuesta` FOREIGN KEY (`id_propuesta`) REFERENCES `propuesta_fet` (`id_propuesta`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.propuesta_fet_detalle: ~0 rows (aproximadamente)

--  estructura para tabla unifet.restriccion
CREATE TABLE IF NOT EXISTS `restriccion` (
  `id_restriccion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `id_periodo` int(10) unsigned DEFAULT NULL COMMENT 'NULL = restricción general, no ligada a un periodo específico',
  `tipo` enum('DOCENTE','ESPACIO','HORARIO','OTRO') NOT NULL,
  `id_docente` int(10) unsigned DEFAULT NULL,
  `id_espacio` int(10) unsigned DEFAULT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (`id_restriccion`),
  KEY `fk_restriccion_institucion` (`id_institucion`),
  KEY `fk_restriccion_periodo` (`id_periodo`),
  KEY `fk_restriccion_docente` (`id_docente`),
  KEY `fk_restriccion_espacio` (`id_espacio`),
  CONSTRAINT `fk_restriccion_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE,
  CONSTRAINT `fk_restriccion_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`) ON DELETE CASCADE,
  CONSTRAINT `fk_restriccion_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  CONSTRAINT `fk_restriccion_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.restriccion: ~0 rows (aproximadamente)

--  estructura para tabla unifet.sede
CREATE TABLE IF NOT EXISTS `sede` (
  `id_sede` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_sede`),
  UNIQUE KEY `uq_sede_codigo` (`id_institucion`,`codigo`),
  CONSTRAINT `fk_sede_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.sede: ~0 rows (aproximadamente)

--  estructura para tabla unifet.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_institucion` int(10) unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ADMINISTRADOR','LIDER_PROGRAMA') NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuario_correo` (`correo`),
  KEY `fk_usuario_institucion` (`id_institucion`),
  CONSTRAINT `fk_usuario_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  datos para la tabla unifet.usuario: ~0 rows (aproximadamente)

--  estructura para vista unifet.vista_auditoria_pendiente
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `vista_auditoria_pendiente` (
	`id_auditoria` INT(10) UNSIGNED NOT NULL,
	`fecha_auditoria` DATE NOT NULL,
	`docente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`asignatura` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`programa` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`facultad` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sede` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`bloque` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`espacio` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hora_inicio` TIME NOT NULL,
	`hora_fin` TIME NOT NULL,
	`resultado` ENUM('PENDIENTE','VERIFICADA','NO_REALIZADA') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`observacion` TEXT NULL COLLATE 'utf8mb4_unicode_ci'
);

--  estructura para vista unifet.vista_horario_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `vista_horario_completo` (
	`id_horario` INT(10) UNSIGNED NOT NULL,
	`periodo` VARCHAR(1) NOT NULL COMMENT 'ej: 2026-1, 2026-2' COLLATE 'utf8mb4_unicode_ci',
	`facultad` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`programa` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`asignatura` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`creditos` TINYINT(3) UNSIGNED NOT NULL,
	`docente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sede` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`bloque` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`espacio` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo_espacio` ENUM('AULA','LABORATORIO','SALA','AUDITORIO','OTRO') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`dia_semana` ENUM('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hora_inicio` TIME NOT NULL,
	`hora_fin` TIME NOT NULL,
	`estado` ENUM('BORRADOR','PROPUESTA','CONFIRMADO','CERRADO','MODIFICADO','ELIMINADO') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`origen` ENUM('MANUAL','FET','HIBRIDO') NOT NULL COLLATE 'utf8mb4_unicode_ci'
);

--  estructura para disparador unifet.trg_horario_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_horario_after_insert
AFTER INSERT ON horario
FOR EACH ROW
BEGIN
    INSERT INTO horario_historial (id_horario, id_usuario, accion, valor_nuevo)
    VALUES (
        NEW.id_horario,
        NEW.id_usuario_creador,
        'CREACION',
        JSON_OBJECT(
            'id_asignatura', NEW.id_asignatura,
            'id_docente', NEW.id_docente,
            'id_espacio', NEW.id_espacio,
            'dia_semana', NEW.dia_semana,
            'hora_inicio', NEW.hora_inicio,
            'hora_fin', NEW.hora_fin,
            'estado', NEW.estado
        )
    );
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

--  estructura para disparador unifet.trg_horario_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_horario_after_update
AFTER UPDATE ON horario
FOR EACH ROW
BEGIN
    IF NOT (NEW.id_docente <=> OLD.id_docente
            AND NEW.id_espacio <=> OLD.id_espacio
            AND NEW.dia_semana <=> OLD.dia_semana
            AND NEW.hora_inicio <=> OLD.hora_inicio
            AND NEW.hora_fin <=> OLD.hora_fin
            AND NEW.estado <=> OLD.estado) THEN
        INSERT INTO horario_historial (id_horario, id_usuario, accion, valor_anterior, valor_nuevo, motivo)
        VALUES (
            NEW.id_horario,
            COALESCE(NEW.id_usuario_modificador, NEW.id_usuario_creador),
            IF(NEW.estado = 'CERRADO', 'CIERRE', 'MODIFICACION'),
            JSON_OBJECT(
                'id_docente', OLD.id_docente, 'id_espacio', OLD.id_espacio,
                'dia_semana', OLD.dia_semana, 'hora_inicio', OLD.hora_inicio,
                'hora_fin', OLD.hora_fin, 'estado', OLD.estado
            ),
            JSON_OBJECT(
                'id_docente', NEW.id_docente, 'id_espacio', NEW.id_espacio,
                'dia_semana', NEW.dia_semana, 'hora_inicio', NEW.hora_inicio,
                'hora_fin', NEW.hora_fin, 'estado', NEW.estado
            ),
            NEW.motivo_modificacion
        );
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `vista_auditoria_pendiente`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_auditoria_pendiente` AS SELECT
    a.id_auditoria,
    a.fecha_auditoria,
    v.docente,
    v.asignatura,
    v.programa,
    v.facultad,
    v.sede,
    v.bloque,
    v.espacio,
    v.hora_inicio,
    v.hora_fin,
    a.resultado,
    a.observacion
FROM auditoria_clase a
JOIN vista_horario_completo v ON v.id_horario = a.id_horario 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `vista_horario_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_horario_completo` AS SELECT
    h.id_horario,
    per.codigo                AS periodo,
    fac.nombre                AS facultad,
    prog.nombre               AS programa,
    asig.nombre               AS asignatura,
    asig.creditos,
    CONCAT(doc.nombre, ' ', doc.apellido) AS docente,
    sede.nombre               AS sede,
    bl.nombre                 AS bloque,
    esp.nombre                AS espacio,
    esp.tipo                  AS tipo_espacio,
    h.dia_semana,
    h.hora_inicio,
    h.hora_fin,
    h.estado,
    h.origen
FROM horario h
JOIN periodo_academico per ON per.id_periodo = h.id_periodo
JOIN programa prog          ON prog.id_programa = h.id_programa
JOIN facultad fac            ON fac.id_facultad = prog.id_facultad
JOIN asignatura asig          ON asig.id_asignatura = h.id_asignatura
JOIN docente doc               ON doc.id_docente = h.id_docente
JOIN espacio esp                 ON esp.id_espacio = h.id_espacio
JOIN bloque bl                     ON bl.id_bloque = esp.id_bloque
JOIN sede                         ON sede.id_sede = bl.id_sede 
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
