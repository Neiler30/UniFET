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

    /**
     * Generar código automático para una sede basado en las iniciales del nombre
     * Formato: SD-XXX donde XXX es el consecutivo
     */
    public static function generarCodigo($nombre, $id_institucion, $id_excluir = null) {
        $db = Database::getConexion();
        // Obtener el total de sedes para el consecutivo
        $sql = "SELECT COUNT(*) FROM sede WHERE id_institucion = :id";
        $params = [':id' => (int)$id_institucion];
        if ($id_excluir) {
            $sql .= " AND id_sede != :excluir";
            $params[':excluir'] = (int)$id_excluir;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();
        // Siglas del nombre (primera letra de cada palabra, máx 3)
        $palabras = preg_split('/\s+/', trim($nombre));
        $siglas = '';
        foreach ($palabras as $p) {
            if (strlen($siglas) >= 3) break;
            $siglas .= strtoupper(mb_substr($p, 0, 1));
        }
        if (empty($siglas)) $siglas = 'SD';
        $codigo = $siglas . '-' . str_pad($total + 1, 3, '0', STR_PAD_LEFT);
        // Asegurar unicidad
        $check = $db->prepare("SELECT COUNT(*) FROM sede WHERE codigo = :c AND id_institucion = :i");
        $check->execute([':c' => $codigo, ':i' => (int)$id_institucion]);
        if ($check->fetchColumn() > 0) {
            $codigo = $siglas . '-' . str_pad($total + 2, 3, '0', STR_PAD_LEFT);
        }
        return $codigo;
    }

    /**
     * Obtener una sede con todos sus bloques y cada bloque con sus espacios
     */
    public static function obtenerConBloquesYEspacios($id_sede, $id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM sede WHERE id_sede = :s AND id_institucion = :i");
        $stmt->execute([':s' => (int)$id_sede, ':i' => (int)$id_institucion]);
        $sede = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sede) return null;
        $stmtB = $db->prepare("SELECT * FROM bloque WHERE id_sede = :s ORDER BY nombre ASC");
        $stmtB->execute([':s' => (int)$id_sede]);
        $bloques = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        foreach ($bloques as &$bloque) {
            $stmtE = $db->prepare("SELECT * FROM espacio WHERE id_bloque = :b ORDER BY nombre ASC");
            $stmtE->execute([':b' => (int)$bloque['id_bloque']]);
            $bloque['espacios'] = $stmtE->fetchAll(PDO::FETCH_ASSOC);
        }
        $sede['bloques'] = $bloques;
        return $sede;
    }

    /**
     * Obtener todas las sedes con su jerarquía completa (bloques y espacios)
     * para la vista principal de Gestión de Espacios
     */
    public static function obtenerTodasConJerarquia($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM sede WHERE id_institucion = :i ORDER BY nombre ASC");
        $stmt->execute([':i' => (int)$id_institucion]);
        $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sedes as &$sede) {
            $stmtB = $db->prepare("SELECT * FROM bloque WHERE id_sede = :s ORDER BY nombre ASC");
            $stmtB->execute([':s' => (int)$sede['id_sede']]);
            $bloques = $stmtB->fetchAll(PDO::FETCH_ASSOC);
            $totalEspaciosSede = 0;

            foreach ($bloques as &$bloque) {
                $stmtE = $db->prepare("SELECT * FROM espacio WHERE id_bloque = :b ORDER BY nombre ASC");
                $stmtE->execute([':b' => (int)$bloque['id_bloque']]);
                $espacios = $stmtE->fetchAll(PDO::FETCH_ASSOC);
                $bloque['espacios'] = $espacios;
                $bloque['num_espacios'] = count($espacios);
                $totalEspaciosSede += count($espacios);
            }

            $sede['bloques'] = $bloques;
            $sede['num_bloques'] = count($bloques);
            $sede['num_espacios'] = $totalEspaciosSede;
        }

        return $sedes;
    }
}

