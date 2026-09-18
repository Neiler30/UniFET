<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Facultad;

class FacultadController extends Controller {

    private $id_institucion = 1; // Simplificación temporal, idealmente esto vendría de la sesión o configuración global.

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $facultades = Facultad::obtenerTodas($this->id_institucion);
        $this->render('admin/facultades/index', [
            'titulo' => 'Facultades',
            'facultades' => $facultades
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/facultades/form', [
            'titulo' => 'Nueva Facultad',
            'facultad' => null
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_institucion' => $this->id_institucion,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            // Validaciones simples
            if (!empty($datos['codigo']) && !empty($datos['nombre'])) {
                Facultad::crear($datos);
                $this->redirect('?ruta=admin/institucion/facultades');
            } else {
                $this->render('admin/facultades/form', [
                    'titulo' => 'Nueva Facultad',
                    'facultad' => $datos,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
            }
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $facultad = Facultad::obtenerPorId($id, $this->id_institucion);
        
        if (!$facultad) {
            $this->redirect('?ruta=admin/institucion/facultades');
        }

        $this->render('admin/facultades/form', [
            'titulo' => 'Editar Facultad',
            'facultad' => $facultad
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_facultad'] ?? 0;
            $datos = [
                'id_institucion' => $this->id_institucion,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (!empty($datos['codigo']) && !empty($datos['nombre'])) {
                Facultad::actualizar($id, $datos);
                $this->redirect('?ruta=admin/institucion/facultades');
            } else {
                $datos['id_facultad'] = $id;
                $this->render('admin/facultades/form', [
                    'titulo' => 'Editar Facultad',
                    'facultad' => $datos,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
            }
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Usar POST para acciones destructivas
            Facultad::desactivar($id, $this->id_institucion);
        }
        $this->redirect('?ruta=admin/institucion/facultades');
    }
}
