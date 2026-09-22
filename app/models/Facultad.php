<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Facultad {
    
    public static function obtenerTodas($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM facultad WHERE id_institucion = :id_institucion ORDER BY nombre ASC");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_facultad, $id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM facultad WHERE id_facultad = :id_facultad AND id_institucion = :id_institucion");
        $stmt->bindParam(':id_facultad', $id_facultad, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function existeCodigo($codigo, $id_institucion, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM facultad WHERE codigo = :codigo AND id_institucion = :id_institucion";
        if ($id_excluir) {
            $sql .= " AND id_facultad != :id_excluir";
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
        $stmt = $db->prepare("INSERT INTO facultad (id_institucion, codigo, nombre, estado) VALUES (:id_institucion, :codigo, :nombre, :estado)");
        $stmt->bindParam(':id_institucion', $datos['id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function actualizar($id_facultad, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE facultad SET codigo = :codigo, nombre = :nombre, estado = :estado WHERE id_facultad = :id_facultad AND id_institucion = :id_institucion");
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_facultad', $id_facultad, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $datos['id_institucion'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function desactivar($id_facultad, $id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE facultad SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_facultad = :id_facultad AND id_institucion = :id_institucion");
        $stmt->bindParam(':id_facultad', $id_facultad, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
