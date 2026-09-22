<?php

namespace App\Models;

use App\Config\Database;

class Reporte {
    private static function horariosBase($where = '', $params = []) {
        $db = Database::getConexion();
        $sql = "SELECT * FROM vista_horario_completo WHERE 1=1 {$where} ORDER BY programa, dia_semana, hora_inicio";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function porPrograma($programa = '') {
        return self::horariosBase($programa ? "AND programa LIKE :programa" : '', $programa ? [':programa' => "%{$programa}%"] : []);
    }

    public static function porDocente($docente = '') {
        return self::horariosBase($docente ? "AND docente LIKE :docente" : '', $docente ? [':docente' => "%{$docente}%"] : []);
    }

    public static function porEspacio($espacio = '') {
        return self::horariosBase($espacio ? "AND espacio LIKE :espacio" : '', $espacio ? [':espacio' => "%{$espacio}%"] : []);
    }

    public static function auditorias($resultado = '') {
        $db = Database::getConexion();
        $sql = "SELECT * FROM vista_auditoria_pendiente WHERE 1=1";
        $params = [];
        if ($resultado) {
            $sql .= " AND resultado = :resultado";
            $params[':resultado'] = $resultado;
        }
        $sql .= " ORDER BY fecha_auditoria DESC, programa, hora_inicio";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
