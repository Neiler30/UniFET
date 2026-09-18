<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Bloque {
    
    public static function obtenerTodosPorSede($id_sede) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM bloque WHERE id_sede = :id_sede ORDER BY nombre ASC");
        $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerTodos($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT b.*, s.nombre as sede_nombre 
            FROM bloque b
            JOIN sede s ON b.id_sede = s.id_sede
            WHERE s.id_institucion = :id_institucion 
            ORDER BY s.nombre ASC, b.nombre ASC
        ");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_bloque) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM bloque WHERE id_bloque = :id_bloque");
        $stmt->bindParam(':id_bloque', $id_bloque, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("INSERT INTO bloque (id_sede, codigo, nombre, estado) VALUES (:id_sede, :codigo, :nombre, :estado)");
        $stmt->bindParam(':id_sede', $datos['id_sede'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function actualizar($id_bloque, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE bloque SET id_sede = :id_sede, codigo = :codigo, nombre = :nombre, estado = :estado WHERE id_bloque = :id_bloque");
        $stmt->bindParam(':id_sede', $datos['id_sede'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_bloque', $id_bloque, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function desactivar($id_bloque) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE bloque SET estado = 'INACTIVO' WHERE id_bloque = :id_bloque");
        $stmt->bindParam(':id_bloque', $id_bloque, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
