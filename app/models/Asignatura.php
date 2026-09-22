<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Asignatura {
    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT a.*, aa.nombre AS area_nombre, GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas
            FROM asignatura a
            LEFT JOIN area_academica aa ON aa.id_area_academica = a.id_area_academica
            LEFT JOIN asignatura_programa ap ON ap.id_asignatura = a.id_asignatura
            LEFT JOIN programa p ON p.id_programa = ap.id_programa
            WHERE a.id_institucion = :inst
            GROUP BY a.id_asignatura
            ORDER BY a.nombre
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM asignatura WHERE id_asignatura = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function programas($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT id_programa FROM asignatura_programa WHERE id_asignatura = :id");
        $stmt->execute([':id' => $id]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function guardar($datos, $programas = [], $id = null) {
        $db = Database::getConexion();
        $db->beginTransaction();
        try {
            if ($id) {
                $stmt = $db->prepare("
                    UPDATE asignatura SET id_area_academica = :area, codigo = :codigo, nombre = :nombre, creditos = :creditos, tipo = :tipo, estado = :estado
                    WHERE id_asignatura = :id
                ");
                $stmt->execute([
                    ':area' => $datos['id_area_academica'] ?: null,
                    ':codigo' => $datos['codigo'], ':nombre' => $datos['nombre'], ':creditos' => $datos['creditos'],
                    ':tipo' => $datos['tipo'], ':estado' => $datos['estado'], ':id' => $id,
                ]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO asignatura (id_institucion, id_area_academica, codigo, nombre, creditos, tipo, estado)
                    VALUES (:inst, :area, :codigo, :nombre, :creditos, :tipo, :estado)
                ");
                $stmt->execute([
                    ':inst' => $datos['id_institucion'], ':area' => $datos['id_area_academica'] ?: null, ':codigo' => $datos['codigo'], ':nombre' => $datos['nombre'],
                    ':creditos' => $datos['creditos'], ':tipo' => $datos['tipo'], ':estado' => $datos['estado'],
                ]);
                $id = (int)$db->lastInsertId();
            }
            $db->prepare("DELETE FROM asignatura_programa WHERE id_asignatura = :id")->execute([':id' => $id]);
            $rel = $db->prepare("INSERT IGNORE INTO asignatura_programa (id_asignatura, id_programa) VALUES (:asig, :prog)");
            foreach (array_filter(array_map('intval', (array)$programas)) as $programa) {
                $rel->execute([':asig' => $id, ':prog' => $programa]);
            }
            $db->commit();
            return $id;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE asignatura SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_asignatura = :id");
        return $stmt->execute([':id' => $id]);
    }
}
