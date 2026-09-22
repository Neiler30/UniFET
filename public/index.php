<?php

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}
$features = __DIR__ . '/../app/config/features.php';
if (file_exists($features)) {
    require_once $features;
}

// Autoloader bÃ¡sico para PSR-4
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
use App\Controllers\RegistroLiderController;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Lider\DashboardController as LiderDashboard;
use App\Controllers\Lider\ProgramaController as LiderPrograma;
use App\Controllers\Lider\EspacioController as LiderEspacio;
use App\Controllers\Lider\HorarioController as LiderHorario;
use App\Controllers\Lider\ReporteController as LiderReporte;
use App\Controllers\Admin\FacultadController;
use App\Controllers\Admin\ProgramaController;
use App\Controllers\Admin\SedeController;
use App\Controllers\Admin\BloqueController;
use App\Controllers\Admin\EspacioController;
use App\Controllers\Admin\HorarioController;
use App\Controllers\Admin\PeriodoController;
use App\Controllers\Admin\OfertaController;
use App\Controllers\Admin\DocenteController;
use App\Controllers\Admin\AsignaturaController;
use App\Controllers\Admin\AreaAcademicaController;
use App\Controllers\Admin\RestriccionController;
use App\Controllers\Admin\SistemaController;
use App\Controllers\Admin\ReporteController;
use App\Controllers\Admin\ImportExportController;

$router = new Router();

// Rutas de autenticaciÃ³n
$router->agregarRuta('login', AuthController::class, 'mostrarLogin');
$router->agregarRuta('login_post', AuthController::class, 'procesarLogin');
$router->agregarRuta('logout', AuthController::class, 'logout');
$router->agregarRuta('auth/cambiar_password_temporal', AuthController::class, 'cambiarPasswordTemporal');
$router->agregarRuta('registro/lider', RegistroLiderController::class, 'mostrar');
$router->agregarRuta('registro/lider/guardar', RegistroLiderController::class, 'guardar');

// Rutas de Administrador
$router->agregarRuta('admin/dashboard', AdminDashboard::class, 'index');

// Horarios
$router->agregarRuta('admin/horarios', HorarioController::class, 'index');
$router->agregarRuta('admin/horarios/guardar', HorarioController::class, 'guardar');
$router->agregarRuta('admin/horarios/cerrar', HorarioController::class, 'cerrar');
$router->agregarRuta('admin/horarios/generar_fet', HorarioController::class, 'generarFet');
$router->agregarRuta('admin/horarios/aceptar_propuesta_fet', HorarioController::class, 'aceptarPropuestaFet');
$router->agregarRuta('admin/horarios/descartar_propuesta_fet', HorarioController::class, 'descartarPropuestaFet');
$router->agregarRuta('admin/horarios/eliminar_detalle_fet', HorarioController::class, 'eliminarDetalleFet');
$router->agregarRuta('admin/horarios/verificar_auditoria', HorarioController::class, 'verificarAuditoria');
$router->agregarRuta('admin/horarios/api_asignaturas', HorarioController::class, 'apiAsignaturas');
$router->agregarRuta('admin/horarios/api_docentes', HorarioController::class, 'apiDocentes');
$router->agregarRuta('admin/horarios/api_espacios', HorarioController::class, 'apiEspaciosLibres');

// AcadÃ©mico (Periodos y Oferta)
$router->agregarRuta('admin/academico/periodos', PeriodoController::class, 'index');
$router->agregarRuta('admin/academico/periodos/crear', PeriodoController::class, 'crear');
$router->agregarRuta('admin/academico/periodos/guardar', PeriodoController::class, 'guardar');
$router->agregarRuta('admin/academico/periodos/editar', PeriodoController::class, 'editar');
$router->agregarRuta('admin/academico/periodos/actualizar', PeriodoController::class, 'actualizar');

$router->agregarRuta('admin/academico/oferta', OfertaController::class, 'index');
$router->agregarRuta('admin/academico/oferta/crear', OfertaController::class, 'crear');
$router->agregarRuta('admin/academico/oferta/guardar', OfertaController::class, 'guardar');
$router->agregarRuta('admin/academico/oferta/desactivar', OfertaController::class, 'desactivar');

$router->agregarRuta('admin/academico/docentes', DocenteController::class, 'index');
$router->agregarRuta('admin/academico/docentes/crear', DocenteController::class, 'crear');
$router->agregarRuta('admin/academico/docentes/guardar', DocenteController::class, 'guardar');
$router->agregarRuta('admin/academico/docentes/editar', DocenteController::class, 'editar');
$router->agregarRuta('admin/academico/docentes/actualizar', DocenteController::class, 'actualizar');
$router->agregarRuta('admin/academico/docentes/desactivar', DocenteController::class, 'desactivar');

