<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Auditoria;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;

class AuditoriaController extends Controller {
    /**
     * Pantalla principal de auditoría:
     * - Identifica programas bajo la responsabilidad del líder
     * - Carga fecha actual o seleccionada
     * - Obtiene clases programadas del horario cruzadas con su estado de auditoría
     * - Filtra por estado (Todas, Pendientes, Auditadas sin novedades, Auditadas con novedades)
     */
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programasLider = LiderPanel::programasDelLider($usuario['id_usuario']);
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();

        // Fecha seleccionada o fecha actual
        $fechaSeleccionada = trim($_GET['fecha'] ?? date('Y-m-d'));
        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
            $fechaSeleccionada = date('Y-m-d');
        }

        $filtroEstado = trim($_GET['estado'] ?? '');
        $idPeriodo = (int)($_GET['periodo_id'] ?? ($periodoActivo['id_periodo'] ?? 0));

        $clases = [];
        $resumenEstados = [
            'total'         => 0,
            'pendientes'    => 0,
            'sin_novedades' => 0,
            'con_novedades' => 0
        ];

        if ($programaActivo) {
            // Obtener todas las clases programadas para calcular métricas
            $todasClases = Auditoria::obtenerClasesParaAuditar(
                $programaActivo['id_programa'],
                $fechaSeleccionada,
                $idPeriodo ?: null,
                ''
            );

            foreach ($todasClases as $c) {
                $resumenEstados['total']++;
                if ($c['estado_auditoria'] === 'PENDIENTE') {
                    $resumenEstados['pendientes']++;
                } elseif ($c['estado_auditoria'] === 'CON_NOVEDAD') {
                    $resumenEstados['con_novedades']++;
                } else {
                    $resumenEstados['sin_novedades']++;
                }
            }

            // Clases filtradas por el estado solicitado
            if ($filtroEstado) {
                $clases = Auditoria::obtenerClasesParaAuditar(
                    $programaActivo['id_programa'],
                    $fechaSeleccionada,
                    $idPeriodo ?: null,
                    $filtroEstado
                );
            } else {
                $clases = $todasClases;
            }
        }

        $diaSemanaNombre = Auditoria::obtenerDiaSemanaEsp($fechaSeleccionada);

        $this->render('lider/auditoria/index', [
            'titulo'            => 'Auditoría de Clases',
            'usuario'           => $usuario,
            'programasLider'    => $programasLider,
            'programaActivo'    => $programaActivo,
            'periodoActivo'     => $periodoActivo,
            'fechaSeleccionada' => $fechaSeleccionada,
            'diaSemanaNombre'   => $diaSemanaNombre,
            'filtroEstado'      => $filtroEstado,
            'clases'            => $clases,
            'resumenEstados'    => $resumenEstados
        ]);
    }

    /**
     * Pantalla para auditar una clase específica:
     * - Valida pertenencia de la clase al programa del líder
     * - Muestra datos del horario
     * - Carga lista de verificación (checklist) desmarcado
     * - Controles de resultado y observaciones
     */
    public function auditar() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $idHorario = (int)($_GET['id_horario'] ?? 0);
        $fecha = trim($_GET['fecha'] ?? date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }

        if (!$idHorario) {
            $this->alertAndBack('error', 'Clase no especificada', 'No se indicó una clase para auditar.');
        }

        $clase = Auditoria::obtenerDetalleClase($idHorario);
        if (!$clase) {
            $this->alertAndBack('error', 'Clase no encontrada', 'La clase programada no existe en el sistema.');
        }

        // Validación estricta de seguridad: ¿El líder tiene bajo su responsabilidad este programa?
        if (!LiderPanel::assertPrograma($usuario['id_usuario'], $clase['id_programa'])) {
            $this->alertAndBack('error', 'Acceso no autorizado', 'No tienes autorización para auditar clases de este programa.');
        }

        // Obtener criterios del sistema
        $criterios = Auditoria::obtenerCriterios();

        // Verificar si ya existe una auditoría previa para esa clase en esa fecha
        $auditoriaExistente = Auditoria::obtenerAuditoriaPorHorarioYFecha($idHorario, $fecha);

        $this->render('lider/auditoria/form', [
            'titulo'             => 'Auditar Clase',
            'clase'              => $clase,
            'fecha'              => $fecha,
            'criterios'          => $criterios,
            'auditoriaExistente' => $auditoriaExistente,
            'usuario'            => $usuario
        ]);
    }

    /**
     * Procesar y guardar la auditoría:
     * - Valida seguridad por backend (líder y pertenencia al programa)
     * - Valida que si el resultado es CON_NOVEDAD, las observaciones sean obligatorias
     * - Registra el auditor autenticado y la fecha/hora del sistema
     */
    public function guardar() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=lider/auditoria');
        }

        $usuario = Auth::getUsuarioActual();
        $idHorario = (int)($_POST['id_horario'] ?? 0);
        $fecha = trim($_POST['fecha_auditoria'] ?? date('Y-m-d'));
        $resultado = trim($_POST['resultado'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');

        if (!$idHorario) {
            $this->alertAndBack('error', 'Datos incompletos', 'Falta el identificador de la clase.');
        }

        $clase = Auditoria::obtenerDetalleClase($idHorario);
        if (!$clase) {
            $this->alertAndBack('error', 'Clase no encontrada', 'La clase no existe en el horario.');
        }

        // Validación estricta de backend
        if (!LiderPanel::assertPrograma($usuario['id_usuario'], $clase['id_programa'])) {
            $this->alertAndBack('error', 'Acceso denegado', 'Esta clase no pertenece a ninguno de tus programas asignados.');
        }

        // Validar resultado
        if (!in_array($resultado, ['SIN_NOVEDAD', 'CON_NOVEDAD'], true)) {
            $this->alertAndBack('warning', 'Resultado requerido', 'Debes seleccionar si la clase se desarrolló sin novedades o con novedades.');
        }

        // Validación de observaciones obligatorias cuando hay novedades
        if ($resultado === 'CON_NOVEDAD' && empty($observaciones)) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'warning',
                'title' => 'Observación requerida',
                'text'  => 'Debes registrar la observación encontrada al auditar con novedades.'
            ];
            $this->redirect("?ruta=lider/auditoria/auditar&id_horario={$idHorario}&fecha={$fecha}");
        }

        // Procesar checklist enviado
        $checklistInput = $_POST['criterios'] ?? [];
        $criteriosMaster = Auditoria::obtenerCriterios();
        $checklistParaGuardar = [];

        foreach ($criteriosMaster as $cm) {
            $idC = (int)$cm['id_criterio'];
            $cumple = isset($checklistInput[$idC]['cumple']) && $checklistInput[$idC]['cumple'] == '1';
            $obsItem = trim($checklistInput[$idC]['observacion'] ?? '');

            $checklistParaGuardar[] = [
                'id_criterio' => $idC,
                'criterio'    => $cm['descripcion'],
                'cumple'      => $cumple ? 1 : 0,
                'observacion' => $obsItem
            ];
        }

        $datosAuditoria = [
            'id_horario'         => $idHorario,
            'fecha_auditoria'    => $fecha,
            'id_usuario_auditor' => $usuario['id_usuario'],
            'resultado'          => $resultado,
            'observaciones'      => $observaciones
        ];

        $idAuditoria = Auditoria::guardarAuditoria($datosAuditoria, $checklistParaGuardar);

        if ($idAuditoria) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'success',
                'title' => 'Auditoría registrada',
                'text'  => $resultado === 'CON_NOVEDAD' 
                    ? 'La auditoría fue registrada con novedades exitosamente.' 
                    : 'La auditoría fue registrada sin novedades exitosamente.'
            ];
        } else {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error al guardar',
                'text'  => 'No se pudo guardar la auditoría. Intenta nuevamente.'
            ];
        }

        $this->redirect("?ruta=lider/auditoria&fecha={$fecha}&programa_id={$clase['id_programa']}");
    }

    /**
     * Visualizar el detalle de una auditoría realizada
     */
    public function detalle() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $idAuditoria = (int)($_GET['id'] ?? 0);

        if (!$idAuditoria) {
            $this->alertAndBack('error', 'Auditoría no encontrada', 'No se especificó la auditoría.');
        }

        $auditoria = Auditoria::obtenerAuditoriaPorId($idAuditoria);
        if (!$auditoria) {
            $this->alertAndBack('error', 'No encontrada', 'El registro de auditoría no existe.');
        }

        // Validar alcance
        if (!LiderPanel::assertPrograma($usuario['id_usuario'], $auditoria['id_programa'])) {
            $this->alertAndBack('error', 'Acceso denegado', 'No tienes autorización para ver auditorías de este programa.');
        }

        $this->render('lider/auditoria/detalle', [
            'titulo'    => 'Detalle de Auditoría',
            'auditoria' => $auditoria,
            'usuario'   => $usuario
        ]);
    }

    /**
     * Sección de Historial de Auditorías con filtros
     */
    public function historial() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programasLider = LiderPanel::programasDelLider($usuario['id_usuario']);
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();

        $filtros = [
            'fecha'      => trim($_GET['fecha'] ?? ''),
            'resultado'  => trim($_GET['resultado'] ?? ''),
            'asignatura' => trim($_GET['asignatura'] ?? ''),
            'docente'    => trim($_GET['docente'] ?? ''),
            'id_periodo' => (int)($_GET['periodo_id'] ?? ($periodoActivo['id_periodo'] ?? 0))
        ];

        $auditorias = [];
        if ($programaActivo) {
            $auditorias = Auditoria::obtenerHistorial($programaActivo['id_programa'], $filtros);
        }

        $this->render('lider/auditoria/historial', [
            'titulo'         => 'Historial de Auditorías',
            'programaActivo' => $programaActivo,
            'programasLider' => $programasLider,
            'periodoActivo'  => $periodoActivo,
            'filtros'        => $filtros,
            'auditorias'     => $auditorias
        ]);
    }

    private function alertAndBack($icon, $title, $text) {
        $_SESSION['swal_alerta'] = ['icon' => $icon, 'title' => $title, 'text' => $text];
        $this->redirect('?ruta=lider/auditoria');
    }
}
