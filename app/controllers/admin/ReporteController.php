<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Reporte;

class ReporteController extends Controller {
    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $tipo = $_GET['tipo'] ?? 'programa';
        $filtro = trim($_GET['filtro'] ?? '');
        $resultado = $_GET['resultado'] ?? '';
        if ($tipo === 'docente') {
            $datos = Reporte::porDocente($filtro);
        } elseif ($tipo === 'espacio') {
            $datos = Reporte::porEspacio($filtro);
        } elseif ($tipo === 'auditorias') {
            $datos = Reporte::auditorias($resultado);
        } else {
            $tipo = 'programa';
            $datos = Reporte::porPrograma($filtro);
        }
        $this->render('admin/reportes/index', [
            'titulo' => 'Reportes',
            'tipo' => $tipo,
            'filtro' => $filtro,
            'resultado' => $resultado,
            'datos' => $datos,
        ]);
    }
}
