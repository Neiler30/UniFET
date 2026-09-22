<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Espacio {
    
    public static function obtenerTodos($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT e.*, b.nombre as bloque_nombre, s.nombre as sede_nombre 
            FROM espacio e
            JOIN bloque b ON e.id_bloque = b.id_bloque
            JOIN sede s ON b.id_sede = s.id_sede
            WHERE s.id_institucion = :id_institucion 
            ORDER BY s.nombre ASC, b.nombre ASC, e.nombre ASC
        ");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_espacio) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM espacio WHERE id_espacio = :id_espacio");
        $stmt->bindParam(':id_espacio', $id_espacio, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function existeCodigo($codigo, $id_bloque, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM espacio WHERE codigo = :codigo AND id_bloque = :id_bloque";
        if ($id_excluir) {
            $sql .= " AND id_espacio != :id_excluir";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindParam(':id_bloque', $id_bloque, PDO::PARAM_INT);
        if ($id_excluir) {
            $stmt->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("INSERT INTO espacio (id_bloque, codigo, nombre, tipo, capacidad, caracteristicas, estado) VALUES (:id_bloque, :codigo, :nombre, :tipo, :capacidad, :caracteristicas, 'DISPONIBLE')");
        $stmt->bindParam(':id_bloque', $datos['id_bloque'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
        $stmt->bindParam(':caracteristicas', $datos['caracteristicas'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function actualizar($id_espacio, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE espacio SET id_bloque = :id_bloque, codigo = :codigo, nombre = :nombre, tipo = :tipo, capacidad = :capacidad, caracteristicas = :caracteristicas WHERE id_espacio = :id_espacio");
        $stmt->bindParam(':id_bloque', $datos['id_bloque'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
        $stmt->bindParam(':caracteristicas', $datos['caracteristicas'], PDO::PARAM_STR);
        $stmt->bindParam(':id_espacio', $id_espacio, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function cambiarEstado($id_espacio, $estado) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE espacio SET estado = :estado WHERE id_espacio = :id_espacio");
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_espacio', $id_espacio, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
