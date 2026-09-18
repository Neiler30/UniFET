-- Insertar Institución base
INSERT INTO institucion (codigo, nombre, estado) 
VALUES ('UNICARIBE', 'Unicaribe - Institución Universitaria', 'ACTIVO')
ON DUPLICATE KEY UPDATE nombre = 'Unicaribe - Institución Universitaria';

-- Obtener el ID de la institución recién insertada
SET @id_inst = (SELECT id_institucion FROM institucion WHERE codigo = 'UNICARIBE' LIMIT 1);

-- Insertar Usuario Administrador (password: 123456 -> hash)
-- password_hash('123456', PASSWORD_DEFAULT)
INSERT INTO usuario (id_institucion, nombre, apellido, correo, password_hash, rol, estado)
VALUES (@id_inst, 'Admin', 'Sistema', 'admin@unicaribe.edu', '$2y$10$qVeJg3qtHoP/fuZpBOOrYeMc5WkiWxnhg6D5PX.nNA8wZRqDC1td2', 'ADMINISTRADOR', 'ACTIVO')
ON DUPLICATE KEY UPDATE password_hash = '$2y$10$qVeJg3qtHoP/fuZpBOOrYeMc5WkiWxnhg6D5PX.nNA8wZRqDC1td2';

-- Insertar Usuario Líder (password: 123456)
INSERT INTO usuario (id_institucion, nombre, apellido, correo, password_hash, rol, estado)
VALUES (@id_inst, 'Carlos', 'Líder', 'lider@unicaribe.edu', '$2y$10$qVeJg3qtHoP/fuZpBOOrYeMc5WkiWxnhg6D5PX.nNA8wZRqDC1td2', 'LIDER_PROGRAMA', 'ACTIVO')
ON DUPLICATE KEY UPDATE password_hash = '$2y$10$qVeJg3qtHoP/fuZpBOOrYeMc5WkiWxnhg6D5PX.nNA8wZRqDC1td2';
