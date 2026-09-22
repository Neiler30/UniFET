<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Sede;

class SedeController extends Controller {

    private $id_institucion = 1; // Simplificación temporal

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $sedes = Sede::obtenerTodas($this->id_institucion);
        $this->render('admin/sedes/index', [
            'titulo' => 'Sedes',
            'sedes' => $sedes
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/sedes/form', [
            'titulo' => 'Nueva Sede',
            'sede' => null
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_institucion' => $this->id_institucion,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (empty($datos['codigo']) || empty($datos['nombre'])) {
                $this->render('admin/sedes/form', [
                    'titulo' => 'Nueva Sede',
                    'sede' => $datos,
                    'error' => 'Código y Nombre son obligatorios.'
                ]);
                return;
            }

            if (Sede::existeCodigo($datos['codigo'], $this->id_institucion)) {
                $this->render('admin/sedes/form', [
                    'titulo' => 'Nueva Sede',
                    'sede' => $datos,
                    'error' => 'Ese código ya existe para esta institución.'
                ]);
                return;
            }

            $idCreado = Sede::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_cadena'] = [
                    'tipo' => 'sede',
                    'id' => $idCreado,
                    'nombre' => $datos['nombre']
                ];
            }
            $this->redirect('?ruta=admin/espacios/sedes');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $sede = Sede::obtenerPorId($id, $this->id_institucion);
        
        if (!$sede) {
            $this->redirect('?ruta=admin/espacios/sedes');
        }

        $this->render('admin/sedes/form', [
            'titulo' => 'Editar Sede',
            'sede' => $sede
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_sede'] ?? 0;
            $datos = [
                'id_institucion' => $this->id_institucion,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (!empty($datos['codigo']) && !empty($datos['nombre'])) {
                Sede::actualizar($id, $datos);
                $this->redirect('?ruta=admin/espacios/sedes');
            } else {
                $datos['id_sede'] = $id;
                $this->render('admin/sedes/form', [
                    'titulo' => 'Editar Sede',
                    'sede' => $datos,
                    'error' => 'Código y Nombre son obligatorios.'
                ]);
            }
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Sede::desactivar($id, $this->id_institucion);
            $_SESSION['swal_alerta'] = [
                'title' => 'Registro desactivado',
                'text' => 'La sede ha sido desactivada.',
                'icon' => 'success'
            ];
        }
        $this->redirect('?ruta=admin/espacios/sedes');
    }
}
