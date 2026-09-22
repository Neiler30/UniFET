<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;

class EspacioController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();
        $espacios = LiderPanel::espacios();
        $idEspacio = (int)($_GET['espacio_id'] ?? ($espacios[0]['id_espacio'] ?? 0));

        $this->render('lider/espacios', [
            'titulo' => 'Disponibilidad de Espacios',
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'espacios' => $espacios,
            'idEspacio' => $idEspacio,
            'ocupacion' => $programaActivo && $periodoActivo && $idEspacio ? LiderPanel::ocupacionEspacio($programaActivo['id_programa'], $periodoActivo['id_periodo'], $idEspacio) : []
        ]);
    }
}
