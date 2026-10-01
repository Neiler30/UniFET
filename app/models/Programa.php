<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Programa {

    public static function obtenerTodos($id_institucion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, f.nombre AS facultad_nombre,
                   na.nombre AS nivel_nombre
            FROM programa p
            JOIN facultad f ON p.id_facultad = f.id_facultad
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE f.id_institucion = :id_institucion
            ORDER BY f.nombre ASC, p.nombre ASC
        ");
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, na.nombre AS nivel_nombre
            FROM programa p
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE p.id_programa = :id_programa
        ");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Retorna id_nivel_academico y numero_semestres de un programa.
     * Usado por el formulario de oferta para construir el selector de semestres dinámicamente.
     */
    public static function obtenerConfiguracion($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.id_nivel_academico, p.numero_semestres,
                   na.nombre AS nivel_nombre
            FROM programa p
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE p.id_programa = :id
        ");
        $stmt->bindParam(':id', $id_programa, PDO::PARAM_INT);
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
        $stmt = $db->prepare("
            INSERT INTO programa (id_facultad, id_nivel_academico, codigo, nombre, numero_semestres, estado)
            VALUES (:id_facultad, :id_nivel_academico, :codigo, :nombre, :numero_semestres, :estado)
        ");
        $stmt->bindParam(':id_facultad',          $datos['id_facultad'],         PDO::PARAM_INT);
        $stmt->bindParam(':id_nivel_academico',   $datos['id_nivel_academico'],  PDO::PARAM_INT);
        $stmt->bindParam(':codigo',               $datos['codigo'],              PDO::PARAM_STR);
        $stmt->bindParam(':nombre',               $datos['nombre'],              PDO::PARAM_STR);
        $stmt->bindParam(':numero_semestres',     $datos['numero_semestres'],    PDO::PARAM_INT);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function actualizar($id_programa, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE programa
            SET id_facultad         = :id_facultad,
                id_nivel_academico  = :id_nivel_academico,
                codigo              = :codigo,
                nombre              = :nombre,
                numero_semestres    = :numero_semestres,
                estado              = :estado
            WHERE id_programa = :id_programa
        ");
        $stmt->bindParam(':id_facultad',         $datos['id_facultad'],        PDO::PARAM_INT);
        $stmt->bindParam(':id_nivel_academico',  $datos['id_nivel_academico'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo',              $datos['codigo'],             PDO::PARAM_STR);
        $stmt->bindParam(':nombre',              $datos['nombre'],             PDO::PARAM_STR);
        $stmt->bindParam(':numero_semestres',    $datos['numero_semestres'],   PDO::PARAM_INT);
        $estado = $datos['estado'] ?? 'ACTIVO';
        $stmt->bindParam(':estado',              $estado,                      PDO::PARAM_STR);
        $stmt->bindParam(':id_programa',         $id_programa,                 PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function desactivar($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE programa SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_programa = :id_programa");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
