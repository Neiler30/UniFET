<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Auditoria {
    /**
     * Mapeo de día de semana en español según el día obtenido de la fecha
     */
    public static function obtenerDiaSemanaEsp($fecha) {
        $timestamp = strtotime($fecha);
        $diaIngles = strtoupper(date('l', $timestamp));
        $map = [
            'MONDAY'    => 'LUNES',
            'TUESDAY'   => 'MARTES',
            'WEDNESDAY' => 'MIERCOLES',
            'THURSDAY'  => 'JUEVES',
            'FRIDAY'    => 'VIERNES',
            'SATURDAY'  => 'SABADO',
            'SUNDAY'    => 'DOMINGO'
        ];
        return $map[$diaIngles] ?? 'LUNES';
    }

    /**
     * Obtener los criterios activos de auditoría
     */
    public static function obtenerCriterios() {
        $db = Database::getConexion();
        $stmt = $db->query("
            SELECT * FROM auditoria_criterio 
            WHERE estado = 'ACTIVO' 
            ORDER BY orden ASC, id_criterio ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener las clases programadas para una fecha y un programa (o lista de programas),
     * cruzadas con la auditoría registrada si existe para ese día y horario.
     */
    public static function obtenerClasesParaAuditar($idPrograma, $fecha, $idPeriodo = null, $filtroEstado = '') {
        $db = Database::getConexion();
        $diaSemana = self::obtenerDiaSemanaEsp($fecha);

        $sql = "
            SELECT 
                h.id_horario,
                h.id_periodo,
                h.id_programa,
                h.id_asignatura,
                h.id_docente,
                h.id_espacio,
                h.dia_semana,
                h.hora_inicio,
                h.hora_fin,
                h.estado AS estado_horario,
                per.codigo AS periodo_codigo,
                prog.nombre AS programa_nombre,
                prog.codigo AS programa_codigo,
                asig.nombre AS asignatura_nombre,
                asig.codigo AS asignatura_codigo,
                asig.creditos AS asignatura_creditos,
                CONCAT(doc.nombre, ' ', doc.apellido) AS docente_nombre,
                doc.codigo AS docente_codigo,
                esp.nombre AS espacio_nombre,
                esp.codigo AS espacio_codigo,
                esp.tipo AS espacio_tipo,
                bl.nombre AS bloque_nombre,
                sede.nombre AS sede_nombre,
                ac.id_auditoria,
                ac.fecha_auditoria,
                ac.hora_verificacion,
                ac.id_usuario_auditor,
                ac.resultado,
                ac.observacion,
                ac.fecha_registro,
                CONCAT(u.nombre, ' ', u.apellido) AS auditor_nombre
            FROM horario h
            JOIN periodo_academico per ON per.id_periodo = h.id_periodo
            JOIN programa prog ON prog.id_programa = h.id_programa
            JOIN asignatura asig ON asig.id_asignatura = h.id_asignatura
            JOIN docente doc ON doc.id_docente = h.id_docente
            JOIN espacio esp ON esp.id_espacio = h.id_espacio
            JOIN bloque bl ON bl.id_bloque = esp.id_bloque
            JOIN sede sede ON sede.id_sede = bl.id_sede
            LEFT JOIN auditoria_clase ac ON ac.id_horario = h.id_horario AND ac.fecha_auditoria = :fecha
            LEFT JOIN usuario u ON u.id_usuario = ac.id_usuario_auditor
            WHERE h.id_programa = :id_programa
              AND h.dia_semana = :dia_semana
              AND h.estado != 'ELIMINADO'
        ";

        $params = [
            ':id_programa' => (int)$idPrograma,
            ':dia_semana'  => $diaSemana,
            ':fecha'       => $fecha
        ];

        if ($idPeriodo) {
            $sql .= " AND h.id_periodo = :id_periodo";
            $params[':id_periodo'] = (int)$idPeriodo;
        }

        // Filtro por estado de auditoría si se especifica
        if ($filtroEstado === 'PENDIENTE') {
            $sql .= " AND (ac.id_auditoria IS NULL OR ac.resultado = 'PENDIENTE')";
        } elseif ($filtroEstado === 'AUDITADAS') {
            $sql .= " AND ac.resultado IN ('SIN_NOVEDAD', 'CON_NOVEDAD', 'VERIFICADA')";
        } elseif ($filtroEstado === 'SIN_NOVEDAD') {
            $sql .= " AND ac.resultado = 'SIN_NOVEDAD'";
        } elseif ($filtroEstado === 'CON_NOVEDAD') {
            $sql .= " AND ac.resultado = 'CON_NOVEDAD'";
        }

        $sql .= " ORDER BY h.hora_inicio ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enriquecer cada fila con su estado amigable
        foreach ($rows as &$row) {
            $row['estado_auditoria'] = self::calcularEstadoAuditoria($row['resultado'] ?? null);
        }

        return $rows;
    }

    /**
     * Determina el estado amigable de la auditoría: PENDIENTE, AUDITADA, AUDITADA_CON_NOVEDADES
     */
    public static function calcularEstadoAuditoria($resultado) {
        if (!$resultado || $resultado === 'PENDIENTE') {
            return 'PENDIENTE';
        }
        if ($resultado === 'CON_NOVEDAD') {
            return 'CON_NOVEDAD';
        }
        return 'SIN_NOVEDAD';
    }

    /**
     * Obtener una clase / horario específica con todos sus detalles para auditar o ver
     */
    public static function obtenerDetalleClase($idHorario) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT 
                h.id_horario,
                h.id_periodo,
                h.id_programa,
                h.id_asignatura,
                h.id_docente,
                h.id_espacio,
                h.dia_semana,
                h.hora_inicio,
                h.hora_fin,
                h.estado AS estado_horario,
                h.origen AS origen_horario,
                per.codigo AS periodo_codigo,
                prog.nombre AS programa_nombre,
                prog.codigo AS programa_codigo,
                fac.nombre AS facultad_nombre,
                asig.nombre AS asignatura_nombre,
                asig.codigo AS asignatura_codigo,
                asig.creditos AS asignatura_creditos,
                aa.nombre AS asignatura_area,
                CONCAT(doc.nombre, ' ', doc.apellido) AS docente_nombre,
                doc.codigo AS docente_codigo,
                doc.correo AS docente_correo,
                esp.nombre AS espacio_nombre,
                esp.codigo AS espacio_codigo,
                esp.tipo AS espacio_tipo,
                bl.nombre AS bloque_nombre,
                sede.nombre AS sede_nombre,
                sede.direccion AS sede_direccion
            FROM horario h
            JOIN periodo_academico per ON per.id_periodo = h.id_periodo
            JOIN programa prog ON prog.id_programa = h.id_programa
            JOIN facultad fac ON fac.id_facultad = prog.id_facultad
            JOIN asignatura asig ON asig.id_asignatura = h.id_asignatura
            LEFT JOIN area_academica aa ON aa.id_area_academica = asig.id_area_academica
            JOIN docente doc ON doc.id_docente = h.id_docente
            JOIN espacio esp ON esp.id_espacio = h.id_espacio
            JOIN bloque bl ON bl.id_bloque = esp.id_bloque
            JOIN sede sede ON sede.id_sede = bl.id_sede
            WHERE h.id_horario = :id
        ");
        $stmt->execute([':id' => (int)$idHorario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una auditoría registrada por ID con sus datos completos y checklist
     */
    public static function obtenerAuditoriaPorId($idAuditoria) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT 
                ac.*,
                h.dia_semana,
                h.hora_inicio,
                h.hora_fin,
                h.id_programa,
                h.id_periodo,
                per.codigo AS periodo_codigo,
                prog.nombre AS programa_nombre,
                prog.codigo AS programa_codigo,
                asig.nombre AS asignatura_nombre,
                asig.codigo AS asignatura_codigo,
                asig.creditos AS asignatura_creditos,
                CONCAT(doc.nombre, ' ', doc.apellido) AS docente_nombre,
                doc.codigo AS docente_codigo,
                esp.nombre AS espacio_nombre,
                esp.codigo AS espacio_codigo,
                bl.nombre AS bloque_nombre,
                sede.nombre AS sede_nombre,
                CONCAT(u.nombre, ' ', u.apellido) AS auditor_nombre,
                u.correo AS auditor_correo
            FROM auditoria_clase ac
            JOIN horario h ON h.id_horario = ac.id_horario
            JOIN periodo_academico per ON per.id_periodo = h.id_periodo
            JOIN programa prog ON prog.id_programa = h.id_programa
            JOIN asignatura asig ON asig.id_asignatura = h.id_asignatura
            JOIN docente doc ON doc.id_docente = h.id_docente
            JOIN espacio esp ON esp.id_espacio = h.id_espacio
            JOIN bloque bl ON bl.id_bloque = esp.id_bloque
            JOIN sede sede ON sede.id_sede = bl.id_sede
            JOIN usuario u ON u.id_usuario = ac.id_usuario_auditor
            WHERE ac.id_auditoria = :id
        ");
        $stmt->execute([':id' => (int)$idAuditoria]);
        $auditoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$auditoria) return null;

        // Obtener detalles del checklist
        $stmtDetalles = $db->prepare("
            SELECT * FROM auditoria_detalle 
            WHERE id_auditoria = :id 
            ORDER BY id_auditoria_detalle ASC
        ");
        $stmtDetalles->execute([':id' => (int)$idAuditoria]);
        $auditoria['detalles'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

        return $auditoria;
    }

    /**
     * Obtener una auditoría registrada para un horario en una fecha específica
     */
    public static function obtenerAuditoriaPorHorarioYFecha($idHorario, $fecha) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT id_auditoria FROM auditoria_clase 
            WHERE id_horario = :id_horario AND fecha_auditoria = :fecha
            LIMIT 1
        ");
        $stmt->execute([':id_horario' => (int)$idHorario, ':fecha' => $fecha]);
        $id = $stmt->fetchColumn();
        return $id ? self::obtenerAuditoriaPorId($id) : null;
    }

    /**
     * Registrar o actualizar una auditoría con su checklist y observaciones
     */
    public static function guardarAuditoria($datos, $checklist) {
        $db = Database::getConexion();
        $db->beginTransaction();

        try {
            $idHorario        = (int)$datos['id_horario'];
            $fechaAuditoria   = $datos['fecha_auditoria'];
            $idUsuarioAuditor = (int)$datos['id_usuario_auditor'];
            $resultado        = $datos['resultado']; // 'SIN_NOVEDAD' o 'CON_NOVEDAD'
            $observaciones    = trim($datos['observaciones'] ?? '');
            $horaVerificacion = date('H:i:s');

            // Verificar si ya existe una auditoría para este horario y fecha
            $stmtBuscar = $db->prepare("
                SELECT id_auditoria FROM auditoria_clase 
                WHERE id_horario = :id_horario AND fecha_auditoria = :fecha
            ");
            $stmtBuscar->execute([':id_horario' => $idHorario, ':fecha' => $fechaAuditoria]);
            $idExistente = $stmtBuscar->fetchColumn();

            if ($idExistente) {
                // Actualizar
                $idAuditoria = (int)$idExistente;
                $stmtUpdate = $db->prepare("
                    UPDATE auditoria_clase
                    SET id_usuario_auditor = :auditor,
                        resultado = :resultado,
                        observacion = :observacion,
                        hora_verificacion = :hora,
                        fecha_registro = CURRENT_TIMESTAMP
                    WHERE id_auditoria = :id
                ");
                $stmtUpdate->execute([
                    ':auditor'     => $idUsuarioAuditor,
                    ':resultado'   => $resultado,
                    ':observacion' => $observaciones,
                    ':hora'        => $horaVerificacion,
                    ':id'          => $idAuditoria
                ]);

                // Limpiar detalles anteriores para reinsertar el nuevo checklist
                $stmtDel = $db->prepare("DELETE FROM auditoria_detalle WHERE id_auditoria = :id");
                $stmtDel->execute([':id' => $idAuditoria]);
            } else {
                // Insertar nueva auditoría
                $stmtInsert = $db->prepare("
                    INSERT INTO auditoria_clase 
                    (id_horario, fecha_auditoria, hora_verificacion, id_usuario_auditor, resultado, observacion)
                    VALUES 
                    (:id_horario, :fecha, :hora, :auditor, :resultado, :observacion)
                ");
                $stmtInsert->execute([
                    ':id_horario'  => $idHorario,
                    ':fecha'       => $fechaAuditoria,
                    ':hora'        => $horaVerificacion,
                    ':auditor'     => $idUsuarioAuditor,
                    ':resultado'   => $resultado,
                    ':observacion' => $observaciones
                ]);
                $idAuditoria = (int)$db->lastInsertId();
            }

            // Insertar items del checklist
            $stmtItem = $db->prepare("
                INSERT INTO auditoria_detalle (id_auditoria, id_criterio, criterio, cumple, observacion)
                VALUES (:id_auditoria, :id_criterio, :criterio, :cumple, :observacion)
            ");

            foreach ($checklist as $item) {
                $stmtItem->execute([
                    ':id_auditoria' => $idAuditoria,
                    ':id_criterio'  => !empty($item['id_criterio']) ? (int)$item['id_criterio'] : null,
                    ':criterio'     => trim($item['criterio']),
                    ':cumple'       => !empty($item['cumple']) ? 1 : 0,
                    ':observacion'  => !empty($item['observacion']) ? trim($item['observacion']) : null
                ]);
            }

            $db->commit();
            return $idAuditoria;
        } catch (\Throwable $e) {
            $db->rollBack();
            error_log("Error al guardar auditoria: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener el historial de auditorías realizadas para un programa (o programas del líder),
     * con soporte para filtros de fecha, asignatura, docente, resultado y periodo.
     */
    public static function obtenerHistorial($idPrograma, $filtros = []) {
        $db = Database::getConexion();
        $sql = "
            SELECT 
                ac.id_auditoria,
                ac.id_horario,
                ac.fecha_auditoria,
                ac.hora_verificacion,
                ac.resultado,
                ac.observacion,
                ac.fecha_registro,
                h.dia_semana,
                h.hora_inicio,
                h.hora_fin,
                h.id_programa,
                prog.nombre AS programa_nombre,
                prog.codigo AS programa_codigo,
                asig.nombre AS asignatura_nombre,
                asig.codigo AS asignatura_codigo,
                CONCAT(doc.nombre, ' ', doc.apellido) AS docente_nombre,
                doc.codigo AS docente_codigo,
                esp.nombre AS espacio_nombre,
                bl.nombre AS bloque_nombre,
                sede.nombre AS sede_nombre,
                per.codigo AS periodo_codigo,
                CONCAT(u.nombre, ' ', u.apellido) AS auditor_nombre
            FROM auditoria_clase ac
            JOIN horario h ON h.id_horario = ac.id_horario
            JOIN periodo_academico per ON per.id_periodo = h.id_periodo
            JOIN programa prog ON prog.id_programa = h.id_programa
            JOIN asignatura asig ON asig.id_asignatura = h.id_asignatura
            JOIN docente doc ON doc.id_docente = h.id_docente
            JOIN espacio esp ON esp.id_espacio = h.id_espacio
            JOIN bloque bl ON bl.id_bloque = esp.id_bloque
            JOIN sede sede ON sede.id_sede = bl.id_sede
            JOIN usuario u ON u.id_usuario = ac.id_usuario_auditor
            WHERE h.id_programa = :programa
        ";
        $params = [':programa' => (int)$idPrograma];

        if (!empty($filtros['id_periodo'])) {
            $sql .= " AND h.id_periodo = :id_periodo";
            $params[':id_periodo'] = (int)$filtros['id_periodo'];
        }

        if (!empty($filtros['fecha'])) {
            $sql .= " AND ac.fecha_auditoria = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }

        if (!empty($filtros['resultado'])) {
            $sql .= " AND ac.resultado = :resultado";
            $params[':resultado'] = $filtros['resultado'];
        }

        if (!empty($filtros['asignatura'])) {
            $sql .= " AND (asig.nombre LIKE :asignatura OR asig.codigo LIKE :asignatura)";
            $params[':asignatura'] = '%' . $filtros['asignatura'] . '%';
        }

        if (!empty($filtros['docente'])) {
            $sql .= " AND (doc.nombre LIKE :docente OR doc.apellido LIKE :docente OR CONCAT(doc.nombre, ' ', doc.apellido) LIKE :docente)";
            $params[':docente'] = '%' . $filtros['docente'] . '%';
        }

        $sql .= " ORDER BY ac.fecha_auditoria DESC, h.hora_inicio ASC, ac.id_auditoria DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as &$f) {
            $f['estado_auditoria'] = self::calcularEstadoAuditoria($f['resultado']);
        }

        return $filas;
    }
}
