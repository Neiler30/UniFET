<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;

use App\Models\DashboardModel;

class DashboardController extends Controller {
    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        
        $kpis = DashboardModel::getKpis();
        $ultimos_horarios = DashboardModel::getUltimosHorarios();
        $auditorias = DashboardModel::getAuditoriasHoy();

        $this->render('admin/dashboard', [
            'titulo' => 'Dashboard Administrador',
            'kpis' => $kpis,
            'ultimos_horarios' => $ultimos_horarios,
            'auditorias' => $auditorias
        ]);
    }
}
