<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class DashboardModel {
    public static function getKpis() {
        $db = Database::getConexion();
        
        $kpis = [
            'facultades' => 0,
            'programas' => 0,
            'docentes' => 0,
            'espacios' => 0,
            'auditorias_pendientes' => 0
        ];

        $kpis['facultades'] = $db->query("SELECT COUNT(*) FROM facultad WHERE estado = 'ACTIVO'")->fetchColumn();
        $kpis['programas'] = $db->query("SELECT COUNT(*) FROM programa WHERE estado = 'ACTIVO'")->fetchColumn();
        $kpis['docentes'] = $db->query("SELECT COUNT(*) FROM docente WHERE estado = 'ACTIVO'")->fetchColumn();
        $kpis['espacios'] = $db->query("SELECT COUNT(*) FROM espacio WHERE estado = 'DISPONIBLE'")->fetchColumn();
        $kpis['auditorias_pendientes'] = $db->query("SELECT COUNT(*) FROM auditoria_clase WHERE resultado = 'PENDIENTE'")->fetchColumn();
        
        return $kpis;
    }

    public static function getUltimosHorarios($limite = 8) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM vista_horario_completo ORDER BY id_horario DESC LIMIT :limite");
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAuditoriasHoy() {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM vista_auditoria_pendiente WHERE fecha_auditoria = CURDATE()");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
