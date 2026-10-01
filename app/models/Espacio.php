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
        $stmt = $db->prepare("
            SELECT e.*, b.nombre AS bloque_nombre, b.id_sede, s.nombre AS sede_nombre
            FROM espacio e
            JOIN bloque b ON e.id_bloque = b.id_bloque
            JOIN sede s ON b.id_sede = s.id_sede
            WHERE e.id_espacio = :id_espacio
        ");
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

    private static function sanitizarTipo($tipo) {
        $validos = ['AULA', 'LABORATORIO', 'SALA', 'AUDITORIO', 'OTRO'];
        $tipo = strtoupper(trim($tipo ?? 'AULA'));
        if (in_array($tipo, $validos)) {
            return $tipo;
        }
        if (in_array($tipo, ['OFICINA', 'SALA_REUNIONES', 'SALA REUNIONES', 'BIBLIOTECA', 'SALA_INFORMATICA'])) {
            return 'SALA';
        }
        return 'OTRO';
    }

    private static function sanitizarCaracteristicas($caract) {
        $caract = trim($caract ?? '');
        if ($caract === '' || $caract === '[]') {
            return null;
        }
        json_decode($caract);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $caract;
        }
        // Si el usuario escribió texto separado por comas o simple texto, convertir a JSON válido
        $items = array_map('trim', explode(',', $caract));
        $items = array_values(array_filter($items));
        return json_encode($items, JSON_UNESCAPED_UNICODE);
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("INSERT INTO espacio (id_bloque, codigo, nombre, tipo, capacidad, caracteristicas, estado) VALUES (:id_bloque, :codigo, :nombre, :tipo, :capacidad, :caracteristicas, 'DISPONIBLE')");
        $stmt->bindParam(':id_bloque', $datos['id_bloque'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $tipo = self::sanitizarTipo($datos['tipo'] ?? 'AULA');
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $capacidad = (int)($datos['capacidad'] ?? 0);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $caracteristicas = self::sanitizarCaracteristicas($datos['caracteristicas'] ?? null);
        if ($caracteristicas === null) {
            $stmt->bindValue(':caracteristicas', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':caracteristicas', $caracteristicas, PDO::PARAM_STR);
        }
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
        $tipo = self::sanitizarTipo($datos['tipo'] ?? 'AULA');
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $capacidad = (int)($datos['capacidad'] ?? 0);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $caracteristicas = self::sanitizarCaracteristicas($datos['caracteristicas'] ?? null);
        if ($caracteristicas === null) {
            $stmt->bindValue(':caracteristicas', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':caracteristicas', $caracteristicas, PDO::PARAM_STR);
        }
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

    /**
     * Generar código automático para un espacio
     * Formato: ESP-XXX donde XXX es el consecutivo dentro del bloque
     */
    public static function generarCodigo($nombre, $id_bloque, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM espacio WHERE id_bloque = :id";
        $params = [':id' => (int)$id_bloque];
        if ($id_excluir) {
            $sql .= " AND id_espacio != :excluir";
            $params[':excluir'] = (int)$id_excluir;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();
        $palabras = preg_split('/\s+/', trim($nombre));
        $siglas = '';
        foreach ($palabras as $p) {
            if (strlen($siglas) >= 3) break;
            $siglas .= strtoupper(mb_substr($p, 0, 1));
        }
        if (empty($siglas)) $siglas = 'ESP';
        $codigo = $siglas . '-' . str_pad($total + 1, 3, '0', STR_PAD_LEFT);
        $check = $db->prepare("SELECT COUNT(*) FROM espacio WHERE codigo = :c AND id_bloque = :i");
        $check->execute([':c' => $codigo, ':i' => (int)$id_bloque]);
        if ($check->fetchColumn() > 0) {
            $codigo = $siglas . '-' . str_pad($total + 2, 3, '0', STR_PAD_LEFT);
        }
        return $codigo;
    }

    /**
     * Obtener todos los espacios de un bloque
     */
    public static function obtenerPorBloque($id_bloque) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM espacio WHERE id_bloque = :id ORDER BY nombre ASC");
        $stmt->execute([':id' => (int)$id_bloque]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
