<?php

namespace App\Models;

use App\Config\Database;

class Restriccion {
    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT r.*, pa.codigo AS periodo_codigo,
                   CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre,
                   e.nombre AS espacio_nombre
            FROM restriccion r
            LEFT JOIN periodo_academico pa ON pa.id_periodo = r.id_periodo
            LEFT JOIN docente d ON d.id_docente = r.id_docente
            LEFT JOIN espacio e ON e.id_espacio = r.id_espacio
            WHERE r.id_institucion = :inst
            ORDER BY r.estado, r.tipo, r.dia_semana
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM restriccion WHERE id_restriccion = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function guardar($datos, $id = null) {
        $db = Database::getConexion();
        $params = [
            ':inst' => $datos['id_institucion'],
            ':periodo' => $datos['id_periodo'] ?: null,
            ':tipo' => $datos['tipo'],
            ':docente' => $datos['id_docente'] ?: null,
            ':espacio' => $datos['id_espacio'] ?: null,
            ':dia' => $datos['dia_semana'] ?: null,
            ':inicio' => $datos['hora_inicio'] ?: null,
            ':fin' => $datos['hora_fin'] ?: null,
            ':descripcion' => $datos['descripcion'],
            ':estado' => $datos['estado'],
        ];
        if ($id) {
            $params[':id'] = $id;
            $stmt = $db->prepare("
                UPDATE restriccion
                SET id_periodo = :periodo, tipo = :tipo, id_docente = :docente, id_espacio = :espacio,
                    dia_semana = :dia, hora_inicio = :inicio, hora_fin = :fin, descripcion = :descripcion, estado = :estado
                WHERE id_restriccion = :id
            ");
        } else {
            $stmt = $db->prepare("
                INSERT INTO restriccion (id_institucion, id_periodo, tipo, id_docente, id_espacio, dia_semana, hora_inicio, hora_fin, descripcion, estado)
                VALUES (:inst, :periodo, :tipo, :docente, :espacio, :dia, :inicio, :fin, :descripcion, :estado)
            ");
        }
        return $stmt->execute($params);
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE restriccion SET estado = IF(estado = 'ACTIVA', 'INACTIVA', 'ACTIVA') WHERE id_restriccion = :id");
        return $stmt->execute([':id' => $id]);
    }
}
