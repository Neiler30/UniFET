<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class NivelAcademico {

    public static function obtenerTodos() {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT id_nivel_academico, nombre, descripcion, estado
            FROM nivel_academico
            ORDER BY id_nivel_academico ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerActivos() {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT id_nivel_academico, nombre, descripcion
            FROM nivel_academico
            WHERE estado = 'ACTIVO'
            ORDER BY id_nivel_academico ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM nivel_academico WHERE id_nivel_academico = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
