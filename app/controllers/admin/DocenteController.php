<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Docente;
use App\Models\Programa;
use App\Models\Asignatura;

class DocenteController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/docentes/index', [
            'titulo' => 'Docentes',
            'docentes' => Docente::obtenerTodos($this->id_institucion),
        ]);
    }

    public function crear() { $this->formulario(); }
    public function editar() { $this->formulario((int)($_GET['id'] ?? 0)); }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $docente = $id ? Docente::obtenerPorId($id) : null;
        $rel = $id ? Docente::relaciones($id) : ['programas' => [], 'asignaturas' => []];
        $this->render('admin/docentes/form', [
            'titulo' => $id ? 'Editar Docente' : 'Nuevo Docente',
            'docente' => $docente,
            'relaciones' => $rel,
            'programas' => Programa::obtenerTodos($this->id_institucion),
            'asignaturas' => Asignatura::obtenerTodos($this->id_institucion),
        ]);
    }

    public function guardar() { $this->persistir(); }
    public function actualizar() { $this->persistir((int)($_POST['id_docente'] ?? 0)); }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=admin/academico/docentes');
        Docente::guardar([
            'id_institucion' => $this->id_institucion,
            'codigo' => trim($_POST['codigo'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'estado' => $_POST['estado'] ?? 'ACTIVO',
        ], $_POST['programas'] ?? [], $_POST['asignaturas'] ?? [], $id ?: null);
        $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Docente guardado', 'text' => 'La informacion academica del docente quedo actualizada.'];
        $this->redirect('?ruta=admin/academico/docentes');
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') Docente::cambiarEstado((int)($_GET['id'] ?? 0));
        $this->redirect('?ruta=admin/academico/docentes');
    }
}
