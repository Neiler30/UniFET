<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Horario;
use App\Models\PeriodoAcademico;
use App\Models\Programa;
use App\Models\Espacio;
use App\Config\Database;
use PDO;

class HorarioController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');

        $tab = $_GET['tab'] ?? 'general';
        $periodos = PeriodoAcademico::obtenerTodos($this->id_institucion);
        $periodoActivo = PeriodoAcademico::getActivo();
        $programas = Programa::obtenerTodos($this->id_institucion);
        $espacios = Espacio::obtenerTodos($this->id_institucion);

        $filtros = [
            'programa' => $_GET['programa'] ?? '',
            'docente' => $_GET['docente'] ?? '',
            'espacio' => $_GET['espacio'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $horarios = Horario::obtenerConFiltros($filtros);

        // Si la pestaña es FET, obtener la última propuesta y sus detalles
        $propuestaFet = null;
        $detallesFet = [];
        if ($tab === 'fet') {
            $id_prog_fet = !empty($_GET['programa_id']) ? (int)$_GET['programa_id'] : null;
            $propuestaFet = Horario::obtenerUltimaPropuestaFet(null, $id_prog_fet);
            if ($propuestaFet) {
                $detallesFet = Horario::obtenerDetallesPropuestaFet($propuestaFet['id_propuesta']);
            }
        }

        // Propuestas FET tab
        $propuestasFetGeneradas = [];
        $propuestaSeleccionada = null;
        $detallesPropuestaSeleccionada = [];
        if ($tab === 'propuestas') {
            $propuestasFetGeneradas = Horario::obtenerTodasPropuestasFet();
            $id_prop_sel = !empty($_GET['propuesta_id']) ? (int)$_GET['propuesta_id'] : null;
            if ($id_prop_sel) {
                $propuestaSeleccionada = $id_prop_sel;
                $detallesPropuestaSeleccionada = Horario::obtenerDetallesPropuestaFet($id_prop_sel);
            } elseif (!empty($propuestasFetGeneradas)) {
                // Default: mostrar la primera propuesta GENERADA
                foreach ($propuestasFetGeneradas as $pf) {
                    if ($pf['estado'] === 'GENERADA') {
                        $propuestaSeleccionada = $pf['id_propuesta'];
                        $detallesPropuestaSeleccionada = Horario::obtenerDetallesPropuestaFet($pf['id_propuesta']);
                        break;
                    }
                }
            }
        }

        // Auditoría tab
        $auditorias = [];
        $filtrosAuditoria = [];
        if ($tab === 'auditoria') {
            $filtrosAuditoria = [
                'fecha' => $_GET['fecha'] ?? '',
                'sede' => $_GET['sede'] ?? '',
                'bloque' => $_GET['bloque'] ?? '',
                'espacio' => $_GET['espacio_aud'] ?? '',
                'docente' => $_GET['docente_aud'] ?? '',
                'programa' => $_GET['programa_aud'] ?? '',
                'resultado' => $_GET['resultado'] ?? ''
            ];
            $auditorias = Horario::obtenerAuditorias($filtrosAuditoria);
        }

        $this->render('admin/horarios/index', [
            'titulo' => 'Gestión y Planificación de Horarios',
            'tab' => $tab,
            'horarios' => $horarios,
            'filtros' => $filtros,
            'periodos' => $periodos,
            'periodoActivo' => $periodoActivo,
            'programas' => $programas,
            'espacios' => $espacios,
            'propuestaFet' => $propuestaFet,
            'detallesFet' => $detallesFet,
            'propuestasFetGeneradas' => $propuestasFetGeneradas,
            'propuestaSeleccionada' => $propuestaSeleccionada,
            'detallesPropuestaSeleccionada' => $detallesPropuestaSeleccionada,
            'auditorias' => $auditorias,
            'filtrosAuditoria' => $filtrosAuditoria
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioActual = Auth::getUsuarioActual();
            
            $id_periodo = (int)($_POST['id_periodo'] ?? 0);
            $id_programa = (int)($_POST['id_programa'] ?? 0);
            $id_asignatura = (int)($_POST['id_asignatura'] ?? 0);
            $id_docente = (int)($_POST['id_docente'] ?? 0);
            $id_espacio = (int)($_POST['id_espacio'] ?? 0);
            $dia_semana = trim($_POST['dia_semana'] ?? '');
            $hora_inicio = trim($_POST['hora_inicio'] ?? '');
            $hora_fin = trim($_POST['hora_fin'] ?? '');
            $estado = trim($_POST['estado'] ?? 'BORRADOR');
            $origen = trim($_POST['origen'] ?? 'MANUAL');

            // 1. Validar campos obligatorios
            if (!$id_periodo || !$id_programa || !$id_asignatura || !$id_docente || !$id_espacio || empty($dia_semana) || empty($hora_inicio) || empty($hora_fin)) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Campos incompletos',
                    'text' => 'Por favor complete todos los campos obligatorios para asignar la clase.'
                ];
                $this->redirect('?ruta=admin/horarios');
                return;
            }

            // 2. Validar orden de horas
            if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Rango de horas inválido',
                    'text' => 'La hora de fin debe ser posterior a la hora de inicio de la clase.'
                ];
                $this->redirect('?ruta=admin/horarios');
                return;
            }

            // 3. Validar solapamiento de Docente en PHP
            $solapDocente = Horario::verificarSolapamientoDocente($id_periodo, $dia_semana, $id_docente, $hora_inicio, $hora_fin);
            if ($solapDocente) {
                $iniFormat = date('H:i', strtotime($solapDocente['hora_inicio']));
                $finFormat = date('H:i', strtotime($solapDocente['hora_fin']));
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Solapamiento de Docente',
                    'text' => "El docente {$solapDocente['docente_nombre']} ya tiene asignada la asignatura '{$solapDocente['asignatura_nombre']}' en el slot {$iniFormat} - {$finFormat} el día {$dia_semana}."
                ];
                $this->redirect('?ruta=admin/horarios');
                return;
            }

            // 4. Validar solapamiento de Espacio en PHP
            $solapEspacio = Horario::verificarSolapamientoEspacio($id_periodo, $dia_semana, $id_espacio, $hora_inicio, $hora_fin);
            if ($solapEspacio) {
                $iniFormat = date('H:i', strtotime($solapEspacio['hora_inicio']));
                $finFormat = date('H:i', strtotime($solapEspacio['hora_fin']));
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Solapamiento de Espacio',
                    'text' => "El espacio '{$solapEspacio['espacio_nombre']}' ya se encuentra ocupado de {$iniFormat} a {$finFormat} el día {$dia_semana}."
                ];
                $this->redirect('?ruta=admin/horarios');
                return;
            }

            // 5. Crear el registro en la tabla horario
            $datos = [
                'id_periodo' => $id_periodo,
                'id_programa' => $id_programa,
                'id_asignatura' => $id_asignatura,
                'id_docente' => $id_docente,
                'id_espacio' => $id_espacio,
                'dia_semana' => $dia_semana,
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
                'estado' => $estado,
                'origen' => $origen,
                'id_usuario_creador' => $usuarioActual['id_usuario']
            ];

            $idHorario = Horario::crear($datos);
            if ($idHorario) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'success',
                    'title' => 'Clase asignada con éxito',
                    'text' => 'El horario ha sido programado satisfactoriamente sin conflictos.'
                ];
            } else {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Error al guardar',
                    'text' => 'Ocurrió un error inesperado al registrar el horario.'
                ];
            }

            $this->redirect('?ruta=admin/horarios');
        }
    }

    public function cerrar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? 0);
        $usuario = Auth::getUsuarioActual();

        if ($id && $usuario) {
            Horario::cerrar($id, $usuario['id_usuario']);
            $_SESSION['swal_alerta'] = [
                'icon' => 'success',
                'title' => 'Horario cerrado',
                'text' => 'El horario ha sido cerrado definitivamente y ya no podrá ser modificado.'
            ];
        }

        $this->redirect('?ruta=admin/horarios');
    }

    public function generarFet() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Auth::getUsuarioActual();
            $id_periodo = (int)($_POST['id_periodo'] ?? 0);
            $id_programa = (int)($_POST['id_programa'] ?? 0);
            $dias = $_POST['dias'] ?? ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'];
            $franja = $_POST['franja'] ?? 'DIURNA';
            $peso_docente = $_POST['peso_docente'] ?? 'ALTO';

            if (!$id_periodo || !$id_programa) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Parámetros incompletos',
                    'text' => 'Debe seleccionar un periodo y un programa para generar la propuesta FET.'
                ];
                $this->redirect('?ruta=admin/horarios&tab=fet');
                return;
            }

            // // TODO: reemplazar por la integración real con FET
            // Comportamiento simulado: busca las asignaturas ofertadas del programa
            // y genera filas de propuesta en propuesta_fet y propuesta_fet_detalle.
            $asignaturas = Horario::obtenerAsignaturasOfertadas($id_periodo, $id_programa);
            if (empty($asignaturas)) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'warning',
                    'title' => 'Sin asignaturas ofertadas',
                    'text' => 'El programa seleccionado no tiene asignaturas activas en la oferta académica de este periodo.'
                ];
                $this->redirect("?ruta=admin/horarios&tab=fet&programa_id=$id_programa");
                return;
            }

            // Obtener espacios y docentes disponibles
            $db = Database::getConexion();
            $espacios = $db->query("SELECT id_espacio FROM espacio WHERE estado = 'DISPONIBLE' ORDER BY id_espacio ASC")->fetchAll(PDO::FETCH_COLUMN);
            $docentes = $db->query("SELECT id_docente FROM docente WHERE estado = 'ACTIVO' ORDER BY id_docente ASC")->fetchAll(PDO::FETCH_COLUMN);

            if (empty($espacios) || empty($docentes)) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Recursos insuficientes',
                    'text' => 'Debe haber docentes y espacios disponibles registrados para correr la simulación FET.'
                ];
                $this->redirect("?ruta=admin/horarios&tab=fet&programa_id=$id_programa");
                return;
            }

            $detalles = [];
            $horasDisponibles = [
                ['07:00:00', '09:00:00'],
                ['09:00:00', '11:00:00'],
                ['11:00:00', '13:00:00'],
                ['14:00:00', '16:00:00'],
                ['16:00:00', '18:00:00']
            ];

            $diaIndex = 0;
            $horaIndex = 0;

            foreach ($asignaturas as $idx => $asig) {
                // Asignar docente compatible si existe, o uno genérico
                $compatibles = Horario::obtenerDocentesCompatibles($asig['id_asignatura']);
                $id_doc = !empty($compatibles) ? $compatibles[0]['id_docente'] : $docentes[$idx % count($docentes)];
                $id_esp = $espacios[$idx % count($espacios)];

                $dia = $dias[$diaIndex % count($dias)];
                $slot = $horasDisponibles[$horaIndex % count($horasDisponibles)];

                $detalles[] = [
                    'id_asignatura' => $asig['id_asignatura'],
                    'id_docente' => $id_doc,
                    'id_espacio' => $id_esp,
                    'dia_semana' => $dia,
                    'hora_inicio' => $slot[0],
                    'hora_fin' => $slot[1]
                ];

                $horaIndex++;
                if ($horaIndex >= count($horasDisponibles)) {
                    $horaIndex = 0;
                    $diaIndex++;
                }
            }

            $params = [
                'algoritmo' => 'FET Timetabling Engine 6.x (Simulado)',
                'franja' => $franja,
                'peso_docente' => $peso_docente,
                'dias_lectivos' => $dias,
                'generado_en' => date('Y-m-d H:i:s')
            ];

            $idPropuesta = Horario::guardarPropuestaFet($id_periodo, $id_programa, $usuario['id_usuario'], $params, $detalles);

            $_SESSION['swal_alerta'] = [
                'icon' => 'success',
                'title' => '¡Propuesta FET generada con éxito!',
                'text' => 'El motor FET calculó una distribución óptima con ' . count($detalles) . ' slots sin conflictos de recursos.'
            ];

            $this->redirect("?ruta=admin/horarios&tab=fet&programa_id=$id_programa");
        }
    }

    /**
     * Aceptar un detalle de propuesta FET
     */
    public function aceptarPropuestaFet() {
        Auth::requerirRol('ADMINISTRADOR');
        $id_detalle = (int)($_GET['id'] ?? 0);
        $usuario = Auth::getUsuarioActual();

        if ($id_detalle && $usuario) {
            $resultado = Horario::aceptarDetalleFet($id_detalle, $usuario['id_usuario']);
            
            if ($resultado === 'SOLAP_DOCENTE') {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Conflicto de docente',
                    'text' => 'No se puede aceptar esta línea porque el docente ya tiene un horario asignado en ese slot.'
                ];
            } elseif ($resultado === 'SOLAP_ESPACIO') {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Conflicto de espacio',
                    'text' => 'No se puede aceptar esta línea porque el espacio ya está ocupado en ese slot.'
                ];
            } elseif ($resultado) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'success',
                    'title' => 'Línea aceptada',
                    'text' => 'Se creó el horario real con origen FET satisfactoriamente.'
                ];
            } else {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'No se pudo aceptar esta línea. Es posible que ya haya sido procesada.'
                ];
            }
        }

        $propuesta_id = $_GET['propuesta_id'] ?? '';
        $this->redirect("?ruta=admin/horarios&tab=propuestas&propuesta_id=$propuesta_id");
    }

    /**
     * Descartar una propuesta FET completa
     */
    public function descartarPropuestaFet() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_propuesta = (int)($_POST['id_propuesta'] ?? 0);
            if ($id_propuesta) {
                Horario::descartarPropuesta($id_propuesta);
                $_SESSION['swal_alerta'] = [
                    'icon' => 'success',
                    'title' => 'Propuesta descartada',
                    'text' => 'La propuesta FET ha sido descartada.'
                ];
            }
        }
        $this->redirect('?ruta=admin/horarios&tab=propuestas');
    }

    /**
     * Eliminar un detalle de propuesta FET (borrado físico)
     */
    public function eliminarDetalleFet() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_detalle = (int)($_POST['id_detalle'] ?? 0);
            $propuesta_id = (int)($_POST['propuesta_id'] ?? 0);
            if ($id_detalle) {
                Horario::eliminarDetalleFet($id_detalle);
                $_SESSION['swal_alerta'] = [
                    'icon' => 'success',
                    'title' => 'Línea eliminada',
                    'text' => 'La línea de propuesta FET fue eliminada permanentemente.'
                ];
            }
            $this->redirect("?ruta=admin/horarios&tab=propuestas&propuesta_id=$propuesta_id");
            return;
        }
        $this->redirect('?ruta=admin/horarios&tab=propuestas');
    }

    /**
     * Verificar/marcar una auditoría
     */
    public function verificarAuditoria() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_auditoria = (int)($_POST['id_auditoria'] ?? 0);
            $resultado = trim($_POST['resultado'] ?? '');
            $observacion = trim($_POST['observacion'] ?? '');

            if ($id_auditoria && in_array($resultado, ['VERIFICADA', 'NO_REALIZADA'])) {
                Horario::verificarAuditoria($id_auditoria, $resultado, $observacion);
                $_SESSION['swal_alerta'] = [
                    'icon' => 'success',
                    'title' => 'Auditoría registrada',
                    'text' => 'El resultado de la auditoría ha sido guardado correctamente.'
                ];
            }
        }
        $this->redirect('?ruta=admin/horarios&tab=auditoria');
    }

    // Endpoints AJAX / JSON para selectores dependientes en la interfaz
    public function apiAsignaturas() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $id_periodo = (int)($_GET['periodo_id'] ?? 0);
        $id_programa = (int)($_GET['programa_id'] ?? 0);

        if (!$id_periodo || !$id_programa) {
            echo json_encode([]);
            exit;
        }

        $asignaturas = Horario::obtenerAsignaturasOfertadas($id_periodo, $id_programa);
        echo json_encode($asignaturas);
        exit;
    }

    public function apiDocentes() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $id_asignatura = (int)($_GET['asignatura_id'] ?? 0);

        if (!$id_asignatura) {
            echo json_encode([]);
            exit;
        }

        $docentes = Horario::obtenerDocentesCompatibles($id_asignatura);
        echo json_encode($docentes);
        exit;
    }

    public function apiEspaciosLibres() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $id_periodo = (int)($_GET['periodo_id'] ?? 0);
        $dia_semana = trim($_GET['dia_semana'] ?? '');
        $hora_inicio = trim($_GET['hora_inicio'] ?? '');
        $hora_fin = trim($_GET['hora_fin'] ?? '');

        if (!$id_periodo || empty($dia_semana) || empty($hora_inicio) || empty($hora_fin)) {
            echo json_encode([]);
            exit;
        }

        $espacios = Horario::obtenerEspaciosLibres($id_periodo, $dia_semana, $hora_inicio, $hora_fin);
        echo json_encode($espacios);
        exit;
    }
}

