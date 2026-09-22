<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Restriccion;
use App\Models\PeriodoAcademico;
use App\Models\Docente;
use App\Models\Espacio;

class RestriccionController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/restricciones/index', [
            'titulo' => 'Restricciones',
            'restricciones' => Restriccion::obtenerTodos($this->id_institucion),
        ]);
    }

    public function crear() { $this->formulario(); }
    public function editar() { $this->formulario((int)($_GET['id'] ?? 0)); }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/restricciones/form', [
            'titulo' => $id ? 'Editar Restriccion' : 'Nueva Restriccion',
            'restriccion' => $id ? Restriccion::obtenerPorId($id) : null,
            'periodos' => PeriodoAcademico::obtenerTodos($this->id_institucion),
            'docentes' => Docente::obtenerTodos($this->id_institucion),
            'espacios' => Espacio::obtenerTodos($this->id_institucion),
        ]);
    }

    public function guardar() { $this->persistir(); }
    public function actualizar() { $this->persistir((int)($_POST['id_restriccion'] ?? 0)); }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=admin/academico/restricciones');
        Restriccion::guardar([
            'id_institucion' => $this->id_institucion,
            'id_periodo' => (int)($_POST['id_periodo'] ?? 0),
            'tipo' => $_POST['tipo'] ?? 'OTRO',
            'id_docente' => (int)($_POST['id_docente'] ?? 0),
            'id_espacio' => (int)($_POST['id_espacio'] ?? 0),
            'dia_semana' => $_POST['dia_semana'] ?? '',
            'hora_inicio' => $_POST['hora_inicio'] ?? '',
            'hora_fin' => $_POST['hora_fin'] ?? '',
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estado' => $_POST['estado'] ?? 'ACTIVA',
        ], $id ?: null);
        $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Restriccion guardada', 'text' => 'La regla quedo disponible para planeacion y FET.'];
        $this->redirect('?ruta=admin/academico/restricciones');
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') Restriccion::cambiarEstado((int)($_GET['id'] ?? 0));
        $this->redirect('?ruta=admin/academico/restricciones');
    }
}
