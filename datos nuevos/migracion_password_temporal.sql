-- ============================================================================
-- MIGRACIÓN — Contraseña temporal + estado PENDIENTE (autorregistro futuro)
-- Aplicar sobre la base `unifet` ya existente.
-- ============================================================================
USE unifet;

-- 1) Marca si la contraseña actual es temporal (obliga a cambiarla al
--    primer login). Por defecto 0 para no afectar usuarios ya existentes.
ALTER TABLE usuario
    ADD COLUMN password_temporal TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash;

-- 2) Nuevo estado PENDIENTE, para cuando un líder se autorregistre y
--    quede esperando aprobación del Administrador (funcionalidad futura,
--    ver el prompt adjunto para el toggle que la activa/desactiva).
ALTER TABLE usuario
    MODIFY estado ENUM('ACTIVO','INACTIVO','PENDIENTE') NOT NULL DEFAULT 'ACTIVO';

-- Nota: un usuario en estado PENDIENTE no puede iniciar sesión (se trata
-- igual que INACTIVO a efectos de login), hasta que el Administrador lo
-- pase a ACTIVO desde la cola de aprobación.