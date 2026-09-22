<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Horario;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;
use App\Config\Database;
use PDO;

class HorarioController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $periodoActivo = PeriodoAcademico::getActivo();
        $periodos = PeriodoAcademico::obtenerTodos(1);
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $tab = $_GET['tab'] ?? 'general';
        $idPeriodo = (int)($_GET['periodo_id'] ?? ($periodoActivo['id_periodo'] ?? 0));
        $detalleEditar = null;
        if (!empty($_GET['editar_detalle']) && $programaActivo) {
            $detalleEditar = LiderPanel::detallePropuesta($programaActivo['id_programa'], (int)$_GET['editar_detalle']);
        }

        $this->render('lider/horarios', [
            'titulo' => 'Horarios del Programa',
            'tab' => in_array($tab, ['general', 'fet', 'propuestas', 'auditoria'], true) ? $tab : 'general',
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'periodos' => $periodos,
            'idPeriodo' => $idPeriodo,
            'horarios' => $programaActivo ? LiderPanel::horarios($programaActivo['id_programa'], $idPeriodo ?: null) : [],
            'cerrado' => $programaActivo && $idPeriodo ? LiderPanel::horarioCerrado($programaActivo['id_programa'], $idPeriodo) : false,
            'propuestas' => $programaActivo ? LiderPanel::propuestas($programaActivo['id_programa'], $idPeriodo ?: null) : [],
            'detallesPropuestas' => $programaActivo ? LiderPanel::detallesPropuestas($programaActivo['id_programa']) : [],
            'auditorias' => $programaActivo ? LiderPanel::auditorias($programaActivo['id_programa'], [
                'fecha' => $_GET['fecha'] ?? '',
                'sede' => $_GET['sede'] ?? '',
                'bloque' => $_GET['bloque'] ?? '',
                'espacio' => $_GET['espacio'] ?? '',
                'docente' => $_GET['docente'] ?? ''
            ]) : [],
            'detalleEditar' => $detalleEditar
        ]);
    }

    public function guardar() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=lider/horarios');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_POST['id_programa'] ?? null);
        if (!$programaActivo) $this->alertAndBack('error', 'Sin programa asignado', 'No tienes un programa activo para guardar horarios.');

        $idPeriodo = (int)($_POST['id_periodo'] ?? 0);
        $idAsignatura = (int)($_POST['id_asignatura'] ?? 0);
        $idDocente = (int)($_POST['id_docente'] ?? 0);
        $idEspacio = (int)($_POST['id_espacio'] ?? 0);
        $dia = trim($_POST['dia_semana'] ?? '');
        $inicio = trim($_POST['hora_inicio'] ?? '');
        $fin = trim($_POST['hora_fin'] ?? '');
        $origen = trim($_POST['origen'] ?? 'MANUAL');
        $idDetalle = (int)($_POST['id_detalle'] ?? 0);

        if (!$idPeriodo || !$idAsignatura || !$idDocente || !$idEspacio || !$dia || !$inicio || !$fin) {
            $this->alertAndBack('error', 'Campos incompletos', 'Completa todos los datos para asignar la clase.');
        }
        if (LiderPanel::horarioCerrado($programaActivo['id_programa'], $idPeriodo)) {
            $this->alertAndBack('warning', 'Horario cerrado', 'Este programa y periodo ya esta cerrado para el lider.');
        }
        if (strtotime($inicio) >= strtotime($fin)) {
            $this->alertAndBack('error', 'Rango invalido', 'La hora de fin debe ser posterior a la hora de inicio.');
        }
        if (!LiderPanel::detallePropuesta($programaActivo['id_programa'], $idDetalle) && $idDetalle) {
            $this->alertAndBack('error', 'Propuesta invalida', 'La linea FET no pertenece a tu programa.');
        }
        if (!$this->asignaturaPermitida($programaActivo['id_programa'], $idPeriodo, $idAsignatura) || !$this->docentePermitido($programaActivo['id_programa'], $idAsignatura, $idDocente)) {
            $this->alertAndBack('error', 'Datos fuera de alcance', 'La asignatura o el docente no pertenecen a tu programa.');
        }
        if (Horario::verificarSolapamientoDocente($idPeriodo, $dia, $idDocente, $inicio, $fin)) {
            $this->alertAndBack('error', 'Conflicto de docente', 'El docente ya tiene una clase asignada en ese rango.');
        }
        if (Horario::verificarSolapamientoEspacio($idPeriodo, $dia, $idEspacio, $inicio, $fin)) {
            $this->alertAndBack('error', 'Conflicto de espacio', 'El espacio ya esta ocupado en ese rango.');
        }

        $idHorario = Horario::crear([
            'id_periodo' => $idPeriodo,
            'id_programa' => $programaActivo['id_programa'],
            'id_asignatura' => $idAsignatura,
            'id_docente' => $idDocente,
            'id_espacio' => $idEspacio,
            'dia_semana' => $dia,
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
            'estado' => $origen === 'FET' ? 'CONFIRMADO' : 'BORRADOR',
            'origen' => $origen,
            'id_usuario_creador' => $usuario['id_usuario']
        ]);
        if ($idHorario && $idDetalle) {
            $db = Database::getConexion();
            $db->prepare("UPDATE propuesta_fet_detalle SET id_horario_resultante = :horario WHERE id_detalle = :detalle")
                ->execute([':horario' => $idHorario, ':detalle' => $idDetalle]);
        }
        $_SESSION['swal_alerta'] = $idHorario
            ? ['icon' => 'success', 'title' => 'Clase guardada', 'text' => 'La clase fue registrada dentro del alcance de tu programa.']
            : ['icon' => 'error', 'title' => 'Error al guardar', 'text' => 'No se pudo crear el horario.'];
        $this->redirect('?ruta=lider/horarios');
    }

    public function cerrar() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $idPeriodo = (int)($_GET['periodo_id'] ?? 0);
        if ($programaActivo && $idPeriodo) {
            LiderPanel::cerrarHorario($programaActivo['id_programa'], $idPeriodo, $usuario['id_usuario']);
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Horario cerrado', 'text' => 'El periodo quedo cerrado para tu programa.'];
        }
        $this->redirect('?ruta=lider/horarios');
    }

    public function generarFet() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=lider/horarios&tab=fet');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_POST['id_programa'] ?? null);
        $idPeriodo = (int)($_POST['id_periodo'] ?? 0);
        if (!$programaActivo || !$idPeriodo) $this->alertAndBack('error', 'Parametros incompletos', 'Selecciona periodo y programa activo.');

        $asignaturas = LiderPanel::asignaturasOfertadas($programaActivo['id_programa'], $idPeriodo);
        $espacios = Database::getConexion()->query("SELECT id_espacio FROM espacio WHERE estado = 'DISPONIBLE' ORDER BY id_espacio")->fetchAll(PDO::FETCH_COLUMN);
        if (empty($asignaturas) || empty($espacios)) $this->alertAndBack('warning', 'Recursos insuficientes', 'Faltan asignaturas ofertadas o espacios disponibles.');

        $dias = $_POST['dias'] ?? ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'];
        $slots = [['07:00:00','09:00:00'], ['09:00:00','11:00:00'], ['11:00:00','13:00:00'], ['14:00:00','16:00:00'], ['16:00:00','18:00:00']];
        $detalles = [];
        foreach ($asignaturas as $i => $asignatura) {
            $compatibles = LiderPanel::docentesCompatibles($programaActivo['id_programa'], $asignatura['id_asignatura']);
            if (empty($compatibles)) continue;
            $slot = $slots[$i % count($slots)];
            $detalles[] = [
                'id_asignatura' => $asignatura['id_asignatura'],
                'id_docente' => $compatibles[0]['id_docente'],
                'id_espacio' => $espacios[$i % count($espacios)],
                'dia_semana' => $dias[$i % count($dias)],
                'hora_inicio' => $slot[0],
                'hora_fin' => $slot[1]
            ];
        }
        if (empty($detalles)) $this->alertAndBack('warning', 'Sin docentes compatibles', 'No hay docentes compatibles vinculados a tu programa.');

        Horario::guardarPropuestaFet($idPeriodo, $programaActivo['id_programa'], $usuario['id_usuario'], [
            'algoritmo' => 'FET Timetabling Engine 6.x (Simulado)',
            'comentario' => 'TODO: integracion real con FET',
            'generado_en' => date('Y-m-d H:i:s')
        ], $detalles);
        $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Propuesta FET generada', 'text' => count($detalles) . ' lineas quedaron listas para revision.'];
        $this->redirect('?ruta=lider/horarios&tab=propuestas');
    }

    public function aceptarFet() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $resultado = $programaActivo ? LiderPanel::aceptarDetalleFet($programaActivo['id_programa'], (int)($_GET['id'] ?? 0), $usuario['id_usuario']) : false;
        $mensajes = [
            'SOLAP_DOCENTE' => ['error', 'Conflicto de docente', 'El docente ya tiene clase en ese rango.'],
            'SOLAP_ESPACIO' => ['error', 'Conflicto de espacio', 'El espacio ya esta ocupado en ese rango.']
        ];
        if (isset($mensajes[$resultado])) {
            [$icon, $title, $text] = $mensajes[$resultado];
            $_SESSION['swal_alerta'] = ['icon' => $icon, 'title' => $title, 'text' => $text];
        } else {
            $_SESSION['swal_alerta'] = $resultado
                ? ['icon' => 'success', 'title' => 'Linea aceptada', 'text' => 'Se creo un horario CONFIRMADO con origen FET.']
                : ['icon' => 'error', 'title' => 'No se pudo aceptar', 'text' => 'La linea no pertenece a tu programa o ya fue procesada.'];
        }
        $this->redirect('?ruta=lider/horarios&tab=propuestas');
    }

    public function descartarFet() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_POST['programa_id'] ?? null);
        $ok = $programaActivo ? LiderPanel::descartarDetalleFet($programaActivo['id_programa'], (int)($_POST['id_detalle'] ?? 0)) : false;
        $_SESSION['swal_alerta'] = $ok
            ? ['icon' => 'success', 'title' => 'Linea descartada', 'text' => 'La linea FET fue retirada antes de crear horario.']
            : ['icon' => 'error', 'title' => 'No se pudo descartar', 'text' => 'La linea no pertenece a tu programa o ya fue procesada.'];
        $this->redirect('?ruta=lider/horarios&tab=propuestas');
    }

    public function verificarAuditoria() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=lider/horarios&tab=auditoria');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_POST['programa_id'] ?? null);
        $idAuditoria = (int)($_POST['id_auditoria'] ?? 0);
        $resultado = trim($_POST['resultado'] ?? '');
        if ($programaActivo && LiderPanel::auditoriaPertenece($programaActivo['id_programa'], $idAuditoria) && in_array($resultado, ['VERIFICADA', 'NO_REALIZADA'], true)) {
            Horario::verificarAuditoria($idAuditoria, $resultado, trim($_POST['observacion'] ?? ''));
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Auditoria registrada', 'text' => 'El resultado quedo guardado.'];
        }
        $this->redirect('?ruta=lider/horarios&tab=auditoria');
    }

    public function apiAsignaturas() {
        Auth::requerirRol('LIDER_PROGRAMA');
        header('Content-Type: application/json');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        echo json_encode($programaActivo ? LiderPanel::asignaturasOfertadas($programaActivo['id_programa'], (int)($_GET['periodo_id'] ?? 0)) : []);
        exit;
    }

    public function apiDocentes() {
        Auth::requerirRol('LIDER_PROGRAMA');
        header('Content-Type: application/json');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        echo json_encode($programaActivo ? LiderPanel::docentesCompatibles($programaActivo['id_programa'], (int)($_GET['asignatura_id'] ?? 0)) : []);
        exit;
    }

    public function apiEspaciosLibres() {
        Auth::requerirRol('LIDER_PROGRAMA');
        header('Content-Type: application/json');
        $idPeriodo = (int)($_GET['periodo_id'] ?? 0);
        $dia = trim($_GET['dia_semana'] ?? '');
        $inicio = trim($_GET['hora_inicio'] ?? '');
        $fin = trim($_GET['hora_fin'] ?? '');
        echo json_encode($idPeriodo && $dia && $inicio && $fin ? Horario::obtenerEspaciosLibres($idPeriodo, $dia, $inicio, $fin) : []);
        exit;
    }

    private function asignaturaPermitida($idPrograma, $idPeriodo, $idAsignatura) {
        foreach (LiderPanel::asignaturasOfertadas($idPrograma, $idPeriodo) as $asignatura) {
            if ((int)$asignatura['id_asignatura'] === (int)$idAsignatura) return true;
        }
        return false;
    }

    private function docentePermitido($idPrograma, $idAsignatura, $idDocente) {
        foreach (LiderPanel::docentesCompatibles($idPrograma, $idAsignatura) as $docente) {
            if ((int)$docente['id_docente'] === (int)$idDocente) return true;
        }
        return false;
    }

    private function alertAndBack($icon, $title, $text) {
        $_SESSION['swal_alerta'] = ['icon' => $icon, 'title' => $title, 'text' => $text];
        $this->redirect('?ruta=lider/horarios');
    }
}
