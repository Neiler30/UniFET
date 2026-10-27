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
     * @param string $modoDuplicados 'omitir', 'actualizar', 'estricto'
     * @return array Resumen de la operación ['creados' => int, 'actualizados' => int, 'omitidos' => int, 'errores' => int]
     */
    public static function process(string $entityKey, array $filas, int $id_institucion = 1, string $modoDuplicados = 'omitir'): array {
        $db = Database::getConexion();
        $db->beginTransaction();

        $resumen = [
            'creados'      => 0,
            'actualizados' => 0,
            'omitidos'     => 0,
            'errores'      => 0
        ];

        try {
            foreach ($filas as $fila) {
                // Solo procesar registros válidos: NUEVOS, COMPLETAR o ACTUALIZAR (sobrescribir divergencias)
                if (!in_array($fila['estado'], ['NUEVO', 'COMPLETAR', 'ACTUALIZAR'], true)) {
                    continue;
                }

                $esNuevo = ($fila['estado'] === 'NUEVO');

                try {
                    switch ($entityKey) {
                        case 'ASIGNATURA':
                            self::insertarAsignatura($db, $fila, $id_institucion, $modoDuplicados);
                            break;
                            
                        case 'DOCENTE':
                            self::insertarDocente($db, $fila, $id_institucion, $modoDuplicados);
                            break;

                        case 'ESPACIO':
                            self::insertarEspacio($db, $fila, $id_institucion);
                            break;
                        
                        case 'PROGRAMA':
                            self::insertarPrograma($db, $fila, $id_institucion, $modoDuplicados);
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
                } catch (\PDOException $pe) {
                    $isDuplicate = ($pe->getCode() == 23000 || (isset($pe->errorInfo[1]) && $pe->errorInfo[1] == 1062));
                    if ($isDuplicate && ($modoDuplicados === 'omitir' || $modoDuplicados === 'actualizar')) {
                        // En modo omitir/actualizar, no abortar la transacción completa
                        $resumen['omitidos']++;
                        continue;
                    }
                    throw $pe;
                }
            }

            $db->commit();
            return $resumen;
            
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception("Error crítico durante la importación. Todos los cambios han sido revertidos. Detalles: " . $e->getMessage());
        }
    }

    private static function insertarAsignatura(PDO $db, array $fila, int $id_institucion, string $modoDuplicados = 'omitir') {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $codigo = trim((string)($fila['codigo'] ?? ''));
        $nombre = trim((string)($fila['nombre'] ?? ''));
        $creditos = (int)($fila['creditos'] ?? 0);
        $idArea = !empty($fila['id_area_academica']) ? (int)$fila['id_area_academica'] : null;
        $idPrograma = !empty($fila['id_programa']) ? (int)$fila['id_programa'] : null;
        $semestre = !empty($fila['semestre']) ? (int)$fila['semestre'] : null;
        $idNivel = !empty($fila['id_nivel_academico']) ? (int)$fila['id_nivel_academico'] : null;

        $idAsignatura = $idExistente;

        if ($idAsignatura) {
            // Verificar si el código ya pertenece a OTRA asignatura en BD
            if ($codigo !== '') {
                $stmtCheck = $db->prepare("SELECT id_asignatura FROM asignatura WHERE id_institucion = :inst AND codigo = :cod AND id_asignatura != :id LIMIT 1");
                $stmtCheck->execute([':inst' => $id_institucion, ':cod' => $codigo, ':id' => $idAsignatura]);
                $otroId = $stmtCheck->fetchColumn();
                if ($otroId) {
                    if ($modoDuplicados === 'actualizar') {
                        $idAsignatura = (int)$otroId;
                    } elseif ($modoDuplicados === 'omitir') {
                        // Conservar el código original de BD para no provocar error de clave duplicada
                        $codigo = $fila['codigo_bd'] ?? '';
                    }
                }
            }

            $stmt = $db->prepare("
                UPDATE asignatura 
                SET codigo = :codigo, 
                    nombre = :nombre, 
                    creditos = :creditos, 
                    id_area_academica = COALESCE(:area, id_area_academica)
                WHERE id_asignatura = :id
            ");
            $stmt->execute([
                ':codigo'   => $codigo !== '' ? $codigo : ($fila['codigo_bd'] ?? '0'),
                ':nombre'   => $nombre,
                ':creditos' => $creditos,
                ':area'     => $idArea,
                ':id'       => $idAsignatura
            ]);
        } else {
            // Buscar si ya existe por código en la institución
            if ($codigo !== '') {
                $stmtBuscar = $db->prepare("SELECT id_asignatura FROM asignatura WHERE id_institucion = :inst AND codigo = :cod LIMIT 1");
                $stmtBuscar->execute([':inst' => $id_institucion, ':cod' => $codigo]);
                $idPorCod = $stmtBuscar->fetchColumn();
                if ($idPorCod) {
                    $idAsignatura = (int)$idPorCod;
                    if ($modoDuplicados === 'actualizar') {
                        $stmtUpd = $db->prepare("UPDATE asignatura SET nombre = :nombre, creditos = :creditos, id_area_academica = COALESCE(:area, id_area_academica) WHERE id_asignatura = :id");
                        $stmtUpd->execute([':nombre' => $nombre, ':creditos' => $creditos, ':area' => $idArea, ':id' => $idAsignatura]);
                    }
                }
            }

            if (!$idAsignatura) {
                $stmt = $db->prepare("
                    INSERT INTO asignatura (id_institucion, codigo, nombre, creditos, id_area_academica)
                    VALUES (:inst, :codigo, :nombre, :creditos, :area)
                    ON DUPLICATE KEY UPDATE 
                        nombre = VALUES(nombre),
                        creditos = VALUES(creditos),
                        id_area_academica = COALESCE(VALUES(id_area_academica), id_area_academica)
                ");
                
                $stmt->execute([
                    ':inst'     => $id_institucion,
                    ':codigo'   => $codigo !== '' ? $codigo : '0',
                    ':nombre'   => $nombre,
                    ':creditos' => $creditos,
                    ':area'     => $idArea
                ]);
                $idAsignatura = (int)$db->lastInsertId();
                if (!$idAsignatura && $codigo !== '') {
                    $stmtBuscar = $db->prepare("SELECT id_asignatura FROM asignatura WHERE id_institucion = :inst AND codigo = :cod LIMIT 1");
                    $stmtBuscar->execute([':inst' => $id_institucion, ':cod' => $codigo]);
                    $idAsignatura = (int)$stmtBuscar->fetchColumn();
                }
            }
        }

        // 2. Asociar curricularmente al programa en asignatura_programa
        if ($idAsignatura && $idPrograma) {
            $stmtRel = $db->prepare("
                INSERT INTO asignatura_programa (id_asignatura, id_programa, semestre)
                VALUES (:asig, :prog, :sem)
                ON DUPLICATE KEY UPDATE 
                    semestre = COALESCE(VALUES(semestre), semestre)
            ");
            $stmtRel->execute([
                ':asig' => $idAsignatura,
                ':prog' => $idPrograma,
                ':sem'  => ($semestre && $semestre > 0) ? $semestre : 1
            ]);

            // 3. Vincular Nivel Académico al Programa si fue especificado
            if ($idNivel) {
                $stmtNivel = $db->prepare("
                    UPDATE programa 
                    SET id_nivel_academico = :nivel 
                    WHERE id_programa = :prog AND (id_nivel_academico IS NULL OR id_nivel_academico = 0)
                ");
                $stmtNivel->execute([
                    ':nivel' => $idNivel,
                    ':prog'  => $idPrograma
                ]);
            }
        }
    }

    private static function insertarDocente(PDO $db, array $fila, int $id_institucion, string $modoDuplicados = 'omitir') {
        $idExistente = !empty($fila['id_existente']) ? (int)$fila['id_existente'] : null;
        $doc = trim((string)($fila['documento'] ?? ''));
        $nomCompleto = trim((string)($fila['nombre'] ?? ''));
        $partes = explode(' ', $nomCompleto, 2);
        $nombre = $partes[0] ?? $nomCompleto;
        $apellido = $partes[1] ?? '';
        $correo = trim((string)($fila['correo'] ?? ''));

        if ($idExistente) {
            if ($doc !== '') {
                $stmtCheck = $db->prepare("SELECT id_docente FROM docente WHERE id_institucion = :inst AND codigo = :doc AND id_docente != :id LIMIT 1");
                $stmtCheck->execute([':inst' => $id_institucion, ':doc' => $doc, ':id' => $idExistente]);
                $otroId = $stmtCheck->fetchColumn();
                if ($otroId) {
                    if ($modoDuplicados === 'actualizar') {
                        $idExistente = (int)$otroId;
                    } elseif ($modoDuplicados === 'omitir') {
                        $doc = $fila['codigo_bd'] ?? '';
                    }
                }
            }

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

    private static function insertarPrograma(PDO $db, array $fila, int $id_institucion, string $modoDuplicados = 'omitir') {
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
            } else {
                $stmtCheck = $db->prepare("SELECT id_programa FROM programa WHERE codigo = :cod AND id_programa != :id LIMIT 1");
                $stmtCheck->execute([':cod' => $codigo, ':id' => $idExistente]);
                $otroId = $stmtCheck->fetchColumn();
                if ($otroId) {
                    if ($modoDuplicados === 'actualizar') {
                        $idExistente = (int)$otroId;
                    } elseif ($modoDuplicados === 'omitir') {
                        $codigo = $fila['codigo_bd'] ?? '';
                    }
                }
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
