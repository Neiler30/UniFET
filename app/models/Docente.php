<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Docente {
    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*,
                   GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas,
                   GROUP_CONCAT(DISTINCT a.nombre ORDER BY a.nombre SEPARATOR ', ') AS asignaturas
            FROM docente d
            LEFT JOIN docente_programa dp ON dp.id_docente = d.id_docente
            LEFT JOIN programa p ON p.id_programa = dp.id_programa
            LEFT JOIN docente_asignatura da ON da.id_docente = d.id_docente
            LEFT JOIN asignatura a ON a.id_asignatura = da.id_asignatura
            WHERE d.id_institucion = :inst
            GROUP BY d.id_docente
            ORDER BY d.apellido, d.nombre
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM docente WHERE id_docente = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function relaciones($id) {
        $db = Database::getConexion();
        $programas = $db->prepare("SELECT id_programa FROM docente_programa WHERE id_docente = :id");
        $programas->execute([':id' => $id]);
        $asignaturas = $db->prepare("SELECT id_asignatura FROM docente_asignatura WHERE id_docente = :id");
        $asignaturas->execute([':id' => $id]);
        return [
            'programas' => array_map('intval', $programas->fetchAll(PDO::FETCH_COLUMN)),
            'asignaturas' => array_map('intval', $asignaturas->fetchAll(PDO::FETCH_COLUMN)),
        ];
    }

    public static function guardar($datos, $programas = [], $asignaturas = [], $id = null) {
        $db = Database::getConexion();
        $db->beginTransaction();
        try {
            if ($id) {
                $stmt = $db->prepare("
                    UPDATE docente
                    SET codigo = :codigo, nombre = :nombre, apellido = :apellido, correo = :correo, estado = :estado
                    WHERE id_docente = :id
                ");
                $stmt->execute([
                    ':codigo' => $datos['codigo'],
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':correo' => $datos['correo'],
                    ':estado' => $datos['estado'],
                    ':id' => $id,
                ]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO docente (id_institucion, codigo, nombre, apellido, correo, estado)
                    VALUES (:inst, :codigo, :nombre, :apellido, :correo, :estado)
                ");
                $stmt->execute([
                    ':inst' => $datos['id_institucion'],
                    ':codigo' => $datos['codigo'],
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':correo' => $datos['correo'],
                    ':estado' => $datos['estado'],
                ]);
                $id = (int)$db->lastInsertId();
            }
            self::sincronizar($db, $id, 'docente_programa', 'id_programa', $programas);
            self::sincronizar($db, $id, 'docente_asignatura', 'id_asignatura', $asignaturas);
            $db->commit();
            return $id;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function sincronizar(PDO $db, $id_docente, $tabla, $columna, $valores) {
        $db->prepare("DELETE FROM {$tabla} WHERE id_docente = :id")->execute([':id' => $id_docente]);
        $stmt = $db->prepare("INSERT IGNORE INTO {$tabla} (id_docente, {$columna}) VALUES (:docente, :valor)");
        foreach (array_filter(array_map('intval', (array)$valores)) as $valor) {
            $stmt->execute([':docente' => $id_docente, ':valor' => $valor]);
        }
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE docente SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_docente = :id");
        return $stmt->execute([':id' => $id]);
    }
}
