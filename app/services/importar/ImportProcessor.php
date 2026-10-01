<?php

namespace App\Services\Importar;

use App\Config\Database;
use PDO;
use Exception;

class ImportProcessor {
    
    /**
     * Procesa la inserción y actualización técnica de filas en la base de datos usando transacciones.
     * @param string $entityKey Ej: 'ASIGNATURA', 'DOCENTE', 'PROGRAMA', etc.
     * @param array $filas Las filas estructuradas y validadas
     * @param int $id_institucion ID de la institución
     * @return array Resumen de la operación ['creados' => int, 'actualizados' => int, 'errores' => int]
     */
    public static function process(string $entityKey, array $filas, int $id_institucion = 1): array {
        $db = Database::getConexion();
        $db->beginTransaction();

        $resumen = [
            'creados'      => 0,
            'actualizados' => 0,
            'errores'      => 0
        ];

        try {
            foreach ($filas as $fila) {
                // Solo procesar registros válidos: NUEVOS, COMPLETAR o ACTUALIZAR (sobrescribir divergencias)
                if (!in_array($fila['estado'], ['NUEVO', 'COMPLETAR', 'ACTUALIZAR'], true)) {
                    continue;
                }

                $esNuevo = ($fila['estado'] === 'NUEVO');

                switch ($entityKey) {
                    case 'ASIGNATURA':
                        self::insertarAsignatura($db, $fila, $id_institucion);
                        break;
                        
                    case 'DOCENTE':
                        self::insertarDocente($db, $fila, $id_institucion);
                        break;

                    case 'ESPACIO':
                        self::insertarEspacio($db, $fila, $id_institucion);
                        break;
                    
                    case 'PROGRAMA':
                        self::insertarPrograma($db, $fila, $id_institucion);
                        break;
                        
                    case 'FACULTAD':
                        self::insertarFacultad($db, $fila, $id_institucion);
                        break;
                        
                    case 'SEDE':
                        self::insertarSede($db, $fila, $id_institucion);
                        break;
                        
                    case 'BLOQUE':
                        self::insertarBloque($db, $fila, $id_institucion);
                        break;
                    
                    default:
                        throw new Exception("El procesador de importación no soporta la entidad: {$entityKey}");
                }

                if ($esNuevo) {
                    $resumen['creados']++;
                } else {
                    $resumen['actualizados']++;
                }
            }

            $db->commit();
            return $resumen;
            
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception("Error crítico durante la importación. Todos los cambios han sido revertidos. Detalles: " . $e->getMessage());
        }
    }

