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
        $programasLider = LiderPanel::programasDelLider($usuario['id_usuario']);
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();

        $kpis = $programaActivo ? LiderPanel::kpis($programaActivo['id_programa'], $periodoActivo['id_periodo'] ?? null) : [];
        $docentes = $programaActivo ? LiderPanel::docentes($programaActivo['id_programa']) : [];
        $asignaturas = $programaActivo ? LiderPanel::asignaturas($programaActivo['id_programa']) : [];
        $asignaciones = $programaActivo ? LiderPanel::asignacionesDocente($programaActivo['id_programa'], $periodoActivo['id_periodo'] ?? null) : [];

        $this->render('lider/programa', [
            'titulo'         => 'Mi Programa',
            'programasLider' => $programasLider,
            'programaActivo' => $programaActivo,
            'periodoActivo'  => $periodoActivo,
            'kpis'           => $kpis,
            'docentes'       => $docentes,
            'asignaturas'    => $asignaturas,
            'asignaciones'   => $asignaciones
        ]);
    }

    public function asignarDocente() {
        Auth::requerirRol('LIDER_PROGRAMA');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=lider/programa');

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
            ? ['icon' => 'success', 'title' => 'Asignación registrada', 'text' => 'El docente quedó asociado a la asignatura dentro de tu programa.']
            : ['icon' => 'error', 'title' => 'No se pudo asignar', 'text' => 'Verifica que el docente y la asignatura pertenezcan a tu programa.'];
        $this->redirect('?ruta=lider/programa');
    }
}
