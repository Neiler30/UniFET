<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Programa {
    
    public static function obtenerTodos($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, f.nombre as facultad_nombre 
            FROM programa p
            JOIN facultad f ON p.id_facultad = f.id_facultad
            WHERE f.id_institucion = :id_institucion 
            ORDER BY f.nombre ASC, p.nombre ASC
        ");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM programa WHERE id_programa = :id_programa");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function existeCodigo($codigo, $id_facultad, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM programa WHERE codigo = :codigo AND id_facultad = :id_facultad";
        if ($id_excluir) {
            $sql .= " AND id_programa != :id_excluir";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':id_facultad', $id_facultad, PDO::PARAM_INT);
        if ($id_excluir) {
            $stmt->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("INSERT INTO programa (id_facultad, codigo, nombre, estado) VALUES (:id_facultad, :codigo, :nombre, :estado)");
        $stmt->bindParam(':id_facultad', $datos['id_facultad'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function actualizar($id_programa, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE programa SET id_facultad = :id_facultad, codigo = :codigo, nombre = :nombre, estado = :estado WHERE id_programa = :id_programa");
        $stmt->bindParam(':id_facultad', $datos['id_facultad'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function desactivar($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE programa SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_programa = :id_programa");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
