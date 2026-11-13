

--
-- Estructura de tabla para la tabla `area_academica`
CREATE DATABASE Unifet;
USE Unifet;


CREATE TABLE `area_academica` (
  `id_area_academica` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Estructura de tabla para la tabla `asignacion_docente`
--

CREATE TABLE `asignacion_docente` (
  `id_asignacion` int(10) UNSIGNED NOT NULL,
  `id_periodo` int(10) UNSIGNED NOT NULL,
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Estructura de tabla para la tabla `asignatura`
--

CREATE TABLE `asignatura` (
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `creditos` tinyint(3) UNSIGNED NOT NULL,
  `id_area_academica` int(10) UNSIGNED DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
----------------------------

--
-- Estructura de tabla para la tabla `asignatura_programa`
--

CREATE TABLE `asignatura_programa` (
  `id_asignatura_programa` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `semestre` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Semestre curricular dentro del programa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `auditoria_clase`
--

CREATE TABLE `auditoria_clase` (
  `id_auditoria` int(10) UNSIGNED NOT NULL,
  `id_horario` int(10) UNSIGNED NOT NULL COMMENT 'Apunta al horario definitivo',
  `fecha_auditoria` date NOT NULL,
  `hora_verificacion` time DEFAULT NULL,
  `id_usuario_auditor` int(10) UNSIGNED NOT NULL,
  `resultado` enum('PENDIENTE','SIN_NOVEDAD','CON_NOVEDAD','VERIFICADA','NO_REALIZADA') NOT NULL DEFAULT 'PENDIENTE',
  `observacion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de tabla para la tabla `auditoria_criterio`
--

CREATE TABLE `auditoria_criterio` (
  `id_criterio` int(10) UNSIGNED NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `orden` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_detalle`
--

CREATE TABLE `auditoria_detalle` (
  `id_auditoria_detalle` int(10) UNSIGNED NOT NULL,
  `id_auditoria` int(10) UNSIGNED NOT NULL,
  `id_criterio` int(10) UNSIGNED DEFAULT NULL,
  `criterio` varchar(255) NOT NULL,
  `cumple` tinyint(1) NOT NULL DEFAULT 0,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloque`
--

CREATE TABLE `bloque` (
  `id_bloque` int(10) UNSIGNED NOT NULL,
  `id_sede` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_asignatura`
--

CREATE TABLE `docente_asignatura` (
  `id_docente_asignatura` int(10) UNSIGNED NOT NULL,
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente_programa`
--

CREATE TABLE `docente_programa` (
  `id_docente_programa` int(10) UNSIGNED NOT NULL,
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de tabla para la tabla `espacio`
--

CREATE TABLE `espacio` (
  `id_espacio` int(10) UNSIGNED NOT NULL,
  `id_bloque` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('AULA','LABORATORIO','SALA','AUDITORIO','OTRO') NOT NULL,
  `capacidad` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `caracteristicas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON: proyector, aire acondicionado, PCs',
  `estado` enum('DISPONIBLE','MANTENIMIENTO','INACTIVO') NOT NULL DEFAULT 'DISPONIBLE'
) ;

-- Estructura de tabla para la tabla `facultad`
--

CREATE TABLE `facultad` (
  `id_facultad` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `id_horario` int(10) UNSIGNED NOT NULL,
  `id_periodo` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_espacio` int(10) UNSIGNED NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('BORRADOR','PROPUESTA','CONFIRMADO','CERRADO','MODIFICADO','ELIMINADO') NOT NULL DEFAULT 'BORRADOR',
  `origen` enum('MANUAL','FET','HIBRIDO') NOT NULL DEFAULT 'MANUAL',
  `id_usuario_creador` int(10) UNSIGNED NOT NULL,
  `id_usuario_modificador` int(10) UNSIGNED DEFAULT NULL,
  `motivo_modificacion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Disparadores `horario`
--
DELIMITER $$
CREATE TRIGGER `trg_horario_after_insert` AFTER INSERT ON `horario` FOR EACH ROW BEGIN
    INSERT INTO `horario_historial`
        (`id_horario`, `id_usuario`, `accion`, `valor_nuevo`)
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_horario_after_update` AFTER UPDATE ON `horario` FOR EACH ROW BEGIN
    IF NOT (
        NEW.id_docente <=> OLD.id_docente
        AND NEW.id_espacio <=> OLD.id_espacio
        AND NEW.dia_semana <=> OLD.dia_semana
        AND NEW.hora_inicio <=> OLD.hora_inicio
        AND NEW.hora_fin <=> OLD.hora_fin
        AND NEW.estado <=> OLD.estado
    ) THEN
        INSERT INTO `horario_historial`
            (
                `id_horario`,
                `id_usuario`,
                `accion`,
                `valor_anterior`,
                `valor_nuevo`,
                `motivo`
            )
        VALUES (
            NEW.id_horario,
            COALESCE(
                NEW.id_usuario_modificador,
                NEW.id_usuario_creador
            ),
            IF(NEW.estado = 'CERRADO', 'CIERRE', 'MODIFICACION'),
            JSON_OBJECT(
                'id_docente', OLD.id_docente,
                'id_espacio', OLD.id_espacio,
                'dia_semana', OLD.dia_semana,
                'hora_inicio', OLD.hora_inicio,
                'hora_fin', OLD.hora_fin,
                'estado', OLD.estado
            ),
            JSON_OBJECT(
                'id_docente', NEW.id_docente,
                'id_espacio', NEW.id_espacio,
                'dia_semana', NEW.dia_semana,
                'hora_inicio', NEW.hora_inicio,
                'hora_fin', NEW.hora_fin,
                'estado', NEW.estado
            ),
            NEW.motivo_modificacion
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_historial`
--

CREATE TABLE `horario_historial` (
  `id_historial` bigint(20) UNSIGNED NOT NULL,
  `id_horario` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `accion` enum('CREACION','MODIFICACION','CIERRE','REAPERTURA','ELIMINACION') NOT NULL,
  `valor_anterior` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `valor_nuevo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- Estructura de tabla para la tabla `importacion_excel`
--

CREATE TABLE `importacion_excel` (
  `id_importacion` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `tipo_entidad` enum('DOCENTE','ASIGNATURA','ESPACIO','PROGRAMA','OTRO') NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `total_filas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `filas_exitosas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `filas_error` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `estado` enum('PENDIENTE','PROCESADO','PROCESADO_CON_ERRORES','FALLIDO') NOT NULL DEFAULT 'PENDIENTE',
  `detalle_errores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fecha_importacion` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion`
--

CREATE TABLE `institucion` (
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `nombre_sistema` varchar(150) DEFAULT NULL,
  `lema` varchar(255) DEFAULT NULL,
  `dominio_correo` varchar(100) NOT NULL DEFAULT '@unifet.edu.co',
  `logo_url` varchar(255) DEFAULT NULL,
  `logo_documentos_url` varchar(255) DEFAULT NULL,
  `favicon_url` varchar(255) DEFAULT NULL,
  `color_principal` varchar(7) DEFAULT NULL,
  `color_secundario` varchar(7) DEFAULT NULL,
  `color_acento` varchar(7) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `nivel_academico`
--

CREATE TABLE `nivel_academico` (
  `id_nivel_academico` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oferta_academica`
--

CREATE TABLE `oferta_academica` (
  `id_oferta` int(10) UNSIGNED NOT NULL,
  `id_periodo` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de tabla para la tabla `periodo_academico`
--

CREATE TABLE `periodo_academico` (
  `id_periodo` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `id_periodo_base` int(10) UNSIGNED DEFAULT NULL,
  `codigo` varchar(20) NOT NULL COMMENT 'Ejemplo: 2026-1, 2026-2',
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('PLANIFICACION','ACTIVO','CERRADO','HISTORICO') NOT NULL DEFAULT 'PLANIFICACION',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-----------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--

CREATE TABLE `programa` (
  `id_programa` int(10) UNSIGNED NOT NULL,
  `id_facultad` int(10) UNSIGNED NOT NULL,
  `id_nivel_academico` int(10) UNSIGNED DEFAULT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `numero_semestres` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `programa_lider`
--

CREATE TABLE `programa_lider` (
  `id_programa_lider` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propuesta_fet`
--

CREATE TABLE `propuesta_fet` (
  `id_propuesta` int(10) UNSIGNED NOT NULL,
  `id_periodo` int(10) UNSIGNED NOT NULL,
  `id_programa` int(10) UNSIGNED NOT NULL,
  `id_usuario_solicitante` int(10) UNSIGNED NOT NULL,
  `parametros_entrada` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Restricciones y configuración enviadas a FET',
  `estado` enum('GENERADA','ACEPTADA','MODIFICADA','DESCARTADA') NOT NULL DEFAULT 'GENERADA',
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propuesta_fet_detalle`
--

CREATE TABLE `propuesta_fet_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_propuesta` int(10) UNSIGNED NOT NULL,
  `id_asignatura` int(10) UNSIGNED NOT NULL,
  `id_docente` int(10) UNSIGNED NOT NULL,
  `id_espacio` int(10) UNSIGNED NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `id_horario_resultante` int(10) UNSIGNED DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restriccion`
--

CREATE TABLE `restriccion` (
  `id_restriccion` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `id_periodo` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = restricción general',
  `tipo` enum('DOCENTE','ESPACIO','HORARIO','OTRO') NOT NULL,
  `id_docente` int(10) UNSIGNED DEFAULT NULL,
  `id_espacio` int(10) UNSIGNED DEFAULT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `id_sede` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_institucion` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_temporal` tinyint(1) NOT NULL DEFAULT 0,
  `rol` enum('ADMINISTRADOR','LIDER_PROGRAMA') NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_auditoria_pendiente`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_auditoria_pendiente` (
`id_auditoria` int(10) unsigned
,`fecha_auditoria` date
,`docente` varchar(201)
,`asignatura` varchar(200)
,`programa` varchar(150)
,`facultad` varchar(150)
,`sede` varchar(150)
,`bloque` varchar(100)
,`espacio` varchar(150)
,`hora_inicio` time
,`hora_fin` time
,`resultado` enum('PENDIENTE','SIN_NOVEDAD','CON_NOVEDAD','VERIFICADA','NO_REALIZADA')
,`observacion` text
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_horario_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_horario_completo` (
`id_horario` int(10) unsigned
,`periodo` varchar(20)
,`facultad` varchar(150)
,`programa` varchar(150)
,`asignatura` varchar(200)
,`creditos` tinyint(3) unsigned
,`docente` varchar(201)
,`sede` varchar(150)
,`bloque` varchar(100)
,`espacio` varchar(150)
,`tipo_espacio` enum('AULA','LABORATORIO','SALA','AUDITORIO','OTRO')
,`dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO')
,`hora_inicio` time
,`hora_fin` time
,`estado` enum('BORRADOR','PROPUESTA','CONFIRMADO','CERRADO','MODIFICADO','ELIMINADO')
,`origen` enum('MANUAL','FET','HIBRIDO')
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area_academica`
--
ALTER TABLE `area_academica`
  ADD PRIMARY KEY (`id_area_academica`),
  ADD UNIQUE KEY `uq_area_academica_nombre` (`id_institucion`,`nombre`);

--
-- Indices de la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD UNIQUE KEY `uq_asignacion` (`id_periodo`,`id_docente`,`id_asignatura`,`id_programa`),
  ADD KEY `fk_asig_docente` (`id_docente`),
  ADD KEY `fk_asig_asignatura` (`id_asignatura`),
  ADD KEY `fk_asig_programa` (`id_programa`);

--
-- Indices de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD UNIQUE KEY `uq_asignatura_codigo` (`id_institucion`,`codigo`),
  ADD KEY `fk_asignatura_area` (`id_area_academica`);

--
-- Indices de la tabla `asignatura_programa`
--
ALTER TABLE `asignatura_programa`
  ADD PRIMARY KEY (`id_asignatura_programa`),
  ADD UNIQUE KEY `uq_asignatura_programa` (`id_asignatura`,`id_programa`),
  ADD KEY `fk_ap_programa` (`id_programa`);

--
-- Indices de la tabla `auditoria_clase`
--
ALTER TABLE `auditoria_clase`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `fk_auditoria_horario` (`id_horario`),
  ADD KEY `fk_auditoria_usuario` (`id_usuario_auditor`),
  ADD KEY `idx_auditoria_fecha` (`fecha_auditoria`,`resultado`);

--
-- Indices de la tabla `auditoria_criterio`
--
ALTER TABLE `auditoria_criterio`
  ADD PRIMARY KEY (`id_criterio`);

--
-- Indices de la tabla `auditoria_detalle`
--
ALTER TABLE `auditoria_detalle`
  ADD PRIMARY KEY (`id_auditoria_detalle`),
  ADD KEY `fk_adetalle_auditoria` (`id_auditoria`),
  ADD KEY `fk_adetalle_criterio` (`id_criterio`);

--
-- Indices de la tabla `bloque`
--
ALTER TABLE `bloque`
  ADD PRIMARY KEY (`id_bloque`),
  ADD UNIQUE KEY `uq_bloque_codigo` (`id_sede`,`codigo`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `uq_docente_codigo` (`id_institucion`,`codigo`),
  ADD KEY `fk_docente_usuario` (`id_usuario`);

--
-- Indices de la tabla `docente_asignatura`
--
ALTER TABLE `docente_asignatura`
  ADD PRIMARY KEY (`id_docente_asignatura`),
  ADD UNIQUE KEY `uq_docente_asignatura` (`id_docente`,`id_asignatura`),
  ADD KEY `fk_da_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `docente_programa`
--
ALTER TABLE `docente_programa`
  ADD PRIMARY KEY (`id_docente_programa`),
  ADD UNIQUE KEY `uq_docente_programa` (`id_docente`,`id_programa`),
  ADD KEY `fk_dp_programa` (`id_programa`);

--
-- Indices de la tabla `espacio`
--
ALTER TABLE `espacio`
  ADD PRIMARY KEY (`id_espacio`),
  ADD UNIQUE KEY `uq_espacio_codigo` (`id_bloque`,`codigo`);

--
-- Indices de la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD PRIMARY KEY (`id_facultad`),
  ADD UNIQUE KEY `uq_facultad_codigo` (`id_institucion`,`codigo`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD UNIQUE KEY `uq_horario_espacio_slot` (`id_periodo`,`id_espacio`,`dia_semana`,`hora_inicio`),
  ADD UNIQUE KEY `uq_horario_docente_slot` (`id_periodo`,`id_docente`,`dia_semana`,`hora_inicio`),
  ADD KEY `fk_horario_programa` (`id_programa`),
  ADD KEY `fk_horario_asignatura` (`id_asignatura`),
  ADD KEY `fk_horario_usuario_creador` (`id_usuario_creador`),
  ADD KEY `fk_horario_usuario_modificador` (`id_usuario_modificador`),
  ADD KEY `idx_horario_filtros` (`id_periodo`,`id_programa`,`dia_semana`,`estado`),
  ADD KEY `idx_horario_espacio` (`id_espacio`,`dia_semana`),
  ADD KEY `idx_horario_docente` (`id_docente`,`dia_semana`);

--
-- Indices de la tabla `horario_historial`
--
ALTER TABLE `horario_historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `fk_hh_horario` (`id_horario`),
  ADD KEY `fk_hh_usuario` (`id_usuario`);

--
-- Indices de la tabla `importacion_excel`
--
ALTER TABLE `importacion_excel`
  ADD PRIMARY KEY (`id_importacion`),
  ADD KEY `fk_import_institucion` (`id_institucion`),
  ADD KEY `fk_import_usuario` (`id_usuario`);

--
-- Indices de la tabla `institucion`
--
ALTER TABLE `institucion`
  ADD PRIMARY KEY (`id_institucion`),
  ADD UNIQUE KEY `uq_institucion_codigo` (`codigo`);

--
-- Indices de la tabla `nivel_academico`
--
ALTER TABLE `nivel_academico`
  ADD PRIMARY KEY (`id_nivel_academico`),
  ADD UNIQUE KEY `uq_nivel_nombre` (`nombre`);

--
-- Indices de la tabla `oferta_academica`
--
ALTER TABLE `oferta_academica`
  ADD PRIMARY KEY (`id_oferta`),
  ADD UNIQUE KEY `uq_oferta` (`id_periodo`,`id_programa`,`id_asignatura`),
  ADD KEY `fk_oferta_programa` (`id_programa`),
  ADD KEY `fk_oferta_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `periodo_academico`
--
ALTER TABLE `periodo_academico`
  ADD PRIMARY KEY (`id_periodo`),
  ADD UNIQUE KEY `uq_periodo_codigo` (`id_institucion`,`codigo`),
  ADD KEY `fk_periodo_base` (`id_periodo_base`);

--
-- Indices de la tabla `programa`
--
ALTER TABLE `programa`
  ADD PRIMARY KEY (`id_programa`),
  ADD UNIQUE KEY `uq_programa_codigo` (`id_facultad`,`codigo`),
  ADD KEY `fk_programa_nivel` (`id_nivel_academico`);

--
-- Indices de la tabla `programa_lider`
--
ALTER TABLE `programa_lider`
  ADD PRIMARY KEY (`id_programa_lider`),
  ADD UNIQUE KEY `uq_programa_lider` (`id_programa`,`id_usuario`),
  ADD KEY `fk_pl_usuario` (`id_usuario`);

--
-- Indices de la tabla `propuesta_fet`
--
ALTER TABLE `propuesta_fet`
  ADD PRIMARY KEY (`id_propuesta`),
  ADD KEY `fk_pf_periodo` (`id_periodo`),
  ADD KEY `fk_pf_programa` (`id_programa`),
  ADD KEY `fk_pf_usuario` (`id_usuario_solicitante`);

--
-- Indices de la tabla `propuesta_fet_detalle`
--
ALTER TABLE `propuesta_fet_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_pfd_propuesta` (`id_propuesta`),
  ADD KEY `fk_pfd_asignatura` (`id_asignatura`),
  ADD KEY `fk_pfd_docente` (`id_docente`),
  ADD KEY `fk_pfd_espacio` (`id_espacio`),
  ADD KEY `fk_pfd_horario` (`id_horario_resultante`);

--
-- Indices de la tabla `restriccion`
--
ALTER TABLE `restriccion`
  ADD PRIMARY KEY (`id_restriccion`),
  ADD KEY `fk_restriccion_institucion` (`id_institucion`),
  ADD KEY `fk_restriccion_periodo` (`id_periodo`),
  ADD KEY `fk_restriccion_docente` (`id_docente`),
  ADD KEY `fk_restriccion_espacio` (`id_espacio`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`id_sede`),
  ADD UNIQUE KEY `uq_sede_codigo` (`id_institucion`,`codigo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuario_correo` (`correo`),
  ADD KEY `fk_usuario_institucion` (`id_institucion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area_academica`
--
ALTER TABLE `area_academica`
  MODIFY `id_area_academica` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  MODIFY `id_asignacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  MODIFY `id_asignatura` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=532;

--
-- AUTO_INCREMENT de la tabla `asignatura_programa`
--
ALTER TABLE `asignatura_programa`
  MODIFY `id_asignatura_programa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=578;

--
-- AUTO_INCREMENT de la tabla `auditoria_clase`
--
ALTER TABLE `auditoria_clase`
  MODIFY `id_auditoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `auditoria_criterio`
--
ALTER TABLE `auditoria_criterio`
  MODIFY `id_criterio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `auditoria_detalle`
--
ALTER TABLE `auditoria_detalle`
  MODIFY `id_auditoria_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `bloque`
--
ALTER TABLE `bloque`
  MODIFY `id_bloque` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `id_docente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `docente_asignatura`
--
ALTER TABLE `docente_asignatura`
  MODIFY `id_docente_asignatura` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `docente_programa`
--
ALTER TABLE `docente_programa`
  MODIFY `id_docente_programa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `espacio`
--
ALTER TABLE `espacio`
  MODIFY `id_espacio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facultad`
--
ALTER TABLE `facultad`
  MODIFY `id_facultad` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `id_horario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario_historial`
--
ALTER TABLE `horario_historial`
  MODIFY `id_historial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `importacion_excel`
--
ALTER TABLE `importacion_excel`
  MODIFY `id_importacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `institucion`
--
ALTER TABLE `institucion`
  MODIFY `id_institucion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `nivel_academico`
--
ALTER TABLE `nivel_academico`
  MODIFY `id_nivel_academico` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `oferta_academica`
--
ALTER TABLE `oferta_academica`
  MODIFY `id_oferta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=577;

--
-- AUTO_INCREMENT de la tabla `periodo_academico`
--
ALTER TABLE `periodo_academico`
  MODIFY `id_periodo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `programa`
--
ALTER TABLE `programa`
  MODIFY `id_programa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `programa_lider`
--
ALTER TABLE `programa_lider`
  MODIFY `id_programa_lider` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `propuesta_fet`
--
ALTER TABLE `propuesta_fet`
  MODIFY `id_propuesta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propuesta_fet_detalle`
--
ALTER TABLE `propuesta_fet_detalle`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `restriccion`
--
ALTER TABLE `restriccion`
  MODIFY `id_restriccion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sede`
--
ALTER TABLE `sede`
  MODIFY `id_sede` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_auditoria_pendiente`
--
DROP TABLE IF EXISTS `vista_auditoria_pendiente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_auditoria_pendiente`  AS SELECT `a`.`id_auditoria` AS `id_auditoria`, `a`.`fecha_auditoria` AS `fecha_auditoria`, `v`.`docente` AS `docente`, `v`.`asignatura` AS `asignatura`, `v`.`programa` AS `programa`, `v`.`facultad` AS `facultad`, `v`.`sede` AS `sede`, `v`.`bloque` AS `bloque`, `v`.`espacio` AS `espacio`, `v`.`hora_inicio` AS `hora_inicio`, `v`.`hora_fin` AS `hora_fin`, `a`.`resultado` AS `resultado`, `a`.`observacion` AS `observacion` FROM (`auditoria_clase` `a` join `vista_horario_completo` `v` on(`v`.`id_horario` = `a`.`id_horario`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_horario_completo`
--
DROP TABLE IF EXISTS `vista_horario_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_horario_completo`  AS SELECT `h`.`id_horario` AS `id_horario`, `per`.`codigo` AS `periodo`, `fac`.`nombre` AS `facultad`, `prog`.`nombre` AS `programa`, `asig`.`nombre` AS `asignatura`, `asig`.`creditos` AS `creditos`, concat(`doc`.`nombre`,' ',`doc`.`apellido`) AS `docente`, `sede`.`nombre` AS `sede`, `bl`.`nombre` AS `bloque`, `esp`.`nombre` AS `espacio`, `esp`.`tipo` AS `tipo_espacio`, `h`.`dia_semana` AS `dia_semana`, `h`.`hora_inicio` AS `hora_inicio`, `h`.`hora_fin` AS `hora_fin`, `h`.`estado` AS `estado`, `h`.`origen` AS `origen` FROM ((((((((`horario` `h` join `periodo_academico` `per` on(`per`.`id_periodo` = `h`.`id_periodo`)) join `programa` `prog` on(`prog`.`id_programa` = `h`.`id_programa`)) join `facultad` `fac` on(`fac`.`id_facultad` = `prog`.`id_facultad`)) join `asignatura` `asig` on(`asig`.`id_asignatura` = `h`.`id_asignatura`)) join `docente` `doc` on(`doc`.`id_docente` = `h`.`id_docente`)) join `espacio` `esp` on(`esp`.`id_espacio` = `h`.`id_espacio`)) join `bloque` `bl` on(`bl`.`id_bloque` = `esp`.`id_bloque`)) join `sede` on(`sede`.`id_sede` = `bl`.`id_sede`)) ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `area_academica`
--
ALTER TABLE `area_academica`
  ADD CONSTRAINT `fk_area_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);

--
-- Filtros para la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD CONSTRAINT `fk_asig_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_asig_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  ADD CONSTRAINT `fk_asig_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asig_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`);

--
-- Filtros para la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD CONSTRAINT `fk_asignatura_area` FOREIGN KEY (`id_area_academica`) REFERENCES `area_academica` (`id_area_academica`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_asignatura_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);

--
-- Filtros para la tabla `asignatura_programa`
--
ALTER TABLE `asignatura_programa`
  ADD CONSTRAINT `fk_ap_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ap_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria_clase`
--
ALTER TABLE `auditoria_clase`
  ADD CONSTRAINT `fk_auditoria_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario_auditor`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `auditoria_detalle`
--
ALTER TABLE `auditoria_detalle`
  ADD CONSTRAINT `fk_adetalle_auditoria` FOREIGN KEY (`id_auditoria`) REFERENCES `auditoria_clase` (`id_auditoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_adetalle_criterio` FOREIGN KEY (`id_criterio`) REFERENCES `auditoria_criterio` (`id_criterio`) ON DELETE SET NULL;

--
-- Filtros para la tabla `bloque`
--
ALTER TABLE `bloque`
  ADD CONSTRAINT `fk_bloque_sede` FOREIGN KEY (`id_sede`) REFERENCES `sede` (`id_sede`);

--
-- Filtros para la tabla `docente`
--
ALTER TABLE `docente`
  ADD CONSTRAINT `fk_docente_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  ADD CONSTRAINT `fk_docente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `docente_asignatura`
--
ALTER TABLE `docente_asignatura`
  ADD CONSTRAINT `fk_da_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_da_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `docente_programa`
--
ALTER TABLE `docente_programa`
  ADD CONSTRAINT `fk_dp_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dp_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `espacio`
--
ALTER TABLE `espacio`
  ADD CONSTRAINT `fk_espacio_bloque` FOREIGN KEY (`id_bloque`) REFERENCES `bloque` (`id_bloque`);

--
-- Filtros para la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD CONSTRAINT `fk_facultad_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);

--
-- Filtros para la tabla `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `fk_horario_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_horario_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  ADD CONSTRAINT `fk_horario_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`),
  ADD CONSTRAINT `fk_horario_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_horario_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`),
  ADD CONSTRAINT `fk_horario_usuario_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_horario_usuario_modificador` FOREIGN KEY (`id_usuario_modificador`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `horario_historial`
--
ALTER TABLE `horario_historial`
  ADD CONSTRAINT `fk_hh_horario` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hh_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `importacion_excel`
--
ALTER TABLE `importacion_excel`
  ADD CONSTRAINT `fk_import_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  ADD CONSTRAINT `fk_import_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `oferta_academica`
--
ALTER TABLE `oferta_academica`
  ADD CONSTRAINT `fk_oferta_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_oferta_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oferta_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`);

--
-- Filtros para la tabla `periodo_academico`
--
ALTER TABLE `periodo_academico`
  ADD CONSTRAINT `fk_periodo_base` FOREIGN KEY (`id_periodo_base`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_periodo_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);

--
-- Filtros para la tabla `programa`
--
ALTER TABLE `programa`
  ADD CONSTRAINT `fk_programa_facultad` FOREIGN KEY (`id_facultad`) REFERENCES `facultad` (`id_facultad`),
  ADD CONSTRAINT `fk_programa_nivel` FOREIGN KEY (`id_nivel_academico`) REFERENCES `nivel_academico` (`id_nivel_academico`) ON DELETE SET NULL;

--
-- Filtros para la tabla `programa_lider`
--
ALTER TABLE `programa_lider`
  ADD CONSTRAINT `fk_pl_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pl_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propuesta_fet`
--
ALTER TABLE `propuesta_fet`
  ADD CONSTRAINT `fk_pf_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pf_programa` FOREIGN KEY (`id_programa`) REFERENCES `programa` (`id_programa`),
  ADD CONSTRAINT `fk_pf_usuario` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `propuesta_fet_detalle`
--
ALTER TABLE `propuesta_fet_detalle`
  ADD CONSTRAINT `fk_pfd_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_pfd_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`),
  ADD CONSTRAINT `fk_pfd_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`),
  ADD CONSTRAINT `fk_pfd_horario` FOREIGN KEY (`id_horario_resultante`) REFERENCES `horario` (`id_horario`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pfd_propuesta` FOREIGN KEY (`id_propuesta`) REFERENCES `propuesta_fet` (`id_propuesta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `restriccion`
--
ALTER TABLE `restriccion`
  ADD CONSTRAINT `fk_restriccion_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_restriccion_espacio` FOREIGN KEY (`id_espacio`) REFERENCES `espacio` (`id_espacio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_restriccion_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`),
  ADD CONSTRAINT `fk_restriccion_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id_periodo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sede`
--
ALTER TABLE `sede`
  ADD CONSTRAINT `fk_sede_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);
COMMIT;
