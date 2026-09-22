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

    public static function verificarSolapamientoDocente($id_periodo, $dia_semana, $id_docente, $hora_inicio, $hora_fin, $id_horario = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT h.*, CONCAT(d.nombre, ' ', d.apellido) as docente_nombre, a.nombre as asignatura_nombre
            FROM horario h
            JOIN docente d ON h.id_docente = d.id_docente
            JOIN asignatura a ON h.id_asignatura = a.id_asignatura
            WHERE h.id_periodo = :periodo 
              AND h.dia_semana = :dia
              AND h.id_docente = :docente
              AND h.estado != 'ELIMINADO'
              AND h.hora_inicio < :hora_fin 
              AND h.hora_fin > :hora_inicio
        ";
        if ($id_horario) {
            $sql .= " AND h.id_horario != :id_horario";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia_semana, PDO::PARAM_STR);
        $stmt->bindParam(':docente', $id_docente, PDO::PARAM_INT);
        $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        if ($id_horario) {
            $stmt->bindParam(':id_horario', $id_horario, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function verificarSolapamientoEspacio($id_periodo, $dia_semana, $id_espacio, $hora_inicio, $hora_fin, $id_horario = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT h.*, e.nombre as espacio_nombre, a.nombre as asignatura_nombre
            FROM horario h
            JOIN espacio e ON h.id_espacio = e.id_espacio
            JOIN asignatura a ON h.id_asignatura = a.id_asignatura
            WHERE h.id_periodo = :periodo 
              AND h.dia_semana = :dia
              AND h.id_espacio = :espacio
              AND h.estado != 'ELIMINADO'
              AND h.hora_inicio < :hora_fin 
              AND h.hora_fin > :hora_inicio
        ";
        if ($id_horario) {
            $sql .= " AND h.id_horario != :id_horario";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia_semana, PDO::PARAM_STR);
        $stmt->bindParam(':espacio', $id_espacio, PDO::PARAM_INT);
        $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        if ($id_horario) {
            $stmt->bindParam(':id_horario', $id_horario, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function crear($datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            INSERT INTO horario (
                id_periodo, id_programa, id_asignatura, id_docente, id_espacio,
                dia_semana, hora_inicio, hora_fin, estado, origen, id_usuario_creador
            ) VALUES (
                :id_periodo, :id_programa, :id_asignatura, :id_docente, :id_espacio,
                :dia_semana, :hora_inicio, :hora_fin, :estado, :origen, :id_usuario_creador
            )
        ");
        $stmt->bindParam(':id_periodo', $datos['id_periodo'], PDO::PARAM_INT);
        $stmt->bindParam(':id_programa', $datos['id_programa'], PDO::PARAM_INT);
        $stmt->bindParam(':id_asignatura', $datos['id_asignatura'], PDO::PARAM_INT);
        $stmt->bindParam(':id_docente', $datos['id_docente'], PDO::PARAM_INT);
        $stmt->bindParam(':id_espacio', $datos['id_espacio'], PDO::PARAM_INT);
        $stmt->bindParam(':dia_semana', $datos['dia_semana'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_inicio', $datos['hora_inicio'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $datos['hora_fin'], PDO::PARAM_STR);
        $estado = $datos['estado'] ?? 'BORRADOR';
        $origen = $datos['origen'] ?? 'MANUAL';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindParam(':id_usuario_creador', $datos['id_usuario_creador'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function cerrar($id_horario, $id_usuario) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE horario 
            SET estado = 'CERRADO', id_usuario_modificador = :usuario, motivo_modificacion = 'Cierre de horario por usuario'
            WHERE id_horario = :id
        ");
        $stmt->bindParam(':id', $id_horario, PDO::PARAM_INT);
        $stmt->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function obtenerAsignaturasOfertadas($id_periodo, $id_programa) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT a.id_asignatura, a.codigo, a.nombre, a.creditos
            FROM oferta_academica o
            JOIN asignatura a ON o.id_asignatura = a.id_asignatura
            WHERE o.id_periodo = :periodo 
              AND o.id_programa = :programa 
              AND o.estado = 'ACTIVA'
            ORDER BY a.nombre ASC
        ");
        $stmt->bindParam(':periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->bindParam(':programa', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerDocentesCompatibles($id_asignatura) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.id_docente, d.codigo, d.nombre, d.apellido, d.correo
            FROM docente_asignatura da
            JOIN docente d ON da.id_docente = d.id_docente
            WHERE da.id_asignatura = :asignatura 
              AND d.estado = 'ACTIVO'
            ORDER BY d.apellido ASC, d.nombre ASC
        ");
        $stmt->bindParam(':asignatura', $id_asignatura, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerEspaciosLibres($id_periodo, $dia_semana, $hora_inicio, $hora_fin) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT e.id_espacio, e.codigo, e.nombre, e.tipo, e.capacidad, 
                   s.nombre as sede_nombre, b.nombre as bloque_nombre
            FROM espacio e
            JOIN bloque b ON e.id_bloque = b.id_bloque
            JOIN sede s ON b.id_sede = s.id_sede
            WHERE e.estado = 'DISPONIBLE'
              AND e.id_espacio NOT IN (
                  SELECT id_espacio 
                  FROM horario 
                  WHERE id_periodo = :periodo 
                    AND dia_semana = :dia 
                    AND estado != 'ELIMINADO'
                    AND hora_inicio < :hora_fin 
                    AND hora_fin > :hora_inicio
              )
            ORDER BY s.nombre ASC, b.nombre ASC, e.nombre ASC
        ");
        $stmt->bindParam(':periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia_semana, PDO::PARAM_STR);
        $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function guardarPropuestaFet($id_periodo, $id_programa, $id_usuario, $parametros, $detalles) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            INSERT INTO propuesta_fet (id_periodo, id_programa, id_usuario_solicitante, parametros_entrada, estado)
            VALUES (:periodo, :programa, :usuario, :params, 'GENERADA')
        ");
        $paramsJson = json_encode($parametros);
        $stmt->bindParam(':periodo', $id_periodo, PDO::PARAM_INT);
        $stmt->bindParam(':programa', $id_programa, PDO::PARAM_INT);
        $stmt->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':params', $paramsJson, PDO::PARAM_STR);
        $stmt->execute();
        $id_propuesta = $db->lastInsertId();

        $stmtDetalle = $db->prepare("
            INSERT INTO propuesta_fet_detalle (id_propuesta, id_asignatura, id_docente, id_espacio, dia_semana, hora_inicio, hora_fin)
            VALUES (:propuesta, :asignatura, :docente, :espacio, :dia, :inicio, :fin)
        ");

        foreach ($detalles as $d) {
            $stmtDetalle->execute([
                ':propuesta' => $id_propuesta,
                ':asignatura' => $d['id_asignatura'],
                ':docente' => $d['id_docente'],
                ':espacio' => $d['id_espacio'],
                ':dia' => $d['dia_semana'],
                ':inicio' => $d['hora_inicio'],
                ':fin' => $d['hora_fin']
            ]);
        }

        return $id_propuesta;
    }

    public static function obtenerDetallesPropuestaFet($id_propuesta) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*, a.nombre as asignatura_nombre, a.codigo as asignatura_codigo,
                   CONCAT(doc.nombre, ' ', doc.apellido) as docente_nombre,
                   esp.nombre as espacio_nombre, esp.tipo as espacio_tipo,
                   bl.nombre as bloque_nombre, s.nombre as sede_nombre
            FROM propuesta_fet_detalle d
            JOIN asignatura a ON d.id_asignatura = a.id_asignatura
            JOIN docente doc ON d.id_docente = doc.id_docente
            JOIN espacio esp ON d.id_espacio = esp.id_espacio
            JOIN bloque bl ON esp.id_bloque = bl.id_bloque
            JOIN sede s ON bl.id_sede = s.id_sede
            WHERE d.id_propuesta = :id
            ORDER BY FIELD(d.dia_semana, 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'), d.hora_inicio ASC
        ");
        $stmt->bindParam(':id', $id_propuesta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerUltimaPropuestaFet($id_periodo = null, $id_programa = null) {
        $db = Database::getConexion();
        $sql = "
            SELECT pf.*, p.codigo as periodo_codigo, pr.nombre as programa_nombre,
                   CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre
            FROM propuesta_fet pf
            JOIN periodo_academico p ON pf.id_periodo = p.id_periodo
            JOIN programa pr ON pf.id_programa = pr.id_programa
            JOIN usuario u ON pf.id_usuario_solicitante = u.id_usuario
            WHERE 1=1
        ";
        if ($id_periodo) $sql .= " AND pf.id_periodo = :per";
        if ($id_programa) $sql .= " AND pf.id_programa = :prog";
        $sql .= " ORDER BY pf.id_propuesta DESC LIMIT 1";

        $stmt = $db->prepare($sql);
        if ($id_periodo) $stmt->bindParam(':per', $id_periodo, PDO::PARAM_INT);
        if ($id_programa) $stmt->bindParam(':prog', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener todas las propuestas FET con estado GENERADA
     */
    public static function obtenerPropuestasFetGeneradas() {
        $db = Database::getConexion();
        $stmt = $db->query("
            SELECT pf.*, p.codigo as periodo_codigo, pr.nombre as programa_nombre,
                   CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre,
                   (SELECT COUNT(*) FROM propuesta_fet_detalle WHERE id_propuesta = pf.id_propuesta) as total_detalles
            FROM propuesta_fet pf
            JOIN periodo_academico p ON pf.id_periodo = p.id_periodo
            JOIN programa pr ON pf.id_programa = pr.id_programa
            JOIN usuario u ON pf.id_usuario_solicitante = u.id_usuario
            WHERE pf.estado = 'GENERADA'
            ORDER BY pf.id_propuesta DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener todas las propuestas FET (todos los estados)
     */
    public static function obtenerTodasPropuestasFet() {
        $db = Database::getConexion();
        $stmt = $db->query("
            SELECT pf.*, p.codigo as periodo_codigo, pr.nombre as programa_nombre,
                   CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre,
                   (SELECT COUNT(*) FROM propuesta_fet_detalle WHERE id_propuesta = pf.id_propuesta) as total_detalles
            FROM propuesta_fet pf
            JOIN periodo_academico p ON pf.id_periodo = p.id_periodo
            JOIN programa pr ON pf.id_programa = pr.id_programa
            JOIN usuario u ON pf.id_usuario_solicitante = u.id_usuario
            ORDER BY pf.id_propuesta DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Aceptar un detalle de propuesta FET: crea horario real con origen='FET'
     */
    public static function aceptarDetalleFet($id_detalle, $id_usuario) {
        $db = Database::getConexion();
        
        // Obtener el detalle y su propuesta
        $stmt = $db->prepare("
            SELECT d.*, pf.id_periodo, pf.id_programa
            FROM propuesta_fet_detalle d
            JOIN propuesta_fet pf ON d.id_propuesta = pf.id_propuesta
            WHERE d.id_detalle = :id AND d.id_horario_resultante IS NULL
        ");
        $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
        $stmt->execute();
        $detalle = $stmt->fetch();
        
        if (!$detalle) return false;

        // Verificar solapamiento de docente
        $solapDoc = self::verificarSolapamientoDocente(
            $detalle['id_periodo'], $detalle['dia_semana'], $detalle['id_docente'],
            $detalle['hora_inicio'], $detalle['hora_fin']
        );
        if ($solapDoc) return 'SOLAP_DOCENTE';

        // Verificar solapamiento de espacio
        $solapEsp = self::verificarSolapamientoEspacio(
            $detalle['id_periodo'], $detalle['dia_semana'], $detalle['id_espacio'],
            $detalle['hora_inicio'], $detalle['hora_fin']
        );
        if ($solapEsp) return 'SOLAP_ESPACIO';

        // Crear horario real
        $datos = [
            'id_periodo' => $detalle['id_periodo'],
            'id_programa' => $detalle['id_programa'],
            'id_asignatura' => $detalle['id_asignatura'],
            'id_docente' => $detalle['id_docente'],
            'id_espacio' => $detalle['id_espacio'],
            'dia_semana' => $detalle['dia_semana'],
            'hora_inicio' => $detalle['hora_inicio'],
            'hora_fin' => $detalle['hora_fin'],
            'estado' => 'PROPUESTA',
            'origen' => 'FET',
            'id_usuario_creador' => $id_usuario
        ];
        
        $idHorario = self::crear($datos);
        if (!$idHorario) return false;

        // Actualizar detalle con el id_horario_resultante
        $stmtUpd = $db->prepare("UPDATE propuesta_fet_detalle SET id_horario_resultante = :idh WHERE id_detalle = :idd");
        $stmtUpd->bindParam(':idh', $idHorario, PDO::PARAM_INT);
        $stmtUpd->bindParam(':idd', $id_detalle, PDO::PARAM_INT);
        $stmtUpd->execute();

        // Si todos los detalles tienen horario resultante, marcar propuesta como ACEPTADA
        $stmtCheck = $db->prepare("
            SELECT COUNT(*) as total, 
                   SUM(CASE WHEN id_horario_resultante IS NOT NULL THEN 1 ELSE 0 END) as aceptados
            FROM propuesta_fet_detalle WHERE id_propuesta = :id
        ");
        $stmtCheck->bindParam(':id', $detalle['id_propuesta'], PDO::PARAM_INT);
        $stmtCheck->execute();
        $conteo = $stmtCheck->fetch();
        if ($conteo['total'] == $conteo['aceptados']) {
            $db->prepare("UPDATE propuesta_fet SET estado = 'ACEPTADA', fecha_resolucion = NOW() WHERE id_propuesta = :id")
               ->execute([':id' => $detalle['id_propuesta']]);
        }

        return $idHorario;
    }

    /**
     * Descartar una propuesta FET completa
     */
    public static function descartarPropuesta($id_propuesta) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE propuesta_fet SET estado = 'DESCARTADA', fecha_resolucion = NOW() WHERE id_propuesta = :id");
        $stmt->bindParam(':id', $id_propuesta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Eliminar un detalle de propuesta FET (borrado físico, solo si no tiene horario resultante)
     */
    public static function eliminarDetalleFet($id_detalle) {
        $db = Database::getConexion();
        $stmt = $db->prepare("DELETE FROM propuesta_fet_detalle WHERE id_detalle = :id AND id_horario_resultante IS NULL");
        $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener auditorías con filtros
     */
    public static function obtenerAuditorias($filtros = []) {
        $db = Database::getConexion();
        $sql = "SELECT * FROM vista_auditoria_pendiente WHERE 1=1";
        $params = [];

        if (!empty($filtros['fecha'])) {
            $sql .= " AND fecha_auditoria = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        if (!empty($filtros['sede'])) {
            $sql .= " AND sede LIKE :sede";
            $params[':sede'] = '%' . $filtros['sede'] . '%';
        }
        if (!empty($filtros['bloque'])) {
            $sql .= " AND bloque LIKE :bloque";
            $params[':bloque'] = '%' . $filtros['bloque'] . '%';
        }
        if (!empty($filtros['espacio'])) {
            $sql .= " AND espacio LIKE :espacio";
            $params[':espacio'] = '%' . $filtros['espacio'] . '%';
        }
        if (!empty($filtros['docente'])) {
            $sql .= " AND docente LIKE :docente";
            $params[':docente'] = '%' . $filtros['docente'] . '%';
        }
        if (!empty($filtros['programa'])) {
            $sql .= " AND programa LIKE :programa";
            $params[':programa'] = '%' . $filtros['programa'] . '%';
        }
        if (!empty($filtros['resultado'])) {
            $sql .= " AND resultado = :resultado";
            $params[':resultado'] = $filtros['resultado'];
        }

        $sql .= " ORDER BY fecha_auditoria DESC, hora_inicio ASC";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Verificar/marcar una auditoría
     */
    public static function verificarAuditoria($id_auditoria, $resultado, $observacion) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE auditoria_clase 
            SET resultado = :resultado, observacion = :observacion, hora_verificacion = CURTIME()
            WHERE id_auditoria = :id
        ");
        $stmt->bindParam(':resultado', $resultado, PDO::PARAM_STR);
        $stmt->bindParam(':observacion', $observacion, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_auditoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener un detalle de propuesta FET por ID
     */
    public static function obtenerDetalleFetPorId($id_detalle) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*, a.nombre as asignatura_nombre, 
                   CONCAT(doc.nombre, ' ', doc.apellido) as docente_nombre,
                   esp.nombre as espacio_nombre
            FROM propuesta_fet_detalle d
            JOIN asignatura a ON d.id_asignatura = a.id_asignatura
            JOIN docente doc ON d.id_docente = doc.id_docente
            JOIN espacio esp ON d.id_espacio = esp.id_espacio
            WHERE d.id_detalle = :id
        ");
        $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}

