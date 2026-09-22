<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;

class ReporteController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();
        $tipo = $_GET['tipo'] ?? 'programa';
        $filtrosAuditoria = ['docente' => trim($_GET['docente'] ?? ''), 'fecha' => trim($_GET['fecha'] ?? '')];

        $datos = [];
        if ($programaActivo) {
            $datos = $tipo === 'auditorias'
                ? LiderPanel::auditorias($programaActivo['id_programa'], $filtrosAuditoria)
                : LiderPanel::horarios($programaActivo['id_programa'], $periodoActivo['id_periodo'] ?? null);
        }

        $this->render('lider/reportes', [
            'titulo' => 'Reportes del Programa',
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'tipo' => in_array($tipo, ['programa', 'docente', 'auditorias'], true) ? $tipo : 'programa',
            'datos' => $datos,
            'docenteFiltro' => $filtrosAuditoria['docente'],
            'fechaFiltro' => $filtrosAuditoria['fecha']
        ]);
    }
}
