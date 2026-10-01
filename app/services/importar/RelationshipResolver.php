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
     * @return array Errores encontrados durante la resolución
     */
    public static function resolve(string $entityKey, array &$filaEstructurada): array {
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
                        // Normalizamos el texto visible en la vista previa al nombre oficial
                        $filaEstructurada['nivel_academico'] = $nivelInfo['nombre'];
                    } else {
                        $errores[] = "Nivel Académico '{$filaEstructurada['nivel_academico']}' no reconocido en el catálogo del sistema.";
                    }
                }
                break;

            case 'ASIGNATURA':
                // Soporte futuro si se vinculan áreas u ofertas
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
