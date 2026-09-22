<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class PeriodoAcademico {
    public static function getActivo() {
        $db = Database::getConexion();
        $stmt = $db->query("SELECT * FROM periodo_academico WHERE estado = 'ACTIVO' LIMIT 1");
        return $stmt->fetch();
    }

    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM periodo_academico WHERE id_institucion = :id_inst ORDER BY fecha_inicio DESC, codigo DESC");
        $stmt->bindParam(':id_inst', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_periodo) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM periodo_academico WHERE id_periodo = :id_periodo");
        $stmt->bindParam(':id_periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function existeCodigo($codigo, $id_institucion, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM periodo_academico WHERE codigo = :codigo AND id_institucion = :id_institucion";
        if ($id_excluir) {
            $sql .= " AND id_periodo != :id_excluir";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        if ($id_excluir) {
            $stmt->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        if (($datos['estado'] ?? '') === 'ACTIVO') {
            $db->exec("UPDATE periodo_academico SET estado = 'PLANIFICACION' WHERE estado = 'ACTIVO'");
        }
        $stmt = $db->prepare("INSERT INTO periodo_academico (id_institucion, id_periodo_base, codigo, nombre, fecha_inicio, fecha_fin, estado) VALUES (:id_inst, :id_periodo_base, :codigo, :nombre, :fecha_inicio, :fecha_fin, :estado)");
        $id_inst = $datos['id_institucion'] ?? 1;
        $id_periodo_base = !empty($datos['id_periodo_base']) ? (int)$datos['id_periodo_base'] : null;
        $stmt->bindParam(':id_inst', $id_inst, PDO::PARAM_INT);
        $stmt->bindParam(':id_periodo_base', $id_periodo_base, PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $datos['fecha_fin'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'PLANIFICACION';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $nuevo = (int)$db->lastInsertId();
            if ($id_periodo_base) {
                self::copiarDatosBase($id_periodo_base, $nuevo);
            }
            return $nuevo;
        }
        return false;
    }

    public static function copiarDatosBase($id_base, $id_nuevo) {
        $db = Database::getConexion();
        $db->prepare("
            INSERT IGNORE INTO oferta_academica (id_periodo, id_programa, id_asignatura, estado)
            SELECT :nuevo, id_programa, id_asignatura, estado
            FROM oferta_academica
            WHERE id_periodo = :base
        ")->execute([':nuevo' => $id_nuevo, ':base' => $id_base]);
        $db->prepare("
            INSERT IGNORE INTO asignacion_docente (id_periodo, id_docente, id_asignatura, id_programa, estado)
            SELECT :nuevo, id_docente, id_asignatura, id_programa, estado
            FROM asignacion_docente
            WHERE id_periodo = :base
        ")->execute([':nuevo' => $id_nuevo, ':base' => $id_base]);
    }

    public static function actualizar($id_periodo, $datos) {
        $db = Database::getConexion();
        if (($datos['estado'] ?? '') === 'ACTIVO') {
            $db->prepare("UPDATE periodo_academico SET estado = 'PLANIFICACION' WHERE estado = 'ACTIVO' AND id_periodo != :id")->execute([':id' => $id_periodo]);
        }
        $stmt = $db->prepare("UPDATE periodo_academico SET codigo = :codigo, nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado WHERE id_periodo = :id_periodo");
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $datos['fecha_fin'], PDO::PARAM_STR);
        $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
        $stmt->bindParam(':id_periodo', $id_periodo, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
