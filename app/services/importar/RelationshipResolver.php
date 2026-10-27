<?php

namespace App\Services\Importar;

use App\Config\Database;
use PDO;

class RelationshipResolver {
    
    /**
     * Resuelve las relaciones basándose en la configuración de la entidad.
     * En un sistema real, cada entidad tendría una clase de resolución específica
     * o se leerían metadatos de las relaciones (ej. 'sede' => tabla 'sede', campo 'nombre').
     * 
     * @param string $entityKey
     * @param array &$filaEstructurada Fila modificada por referencia
     * @param int $id_institucion ID de la institución
     * @return array Errores encontrados durante la resolución
     */
    public static function resolve(string $entityKey, array &$filaEstructurada, int $id_institucion = 1): array {
        $errores = [];
        $db = Database::getConexion();

        switch ($entityKey) {
            case 'ESPACIO':
                if (!empty($filaEstructurada['sede'])) {
                    $sedeNombre = $filaEstructurada['sede'];
                    $stmt = $db->prepare("SELECT id_sede FROM sede WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
                    $stmt->execute([':nombre' => trim($sedeNombre)]);
                    $sedeId = $stmt->fetchColumn();

                    if ($sedeId) {
                        // Ahora buscar el bloque
                        if (!empty($filaEstructurada['bloque'])) {
                            $bloqueNombre = $filaEstructurada['bloque'];
                            $stmt = $db->prepare("SELECT id_bloque FROM bloque WHERE id_sede = :sede AND LOWER(nombre) = LOWER(:nombre) LIMIT 1");
                            $stmt->execute([':sede' => $sedeId, ':nombre' => trim($bloqueNombre)]);
                            $bloqueId = $stmt->fetchColumn();
                            if ($bloqueId) {
                                $filaEstructurada['id_bloque'] = $bloqueId;
                                unset($filaEstructurada['sede'], $filaEstructurada['bloque']);
                            } else {
                                $errores[] = "Bloque '$bloqueNombre' no encontrado en sede '$sedeNombre'";
                            }
                        } else {
                            $errores[] = "Se requiere el bloque para registrar un espacio.";
                        }
                    } else {
                        $errores[] = "Sede '$sedeNombre' no encontrada";
                    }
                }
                break;
                
            case 'BLOQUE':
                if (!empty($filaEstructurada['sede'])) {
                    $sedeNombre = $filaEstructurada['sede'];
                    $stmt = $db->prepare("SELECT id_sede FROM sede WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
                    $stmt->execute([':nombre' => trim($sedeNombre)]);
                    $sedeId = $stmt->fetchColumn();

                    if ($sedeId) {
                        $filaEstructurada['id_sede'] = $sedeId;
                        unset($filaEstructurada['sede']);
                    } else {
                        $errores[] = "Sede '$sedeNombre' no encontrada";
                    }
                }
                break;

            case 'PROGRAMA':
                if (!empty($filaEstructurada['facultad'])) {
                    $facId = self::resolverFacultad($db, (string)$filaEstructurada['facultad']);
                    if ($facId) {
                        $filaEstructurada['id_facultad'] = $facId;
                    } else {
                        $errores[] = "Facultad '{$filaEstructurada['facultad']}' no encontrada. Debe registrarla en el sistema primero.";
                    }
                }

                if (!empty($filaEstructurada['nivel_academico'])) {
                    $nivelInfo = self::resolverNivelAcademico($db, (string)$filaEstructurada['nivel_academico']);
                    if ($nivelInfo) {
                        $filaEstructurada['id_nivel_academico'] = (int)$nivelInfo['id_nivel_academico'];
                        $filaEstructurada['nivel_academico'] = $nivelInfo['nombre'];
                    } else {
                        $errores[] = "Nivel Académico '{$filaEstructurada['nivel_academico']}' no reconocido en el catálogo del sistema.";
                    }
                }
                break;

            case 'ASIGNATURA':
                // 1. Resolver Programa Académico
                if (!empty($filaEstructurada['programa'])) {
                    $progRaw = trim((string)$filaEstructurada['programa']);
                    $prog = self::resolverPrograma($db, $progRaw, $id_institucion);
                    if ($prog) {
                        $filaEstructurada['id_programa'] = (int)$prog['id_programa'];
                        $filaEstructurada['programa'] = $prog['nombre'];
                        // Si el programa en BD ya tiene nivel y el excel no lo trae, aprovecharlo
                        if (empty($filaEstructurada['nivel_academico']) && !empty($prog['nivel_nombre'])) {
                            $filaEstructurada['nivel_academico'] = $prog['nivel_nombre'];
                            $filaEstructurada['id_nivel_academico'] = (int)$prog['id_nivel_academico'];
                        }
                    } else {
                        $errores[] = "Programa Académico '{$progRaw}' no encontrado en el sistema. Asegúrate de registrar el programa primero.";
                    }
                }

                // 2. Resolver Nivel Académico (ej. Profesional, Tecnólogo, etc.)
                if (!empty($filaEstructurada['nivel_academico'])) {
                    $nivelInfo = self::resolverNivelAcademico($db, (string)$filaEstructurada['nivel_academico']);
                    if ($nivelInfo) {
                        $filaEstructurada['id_nivel_academico'] = (int)$nivelInfo['id_nivel_academico'];
                        $filaEstructurada['nivel_academico'] = $nivelInfo['nombre'];
                    } else {
                        $errores[] = "Nivel de Formación '{$filaEstructurada['nivel_academico']}' no reconocido en el sistema.";
                    }
                }

                // 3. Resolver Semestre (soporta romanos I, II, III o arábigos 1, 2, 3)
                if (isset($filaEstructurada['semestre']) && trim((string)$filaEstructurada['semestre']) !== '') {
                    $semRaw = trim((string)$filaEstructurada['semestre']);
                    $semNum = self::resolverSemestre($semRaw);
                    if ($semNum !== null && $semNum > 0) {
                        $filaEstructurada['semestre'] = $semNum;
                    } else {
                        $errores[] = "Semestre curricular '{$semRaw}' inválido. Debe ser número romano (I a X) o arábigo (1 a 20).";
                    }
                }

                // 4. Resolver Área Académica (a partir de area_academica o alias anterior tipo)
                $areaRaw = trim((string)($filaEstructurada['area_academica'] ?? $filaEstructurada['area'] ?? $filaEstructurada['tipo'] ?? ''));
                if ($areaRaw !== '') {
                    $areaInfo = self::resolverAreaAcademica($db, $areaRaw, $id_institucion);
                    if ($areaInfo) {
                        $filaEstructurada['id_area_academica'] = (int)$areaInfo['id_area_academica'];
                        $filaEstructurada['area_academica'] = $areaInfo['nombre'];
                    } else {
                        // Crear automáticamente el área académica si no existía previamente
                        try {
                            $stmtNewArea = $db->prepare("INSERT INTO area_academica (id_institucion, nombre, estado) VALUES (:inst, :nom, 'ACTIVO')");
                            $stmtNewArea->execute([':inst' => $id_institucion, ':nom' => $areaRaw]);
                            $idNewArea = (int)$db->lastInsertId();
                            $filaEstructurada['id_area_academica'] = $idNewArea;
                            $filaEstructurada['area_academica'] = $areaRaw;
                        } catch (\PDOException $e) {
                            // En caso de concurrencia o duplicado
                            $areaInfo = self::resolverAreaAcademica($db, $areaRaw, $id_institucion);
                            if ($areaInfo) {
                                $filaEstructurada['id_area_academica'] = (int)$areaInfo['id_area_academica'];
                                $filaEstructurada['area_academica'] = $areaInfo['nombre'];
                            }
                        }
                    }
                }
                break;
        }

        return $errores;
    }

    /**
     * Resuelve el ID y nombre oficial del nivel académico soportando:
     * - Coincidencia exacta insensible a mayúsculas
     * - Coincidencia sin tildes/acentos
     * - Sinónimos: 'tecnologo' == 'tecnologico', 'profesional' == 'profesional universitario', etc.
     */
    public static function resolverNivelAcademico(PDO $db, string $texto): ?array {
        $textoOriginal = trim($texto);
        if ($textoOriginal === '') {
            return null;
        }

        $stmt = $db->query("SELECT id_nivel_academico, nombre FROM nivel_academico");
        $nivelesBd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 1. Coincidencia exacta directa
        foreach ($nivelesBd as $nivel) {
            if (mb_strtolower($nivel['nombre'], 'UTF-8') === mb_strtolower($textoOriginal, 'UTF-8')) {
                return $nivel;
            }
        }

        // 2. Normalización de cadenas
        $textoLimpio = self::limpiarTexto($textoOriginal);

        foreach ($nivelesBd as $nivel) {
            if (self::limpiarTexto($nivel['nombre']) === $textoLimpio) {
                return $nivel;
            }
        }

        // 3. Diccionario de equivalencias semánticas
        // Tecnológico / Tecnólogo
        $esTecnologico = in_array($textoLimpio, [
            'tecnologo', 'tecnologa', 'tecnologico', 'tecnologica', 'tecnologia', 'ciclo tecnologico'
        ], true) || str_starts_with($textoLimpio, 'tecnolog');

        // Profesional / Profesional Universitario
        $esProfesional = in_array($textoLimpio, [
            'profesional', 'profesional universitario', 'universitario', 'pregrado', 'carrera profesional', 'licenciatura'
        ], true) || (str_contains($textoLimpio, 'profesional') && !str_contains($textoLimpio, 'tecnico'));

        // Técnico Profesional
        $esTecnicoProfesional = in_array($textoLimpio, [
            'tecnico profesional', 'tecnico prof', 'ciclo tecnico profesional'
        ], true) || $textoLimpio === 'tecnico';

        // Técnico Laboral
        $esTecnicoLaboral = in_array($textoLimpio, [
            'tecnico laboral', 'laboral'
        ], true);

        foreach ($nivelesBd as $nivel) {
            $nombreBdLimpio = self::limpiarTexto($nivel['nombre']);
            
            if ($esTecnologico && str_contains($nombreBdLimpio, 'tecnolog')) {
                return $nivel;
            }
            if ($esProfesional && str_contains($nombreBdLimpio, 'profesional') && !str_contains($nombreBdLimpio, 'tecnico')) {
                return $nivel;
            }
            if ($esTecnicoProfesional && str_contains($nombreBdLimpio, 'tecnico') && str_contains($nombreBdLimpio, 'profesional')) {
                return $nivel;
            }
            if ($esTecnicoLaboral && str_contains($nombreBdLimpio, 'tecnico') && str_contains($nombreBdLimpio, 'laboral')) {
                return $nivel;
            }
        }

        return null;
    }

    /**
     * Resuelve la facultad mediante búsqueda insensible a mayúsculas y acentos.
     */
    public static function resolverFacultad(PDO $db, string $texto, int $id_institucion = 1): ?int {
        $textoOriginal = trim($texto);
        if ($textoOriginal === '') {
            return null;
        }

        // 1. Búsqueda exacta SQL
        $stmt = $db->prepare("SELECT id_facultad FROM facultad WHERE LOWER(nombre) = LOWER(:nombre) LIMIT 1");
        $stmt->execute([':nombre' => $textoOriginal]);
        $facId = $stmt->fetchColumn();
        if ($facId) {
            return (int)$facId;
        }

        // 2. Búsqueda con normalización (sin tildes)
        $stmt = $db->query("SELECT id_facultad, nombre FROM facultad");
        $facultades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $textoLimpio = self::limpiarTexto($textoOriginal);
        foreach ($facultades as $fac) {
            if (self::limpiarTexto($fac['nombre']) === $textoLimpio) {
                return (int)$fac['id_facultad'];
            }
        }

        // 3. Coincidencia parcial si contiene el nombre
        foreach ($facultades as $fac) {
            $facLimpio = self::limpiarTexto($fac['nombre']);
            if (str_contains($textoLimpio, $facLimpio) || str_contains($facLimpio, $textoLimpio)) {
                return (int)$fac['id_facultad'];
            }
        }

        return null;
    }

    /**
     * Resuelve el programa académico por código o nombre exacto/flexible.
     */
    public static function resolverPrograma(PDO $db, string $texto, int $id_institucion = 1): ?array {
        $textoOriginal = trim($texto);
        if ($textoOriginal === '') {
            return null;
        }

        // 1. Coincidencia directa por código o nombre
        $stmt = $db->prepare("
            SELECT p.id_programa, p.codigo, p.nombre, p.id_nivel_academico, na.nombre AS nivel_nombre
            FROM programa p
            JOIN facultad f ON f.id_facultad = p.id_facultad
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE f.id_institucion = :inst
              AND (LOWER(TRIM(p.codigo)) = LOWER(TRIM(:txt)) OR LOWER(TRIM(p.nombre)) = LOWER(TRIM(:txt)))
            LIMIT 1
        ");
        $stmt->execute([':inst' => $id_institucion, ':txt' => $textoOriginal]);
        $prog = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prog) {
            return $prog;
        }

        // 2. Coincidencia flexible (sin tildes ni caracteres especiales)
        $stmtAll = $db->prepare("
            SELECT p.id_programa, p.codigo, p.nombre, p.id_nivel_academico, na.nombre AS nivel_nombre
            FROM programa p
            JOIN facultad f ON f.id_facultad = p.id_facultad
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE f.id_institucion = :inst
        ");
        $stmtAll->execute([':inst' => $id_institucion]);
        $programas = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

        $limpioBuscado = self::limpiarTexto($textoOriginal);
        foreach ($programas as $p) {
            if (self::limpiarTexto($p['nombre']) === $limpioBuscado || self::limpiarTexto($p['codigo']) === $limpioBuscado) {
                return $p;
            }
        }

        // 3. Coincidencia por contención de subcadena
        if (mb_strlen($limpioBuscado) >= 5) {
            foreach ($programas as $p) {
                $limpioNom = self::limpiarTexto($p['nombre']);
                if (str_contains($limpioNom, $limpioBuscado) || str_contains($limpioBuscado, $limpioNom)) {
                    return $p;
                }
            }
        }

        return null;
    }

    /**
     * Convierte número romano (I, II, ..., X) o arábigo a entero.
     */
    public static function resolverSemestre(string $texto): ?int {
        $str = strtoupper(trim($texto));
        if ($str === '') return null;
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
     * Resuelve el área académica buscando por nombre exacto o normalizado.
     */
    public static function resolverAreaAcademica(PDO $db, string $texto, int $id_institucion = 1): ?array {
        $textoOriginal = trim($texto);
        if ($textoOriginal === '') {
            return null;
        }

        $stmt = $db->prepare("SELECT id_area_academica, nombre FROM area_academica WHERE id_institucion = :inst");
        $stmt->execute([':inst' => $id_institucion]);
        $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 1. Coincidencia exacta insensible a mayúsculas
        foreach ($areas as $a) {
            if (mb_strtolower($a['nombre'], 'UTF-8') === mb_strtolower($textoOriginal, 'UTF-8')) {
                return $a;
            }
        }

        // 2. Coincidencia normalizada sin tildes ni caracteres especiales
        $limpio = self::limpiarTexto($textoOriginal);
        foreach ($areas as $a) {
            if (self::limpiarTexto($a['nombre']) === $limpio) {
                return $a;
            }
        }

        return null;
    }

    /**
     * Utilidad para limpiar y normalizar texto (minúsculas, sin acentos ni caracteres especiales).
     */
    private static function limpiarTexto(string $str): string {
        $str = mb_strtolower(trim($str), 'UTF-8');
        $str = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $str
        );
        $str = preg_replace('/[^a-z0-9 ]/u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }
}
