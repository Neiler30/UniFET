<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;

class ProgramaController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();
        $tab = $_GET['tab'] ?? 'resumen';

        $this->render('lider/programa', [
            'titulo' => 'Mi Programa',
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'tab' => in_array($tab, ['resumen', 'docentes', 'asignaturas', 'asignaciones'], true) ? $tab : 'resumen',
            'docentes' => $programaActivo ? LiderPanel::docentes($programaActivo['id_programa']) : [],
            'asignaturas' => $programaActivo ? LiderPanel::asignaturas($programaActivo['id_programa']) : [],
            'asignaciones' => $programaActivo ? LiderPanel::asignacionesDocente($programaActivo['id_programa'], $periodoActivo['id_periodo'] ?? null) : []
        ]);
    }

    public function asignarDocente() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=lider/programa&tab=asignaciones');

        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_POST['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();

        $ok = false;
        if ($programaActivo && $periodoActivo) {
            $ok = LiderPanel::asignarDocenteAsignatura(
                $programaActivo['id_programa'],
                (int)$periodoActivo['id_periodo'],
                (int)($_POST['id_asignatura'] ?? 0),
                (int)($_POST['id_docente'] ?? 0)
            );
        }

        $_SESSION['swal_alerta'] = $ok
            ? ['icon' => 'success', 'title' => 'Asignacion registrada', 'text' => 'El docente quedo asociado a la asignatura dentro de tu programa.']
            : ['icon' => 'error', 'title' => 'No se pudo asignar', 'text' => 'Verifica que el docente y la asignatura pertenezcan a tu programa.'];
        $this->redirect('?ruta=lider/programa&tab=asignaciones');
    }
}
