<?php

namespace App\Services\Importar;

use App\Config\Database;
use PDO;

class ExistenceChecker {
    
    /**
     * Verifica la existencia de una entidad en la base de datos y compara técnicamente sus campos.
     * Determina el estado:
     * - NUEVO: No existe previamente en la base de datos.
     * - EXISTENTE: Ya existe en la base de datos con los mismos identificadores o sin divergencia.
     * - ACTUALIZAR: Ya existe por nombre/código pero contiene diferencias (ej. códigos divergentes). Habilitado para sobreescritura.
     * - DUPLICADO: Idéntico a un registro previo analizado en el lote actual o en BD.
     * - CONFLICTO / ERROR: Violación de integridad o inconsistencia insalvable.
     *
     * @param string $entityKey Clave de entidad ('PROGRAMA', 'ASIGNATURA', 'DOCENTE', etc.)
     * @param int $id_institucion ID de la institución activa
     * @param string $modoDuplicados 'omitir', 'actualizar', 'estricto'
     * @return array ['estado' => string, 'detalle' => string, 'id_existente' => ?int, 'codigo_bd' => ?string]
     */
    public static function check(string $entityKey, array &$filaEstructurada, int $id_institucion = 1, string $modoDuplicados = 'omitir'): array {
        $db = Database::getConexion();
        $estado = 'NUEVO';
        $detalle = 'Nuevo registro listo para insertar.';
        $idExistente = null;
        $codigoBd = null;

        switch ($entityKey) {
            case 'PROGRAMA':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $codigo = trim((string)($filaEstructurada['codigo'] ?? ''));
                $id_facultad = !empty($filaEstructurada['id_facultad']) ? (int)$filaEstructurada['id_facultad'] : null;

                // 1. Buscar si existe por nombre (dentro de la facultad si se conoce)
                $dbRowNombre = null;
                if ($id_facultad) {
                    $stmt = $db->prepare("SELECT id_programa, id_facultad, codigo, nombre FROM programa WHERE id_facultad = :fac AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                    $stmt->execute([':fac' => $id_facultad, ':nom' => $nombre]);
                    $dbRowNombre = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                if (!$dbRowNombre) {
                    $stmt = $db->prepare("SELECT id_programa, id_facultad, codigo, nombre FROM programa WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                    $stmt->execute([':nom' => $nombre]);
                    $dbRowNombre = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // 2. Buscar si existe por código
                $dbRowCodigo = null;
                if ($codigo !== '') {
                    $stmt = $db->prepare("SELECT id_programa, id_facultad, codigo, nombre FROM programa WHERE codigo = :cod LIMIT 1");
                    $stmt->execute([':cod' => $codigo]);
                    $dbRowCodigo = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($dbRowNombre) {
                    $idExistente = (int)$dbRowNombre['id_programa'];
                    $codigoBd = $dbRowNombre['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    if ($codigo === '') {
                        // El excel no tiene código, pero en BD ya está registrado con código
                        $estado = 'EXISTENTE';
                        $detalle = "Registro preexistente en BD [Código: '{$codigoBd}'] registrado previamente en el sistema. El archivo no define código; se conservará el código de BD.";
                    } elseif (strcasecmp($codigo, $codigoBd) === 0) {
                        // Ambos tienen código y coinciden
                        $estado = 'EXISTENTE';
                        $detalle = "Coincidencia exacta: El código '{$codigo}' coincide con el registro activo en BD (ID #{$idExistente}). Registro sincronizado.";
                    } else {
                        // Conflicto de código: verificar si $codigo ya le pertenece a otro programa en BD
                        if ($dbRowCodigo && (int)$dbRowCodigo['id_programa'] !== $idExistente) {
                            if ($modoDuplicados === 'omitir') {
                                $estado = 'DUPLICADO';
                                $detalle = "Código '{$codigo}' ya asignado en BD a '{$dbRowCodigo['nombre']}'. Omitido para evitar colisión.";
                            } elseif ($modoDuplicados === 'actualizar') {
                                $idExistente = (int)$dbRowCodigo['id_programa'];
                                $filaEstructurada['id_existente'] = $idExistente;
                                $estado = 'ACTUALIZAR';
                                $detalle = "Código '{$codigo}' existente en BD. Habilitado para actualizar datos de '{$dbRowCodigo['nombre']}'.";
                            } else {
                                $estado = 'CONFLICTO';
                                $detalle = "Conflicto crítico: El código '{$codigo}' ya pertenece al programa '{$dbRowCodigo['nombre']}'.";
                            }
                        } else {
                            if ($modoDuplicados === 'omitir') {
                                $estado = 'EXISTENTE';
                                $detalle = "Programa coincidente por nombre. Conservado código de BD para evitar conflicto.";
                            } else {
                                $estado = 'ACTUALIZAR';
                                $detalle = "Divergencia de código: El archivo especifica '{$codigo}' pero en BD figura como '{$codigoBd}'. Habilitado para sobreescribir en BD.";
                            }
                        }
                    }
                } elseif ($dbRowCodigo) {
                    $idExistente = (int)$dbRowCodigo['id_programa'];
                    $codigoBd = $dbRowCodigo['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    if ($modoDuplicados === 'omitir') {
                        $estado = 'EXISTENTE';
                        $detalle = "Programa con código '{$codigo}' ya existe en BD ('{$dbRowCodigo['nombre']}'). Omitido sin error.";
                    } else {
                        $estado = 'ACTUALIZAR';
                        $detalle = "Divergencia de nombre: El código '{$codigo}' ya está asignado al programa '{$dbRowCodigo['nombre']}' en BD. Habilitado para actualizar datos.";
                    }
                } else {
                    $estado = 'NUEVO';
                    if ($codigo !== '') {
                        $detalle = "Nuevo programa académico. Código asignado: '{$codigo}'. Listo para registrar.";
                    } else {
                        $detalle = "Nuevo programa académico. Sin código en archivo; el sistema generará automáticamente un código institucional.";
                    }
                }
                break;

            case 'ASIGNATURA':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $codigo = trim((string)($filaEstructurada['codigo'] ?? ''));
                $idPrograma = !empty($filaEstructurada['id_programa']) ? (int)$filaEstructurada['id_programa'] : null;
                $semestre = !empty($filaEstructurada['semestre']) ? (int)$filaEstructurada['semestre'] : null;

                $dbRowNombre = null;
                $stmt = $db->prepare("SELECT id_asignatura, codigo, nombre, creditos, id_area_academica FROM asignatura WHERE id_institucion = :inst AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                $stmt->execute([':inst' => $id_institucion, ':nom' => $nombre]);
                $dbRowNombre = $stmt->fetch(PDO::FETCH_ASSOC);

                $dbRowCodigo = null;
                if ($codigo !== '') {
                    $stmt = $db->prepare("SELECT id_asignatura, codigo, nombre, creditos, id_area_academica FROM asignatura WHERE id_institucion = :inst AND codigo = :cod LIMIT 1");
                    $stmt->execute([':inst' => $id_institucion, ':cod' => $codigo]);
                    $dbRowCodigo = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                $asigExistenteRow = $dbRowCodigo ?: $dbRowNombre;

                if ($asigExistenteRow) {
                    $idExistente = (int)$asigExistenteRow['id_asignatura'];
                    $codigoBd = $asigExistenteRow['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    // Si la fila incluye Programa Académico, verificamos la vinculación en asignatura_programa
                    if ($idPrograma) {
                        $stmtRel = $db->prepare("SELECT id_asignatura_programa, semestre FROM asignatura_programa WHERE id_asignatura = :asig AND id_programa = :prog LIMIT 1");
                        $stmtRel->execute([':asig' => $idExistente, ':prog' => $idPrograma]);
                        $relRow = $stmtRel->fetch(PDO::FETCH_ASSOC);

                        if ($relRow) {
                            if ($semestre && (int)$relRow['semestre'] !== (int)$semestre) {
                                $estado = 'ACTUALIZAR';
                                $detalle = "Asignatura vinculada a '{$filaEstructurada['programa']}'. Semestre se actualizará de '{$relRow['semestre']}' a '{$semestre}'.";
                            } else {
                                $estado = 'EXISTENTE';
                                $detalle = "Asignatura y vinculación con programa '{$filaEstructurada['programa']}' ya sincronizadas en BD.";
                            }
                        } else {
                            $estado = 'NUEVO';
                            $detalle = "Asignatura existente en BD [Código: '{$codigoBd}']. Se creará asociación curricular con el programa '{$filaEstructurada['programa']}'.";
                        }
                    } else {
                        // Caso sin programa
                        if ($codigo === '' || strcasecmp($codigo, $codigoBd) === 0) {
                            $estado = 'EXISTENTE';
                            $detalle = "Asignatura coincidente en BD [Código: '{$codigoBd}']. Registro sincronizado.";
                        } else {
                            if ($modoDuplicados === 'omitir') {
                                $estado = 'EXISTENTE';
                                $detalle = "Asignatura registrada en BD. Conservado código '{$codigoBd}'.";
                            } else {
                                $estado = 'ACTUALIZAR';
                                $detalle = "Divergencia de código: El archivo especifica '{$codigo}' pero en BD figura como '{$codigoBd}'. Habilitado para actualizar.";
                            }
                        }
                    }
                } else {
                    $estado = 'NUEVO';
                    $progTxt = !empty($filaEstructurada['programa']) ? " para el programa '{$filaEstructurada['programa']}'" : "";
                    $detalle = $codigo !== '' 
                        ? "Nueva asignatura con código '{$codigo}'{$progTxt}. Lista para registrar." 
                        : "Nueva asignatura sin código{$progTxt}. Se asignará código institucional.";
                }
                break;

            case 'DOCENTE':
                $documento = trim((string)($filaEstructurada['documento'] ?? ''));
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));

                $dbRowDoc = null;
                if ($documento !== '') {
                    $stmt = $db->prepare("SELECT id_docente, codigo, nombre, apellido, correo FROM docente WHERE id_institucion = :inst AND codigo = :doc LIMIT 1");
                    $stmt->execute([':inst' => $id_institucion, ':doc' => $documento]);
                    $dbRowDoc = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                $dbRowNom = null;
                if ($nombre !== '') {
                    $stmt = $db->prepare("SELECT id_docente, codigo, nombre, apellido, correo FROM docente WHERE id_institucion = :inst AND LOWER(CONCAT(TRIM(nombre), ' ', TRIM(apellido))) = LOWER(TRIM(:nom)) LIMIT 1");
                    $stmt->execute([':inst' => $id_institucion, ':nom' => $nombre]);
                    $dbRowNom = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($dbRowDoc) {
                    $idExistente = (int)$dbRowDoc['id_docente'];
                    $codigoBd = $dbRowDoc['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;
                    $nombreCompletoBd = trim($dbRowDoc['nombre'] . ' ' . $dbRowDoc['apellido']);

                    if (strcasecmp($nombre, $nombreCompletoBd) === 0 || $nombre === '') {
                        $estado = 'EXISTENTE';
                        $detalle = "Coincidencia exacta: Docente con documento/código '{$documento}' existente en BD (ID #{$idExistente}).";
                    } else {
                        if ($modoDuplicados === 'omitir') {
                            $estado = 'EXISTENTE';
                            $detalle = "Docente con documento '{$documento}' ya registrado en BD ('{$nombreCompletoBd}'). Conservado sin cambios.";
                        } else {
                            $estado = 'ACTUALIZAR';
                            $detalle = "Divergencia de datos: Documento '{$documento}' coincide, pero el nombre en BD es '{$nombreCompletoBd}'. Habilitado para actualizar.";
                        }
                    }
                } elseif ($dbRowNom) {
                    $idExistente = (int)$dbRowNom['id_docente'];
                    $codigoBd = $dbRowNom['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    if ($documento !== '') {
                        $stmtCheckDoc = $db->prepare("SELECT id_docente, nombre, apellido FROM docente WHERE id_institucion = :inst AND codigo = :doc AND id_docente != :id LIMIT 1");
                        $stmtCheckDoc->execute([':inst' => $id_institucion, ':doc' => $documento, ':id' => $idExistente]);
                        $docColision = $stmtCheckDoc->fetch(PDO::FETCH_ASSOC);
                        if ($docColision) {
                            if ($modoDuplicados === 'omitir') {
                                $estado = 'DUPLICADO';
                                $detalle = "Documento '{$documento}' ya pertenece a otro docente en BD. Omitido para evitar colisión.";
                            } elseif ($modoDuplicados === 'actualizar') {
                                $idExistente = (int)$docColision['id_docente'];
                                $filaEstructurada['id_existente'] = $idExistente;
                                $estado = 'ACTUALIZAR';
                                $detalle = "Documento '{$documento}' encontrado en BD. Actualizando registro correspondiente.";
                            } else {
                                $estado = 'CONFLICTO';
                                $detalle = "Conflicto crítico: El documento '{$documento}' ya está asignado a otro docente.";
                            }
                        } else {
                            $estado = 'ACTUALIZAR';
                            $detalle = "Divergencia de documento: Docente coincide por nombre, pero en BD tiene documento '{$codigoBd}' y el archivo trae '{$documento}'. Habilitado para sobreescribir.";
                        }
                    } else {
                        $estado = 'EXISTENTE';
                        $detalle = "Docente coincidente en BD por nombre. Conservado sin cambios.";
                    }
                } else {
                    $estado = 'NUEVO';
                    $detalle = "Nuevo docente con documento '{$documento}'. Listo para registrar.";
                }
                break;

            case 'ESPACIO':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $id_bloque = !empty($filaEstructurada['id_bloque']) ? (int)$filaEstructurada['id_bloque'] : null;

                if ($id_bloque) {
                    $stmt = $db->prepare("SELECT id_espacio, codigo, nombre, tipo, capacidad FROM espacio WHERE id_bloque = :bloque AND (codigo = :cod OR LOWER(TRIM(nombre)) = LOWER(TRIM(:nom))) LIMIT 1");
                    $stmt->execute([':bloque' => $id_bloque, ':cod' => $nombre, ':nom' => $nombre]);
                    $dbRow = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($dbRow) {
                        $idExistente = (int)$dbRow['id_espacio'];
                        $codigoBd = $dbRow['codigo'];
                        $filaEstructurada['id_existente'] = $idExistente;
                        $filaEstructurada['codigo_bd'] = $codigoBd;

                        $capacidadExcel = (int)($filaEstructurada['capacidad'] ?? 0);
                        if ($capacidadExcel > 0 && $capacidadExcel != (int)$dbRow['capacidad']) {
                            $estado = 'ACTUALIZAR';
                            $detalle = "Espacio físico existente en el bloque. Capacidad en archivo ({$capacidadExcel}) difiere de BD ({$dbRow['capacidad']}). Habilitado para sobreescribir.";
                        } else {
                            $estado = 'EXISTENTE';
                            $detalle = "Coincidencia exacta: El espacio ya se encuentra registrado en el bloque con código '{$codigoBd}'.";
                        }
                    } else {
                        $estado = 'NUEVO';
                        $detalle = "Nuevo espacio físico listo para registrar en el bloque.";
                    }
                } else {
                    $estado = 'ERROR';
                    $detalle = "Bloque de destino no resuelto o no especificado.";
                }
                break;

            case 'FACULTAD':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $stmt = $db->prepare("SELECT id_facultad, codigo, nombre FROM facultad WHERE id_institucion = :inst AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                $stmt->execute([':inst' => $id_institucion, ':nom' => $nombre]);
                $dbRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($dbRow) {
                    $idExistente = (int)$dbRow['id_facultad'];
                    $codigoBd = $dbRow['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    $estado = 'EXISTENTE';
                    $detalle = "Facultad preexistente en BD [Código: '{$codigoBd}']. Registro sincronizado.";
                } else {
                    $estado = 'NUEVO';
                    $detalle = "Nueva facultad. Lista para crear en la institución.";
                }
                break;

            case 'SEDE':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $stmt = $db->prepare("SELECT id_sede, codigo, nombre, direccion FROM sede WHERE id_institucion = :inst AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                $stmt->execute([':inst' => $id_institucion, ':nom' => $nombre]);
                $dbRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($dbRow) {
                    $idExistente = (int)$dbRow['id_sede'];
                    $codigoBd = $dbRow['codigo'];
                    $filaEstructurada['id_existente'] = $idExistente;
                    $filaEstructurada['codigo_bd'] = $codigoBd;

                    $dirExcel = trim((string)($filaEstructurada['direccion'] ?? ''));
                    if ($dirExcel !== '' && strcasecmp($dirExcel, (string)$dbRow['direccion']) !== 0) {
                        $estado = 'ACTUALIZAR';
                        $detalle = "Sede existente [Código: '{$codigoBd}']. Dirección en archivo difiere de BD. Habilitado para sobreescribir dirección.";
                    } else {
                        $estado = 'EXISTENTE';
                        $detalle = "Sede preexistente en BD [Código: '{$codigoBd}']. Registro sincronizado.";
                    }
                } else {
                    $estado = 'NUEVO';
                    $detalle = "Nueva sede física. Lista para registrar.";
                }
                break;

            case 'BLOQUE':
                $nombre = trim((string)($filaEstructurada['nombre'] ?? ''));
                $id_sede = !empty($filaEstructurada['id_sede']) ? (int)$filaEstructurada['id_sede'] : null;

                if ($id_sede) {
                    $stmt = $db->prepare("SELECT id_bloque, codigo, nombre FROM bloque WHERE id_sede = :sede AND LOWER(TRIM(nombre)) = LOWER(TRIM(:nom)) LIMIT 1");
                    $stmt->execute([':sede' => $id_sede, ':nom' => $nombre]);
                    $dbRow = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($dbRow) {
                        $idExistente = (int)$dbRow['id_bloque'];
                        $codigoBd = $dbRow['codigo'];
                        $filaEstructurada['id_existente'] = $idExistente;
                        $filaEstructurada['codigo_bd'] = $codigoBd;

                        $estado = 'EXISTENTE';
                        $detalle = "Bloque preexistente en la sede [Código: '{$codigoBd}']. Registro sincronizado.";
                    } else {
                        $estado = 'NUEVO';
                        $detalle = "Nuevo bloque listo para registrar en la sede.";
                    }
                } else {
                    $estado = 'ERROR';
                    $detalle = "Sede de destino no resuelta o no especificada.";
                }
                break;
        }

        return [
            'estado'       => $estado,
            'detalle'      => $detalle,
            'id_existente' => $idExistente,
            'codigo_bd'    => $codigoBd
        ];
    }
}
