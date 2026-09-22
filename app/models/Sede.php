<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Sede {
    
    public static function obtenerTodas($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM sede WHERE id_institucion = :id_institucion ORDER BY nombre ASC");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_sede, $id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM sede WHERE id_sede = :id_sede AND id_institucion = :id_institucion");
        $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function existeCodigo($codigo, $id_institucion, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM sede WHERE codigo = :codigo AND id_institucion = :id_institucion";
        if ($id_excluir) {
            $sql .= " AND id_sede != :id_excluir";
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
        $stmt = $db->prepare("INSERT INTO sede (id_institucion, codigo, nombre, direccion, estado) VALUES (:id_institucion, :codigo, :nombre, :direccion, :estado)");
        $stmt->bindParam(':id_institucion', $datos['id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function actualizar($id_sede, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE sede SET codigo = :codigo, nombre = :nombre, direccion = :direccion, estado = :estado WHERE id_sede = :id_sede AND id_institucion = :id_institucion");
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $datos['id_institucion'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function desactivar($id_sede, $id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE sede SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_sede = :id_sede AND id_institucion = :id_institucion");
        $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
