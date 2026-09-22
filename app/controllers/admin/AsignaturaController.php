<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Asignatura;
use App\Models\Programa;
use App\Models\AreaAcademica;

class AsignaturaController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $areaFiltro = (int)($_GET['area_id'] ?? 0);
        $asignaturas = Asignatura::obtenerTodos($this->id_institucion);
        if ($areaFiltro) {
            $asignaturas = array_values(array_filter($asignaturas, function ($asignatura) use ($areaFiltro) {
                return (int)($asignatura['id_area_academica'] ?? 0) === $areaFiltro;
            }));
        }
        $this->render('admin/asignaturas/index', [
            'titulo' => 'Asignaturas',
            'asignaturas' => $asignaturas,
            'areas' => AreaAcademica::obtenerTodas($this->id_institucion, true),
            'areaFiltro' => $areaFiltro,
        ]);
    }

    public function crear() { $this->formulario(); }
    public function editar() { $this->formulario((int)($_GET['id'] ?? 0)); }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/asignaturas/form', [
            'titulo' => $id ? 'Editar Asignatura' : 'Nueva Asignatura',
            'asignatura' => $id ? Asignatura::obtenerPorId($id) : null,
            'programasAsignados' => $id ? Asignatura::programas($id) : [],
            'programas' => Programa::obtenerTodos($this->id_institucion),
            'areas' => AreaAcademica::obtenerTodas($this->id_institucion, true),
        ]);
    }

    public function guardar() { $this->persistir(); }
    public function actualizar() { $this->persistir((int)($_POST['id_asignatura'] ?? 0)); }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=admin/academico/asignaturas');
        Asignatura::guardar([
            'id_institucion' => $this->id_institucion,
            'id_area_academica' => (int)($_POST['id_area_academica'] ?? 0),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'creditos' => (int)($_POST['creditos'] ?? 0),
            'tipo' => trim($_POST['tipo'] ?? ''),
            'estado' => $_POST['estado'] ?? 'ACTIVO',
        ], $_POST['programas'] ?? [], $id ?: null);
        $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Asignatura guardada', 'text' => 'La asignatura y sus programas asociados quedaron actualizados.'];
        $this->redirect('?ruta=admin/academico/asignaturas');
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') Asignatura::cambiarEstado((int)($_GET['id'] ?? 0));
        $this->redirect('?ruta=admin/academico/asignaturas');
    }
}
