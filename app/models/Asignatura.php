<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use InvalidArgumentException;

class Asignatura {

    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT a.*, 
                   aa.nombre AS area_nombre, 
                   GROUP_CONCAT(
                       CONCAT(p.nombre, IF(ap.semestre IS NOT NULL, CONCAT(' · Sem ', ap.semestre), '')) 
                       ORDER BY ap.semestre ASC, p.nombre ASC 
                       SEPARATOR ' || '
                   ) AS programas_info,
                   GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas
            FROM asignatura a
            LEFT JOIN area_academica aa ON aa.id_area_academica = a.id_area_academica
            LEFT JOIN asignatura_programa ap ON ap.id_asignatura = a.id_asignatura
            LEFT JOIN programa p ON p.id_programa = ap.id_programa
            WHERE a.id_institucion = :inst
            GROUP BY a.id_asignatura
            ORDER BY a.nombre
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM asignatura WHERE id_asignatura = :id");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve solo los IDs de programa asociados (retrocompatibilidad).
     */
    public static function programas($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT id_programa FROM asignatura_programa WHERE id_asignatura = :id");
        $stmt->execute([':id' => (int)$id]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Devuelve la lista detallada de programas asociados con su semestre respectivo.
     * Consulta requerida:
     * SELECT ap.id_asignatura_programa, ap.id_asignatura, ap.id_programa, ap.semestre, p.codigo, p.nombre ...
     */
    public static function programasConSemestre($id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT 
                ap.id_asignatura_programa,
                ap.id_asignatura,
                ap.id_programa,
                ap.semestre,
                p.codigo AS programa_codigo,
                p.nombre AS programa_nombre,
                p.numero_semestres
            FROM asignatura_programa ap
            INNER JOIN programa p ON p.id_programa = ap.id_programa
            WHERE ap.id_asignatura = :id
            ORDER BY ap.semestre ASC, p.nombre ASC
        ");
        $stmt->execute([':id' => (int)$id_asignatura]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta de asignaturas de un programa ordenadas por semestre curricular.
     */
    public static function obtenerPorPrograma($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT 
                a.id_asignatura,
                a.codigo,
                a.nombre,
                a.creditos,
                a.tipo,
                a.estado,
                aa.nombre AS area_nombre,
                ap.semestre
            FROM asignatura_programa ap
            INNER JOIN asignatura a ON a.id_asignatura = ap.id_asignatura
            LEFT JOIN area_academica aa ON aa.id_area_academica = a.id_area_academica
            WHERE ap.id_programa = :programa AND a.estado = 'ACTIVO'
            ORDER BY ap.semestre ASC, a.nombre ASC
        ");
        $stmt->execute([':programa' => (int)$id_programa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea o actualiza una asignatura y sus relaciones en asignatura_programa dentro de una transacción.
     *
     * @param array $datos      Datos de la asignatura (id_institucion, codigo, nombre, creditos, etc.)
     * @param array $programas  Lista de asociaciones: [['id_programa' => X, 'semestre' => Y], ...]
     * @param int|null $id      ID de la asignatura si es edición
     * @return int              ID de la asignatura guardada
     * @throws \Throwable
     */
    public static function guardar($datos, $programas = [], $id = null) {
        $db = Database::getConexion();
        $db->beginTransaction();
        try {
            if ($id) {
                $stmt = $db->prepare("
                    UPDATE asignatura 
                    SET id_area_academica = :area, 
                        codigo = :codigo, 
                        nombre = :nombre, 
                        creditos = :creditos, 
                        tipo = :tipo, 
                        estado = :estado
                    WHERE id_asignatura = :id
                ");
                $stmt->execute([
                    ':area'     => $datos['id_area_academica'] ?: null,
                    ':codigo'   => $datos['codigo'], 
                    ':nombre'   => $datos['nombre'], 
                    ':creditos' => $datos['creditos'],
                    ':tipo'     => $datos['tipo'], 
                    ':estado'   => $datos['estado'], 
                    ':id'       => (int)$id,
                ]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO asignatura (id_institucion, id_area_academica, codigo, nombre, creditos, tipo, estado)
                    VALUES (:inst, :area, :codigo, :nombre, :creditos, :tipo, :estado)
                ");
                $stmt->execute([
                    ':inst'     => $datos['id_institucion'], 
                    ':area'     => $datos['id_area_academica'] ?: null, 
                    ':codigo'   => $datos['codigo'], 
                    ':nombre'   => $datos['nombre'],
                    ':creditos' => $datos['creditos'], 
                    ':tipo'     => $datos['tipo'], 
                    ':estado'   => $datos['estado'],
                ]);
                $id = (int)$db->lastInsertId();
            }

            // Procesar las asociaciones programa + semestre
            $db->prepare("DELETE FROM asignatura_programa WHERE id_asignatura = :id")->execute([':id' => $id]);

            $relStmt = $db->prepare("
                INSERT INTO asignatura_programa (id_asignatura, id_programa, semestre) 
                VALUES (:asig, :prog, :sem)
            ");

            $programasProcesados = [];

            foreach ($programas as $p) {
                // Soporte tanto para formato ['id_programa' => X, 'semestre' => Y] como para enteros simples
                if (is_array($p)) {
                    $idPrograma = (int)($p['id_programa'] ?? 0);
                    $semestre = isset($p['semestre']) && $p['semestre'] !== '' ? (int)$p['semestre'] : null;
                } else {
                    $idPrograma = (int)$p;
                    $semestre = null;
                }

                if ($idPrograma <= 0) {
                    continue;
                }

                // Validación contra duplicados en el mismo envío
                if (isset($programasProcesados[$idPrograma])) {
                    continue; // Ya procesado, no duplicar
                }

                // El semestre es obligatorio y debe ser entero positivo si se define
                if ($semestre !== null && $semestre <= 0) {
                    throw new InvalidArgumentException("El semestre debe ser un número entero positivo para cada programa asociado.");
                }

                $relStmt->execute([
                    ':asig' => $id, 
                    ':prog' => $idPrograma,
                    ':sem'  => $semestre
                ]);

                $programasProcesados[$idPrograma] = true;
            }

            $db->commit();
            return $id;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    } 

    public static function existeCodigo($codigo, $id_institucion, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM asignatura WHERE codigo = :codigo AND id_institucion = :inst";
        if ($id_excluir) {
            $sql .= " AND id_asignatura != :id_excluir";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':inst', $id_institucion, PDO::PARAM_INT);
        if ($id_excluir) {
            $stmt->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE asignatura SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_asignatura = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
