<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Horario {
    public static function obtenerConFiltros($filtros) {
        $db = Database::getConexion();
        
        $sql = "SELECT * FROM vista_horario_completo WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['programa'])) {
            $sql .= " AND programa LIKE :programa";
            $params[':programa'] = '%' . $filtros['programa'] . '%';
        }
        
        if (!empty($filtros['docente'])) {
            $sql .= " AND docente LIKE :docente";
            $params[':docente'] = '%' . $filtros['docente'] . '%';
        }
        
        if (!empty($filtros['espacio'])) {
            $sql .= " AND espacio LIKE :espacio";
            $params[':espacio'] = '%' . $filtros['espacio'] . '%';
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $sql .= " ORDER BY periodo DESC, dia_semana ASC, hora_inicio ASC";
        
        $stmt = $db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
