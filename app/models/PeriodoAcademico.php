<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class PeriodoAcademico {
    public static function getActivo() {
        $db = Database::getConexion();
        $stmt = $db->query("SELECT * FROM periodo_academico WHERE estado = 'ACTIVO' LIMIT 1");
        return $stmt->fetch();
    }
}
