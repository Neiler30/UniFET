<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\AreaAcademica;

class AreaAcademicaController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/areas_academicas/index', [
            'titulo' => 'Areas Academicas',
            'areas' => AreaAcademica::obtenerTodas($this->id_institucion),
        ]);
    }

    public function crear() { $this->formulario(); }
    public function editar() { $this->formulario((int)($_GET['id'] ?? 0)); }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/areas_academicas/form', [
            'titulo' => $id ? 'Editar Area Academica' : 'Nueva Area Academica',
            'area' => $id ? AreaAcademica::obtenerPorId($id) : null,
        ]);
    }

    public function guardar() { $this->persistir(); }
    public function actualizar() { $this->persistir((int)($_POST['id_area_academica'] ?? 0)); }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/academico/areas');
        }
        AreaAcademica::guardar([
            'id_institucion' => $this->id_institucion,
            'nombre' => trim($_POST['nombre'] ?? ''),
            'estado' => $_POST['estado'] ?? 'ACTIVO',
        ], $id ?: null);
        $_SESSION['swal_alerta'] = [
            'icon' => 'success',
            'title' => 'Area academica guardada',
            'text' => 'El catalogo academico quedo actualizado.',
        ];
        $this->redirect('?ruta=admin/academico/areas');
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AreaAcademica::cambiarEstado((int)($_GET['id'] ?? 0));
        }
        $this->redirect('?ruta=admin/academico/areas');
    }
}