    private static function insertarAsignatura(PDO $db, array $fila, int $id_institucion) {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $codigo = trim((string)($fila['codigo'] ?? ''));
        $nombre = trim((string)($fila['nombre'] ?? ''));
        $creditos = (int)($fila['creditos'] ?? 0);
        $tipo = trim((string)($fila['tipo'] ?? 'Obligatoria'));

        if ($idExistente) {
            $stmt = $db->prepare("
                UPDATE asignatura 
                SET codigo = :codigo, 
                    nombre = :nombre, 
                    creditos = :creditos, 
                    tipo = :tipo
                WHERE id_asignatura = :id
            ");
            $stmt->execute([
                ':codigo'   => $codigo !== '' ? $codigo : ($fila['codigo_bd'] ?? '0'),
                ':nombre'   => $nombre,
                ':creditos' => $creditos,
                ':tipo'     => $tipo,
                ':id'       => $idExistente
            ]);
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO asignatura (id_institucion, codigo, nombre, creditos, tipo)
            VALUES (:inst, :codigo, :nombre, :creditos, :tipo)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre),
                creditos = VALUES(creditos),
                tipo = VALUES(tipo)
        ");
        
        $stmt->execute([
            ':inst'     => $id_institucion,
            ':codigo'   => $codigo !== '' ? $codigo : '0',
            ':nombre'   => $nombre,
            ':creditos' => $creditos,
            ':tipo'     => $tipo
        ]);
    }

    private static function insertarDocente(PDO $db, array $fila, int $id_institucion) {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $doc = trim((string)($fila['documento'] ?? ''));
        $nomCompleto = trim((string)($fila['nombre'] ?? ''));
        $partes = explode(' ', $nomCompleto, 2);
        $nombre = $partes[0] ?? $nomCompleto;
        $apellido = $partes[1] ?? '';
        $correo = trim((string)($fila['correo'] ?? ''));

        if ($idExistente) {
            $stmt = $db->prepare("
                UPDATE docente 
                SET codigo = :doc, 
                    nombre = :nom, 
                    apellido = :ape, 
                    correo = :correo
                WHERE id_docente = :id
            ");
            $stmt->execute([
                ':doc'    => $doc !== '' ? $doc : ($fila['codigo_bd'] ?? ''),
                ':nom'    => $nombre,
                ':ape'    => $apellido,
                ':correo' => $correo,
                ':id'     => $idExistente
            ]);
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO docente (id_institucion, codigo, nombre, apellido, correo)
            VALUES (:inst, :doc, :nom, :ape, :correo)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre),
                apellido = VALUES(apellido),
                correo = VALUES(correo)
        ");
        
        $stmt->execute([
            ':inst'   => $id_institucion,
            ':doc'    => $doc,
            ':nom'    => $nombre,
            ':ape'    => $apellido,
            ':correo' => $correo
        ]);
    }

    private static function insertarEspacio(PDO $db, array $fila, int $id_institucion) {
        $id_bloque = $fila['id_bloque'] ?? null;
        if (!$id_bloque) {
            throw new Exception("El espacio '{$fila['nombre']}' no tiene un bloque válido resuelto.");
        }

        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $nombre = trim((string)($fila['nombre'] ?? ''));
        $tipo = strtoupper(trim((string)($fila['tipo'] ?? 'AULA')));
        if (!in_array($tipo, ['AULA', 'LABORATORIO', 'SALA', 'AUDITORIO', 'OTRO'], true)) {
            $tipo = 'AULA';
        }
        $capacidad = (int)($fila['capacidad'] ?? 30);

        if ($idExistente) {
            $stmt = $db->prepare("
                UPDATE espacio 
                SET codigo = :cod, 
                    nombre = :nom, 
                    tipo = :tipo, 
                    capacidad = :cap
                WHERE id_espacio = :id
            ");
            $stmt->execute([
                ':cod'  => $nombre,
                ':nom'  => $nombre,
                ':tipo' => $tipo,
                ':cap'  => $capacidad,
                ':id'   => $idExistente
            ]);
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO espacio (id_bloque, codigo, nombre, tipo, capacidad)
            VALUES (:bloque, :cod, :nom, :tipo, :cap)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre),
                tipo = VALUES(tipo),
                capacidad = VALUES(capacidad)
        ");
        
        $stmt->execute([
            ':bloque' => $id_bloque,
            ':cod'    => $nombre,
            ':nom'    => $nombre,
            ':tipo'   => $tipo,
            ':cap'    => $capacidad
        ]);
    }

    private static function insertarFacultad(PDO $db, array $fila, int $id_institucion) {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $nombre = trim((string)($fila['nombre'] ?? ''));

        if ($idExistente) {
            $stmt = $db->prepare("UPDATE facultad SET nombre = :nom WHERE id_facultad = :id");
            $stmt->execute([':nom' => $nombre, ':id' => $idExistente]);
            return;
        }

        // Generar código conciso si no existe
        $letras = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($nombre, 0, 4)));
        $codigo = (!empty($letras) ? $letras : 'FAC') . '-' . rand(10, 99);

        $stmt = $db->prepare("
            INSERT INTO facultad (id_institucion, codigo, nombre)
            VALUES (:inst, :cod, :nom)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre)
        ");
        
        $stmt->execute([
            ':inst' => $id_institucion,
            ':cod'  => $codigo,
            ':nom'  => $nombre
        ]);
    }

    private static function insertarPrograma(PDO $db, array $fila, int $id_institucion) {
        $id_facultad = $fila['id_facultad'] ?? null;
        if (!$id_facultad) {
            throw new Exception("El programa '{$fila['nombre']}' no tiene una facultad válida resuelta.");
        }

        $id_nivel = !empty($fila['id_nivel_academico']) ? (int)$fila['id_nivel_academico'] : null;
        $codigo = trim((string)($fila['codigo'] ?? ''));
        $nombre = trim((string)($fila['nombre'] ?? ''));
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;

        // Si existe registro previo en BD (por ID detectado en ExistenceChecker)
        if ($idExistente) {
            // Si el excel no trae código, preservar el que ya tenía en BD
            if ($codigo === '') {
                $codigo = $fila['codigo_bd'] ?? '';
            }

            $stmt = $db->prepare("
                UPDATE programa 
                SET codigo = :cod, 
                    nombre = :nom, 
                    id_facultad = :fac, 
                    id_nivel_academico = COALESCE(:nivel, id_nivel_academico)
                WHERE id_programa = :id
            ");
            $stmt->execute([
                ':cod'   => $codigo,
                ':nom'   => $nombre,
                ':fac'   => $id_facultad,
                ':nivel' => $id_nivel,
                ':id'    => $idExistente
            ]);
            return;
        }

        // Si es nuevo y el Excel no provee código, autogenerar uno coherente
        if ($codigo === '') {
            $stmtExist = $db->prepare("SELECT codigo FROM programa WHERE id_facultad = :fac AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
            $stmtExist->execute([':fac' => $id_facultad, ':nom' => $nombre]);
            $codExist = $stmtExist->fetchColumn();
            if ($codExist) {
                $codigo = $codExist;
            } else {
                $letras = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($nombre, 0, 4)));
                $codigo = (!empty($letras) ? $letras : 'PROG') . '-' . rand(100, 999);
            }
        }

        $stmt = $db->prepare("
            INSERT INTO programa (id_facultad, id_nivel_academico, codigo, nombre)
            VALUES (:fac, :nivel, :cod, :nom)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre),
                id_nivel_academico = COALESCE(VALUES(id_nivel_academico), id_nivel_academico)
        ");
        
        $stmt->execute([
            ':fac'   => $id_facultad,
            ':nivel' => $id_nivel,
            ':cod'   => $codigo,
            ':nom'   => $nombre
        ]);
    }

    private static function insertarSede(PDO $db, array $fila, int $id_institucion) {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $nombre = trim((string)($fila['nombre'] ?? ''));
        $dir = trim((string)($fila['direccion'] ?? ''));

        if ($idExistente) {
            $stmt = $db->prepare("UPDATE sede SET nombre = :nom, direccion = :dir WHERE id_sede = :id");
            $stmt->execute([':nom' => $nombre, ':dir' => $dir, ':id' => $idExistente]);
            return;
        }

        $letras = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($nombre, 0, 4)));
        $codigo = (!empty($letras) ? $letras : 'SEDE') . '-' . rand(10, 99);

