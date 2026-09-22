<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;

class DashboardController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $periodoActivo = PeriodoAcademico::getActivo();
        $programas = LiderPanel::programasDelLider($usuario['id_usuario']);
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);

        $kpis = $programaActivo && $periodoActivo
            ? LiderPanel::kpis($programaActivo['id_programa'], $periodoActivo['id_periodo'])
            : ['asignaturas' => 0, 'docentes' => 0, 'clases' => 0, 'propuestas' => 0];

        $this->render('lider/dashboard', [
            'titulo' => 'Dashboard Lider',
            'programas' => $programas,
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'kpis' => $kpis,
            'horarios' => $programaActivo && $periodoActivo ? LiderPanel::horarios($programaActivo['id_programa'], $periodoActivo['id_periodo'], 6) : [],
            'propuestas' => $programaActivo ? LiderPanel::propuestas($programaActivo['id_programa'], $periodoActivo['id_periodo'] ?? null) : []
        ]);
    }
}
