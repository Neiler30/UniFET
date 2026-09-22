<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class OfertaAcademica {

    public static function obtenerPorPeriodo($id_periodo, $id_programa = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT o.*, p.nombre as programa_nombre, a.codigo as asignatura_codigo, a.nombre as asignatura_nombre, a.creditos
            FROM oferta_academica o
            JOIN programa p ON o.id_programa = p.id_programa
            JOIN asignatura a ON o.id_asignatura = a.id_asignatura
            WHERE o.id_periodo = :id_periodo
        ";
        if ($id_programa) {
            $sql .= " AND o.id_programa = :id_prog";
        }
        $sql .= " ORDER BY p.nombre ASC, a.nombre ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_periodo', $id_periodo, PDO::PARAM_INT);
        if ($id_programa) {
            $stmt->bindParam(':id_prog', $id_programa, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function existeOferta($id_periodo, $id_programa, $id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM oferta_academica WHERE id_periodo = :per AND id_programa = :prog AND id_asignatura = :asig");
        $stmt->execute([':per' => $id_periodo, ':prog' => $id_programa, ':asig' => $id_asignatura]);
        return $stmt->fetchColumn() > 0;
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("INSERT INTO oferta_academica (id_periodo, id_programa, id_asignatura, estado) VALUES (:id_periodo, :id_programa, :id_asignatura, :estado)");
        $stmt->bindParam(':id_periodo', $datos['id_periodo'], PDO::PARAM_INT);
        $stmt->bindParam(':id_programa', $datos['id_programa'], PDO::PARAM_INT);
        $stmt->bindParam(':id_asignatura', $datos['id_asignatura'], PDO::PARAM_INT);
        $estado = $datos['estado'] ?? 'ACTIVA';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function desactivar($id_oferta) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE oferta_academica SET estado = 'INACTIVA' WHERE id_oferta = :id");
        $stmt->bindParam(':id', $id_oferta, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