$router->agregarRuta('admin/academico/areas', AreaAcademicaController::class, 'index');
$router->agregarRuta('admin/academico/areas/crear', AreaAcademicaController::class, 'crear');
$router->agregarRuta('admin/academico/areas/guardar', AreaAcademicaController::class, 'guardar');
$router->agregarRuta('admin/academico/areas/editar', AreaAcademicaController::class, 'editar');
$router->agregarRuta('admin/academico/areas/actualizar', AreaAcademicaController::class, 'actualizar');
$router->agregarRuta('admin/academico/areas/desactivar', AreaAcademicaController::class, 'desactivar');

$router->agregarRuta('admin/academico/asignaturas', AsignaturaController::class, 'index');
$router->agregarRuta('admin/academico/asignaturas/crear', AsignaturaController::class, 'crear');
$router->agregarRuta('admin/academico/asignaturas/guardar', AsignaturaController::class, 'guardar');
$router->agregarRuta('admin/academico/asignaturas/editar', AsignaturaController::class, 'editar');
$router->agregarRuta('admin/academico/asignaturas/actualizar', AsignaturaController::class, 'actualizar');
$router->agregarRuta('admin/academico/asignaturas/desactivar', AsignaturaController::class, 'desactivar');

$router->agregarRuta('admin/academico/restricciones', RestriccionController::class, 'index');
$router->agregarRuta('admin/academico/restricciones/crear', RestriccionController::class, 'crear');
$router->agregarRuta('admin/academico/restricciones/guardar', RestriccionController::class, 'guardar');
$router->agregarRuta('admin/academico/restricciones/editar', RestriccionController::class, 'editar');
$router->agregarRuta('admin/academico/restricciones/actualizar', RestriccionController::class, 'actualizar');
$router->agregarRuta('admin/academico/restricciones/desactivar', RestriccionController::class, 'desactivar');

// InstituciÃ³n (Facultades y Programas)
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

// Reportes
$router->agregarRuta('admin/reportes', ReporteController::class, 'index');

// Importar/Exportar
$router->agregarRuta('admin/importar', ImportExportController::class, 'index');
$router->agregarRuta('admin/importar/validar', ImportExportController::class, 'validar');

// Sistema
$router->agregarRuta('admin/sistema', SistemaController::class, 'personalizacion');
$router->agregarRuta('admin/sistema/personalizacion', SistemaController::class, 'personalizacion');
$router->agregarRuta('admin/sistema/personalizacion/guardar', SistemaController::class, 'guardarPersonalizacion');
$router->agregarRuta('admin/sistema/usuarios', SistemaController::class, 'usuarios');
$router->agregarRuta('admin/sistema/lideres', SistemaController::class, 'lideres');
$router->agregarRuta('admin/sistema/lideres/guardar', SistemaController::class, 'guardarLider');
$router->agregarRuta('admin/sistema/usuarios/crear', SistemaController::class, 'crearUsuario');
$router->agregarRuta('admin/sistema/usuarios/guardar', SistemaController::class, 'guardarUsuario');
$router->agregarRuta('admin/sistema/usuarios/editar', SistemaController::class, 'editarUsuario');
$router->agregarRuta('admin/sistema/usuarios/actualizar', SistemaController::class, 'actualizarUsuario');
$router->agregarRuta('admin/sistema/usuarios/desactivar', SistemaController::class, 'desactivarUsuario');
$router->agregarRuta('admin/sistema/usuarios/aprobar', SistemaController::class, 'aprobarUsuario');
$router->agregarRuta('admin/sistema/usuarios/rechazar', SistemaController::class, 'rechazarUsuario');

// Rutas de LÃ­der de Programa
$router->agregarRuta('lider/dashboard', LiderDashboard::class, 'index');
$router->agregarRuta('lider/programa', LiderPrograma::class, 'index');
$router->agregarRuta('lider/programa/asignar_docente', LiderPrograma::class, 'asignarDocente');
$router->agregarRuta('lider/espacios', LiderEspacio::class, 'index');
$router->agregarRuta('lider/horarios', LiderHorario::class, 'index');
$router->agregarRuta('lider/horarios/guardar', LiderHorario::class, 'guardar');
$router->agregarRuta('lider/horarios/cerrar', LiderHorario::class, 'cerrar');
$router->agregarRuta('lider/horarios/generar_fet', LiderHorario::class, 'generarFet');
$router->agregarRuta('lider/horarios/aceptar_fet', LiderHorario::class, 'aceptarFet');
$router->agregarRuta('lider/horarios/descartar_fet', LiderHorario::class, 'descartarFet');
$router->agregarRuta('lider/horarios/verificar_auditoria', LiderHorario::class, 'verificarAuditoria');
$router->agregarRuta('lider/horarios/api_asignaturas', LiderHorario::class, 'apiAsignaturas');
$router->agregarRuta('lider/horarios/api_docentes', LiderHorario::class, 'apiDocentes');
$router->agregarRuta('lider/horarios/api_espacios', LiderHorario::class, 'apiEspaciosLibres');
$router->agregarRuta('lider/reportes', LiderReporte::class, 'index');

// Despachar (normalizar puntos a barras para compatibilidad con prompt)
$ruta = $_GET['ruta'] ?? 'login';
$ruta = str_replace('.', '/', $ruta);
$router->despachar($ruta);