        $stmt = $db->prepare("
            INSERT INTO sede (id_institucion, codigo, nombre, direccion)
            VALUES (:inst, :cod, :nom, :dir)
            ON DUPLICATE KEY UPDATE 
                direccion = VALUES(direccion)
        ");
        
        $stmt->execute([
            ':inst' => $id_institucion,
            ':cod'  => $codigo,
            ':nom'  => $nombre,
            ':dir'  => $dir
        ]);
    }

    private static function insertarBloque(PDO $db, array $fila, int $id_institucion) {
        $id_sede = $fila['id_sede'] ?? null;
        if (!$id_sede) {
            throw new Exception("El bloque '{$fila['nombre']}' no tiene una sede válida resuelta.");
        }

        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $nombre = trim((string)($fila['nombre'] ?? ''));

        if ($idExistente) {
            $stmt = $db->prepare("UPDATE bloque SET nombre = :nom WHERE id_bloque = :id");
            $stmt->execute([':nom' => $nombre, ':id' => $idExistente]);
            return;
        }

        $letras = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($nombre, 0, 4)));
        $codigo = (!empty($letras) ? $letras : 'BLQ') . '-' . rand(10, 99);

        $stmt = $db->prepare("
            INSERT INTO bloque (id_sede, codigo, nombre)
            VALUES (:sede, :cod, :nom)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre)
        ");
        
        $stmt->execute([
            ':sede' => $id_sede,
            ':cod'  => $codigo,
            ':nom'  => $nombre
        ]);
    }
}
