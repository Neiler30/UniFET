<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class LiderPanel {
    public static function programasDelLider($id_usuario) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, f.nombre AS facultad_nombre
            FROM programa_lider pl
            JOIN programa p ON p.id_programa = pl.id_programa
            JOIN facultad f ON f.id_facultad = p.id_facultad
            WHERE pl.id_usuario = :usuario AND p.estado = 'ACTIVO'
            ORDER BY p.nombre
        ");
        $stmt->execute([':usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public static function resolverProgramaActivo($id_usuario, $solicitado = null) {
        $programas = self::programasDelLider($id_usuario);
        if (empty($programas)) return null;
        foreach ($programas as $programa) {
            if ($solicitado && (int)$programa['id_programa'] === (int)$solicitado) {
                $_SESSION['lider_programa_activo'] = (int)$programa['id_programa'];
                return $programa;
            }
        }
        $sessionId = (int)($_SESSION['lider_programa_activo'] ?? 0);
        foreach ($programas as $programa) {
            if ($sessionId && (int)$programa['id_programa'] === $sessionId) return $programa;
        }
        $_SESSION['lider_programa_activo'] = (int)$programas[0]['id_programa'];
        return $programas[0];
    }

    public static function assertPrograma($id_usuario, $id_programa) {
        foreach (self::programasDelLider($id_usuario) as $programa) {
            if ((int)$programa['id_programa'] === (int)$id_programa) return true;
        }
        return false;
    }

    public static function kpis($id_programa, $id_periodo) {
        $db = Database::getConexion();
        $kpis = ['asignaturas' => 0, 'docentes' => 0, 'clases' => 0, 'propuestas' => 0];
        if (!$id_programa || !$id_periodo) return $kpis;
        $stmt = $db->prepare("SELECT COUNT(*) FROM oferta_academica WHERE id_programa = :programa AND id_periodo = :periodo AND estado = 'ACTIVA'");
        $stmt->execute([':programa' => $id_programa, ':periodo' => $id_periodo]);
        $kpis['asignaturas'] = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(DISTINCT id_docente) FROM docente_programa WHERE id_programa = :programa");
        $stmt->execute([':programa' => $id_programa]);
        $kpis['docentes'] = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM horario WHERE id_programa = :programa AND id_periodo = :periodo AND estado != 'ELIMINADO'");
        $stmt->execute([':programa' => $id_programa, ':periodo' => $id_periodo]);
        $kpis['clases'] = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM propuesta_fet WHERE id_programa = :programa AND id_periodo = :periodo AND estado = 'GENERADA'");
        $stmt->execute([':programa' => $id_programa, ':periodo' => $id_periodo]);
        $kpis['propuestas'] = (int)$stmt->fetchColumn();
        return $kpis;
    }

    public static function horarios($id_programa, $id_periodo = null, $limite = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT v.*, h.id_programa, h.id_periodo
            FROM vista_horario_completo v
            JOIN horario h ON h.id_horario = v.id_horario
            WHERE h.id_programa = :programa
        ";
        $params = [':programa' => $id_programa];
        if ($id_periodo) {
            $sql .= " AND h.id_periodo = :periodo";
            $params[':periodo'] = $id_periodo;
        }
        $sql .= " ORDER BY FIELD(v.dia_semana,'LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'), v.hora_inicio";
        if ($limite) $sql .= " LIMIT " . (int)$limite;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function programa($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT p.*, f.nombre AS facultad_nombre
            FROM programa p
            JOIN facultad f ON f.id_facultad = p.id_facultad
            WHERE p.id_programa = :programa
        ");
        $stmt->execute([':programa' => $id_programa]);
        return $stmt->fetch();
    }

    public static function docentes($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*
            FROM docente_programa dp
            JOIN docente d ON d.id_docente = dp.id_docente
            WHERE dp.id_programa = :programa
            ORDER BY d.apellido, d.nombre
        ");
        $stmt->execute([':programa' => $id_programa]);
        return $stmt->fetchAll();
    }

    public static function asignaturas($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT a.*, aa.nombre AS area_nombre
            FROM asignatura_programa ap
            JOIN asignatura a ON a.id_asignatura = ap.id_asignatura
            LEFT JOIN area_academica aa ON aa.id_area_academica = a.id_area_academica
            WHERE ap.id_programa = :programa
            ORDER BY a.nombre
        ");
        $stmt->execute([':programa' => $id_programa]);
        return $stmt->fetchAll();
    }

    public static function asignaturasOfertadas($id_programa, $id_periodo) {
        return Horario::obtenerAsignaturasOfertadas($id_periodo, $id_programa);
    }

    public static function docentesCompatibles($id_programa, $id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.id_docente, d.codigo, d.nombre, d.apellido, d.correo
            FROM docente d
            JOIN docente_programa dp ON dp.id_docente = d.id_docente AND dp.id_programa = :programa
            JOIN docente_asignatura da ON da.id_docente = d.id_docente AND da.id_asignatura = :asignatura
            WHERE d.estado = 'ACTIVO'
            ORDER BY d.apellido, d.nombre
        ");
        $stmt->execute([':programa' => $id_programa, ':asignatura' => $id_asignatura]);
        return $stmt->fetchAll();
    }

    public static function horarioCerrado($id_programa, $id_periodo) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM horario WHERE id_programa = :programa AND id_periodo = :periodo AND estado = 'CERRADO'");
        $stmt->execute([':programa' => $id_programa, ':periodo' => $id_periodo]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function cerrarHorario($id_programa, $id_periodo, $id_usuario) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE horario
            SET estado = 'CERRADO', id_usuario_modificador = :usuario, motivo_modificacion = 'Cierre por lider de programa'
            WHERE id_programa = :programa AND id_periodo = :periodo AND estado IN ('CONFIRMADO','BORRADOR','PROPUESTA','MODIFICADO')
        ");
        return $stmt->execute([':usuario' => $id_usuario, ':programa' => $id_programa, ':periodo' => $id_periodo]);
    }

    public static function espacios($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT e.*, b.nombre AS bloque_nombre, s.nombre AS sede_nombre
            FROM espacio e
            JOIN bloque b ON b.id_bloque = e.id_bloque
            JOIN sede s ON s.id_sede = b.id_sede
            WHERE s.id_institucion = :institucion
            ORDER BY s.nombre, b.nombre, e.nombre
        ");
        $stmt->execute([':institucion' => $id_institucion]);
        return $stmt->fetchAll();
    }

    public static function ocupacionEspacio($id_programa, $id_periodo, $id_espacio) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT h.*, a.nombre AS asignatura_nombre, CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre,
                   e.nombre AS espacio_nombre, b.nombre AS bloque_nombre, s.nombre AS sede_nombre,
                   p.codigo AS programa_codigo, p.nombre AS programa_nombre, f.nombre AS facultad_nombre,
                   GROUP_CONCAT(DISTINCT CONCAT(u.nombre, ' ', u.apellido, ' <', u.correo, '>') ORDER BY u.apellido SEPARATOR ', ') AS lideres_programa,
                   CASE WHEN h.id_programa = :programa THEN 1 ELSE 0 END AS es_programa_lider
            FROM horario h
            JOIN asignatura a ON a.id_asignatura = h.id_asignatura
            JOIN docente d ON d.id_docente = h.id_docente
            JOIN espacio e ON e.id_espacio = h.id_espacio
            JOIN bloque b ON b.id_bloque = e.id_bloque
            JOIN sede s ON s.id_sede = b.id_sede
            JOIN programa p ON p.id_programa = h.id_programa
            JOIN facultad f ON f.id_facultad = p.id_facultad
            LEFT JOIN programa_lider pl ON pl.id_programa = p.id_programa
            LEFT JOIN usuario u ON u.id_usuario = pl.id_usuario AND u.estado = 'ACTIVO'
            WHERE h.id_periodo = :periodo
              AND h.id_espacio = :espacio
              AND h.estado != 'ELIMINADO'
            GROUP BY h.id_horario
            ORDER BY FIELD(h.dia_semana,'LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'), h.hora_inicio
        ");
        $stmt->execute([':programa' => $id_programa, ':periodo' => $id_periodo, ':espacio' => $id_espacio]);
        return $stmt->fetchAll();
    }

    public static function propuestas($id_programa, $id_periodo = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT pf.*, p.codigo AS periodo_codigo, pr.nombre AS programa_nombre,
                   (SELECT COUNT(*) FROM propuesta_fet_detalle WHERE id_propuesta = pf.id_propuesta) AS total_detalles
            FROM propuesta_fet pf
            JOIN periodo_academico p ON p.id_periodo = pf.id_periodo
            JOIN programa pr ON pr.id_programa = pf.id_programa
            WHERE pf.id_programa = :programa
        ";
        $params = [':programa' => $id_programa];
        if ($id_periodo) {
            $sql .= " AND pf.id_periodo = :periodo";
            $params[':periodo'] = $id_periodo;
        }
        $sql .= " ORDER BY pf.fecha_generacion DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function detallesPropuestas($id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*, pf.id_programa, pf.id_periodo, pf.estado AS propuesta_estado,
                   a.codigo AS asignatura_codigo, a.nombre AS asignatura_nombre,
                   CONCAT(doc.nombre, ' ', doc.apellido) AS docente_nombre,
                   e.nombre AS espacio_nombre, b.nombre AS bloque_nombre, s.nombre AS sede_nombre
            FROM propuesta_fet_detalle d
            JOIN propuesta_fet pf ON pf.id_propuesta = d.id_propuesta
            JOIN asignatura a ON a.id_asignatura = d.id_asignatura
            JOIN docente doc ON doc.id_docente = d.id_docente
            JOIN espacio e ON e.id_espacio = d.id_espacio
            JOIN bloque b ON b.id_bloque = e.id_bloque
            JOIN sede s ON s.id_sede = b.id_sede
            WHERE pf.id_programa = :programa AND pf.estado = 'GENERADA'
            ORDER BY pf.fecha_generacion DESC, d.dia_semana, d.hora_inicio
        ");
        $stmt->execute([':programa' => $id_programa]);
        return $stmt->fetchAll();
    }

    public static function detallePropuesta($id_programa, $id_detalle) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*, pf.id_programa, pf.id_periodo, pf.id_propuesta
            FROM propuesta_fet_detalle d
            JOIN propuesta_fet pf ON pf.id_propuesta = d.id_propuesta
            WHERE d.id_detalle = :detalle AND pf.id_programa = :programa AND pf.estado = 'GENERADA'
        ");
        $stmt->execute([':detalle' => $id_detalle, ':programa' => $id_programa]);
        return $stmt->fetch();
    }

    public static function aceptarDetalleFet($id_programa, $id_detalle, $id_usuario) {
        $detalle = self::detallePropuesta($id_programa, $id_detalle);
        if (!$detalle || !empty($detalle['id_horario_resultante'])) return false;

        if (Horario::verificarSolapamientoDocente($detalle['id_periodo'], $detalle['dia_semana'], $detalle['id_docente'], $detalle['hora_inicio'], $detalle['hora_fin'])) {
            return 'SOLAP_DOCENTE';
        }
        if (Horario::verificarSolapamientoEspacio($detalle['id_periodo'], $detalle['dia_semana'], $detalle['id_espacio'], $detalle['hora_inicio'], $detalle['hora_fin'])) {
            return 'SOLAP_ESPACIO';
        }

        $idHorario = Horario::crear([
            'id_periodo' => $detalle['id_periodo'],
            'id_programa' => $detalle['id_programa'],
            'id_asignatura' => $detalle['id_asignatura'],
            'id_docente' => $detalle['id_docente'],
            'id_espacio' => $detalle['id_espacio'],
            'dia_semana' => $detalle['dia_semana'],
            'hora_inicio' => $detalle['hora_inicio'],
            'hora_fin' => $detalle['hora_fin'],
            'estado' => 'CONFIRMADO',
            'origen' => 'FET',
            'id_usuario_creador' => $id_usuario
        ]);
        if (!$idHorario) return false;

        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE propuesta_fet_detalle SET id_horario_resultante = :horario WHERE id_detalle = :detalle");
        $stmt->execute([':horario' => $idHorario, ':detalle' => $id_detalle]);
        return $idHorario;
    }

    public static function descartarDetalleFet($id_programa, $id_detalle) {
        $detalle = self::detallePropuesta($id_programa, $id_detalle);
        if (!$detalle || !empty($detalle['id_horario_resultante'])) return false;
        return Horario::eliminarDetalleFet($id_detalle);
    }

    public static function auditoriaPertenece($id_programa, $id_auditoria) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM auditoria_clase ac
            JOIN horario h ON h.id_horario = ac.id_horario
            WHERE ac.id_auditoria = :auditoria AND h.id_programa = :programa
        ");
        $stmt->execute([':auditoria' => $id_auditoria, ':programa' => $id_programa]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function auditorias($id_programa, $filtros = []) {
        $db = Database::getConexion();
        $sql = "
            SELECT v.*
            FROM vista_auditoria_pendiente v
            JOIN auditoria_clase ac ON ac.id_auditoria = v.id_auditoria
            JOIN horario h ON h.id_horario = ac.id_horario
            WHERE h.id_programa = :programa
        ";
        $params = [':programa' => $id_programa];
        foreach (['fecha_auditoria' => 'fecha', 'sede' => 'sede', 'bloque' => 'bloque', 'espacio' => 'espacio', 'docente' => 'docente'] as $col => $key) {
            if (!empty($filtros[$key])) {
                if ($key === 'fecha') {
                    $sql .= " AND v.fecha_auditoria = :fecha";
                    $params[':fecha'] = $filtros[$key];
                } else {
                    $sql .= " AND v.{$col} LIKE :{$key}";
                    $params[":{$key}"] = '%' . $filtros[$key] . '%';
                }
            }
        }
        $sql .= " ORDER BY v.fecha_auditoria DESC, v.hora_inicio";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function asignacionesDocente($id_programa, $id_periodo = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT ad.*, pa.codigo AS periodo_codigo,
                   a.codigo AS asignatura_codigo, a.nombre AS asignatura_nombre,
                   CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre, d.codigo AS docente_codigo
            FROM asignacion_docente ad
            JOIN periodo_academico pa ON pa.id_periodo = ad.id_periodo
            JOIN asignatura a ON a.id_asignatura = ad.id_asignatura
            JOIN docente d ON d.id_docente = ad.id_docente
            WHERE ad.id_programa = :programa
        ";
        $params = [':programa' => $id_programa];
        if ($id_periodo) {
            $sql .= " AND ad.id_periodo = :periodo";
            $params[':periodo'] = $id_periodo;
        }
        $sql .= " ORDER BY pa.codigo DESC, a.nombre, d.apellido";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function asignarDocenteAsignatura($id_programa, $id_periodo, $id_asignatura, $id_docente) {
        if (!self::asignaturaEnPrograma($id_programa, $id_asignatura) || !self::docenteEnPrograma($id_programa, $id_docente)) {
            return false;
        }

        $db = Database::getConexion();
        $db->beginTransaction();
        try {
            $db->prepare("
                INSERT IGNORE INTO docente_asignatura (id_docente, id_asignatura)
                VALUES (:docente, :asignatura)
            ")->execute([':docente' => $id_docente, ':asignatura' => $id_asignatura]);

            $stmt = $db->prepare("
                INSERT INTO asignacion_docente (id_periodo, id_docente, id_asignatura, id_programa, estado)
                VALUES (:periodo, :docente, :asignatura, :programa, 'ACTIVA')
                ON DUPLICATE KEY UPDATE estado = 'ACTIVA'
            ");
            $stmt->execute([
                ':periodo' => $id_periodo,
                ':docente' => $id_docente,
                ':asignatura' => $id_asignatura,
                ':programa' => $id_programa
            ]);
            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function asignaturaEnPrograma($id_programa, $id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM asignatura_programa WHERE id_programa = :programa AND id_asignatura = :asignatura");
        $stmt->execute([':programa' => $id_programa, ':asignatura' => $id_asignatura]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function docenteEnPrograma($id_programa, $id_docente) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM docente_programa WHERE id_programa = :programa AND id_docente = :docente");
        $stmt->execute([':programa' => $id_programa, ':docente' => $id_docente]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
