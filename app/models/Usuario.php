<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Usuario {
    public static function buscarPorCorreo($correo) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE correo = :correo AND estado = 'ACTIVO'");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarParaLogin($correo) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT u.*, GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas
            FROM usuario u
            LEFT JOIN programa_lider pl ON pl.id_usuario = u.id_usuario
            LEFT JOIN programa p ON p.id_programa = pl.id_programa
            WHERE u.id_institucion = :inst
            GROUP BY u.id_usuario
            ORDER BY u.estado, u.apellido, u.nombre
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function programas($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT id_programa FROM programa_lider WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function guardar($datos, $programas = [], $id = null) {
        $db = Database::getConexion();
        $db->beginTransaction();
        try {
            if ($id) {
                $sql = "UPDATE usuario SET nombre = :nombre, apellido = :apellido, correo = :correo, rol = :rol, estado = :estado";
                $params = [
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':correo' => $datos['correo'],
                    ':rol' => $datos['rol'],
                    ':estado' => $datos['estado'],
                    ':id' => $id,
                ];
                if (!empty($datos['password'])) {
                    $sql .= ", password_hash = :password_hash";
                    $params[':password_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id_usuario = :id";
                $db->prepare($sql)->execute($params);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO usuario (id_institucion, nombre, apellido, correo, password_hash, password_temporal, rol, estado)
                    VALUES (:inst, :nombre, :apellido, :correo, :password_hash, :password_temporal, :rol, :estado)
                ");
                $stmt->execute([
                    ':inst' => $datos['id_institucion'],
                    ':nombre' => $datos['nombre'],
                    ':apellido' => $datos['apellido'],
                    ':correo' => $datos['correo'],
                    ':password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
                    ':password_temporal' => !empty($datos['password_temporal']) ? 1 : 0,
                    ':rol' => $datos['rol'],
                    ':estado' => $datos['estado'],
                ]);
                $id = (int)$db->lastInsertId();
            }

            $db->prepare("DELETE FROM programa_lider WHERE id_usuario = :id")->execute([':id' => $id]);
            if (($datos['rol'] ?? '') === 'LIDER_PROGRAMA') {
                $rel = $db->prepare("INSERT IGNORE INTO programa_lider (id_programa, id_usuario) VALUES (:programa, :usuario)");
                foreach (array_filter(array_map('intval', (array)$programas)) as $programa) {
                    $rel->execute([':programa' => $programa, ':usuario' => $id]);
                }
            }
            $db->commit();
            return $id;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function generarPasswordTemporal($longitud = 10) {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $password = '';
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
        }
        return $password;
    }

    public static function cambiarPassword($id_usuario, $password, $temporal = false) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE usuario
            SET password_hash = :hash, password_temporal = :temporal
            WHERE id_usuario = :id
        ");
        return $stmt->execute([
            ':hash' => password_hash($password, PASSWORD_DEFAULT),
            ':temporal' => $temporal ? 1 : 0,
            ':id' => $id_usuario
        ]);
    }

    public static function pendientes($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT u.*, GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas
            FROM usuario u
            LEFT JOIN programa_lider pl ON pl.id_usuario = u.id_usuario
            LEFT JOIN programa p ON p.id_programa = pl.id_programa
            WHERE u.id_institucion = :inst AND u.estado = 'PENDIENTE'
            GROUP BY u.id_usuario
            ORDER BY u.fecha_creacion DESC, u.apellido, u.nombre
        ");
        $stmt->execute([':inst' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function cambiarEstadoDirecto($id, $estado) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE usuario SET estado = :estado WHERE id_usuario = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE usuario SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }
}
