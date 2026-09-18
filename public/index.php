<?php

// Autoloader básico para PSR-4
spl_autoload_register(function ($clase) {
    // Prefix base: App\
    $prefijo = 'App\\';
    
    // Directorio base para el prefijo
    $directorio_base = __DIR__ . '/../app/';
    
    $len = strlen($prefijo);
    if (strncmp($prefijo, $clase, $len) !== 0) {
        return;
    }
    
    $clase_relativa = substr($clase, $len);
    $archivo = $directorio_base . str_replace('\\', '/', $clase_relativa) . '.php';
    
    if (file_exists($archivo)) {
        require $archivo;
    }
});

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Lider\DashboardController as LiderDashboard;
use App\Controllers\Admin\FacultadController;
use App\Controllers\Admin\ProgramaController;
use App\Controllers\Admin\SedeController;
use App\Controllers\Admin\BloqueController;
use App\Controllers\Admin\EspacioController;
use App\Controllers\Admin\HorarioController;

$router = new Router();

// Rutas de autenticación
$router->agregarRuta('login', AuthController::class, 'mostrarLogin');
$router->agregarRuta('login_post', AuthController::class, 'procesarLogin');
$router->agregarRuta('logout', AuthController::class, 'logout');

// Rutas de Administrador
$router->agregarRuta('admin/dashboard', AdminDashboard::class, 'index');
$router->agregarRuta('admin/horarios', HorarioController::class, 'index');
// Institución (Facultades y Programas)
$router->agregarRuta('admin/institucion/facultades', FacultadController::class, 'index');
$router->agregarRuta('admin/institucion/facultades/crear', FacultadController::class, 'crear');
$router->agregarRuta('admin/institucion/facultades/guardar', FacultadController::class, 'guardar');
$router->agregarRuta('admin/institucion/facultades/editar', FacultadController::class, 'editar');
$router->agregarRuta('admin/institucion/facultades/actualizar', FacultadController::class, 'actualizar');
$router->agregarRuta('admin/institucion/facultades/desactivar', FacultadController::class, 'desactivar');

$router->agregarRuta('admin/institucion/programas', ProgramaController::class, 'index');
$router->agregarRuta('admin/institucion/programas/crear', ProgramaController::class, 'crear');
$router->agregarRuta('admin/institucion/programas/guardar', ProgramaController::class, 'guardar');
$router->agregarRuta('admin/institucion/programas/editar', ProgramaController::class, 'editar');
$router->agregarRuta('admin/institucion/programas/actualizar', ProgramaController::class, 'actualizar');
$router->agregarRuta('admin/institucion/programas/desactivar', ProgramaController::class, 'desactivar');

// Espacios (Sedes, Bloques, Espacios)
$router->agregarRuta('admin/espacios/sedes', SedeController::class, 'index');
$router->agregarRuta('admin/espacios/sedes/crear', SedeController::class, 'crear');
$router->agregarRuta('admin/espacios/sedes/guardar', SedeController::class, 'guardar');
$router->agregarRuta('admin/espacios/sedes/editar', SedeController::class, 'editar');
$router->agregarRuta('admin/espacios/sedes/actualizar', SedeController::class, 'actualizar');
$router->agregarRuta('admin/espacios/sedes/desactivar', SedeController::class, 'desactivar');

$router->agregarRuta('admin/espacios/bloques', BloqueController::class, 'index');
$router->agregarRuta('admin/espacios/bloques/crear', BloqueController::class, 'crear');
$router->agregarRuta('admin/espacios/bloques/guardar', BloqueController::class, 'guardar');
$router->agregarRuta('admin/espacios/bloques/editar', BloqueController::class, 'editar');
$router->agregarRuta('admin/espacios/bloques/actualizar', BloqueController::class, 'actualizar');
$router->agregarRuta('admin/espacios/bloques/desactivar', BloqueController::class, 'desactivar');

$router->agregarRuta('admin/espacios/espacios', EspacioController::class, 'index');
$router->agregarRuta('admin/espacios/espacios/crear', EspacioController::class, 'crear');
$router->agregarRuta('admin/espacios/espacios/guardar', EspacioController::class, 'guardar');
$router->agregarRuta('admin/espacios/espacios/editar', EspacioController::class, 'editar');
$router->agregarRuta('admin/espacios/espacios/actualizar', EspacioController::class, 'actualizar');
$router->agregarRuta('admin/espacios/espacios/cambiar_estado', EspacioController::class, 'cambiar_estado');


// Rutas de Líder de Programa
$router->agregarRuta('lider/dashboard', LiderDashboard::class, 'index');

// Despachar
$ruta = $_GET['ruta'] ?? 'login';
$router->despachar($ruta);
