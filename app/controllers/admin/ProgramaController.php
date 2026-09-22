<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Programa;
use App\Models\Facultad;

class ProgramaController extends Controller {

    private $id_institucion = 1; // Simplificación temporal

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $programas = Programa::obtenerTodos($this->id_institucion);
        $this->render('admin/programas/index', [
            'titulo' => 'Programas',
            'programas' => $programas
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $facultades = Facultad::obtenerTodas($this->id_institucion);
        $programa = null;
        if (!empty($_GET['facultad_id'])) {
            $programa = ['id_facultad' => (int)$_GET['facultad_id']];
        }
        $this->render('admin/programas/form', [
            'titulo' => 'Nuevo Programa',
            'programa' => $programa,
            'facultades' => $facultades
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_facultad' => (int)($_POST['id_facultad'] ?? 0),
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (empty($datos['id_facultad']) || empty($datos['codigo']) || empty($datos['nombre'])) {
                $facultades = Facultad::obtenerTodas($this->id_institucion);
                $this->render('admin/programas/form', [
                    'titulo' => 'Nuevo Programa',
                    'programa' => $datos,
                    'facultades' => $facultades,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
                return;
            }

            if (Programa::existeCodigo($datos['codigo'], $datos['id_facultad'])) {
                $facultades = Facultad::obtenerTodas($this->id_institucion);
                $this->render('admin/programas/form', [
                    'titulo' => 'Nuevo Programa',
                    'programa' => $datos,
                    'facultades' => $facultades,
                    'error' => 'Ese código ya existe para esta facultad.'
                ]);
                return;
            }

            $idCreado = Programa::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'Programa creado',
                    'text' => 'El programa académico se registró con éxito.',
                    'icon' => 'success'
                ];
            }
            $this->redirect('?ruta=admin/institucion/programas');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $programa = Programa::obtenerPorId($id);
        
        if (!$programa) {
            $this->redirect('?ruta=admin/institucion/programas');
        }

        $facultades = Facultad::obtenerTodas($this->id_institucion);
        $this->render('admin/programas/form', [
            'titulo' => 'Editar Programa',
            'programa' => $programa,
            'facultades' => $facultades
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_programa'] ?? 0;
            $datos = [
                'id_facultad' => $_POST['id_facultad'] ?? 0,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (!empty($datos['id_facultad']) && !empty($datos['codigo']) && !empty($datos['nombre'])) {
                Programa::actualizar($id, $datos);
                $_SESSION['swal_alerta'] = [
                    'title' => 'Programa actualizado',
                    'text' => 'Los cambios se guardaron con éxito.',
                    'icon' => 'success'
                ];
                $this->redirect('?ruta=admin/institucion/programas');
            } else {
                $datos['id_programa'] = $id;
                $facultades = Facultad::obtenerTodas($this->id_institucion);
                $this->render('admin/programas/form', [
                    'titulo' => 'Editar Programa',
                    'programa' => $datos,
                    'facultades' => $facultades,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
            }
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Programa::desactivar($id);
            $_SESSION['swal_alerta'] = [
                'title' => 'Registro desactivado',
                'text' => 'El programa ha sido desactivado.',
                'icon' => 'success'
            ];
        }
        $this->redirect('?ruta=admin/institucion/programas');
    }
}
