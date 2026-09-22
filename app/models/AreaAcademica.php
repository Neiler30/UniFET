<?php

namespace App\Models;

use App\Config\Database;

class AreaAcademica {
    public static function obtenerTodas($id_institucion = 1, $soloActivas = false) {
        $db = Database::getConexion();
        $sql = "SELECT * FROM area_academica WHERE id_institucion = :inst";
        if ($soloActivas) {
            $sql .= " AND estado = 'ACTIVO'";
        }
        $sql .= " ORDER BY estado ASC, nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM area_academica WHERE id_area_academica = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function guardar($datos, $id = null) {
        $db = Database::getConexion();
        if ($id) {
            $stmt = $db->prepare("
                UPDATE area_academica
                SET nombre = :nombre, estado = :estado
                WHERE id_area_academica = :id AND id_institucion = :inst
            ");
            return $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':estado' => $datos['estado'],
                ':id' => $id,
                ':inst' => $datos['id_institucion'],
            ]);
        }
        $stmt = $db->prepare("
            INSERT INTO area_academica (id_institucion, nombre, estado)
            VALUES (:inst, :nombre, :estado)
        ");
        return $stmt->execute([
            ':inst' => $datos['id_institucion'],
            ':nombre' => $datos['nombre'],
            ':estado' => $datos['estado'],
        ]);
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE area_academica SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_area_academica = :id");
        return $stmt->execute([':id' => $id]);
    }
}
