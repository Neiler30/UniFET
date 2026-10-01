<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class OfertaAcademica {

    /**
     * Obtiene la oferta académica de un periodo, con el semestre curricular
     * obtenido directamente desde asignatura_programa.
     */
    public static function obtenerPorPeriodo($id_periodo, $id_programa = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT 
                o.*, 
                p.nombre as programa_nombre, 
                a.codigo as asignatura_codigo, 
                a.nombre as asignatura_nombre, 
                a.creditos,
                ap.semestre as semestre
            FROM oferta_academica o
            JOIN programa p ON o.id_programa = p.id_programa
            JOIN asignatura a ON o.id_asignatura = a.id_asignatura
            LEFT JOIN asignatura_programa ap ON ap.id_asignatura = o.id_asignatura AND ap.id_programa = o.id_programa
            WHERE o.id_periodo = :id_periodo
        ";
        if ($id_programa) {
            $sql .= " AND o.id_programa = :id_prog";
        }
        $sql .= " ORDER BY p.nombre ASC, ap.semestre ASC, a.nombre ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_periodo', $id_periodo, PDO::PARAM_INT);
        if ($id_programa) {
            $stmt->bindParam(':id_prog', $id_programa, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerIdsOfertados($id_periodo, $id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT id_asignatura 
            FROM oferta_academica 
            WHERE id_periodo = :per AND id_programa = :prog AND estado = 'ACTIVA'
        ");
        $stmt->execute([':per' => (int)$id_periodo, ':prog' => (int)$id_programa]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function existeOferta($id_periodo, $id_programa, $id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM oferta_academica WHERE id_periodo = :per AND id_programa = :prog AND id_asignatura = :asig");
        $stmt->execute([':per' => (int)$id_periodo, ':prog' => (int)$id_programa, ':asig' => (int)$id_asignatura]);
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
            return (int)$db->lastInsertId();
        }
        return false;
    }

    /**
     * Guarda múltiples asignaturas a la oferta académica para el periodo y programa.
     */
    public static function crearMultiples($id_periodo, $id_programa, array $id_asignaturas) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            INSERT INTO oferta_academica (id_periodo, id_programa, id_asignatura, estado)
            VALUES (:id_periodo, :id_programa, :id_asignatura, 'ACTIVA')
            ON DUPLICATE KEY UPDATE estado = 'ACTIVA'
        ");
        $insertados = 0;
        foreach ($id_asignaturas as $idAsig) {
            $idAsig = (int)$idAsig;
            if ($idAsig <= 0) continue;
            $stmt->execute([
                ':id_periodo'    => (int)$id_periodo,
                ':id_programa'   => (int)$id_programa,
                ':id_asignatura' => $idAsig,
            ]);
            $insertados++;
        }
        return $insertados;
    }

    public static function desactivar($id_oferta) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE oferta_academica SET estado = 'INACTIVA' WHERE id_oferta = :id");
        $stmt->bindParam(':id', $id_oferta, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
