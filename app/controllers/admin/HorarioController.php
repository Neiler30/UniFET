<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Horario;

class HorarioController extends Controller {

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        
        $filtros = [
            'programa' => $_GET['programa'] ?? '',
            'docente' => $_GET['docente'] ?? '',
            'espacio' => $_GET['espacio'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $horarios = Horario::obtenerConFiltros($filtros);

        $this->render('admin/horarios/index', [
            'titulo' => 'Consulta de Horarios',
            'horarios' => $horarios,
            'filtros' => $filtros
        ]);
    }
}
