<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;

class DashboardController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        echo "<h1>Dashboard Líder de Programa</h1>";
        echo "<p>Bienvenido, " . Auth::getUsuarioActual()['nombre'] . "</p>";
        echo "<a href='?ruta=logout'>Cerrar sesión</a>";
    }
}
