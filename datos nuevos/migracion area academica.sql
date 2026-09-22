-- ============================================================================
-- MIGRACIÓN — Área Académica (nueva entidad que el profe nos compartió)

-- ============================================================================
USE unifet;

-- 1) Nueva tabla área académica ( cuelga de la institución)
CREATE TABLE IF NOT EXISTS area_academica (
    id_area_academica INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_institucion    INT UNSIGNED NOT NULL,
    nombre            VARCHAR(150) NOT NULL,
    estado            ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    CONSTRAINT uq_area_academica_nombre UNIQUE (id_institucion, nombre),
    CONSTRAINT fk_area_institucion FOREIGN KEY (id_institucion)
        REFERENCES institucion(id_institucion) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 2) Relación Área Académica → Asignatura (1:N, confirmada por el profesor:
--    el área se asocia a la asignatura, NO al programa)
ALTER TABLE asignatura
    ADD COLUMN id_area_academica INT UNSIGNED NULL AFTER creditos,
    ADD CONSTRAINT fk_asignatura_area FOREIGN KEY (id_area_academica)
        REFERENCES area_academica(id_area_academica) ON DELETE SET NULL;

-- 3) areas académicas oficiales entregadas por el profesor
INSERT INTO area_academica (id_institucion, nombre) VALUES
    (1, 'Administración'),
    (1, 'Área VEAN'),
    (1, 'Contable'),
    (1, 'Disciplinar Administración'),
    (1, 'Disciplinar Agronegocios'),
    (1, 'Disciplinar Agropecuaria'),
    (1, 'Disciplinar Ambiental'),
    (1, 'Disciplinar Contaduría'),
    (1, 'Disciplinar Eléctrica'),
    (1, 'Disciplinar Industrial'),
    (1, 'Disciplinar Infancia'),
    (1, 'Disciplinar Informática'),
    (1, 'Disciplinar Logística'),
    (1, 'Disciplinar SST'),
    (1, 'Disciplinar Trabajo Social'),
    (1, 'Disciplinar Turismo'),
    (1, 'Economía'),
    (1, 'Humanidades'),
    (1, 'Investigación'),
    (1, 'Numérica'),
    (1, 'Ofimática'),
    (1, 'VEAN'),
    (1, 'Visibilidad Institucional');

-- ============================================================================
-- PENDIENTE DE DECISIÓN — Catálogo oficial de facultades
-- A partir de ahora, cualquier dato de prueba nuevo debe usar SOLO estos
-- 6 nombres (no inventar facultades adicionales):
--   1. Ciencias AEC
--   2. Humanidades
--   3. Ingeniería
--   4. Transversal CAEC
--   5. Transversal Humanidades
--   6. Transversal Ingeniería
-- Si tu base de datos de desarrollo ya tiene facultades de prueba con
-- otros nombres, actualízalas o reemplázalas manualmente antes de seguir
-- cargando programas/asignaturas nuevas, para no tener que migrar
-- referencias de programa.id_facultad más adelante.
-- ============================================================================