<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use Exception;
use Throwable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ImportExport {

    public static function phpspreadsheetDisponible() {
        return class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');
    }

    /**
     * Normaliza un texto para comparaciones flexibles:
     * - Remueve tildes y diacríticos
     * - Pasa a minúsculas
     * - Colapsa espacios y caracteres especiales
     */
    public static function normalizarCanonico($str) {
        if ($str === null || $str === '') return '';
        $str = mb_strtolower(trim((string)$str), 'UTF-8');
        $map = [
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u', 'ü'=>'u', 'ñ'=>'n',
            'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
            'Á'=>'a', 'É'=>'e', 'Í'=>'i', 'Ó'=>'o', 'Ú'=>'u', 'Ü'=>'u', 'Ñ'=>'n'
        ];
        $str = strtr($str, $map);
        // Quitar caracteres no alfanuméricos excepto espacios
        $str = preg_replace('/[^a-z0-9 ]/u', ' ', $str);
        // Colapsar espacios múltiples
        return preg_replace('/\s+/', ' ', trim($str));
    }

    /**
     * Convierte número romano (I, II, ..., X) o arábigo a entero positivo.
     */
    public static function romanoAEntero($val) {
        if ($val === null || $val === '') return null;
        $str = strtoupper(trim((string)$val));
        
        // Si ya es un número arábigo
        if (is_numeric($str)) {
            $num = (int)$str;
            return ($num >= 1 && $num <= 20) ? $num : null;
        }

        $romanos = [
            'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5,
            'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10,
            'XI' => 11, 'XII' => 12, 'XIII' => 13, 'XIV' => 14, 'XV' => 15,
            'XVI' => 16, 'XVII' => 17, 'XVIII' => 18, 'XIX' => 19, 'XX' => 20
        ];

        return $romanos[$str] ?? null;
    }

    /**
     * Convierte número entero a número romano para la exportación.
     */
    public static function enteroARomano($num) {
        $n = (int)$num;
        $map = [
            20 => 'XX', 19 => 'XIX', 18 => 'XVIII', 17 => 'XVII', 16 => 'XVI',
            15 => 'XV', 14 => 'XIV', 13 => 'XIII', 12 => 'XII', 11 => 'XI',
            10 => 'X', 9 => 'IX', 8 => 'VIII', 7 => 'VII', 6 => 'VI',
            5 => 'V', 4 => 'IV', 3 => 'III', 2 => 'II', 1 => 'I'
        ];
        return $map[$n] ?? (string)$n;
    }

    /**
     * Obtiene el mapa de programas indexados por clave canónica.
     */
    public static function obtenerMapaProgramas($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, na.nombre AS nivel_nombre
            FROM programa p
            JOIN facultad f ON p.id_facultad = f.id_facultad
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE f.id_institucion = :inst
        ");
        $stmt->execute([':inst' => $id_institucion]);
        $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapa = [];
        foreach ($programas as $p) {
            $clave = self::normalizarCanonico($p['nombre']);
            $mapa[$clave] = $p;
            // También indexar por código
            $claveCod = self::normalizarCanonico($p['codigo']);
            $mapa[$claveCod] = $p;
        }
        return $mapa;
    }

    /**
     * Obtiene el mapa de áreas académicas indexadas por clave canónica.
     */
    public static function obtenerMapaAreas($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM area_academica WHERE id_institucion = :inst");
        $stmt->execute([':inst' => $id_institucion]);
        $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapa = [];
        foreach ($areas as $a) {
            $clave = self::normalizarCanonico($a['nombre']);
            $mapa[$clave] = $a;
        }
        return $mapa;
    }

    /**
     * Procesa y valida el archivo Excel de asignaturas sin insertar en BD.
     * Retorna array con la previsualización y el resumen de estadísticas.
     */
    public static function previsualizarAsignaturas($rutaArchivo, $id_institucion = 1) {
        if (!self::phpspreadsheetDisponible()) {
            throw new Exception("PhpSpreadsheet no está disponible en el servidor.");
        }

        $spreadsheet = IOFactory::load($rutaArchivo);
        $hoja = $spreadsheet->getActiveSheet();
        $filasExcel = $hoja->toArray(null, true, true, true);

        if (empty($filasExcel)) {
            throw new Exception("El archivo Excel está vacío.");
        }

        // Cargar mapas de catálogos existentes para comparación rápida en memoria
        $mapaProgramas = self::obtenerMapaProgramas($id_institucion);
        $mapaAreas     = self::obtenerMapaAreas($id_institucion);

        // Cargar asignaturas y relaciones existentes en memoria
        $db = Database::getConexion();
        $stmtAsig = $db->prepare("SELECT id_asignatura, codigo, nombre, creditos, id_area_academica FROM asignatura WHERE id_institucion = :inst");
        $stmtAsig->execute([':inst' => $id_institucion]);
        $asignaturasBD = [];
        foreach ($stmtAsig->fetchAll(PDO::FETCH_ASSOC) as $a) {
            $asignaturasBD[strtoupper(trim($a['codigo']))] = $a;
        }

        $stmtRel = $db->prepare("SELECT ap.id_asignatura, ap.id_programa, ap.semestre, a.codigo FROM asignatura_programa ap JOIN asignatura a ON a.id_asignatura = ap.id_asignatura WHERE a.id_institucion = :inst");
        $stmtRel->execute([':inst' => $id_institucion]);
        $relacionesBD = [];
        foreach ($stmtRel->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $key = strtoupper(trim($r['codigo'])) . '|' . (int)$r['id_programa'];
            $relacionesBD[$key] = $r;
        }

        // Mapear encabezados dinámicamente por nombre de columna
        $headersRaw = array_shift($filasExcel);
        $colIndex = [];
        foreach ($headersRaw as $col => $nom) {
            $canon = self::normalizarCanonico($nom);
            if (str_contains($canon, 'programa')) {
                $colIndex['programa'] = $col;
            } elseif (str_contains($canon, 'codigo') && str_contains($canon, 'asig')) {
                $colIndex['codigo'] = $col;
            } elseif (str_contains($canon, 'codigo')) {
                if (!isset($colIndex['codigo'])) $colIndex['codigo'] = $col;
            } elseif (str_contains($canon, 'asig') || str_contains($canon, 'nombre')) {
                if (!isset($colIndex['asignatura'])) $colIndex['asignatura'] = $col;
            } elseif (str_contains($canon, 'semestre')) {
                $colIndex['semestre'] = $col;
            } elseif (str_contains($canon, 'credito')) {
                $colIndex['creditos'] = $col;
            } elseif (str_contains($canon, 'nivel')) {
                $colIndex['nivel'] = $col;
            } elseif (str_contains($canon, 'componente')) {
                $colIndex['componente'] = $col;
            } elseif (str_contains($canon, 'area')) {
                $colIndex['area'] = $col;
            }
        }

        // Fallback si alguna columna clave no se detectó
        if (!isset($colIndex['codigo']) || !isset($colIndex['asignatura']) || !isset($colIndex['programa'])) {
            throw new Exception("El archivo no contiene las columnas requeridas (PROGRAMA, ASIGNATURA, CODIGO ASIGNATURA). Verifique los encabezados.");
        }

        $filasProcesadas = [];
        $duplicadosEnArchivo = [];
        $asignaturasVistasEnArchivo = [];

        $stats = [
            'total_filas'         => 0,
            'nuevas_asignaturas'  => 0,
            'existentes'          => 0,
            'nuevas_asociaciones' => 0,
            'duplicados_archivo'  => 0,
            'conflictos'          => 0,
            'completar'           => 0,
            'semestres_actualizados' => 0,
            'errores'             => 0,
            'listos_para_importar'=> 0,
            'semestres_a_actualizar' => 0,
            'nuevos_con_codigo_duplicado' => 0,
        ];

        $numFila = 1;
        foreach ($filasExcel as $rawRow) {
            $numFila++;

            $progNombreRaw   = trim((string)($rawRow[$colIndex['programa'] ?? ''] ?? ''));
            $asigNombreRaw   = trim((string)($rawRow[$colIndex['asignatura'] ?? ''] ?? ''));
            $asigCodigoRaw   = strtoupper(trim((string)($rawRow[$colIndex['codigo'] ?? ''] ?? '')));
            $semestreRaw     = trim((string)($rawRow[$colIndex['semestre'] ?? ''] ?? ''));
            $creditosRaw     = trim((string)($rawRow[$colIndex['creditos'] ?? ''] ?? ''));
            $nivelRaw        = trim((string)($rawRow[$colIndex['nivel'] ?? ''] ?? ''));
            $componenteRaw   = trim((string)($rawRow[$colIndex['componente'] ?? ''] ?? ''));
            $areaRaw         = trim((string)($rawRow[$colIndex['area'] ?? ''] ?? ''));

            // Omitir filas completamente vacías
            if ($progNombreRaw === '' && $asigNombreRaw === '' && $asigCodigoRaw === '') {
                continue;
            }

            $stats['total_filas']++;

            $erroresFila = [];
            $advertenciasFila = [];
            $estado = 'NUEVO';
            $detalle = '';

            // 1. Validar Código
            if ($asigCodigoRaw === '') {
                $erroresFila[] = 'Código de asignatura obligatorio';
            }

            // 2. Validar Nombre
            if ($asigNombreRaw === '') {
                $erroresFila[] = 'Nombre de asignatura obligatorio';
            }

            // 3. Validar Créditos — 0 es válido (Proyecto de Grado se registra con 00)
            $creditosNum = (int)$creditosRaw;
            if ($creditosNum < 0) {
                $erroresFila[] = "Créditos inválidos («{$creditosRaw}»). Debe ser 0 (Proyecto de Grado) o un entero positivo";
            }

            // 4. Validar Semestre (Romano o Arábigo)
            $semestreNum = self::romanoAEntero($semestreRaw);
            if ($semestreNum === null) {
                $erroresFila[] = "Semestre inválido («{$semestreRaw}»). Debe ser entre I y XX (o 1 y 20)";
            }

            // 5. Validar Programa
            $programaBD = null;
            if ($progNombreRaw === '') {
                $erroresFila[] = 'Programa académico obligatorio';
            } else {
                $canonProg = self::normalizarCanonico($progNombreRaw);
                if (isset($mapaProgramas[$canonProg])) {
                    $programaBD = $mapaProgramas[$canonProg];
                } else {
                    $erroresFila[] = "Programa no encontrado en la base de datos («{$progNombreRaw}»)";
                }
            }

            // 6. Validar Nivel de Formación (Consistencia con el programa)
            if ($programaBD && !empty($programaBD['nivel_nombre']) && $nivelRaw !== '') {
                $canonNivelExcel = self::normalizarCanonico($nivelRaw);
                $canonNivelProg  = self::normalizarCanonico($programaBD['nivel_nombre']);
                if ($canonNivelExcel !== '' && !str_contains($canonNivelProg, $canonNivelExcel) && !str_contains($canonNivelExcel, $canonNivelProg)) {
                    $advertenciasFila[] = "Nivel del Excel («{$nivelRaw}») difiere del programa en BD («{$programaBD['nivel_nombre']}»)";
                }
            }

            // 7. Resolver Área Académica (usando AREA o COMPONENTE)
            $areaBD = null;
            $textoAreaBuscar = $areaRaw !== '' ? $areaRaw : $componenteRaw;
            if ($textoAreaBuscar !== '') {
                $canonArea = self::normalizarCanonico($textoAreaBuscar);
                if (isset($mapaAreas[$canonArea])) {
                    $areaBD = $mapaAreas[$canonArea];
                }
            }

            // Determinar si hay error fatal de validación
            if (!empty($erroresFila)) {
                $estado = 'ERROR';
                $detalle = implode(' | ', $erroresFila);
                $stats['errores']++;
            } else {
                // Chequeo de duplicado dentro del mismo Excel
                $idProg = (int)$programaBD['id_programa'];
                $canonNombreExcel = self::normalizarCanonico($asigNombreRaw);
                $claveArchivo = $asigCodigoRaw . '|' . $idProg . '|' . $canonNombreExcel;

                if (isset($duplicadosEnArchivo[$claveArchivo])) {
                    $estado = 'DUPLICADO';
                    $detalle = 'Fila repetida en el mismo archivo Excel';
                    $stats['duplicados_archivo']++;
                } else {
                    $duplicadosEnArchivo[$claveArchivo] = true;

                    // Comparar contra Base de Datos
                    $claveBD = $asigCodigoRaw . '|' . $idProg;
                    $existeAsigBD = isset($asignaturasBD[$asigCodigoRaw]);
                    $existeRelBD  = isset($relacionesBD[$claveBD]);

                    if ($existeAsigBD) {
                        $asigExistente = $asignaturasBD[$asigCodigoRaw];

                        // Check for missing data that can be completed
                        $faltanDatos = false;
                        $detallesFaltan = [];
                        // Créditos
                        if ((int)($asigExistente['creditos'] ?? 0) === 0 && $creditosNum > 0) {
                            $faltanDatos = true;
                            $detallesFaltan[] = "creditos";
                        }
                        // Área académica
                        if (empty($asigExistente['id_area_academica']) && $areaBD) {
                            $faltanDatos = true;
                            $detallesFaltan[] = "area académica";
                        }
                        // Nivel (asumiendo campo 'tipo' como nivel, simplificado)
                        if (empty($asigExistente['tipo']) && $nivelRaw !== '') {
                            $faltanDatos = true;
                            $detallesFaltan[] = "tipo";
                        }

                        if ($faltanDatos) {
                            $estado = 'COMPLETAR';
                            $detalle = "Faltan datos en BD: " . implode(', ', $detallesFaltan);
                            $stats['completar']++;
                        } else {
                            // Existing record with same data, evaluate name/credit conflicts as before
                            $canonNombreExcel = self::normalizarCanonico($asigNombreRaw);
                            $canonNombreBD    = self::normalizarCanonico($asigExistente['nombre']);

                            if ($canonNombreExcel !== $canonNombreBD && !str_contains($canonNombreBD, $canonNombreExcel) && !str_contains($canonNombreExcel, $canonNombreBD)) {
                                $estado = 'NUEVO';
                                $detalle = "Código ya existe con otro nombre en BD («{$asigExistente['nombre']}»). Se creará como NUEVA.";
                                $stats['nuevos_con_codigo_duplicado']++;
                                if (!isset($asignaturasVistasEnArchivo[$asigCodigoRaw . '|' . $canonNombreExcel])) {
                                    $stats['nuevas_asignaturas']++;
                                    $asignaturasVistasEnArchivo[$asigCodigoRaw . '|' . $canonNombreExcel] = true;
                                }
                                $stats['listos_para_importar']++;
                            } elseif ((int)($asigExistente['creditos'] ?? 0) !== $creditosNum) {
                                $estado = 'CONFLICTO';
                                $detalle = "Código ya existe con créditos distintos ({$asigExistente['creditos']} vs {$creditosNum})";
                                $stats['conflictos']++;
                            } elseif ($existeRelBD) {
                                $relExistente = $relacionesBD[$claveBD];
                                $semestreBD = (int)($relExistente['semestre'] ?? 0);

                                if ($semestreBD === $semestreNum) {
                                    $estado = 'EXISTENTE';
                                    $detalle = "Ya existe la asignatura y su asociación con Semestre {$semestreNum}";
                                    $stats['existentes']++;
                                } elseif ($semestreBD === 0 && $semestreNum > 0) {
                                    $estado = 'COMPLETAR';
                                    $detalle = "Actualizar semestre 0 a {$semestreNum}";
                                    $stats['semestres_a_actualizar']++;
                                    $stats['completar']++;
                                } else {
                                    $estado = 'CONFLICTO';
                                    $detalle = "Ya existe en este programa pero con Semestre {$semestreBD} en BD";
                                    $stats['conflictos']++;
                                }
                            } else {
                                // Asignatura existe pero no estaba asociada a este programa
                                $estado = 'LISTO';
                                $sufijoCred = ($creditosNum === 0) ? ' · 00 créditos (Proyecto de Grado)' : '';
                                $detalle = "Asignatura ya existe. Se creará la asociación con el programa en Semestre {$semestreNum}{$sufijoCred}";
                                $stats['nuevas_asociaciones']++;
                                $stats['listos_para_importar']++;
                            }
                        }
                    } else {
                        // Asignatura completamente nueva
                        $estado = 'NUEVO';
                        $sufijoCred = ($creditosNum === 0) ? ' · 00 créditos (Proyecto de Grado)' : '';
                        $detalle = "Asignatura nueva y relación en Semestre {$semestreNum} listas para crear{$sufijoCred}";
                        if (!isset($asignaturasVistasEnArchivo[$asigCodigoRaw . '|' . self::normalizarCanonico($asigNombreRaw)])) {
                            $stats['nuevas_asignaturas']++;
                            $asignaturasVistasEnArchivo[$asigCodigoRaw . '|' . self::normalizarCanonico($asigNombreRaw)] = true;
                        }
                        $stats['nuevas_asociaciones']++;
                        $stats['listos_para_importar']++;
                    }

                    if (!empty($advertenciasFila)) {
                        $detalle .= ' (' . implode(', ', $advertenciasFila) . ')';
                    }
                }
            }

            $filasProcesadas[] = [
                'fila_excel'          => $numFila,
                'codigo'              => $asigCodigoRaw,
                'asignatura'          => $asigNombreRaw,
                'programa_texto'      => $progNombreRaw,
                'id_programa'         => $programaBD ? (int)$programaBD['id_programa'] : null,
                'programa_oficial'    => $programaBD ? $programaBD['nombre'] : null,
                'semestre_texto'      => $semestreRaw,
                'semestre_num'        => $semestreNum,
                'creditos'            => $creditosNum,
                'nivel_texto'         => $nivelRaw,
                'area_texto'          => $areaRaw !== '' ? $areaRaw : $componenteRaw,
                'id_area_academica'   => $areaBD ? (int)$areaBD['id_area_academica'] : null,
                'area_oficial'        => $areaBD ? $areaBD['nombre'] : null,
                'estado'              => $estado,
                'detalle'             => $detalle,
                'valida_importar'     => in_array($estado, ['NUEVO', 'LISTO', 'COMPLETAR'], true),
            ];
        }

        return [
            'stats' => $stats,
            'filas' => $filasProcesadas,
        ];
    }

    /**
     * Ejecuta la importación en la base de datos dentro de una transacción PDO atómica.
     * Solo inserta las filas marcadas como válidas (NUEVO o LISTO).
     */
    public static function ejecutarImportacionAsignaturas(array $filas, $id_institucion = 1) {
        $db = Database::getConexion();
        $db->beginTransaction();

        try {
            $asignaturasCreadas = 0;
            $asignaturasExistentesOmitidas = 0;
            $asociacionesCreadas = 0;
            $filasOmitidas = 0;

            // Cache local para asignaturas que se van creando dentro del lote
            $stmtBuscarAsig = $db->prepare("SELECT id_asignatura FROM asignatura WHERE codigo = :cod AND id_institucion = :inst LIMIT 1");
            $stmtCrearAsig  = $db->prepare("
                INSERT INTO asignatura (id_institucion, id_area_academica, codigo, nombre, creditos, estado)
                VALUES (:inst, :area, :cod, :nom, :cred, 'ACTIVO')
            ");

            $stmtCrearRel = $db->prepare("
                INSERT INTO asignatura_programa (id_asignatura, id_programa, semestre)
                VALUES (:asig, :prog, :sem)
                ON DUPLICATE KEY UPDATE semestre = VALUES(semestre)
            ");
            // Prepared statement to update missing fields for COMPLETAR rows
            $stmtActualizarAsig = $db->prepare("UPDATE asignatura SET creditos = :cred, id_area_academica = :area WHERE id_asignatura = :id");

            $cacheAsignaturas = [];

            foreach ($filas as $f) {
                // Solo procesar filas aprobadas
                if (empty($f['valida_importar']) && !in_array($f['estado'], ['NUEVO', 'LISTO', 'COMPLETAR'], true)) {
                    $filasOmitidas++;
                    continue;
                }

                $codigo      = trim((string)$f['codigo']);
                $idPrograma  = (int)($f['id_programa'] ?? 0);
                $semestreNum = (int)($f['semestre_num'] ?? 0);

                if ($codigo === '' || $idPrograma <= 0 || $semestreNum <= 0) {
                    $filasOmitidas++;
                    continue;
                }

                // 1. Obtener o crear Asignatura
                $idAsignatura = null;
                $nombreNorm = strtolower(trim((string)$f['asignatura']));
                $cacheKey = $codigo . '||' . $nombreNorm;
                if (isset($cacheAsignaturas[$cacheKey])) {
                    $idAsignatura = $cacheAsignaturas[$cacheKey];
                } else {
                    // Buscar todas las asignaturas con ese código y filtrar por nombre exacto
                    $stmtBuscarTodas = $db->prepare(
                        "SELECT id_asignatura, nombre FROM asignatura WHERE codigo = :cod AND id_institucion = :inst"
                    );
                    $stmtBuscarTodas->execute([':cod' => $codigo, ':inst' => $id_institucion]);
                    $todasConCodigo = $stmtBuscarTodas->fetchAll(PDO::FETCH_ASSOC);
                    $idExistente = false;
                    foreach ($todasConCodigo as $candidata) {
                        if (strtolower(trim($candidata['nombre'])) === $nombreNorm) {
                            $idExistente = $candidata['id_asignatura'];
                            break;
                        }
                    }

                    if ($idExistente) {
                        $idAsignatura = (int)$idExistente;
                        // Si la fila está marcada como COMPLETAR, actualizar los datos faltantes
                        if ($f['estado'] === 'COMPLETAR') {
                            $stmtActualizarAsig->execute([
                                ':cred' => max(0, (int)($f['creditos'] ?? 0)),
                                ':area' => !empty($f['id_area_academica']) ? (int)$f['id_area_academica'] : null,
                                ':id'   => $idAsignatura,
                            ]);
                        }
                        $asignaturasExistentesOmitidas++;
                    } else {
                        // Crear Asignatura
                        $stmtCrearAsig->execute([
                            ':inst' => $id_institucion,
                            ':area' => !empty($f['id_area_academica']) ? (int)$f['id_area_academica'] : null,
                            ':cod'  => $codigo,
                            ':nom'  => trim((string)$f['asignatura']),
                            ':cred' => max(0, (int)($f['creditos'] ?? 0)),
                        ]);
                        $idAsignatura = (int)$db->lastInsertId();
                        $asignaturasCreadas++;
                    }
                    $cacheAsignaturas[$cacheKey] = $idAsignatura;
                }

                // 2. Crear o asegurar relación asignatura_programa con su semestre
                $stmtCrearRel->execute([
                    ':asig' => $idAsignatura,
                    ':prog' => $idPrograma,
                    ':sem'  => $semestreNum,
                ]);

                if ($stmtCrearRel->rowCount() > 0) {
                    $asociacionesCreadas++;
                }
            }

            $db->commit();

            return [
                'exito'                => true,
                'asignaturas_creadas'  => $asignaturasCreadas,
                'asociaciones_creadas' => $asociacionesCreadas,
                'existentes_omitidas'  => $asignaturasExistentesOmitidas,
                'filas_omitidas'       => $filasOmitidas,
            ];
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Exporta las asignaturas y sus relaciones en formato Excel (.xlsx)
     * exactamente compatible con la estructura requerida.
     */
    public static function exportarAsignaturas($id_institucion = 1) {
        if (!self::phpspreadsheetDisponible()) {
            throw new Exception("PhpSpreadsheet no está disponible para exportar.");
        }

        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT 
                p.nombre AS programa_nombre,
                a.nombre AS asignatura_nombre,
                a.codigo AS asignatura_codigo,
                ap.semestre,
                a.creditos,
                COALESCE(na.nombre, 'Profesional') AS nivel_nombre,
                COALESCE(aa.nombre, 'General') AS area_nombre
            FROM asignatura_programa ap
            JOIN asignatura a ON a.id_asignatura = ap.id_asignatura
            JOIN programa p ON p.id_programa = ap.id_programa
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            LEFT JOIN area_academica aa ON aa.id_area_academica = a.id_area_academica
            WHERE a.id_institucion = :inst
            ORDER BY p.nombre ASC, ap.semestre ASC, a.nombre ASC
        ");
        $stmt->execute([':inst' => $id_institucion]);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Asignaturas');

        // Encabezados oficiales
        $encabezados = [
            'A1' => 'PROGRAMA',
            'B1' => 'ASIGNATURA',
            'C1' => 'CODIGO ASIGNATURA',
            'D1' => 'SEMESTRE',
            'E1' => 'CREDITOS',
            'F1' => 'NIVEL DE FORMACION',
            'G1' => 'COMPONENTE',
            'H1' => 'AREA',
        ];

        foreach ($encabezados as $celda => $texto) {
            $hoja->setCellValue($celda, $texto);
        }

        // Estilos para encabezados
        $hoja->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B0D8F'], // Color primario UniFET
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $hoja->getRowDimension(1)->setRowHeight(26);

        // Llenar datos
        $fila = 2;
        foreach ($registros as $reg) {
            $semestreRomano = !empty($reg['semestre']) ? self::enteroARomano($reg['semestre']) : '';
            $creditosFormateados = sprintf('%02d', (int)$reg['creditos']);

            $hoja->setCellValue("A{$fila}", $reg['programa_nombre']);
            $hoja->setCellValue("B{$fila}", $reg['asignatura_nombre']);
            $hoja->setCellValue("C{$fila}", $reg['asignatura_codigo']);
            $hoja->setCellValue("D{$fila}", $semestreRomano);
            $hoja->setCellValue("E{$fila}", $creditosFormateados);
            $hoja->setCellValue("F{$fila}", $reg['nivel_nombre']);
            $hoja->setCellValue("G{$fila}", $reg['area_nombre']);
            $hoja->setCellValue("H{$fila}", $reg['area_nombre']);

            // Alineaciones
            $hoja->getStyle("C{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle("D{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle("E{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle("F{$fila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $fila++;
        }

        // Autoajustar ancho de columnas
        foreach (range('A', 'H') as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Genera una plantilla Excel descargable con dos hojas:
     * 1. PLANTILLA_ASIGNATURAS (con columnas oficiales y filas de ejemplo)
     * 2. INSTRUCCIONES (guía clara para evitar errores en semestres, créditos y programas)
     */
    public static function generarPlantillaExcel() {
        if (!self::phpspreadsheetDisponible()) {
            throw new Exception("PhpSpreadsheet no está disponible.");
        }

        $spreadsheet = new Spreadsheet();

        // Hoja 1: Plantilla
        $hoja1 = $spreadsheet->getActiveSheet();
        $hoja1->setTitle('Asignaturas');

        $headers = [
            'A1' => 'PROGRAMA',
            'B1' => 'ASIGNATURA',
            'C1' => 'CODIGO ASIGNATURA',
            'D1' => 'SEMESTRE',
            'E1' => 'CREDITOS',
            'F1' => 'NIVEL DE FORMACION',
            'G1' => 'COMPONENTE',
            'H1' => 'AREA',
        ];

        foreach ($headers as $c => $v) {
            $hoja1->setCellValue($c, $v);
        }

        $hoja1->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B0D8F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $hoja1->getRowDimension(1)->setRowHeight(25);

        // Filas de ejemplo instructivas
        $ejemplos = [
            ['Contaduría Pública', 'Auditoría Tributaria', 'CONTAD953', 'V', '03', 'Profesional', 'Disciplinar Contaduría', 'Disciplinar Contaduría'],
            ['Administración de Agronegocios', 'Fundamentos de Futuros y Opciones Agrícolas', 'AGRON998', 'IX', '02', 'Profesional', 'Disciplinar Agronegocios', 'Disciplinar Agronegocios'],
            ['Ingeniería Industrial', 'Cálculo Multivariado', 'CALC301', 'III', '04', 'Profesional', 'Numérica', 'Numérica'],
        ];

        $f = 2;
        foreach ($ejemplos as $ej) {
            $hoja1->setCellValue("A{$f}", $ej[0]);
            $hoja1->setCellValue("B{$f}", $ej[1]);
            $hoja1->setCellValue("C{$f}", $ej[2]);
            $hoja1->setCellValue("D{$f}", $ej[3]);
            $hoja1->setCellValue("E{$f}", $ej[4]);
            $hoja1->setCellValue("F{$f}", $ej[5]);
            $hoja1->setCellValue("G{$f}", $ej[6]);
            $hoja1->setCellValue("H{$f}", $ej[7]);
            $f++;
        }

        foreach (range('A', 'H') as $col) {
            $hoja1->getColumnDimension($col)->setAutoSize(true);
        }

        // Hoja 2: Instrucciones
        $hoja2 = $spreadsheet->createSheet();
        $hoja2->setTitle('Instrucciones');

        $hoja2->setCellValue('A1', 'GUÍA DE CARGA MASIVA DE ASIGNATURAS - UniFET');
        $hoja2->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('3B0D8F');

        $instrucciones = [
            ['COLUMNA', 'REQUISITO', 'EJEMPLOS Y OBSERVACIONES'],
            ['PROGRAMA', 'Obligatorio', 'Debe coincidir con el nombre de un programa existente en UniFET (ej: «Contaduría Pública», «Ingeniería Industrial»).'],
            ['ASIGNATURA', 'Obligatorio', 'Nombre completo de la asignatura curricular (ej: «Auditoría Tributaria»).'],
            ['CODIGO ASIGNATURA', 'Obligatorio / Clave única', 'Código identificador único. Si ya existe en el sistema, no se duplicará (ej: «CONTAD953»).'],
            ['SEMESTRE', 'Obligatorio', 'Número romano (I, II, III, IV, V, VI, VII, VIII, IX, X) o número arábigo (1 a 20). Indica la ubicación en el programa.'],
            ['CREDITOS', 'Obligatorio', 'Número entero positivo (ej: 02, 03, 04).'],
            ['NIVEL DE FORMACION', 'Opcional / Informativo', 'Nivel del programa (Técnico Laboral, Técnico Profesional, Tecnólogo, Profesional).'],
            ['COMPONENTE / AREA', 'Opcional / Catálogo', 'Nombre del área académica (ej: «Disciplinar Contaduría», «Numérica», «Humanidades»).'],
        ];

        $rowNum = 3;
        foreach ($instrucciones as $idx => $inst) {
            $hoja2->setCellValue("A{$rowNum}", $inst[0]);
            $hoja2->setCellValue("B{$rowNum}", $inst[1]);
            $hoja2->setCellValue("C{$rowNum}", $inst[2]);

            if ($idx === 0) {
                $hoja2->getStyle("A{$rowNum}:C{$rowNum}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                ]);
            }
            $rowNum++;
        }

        $hoja2->getColumnDimension('A')->setWidth(26);
        $hoja2->getColumnDimension('B')->setWidth(26);
        $hoja2->getColumnDimension('C')->setWidth(65);

        // Seleccionar la primera hoja por defecto
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
}
