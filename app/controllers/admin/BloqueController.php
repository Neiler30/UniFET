<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Bloque;
use App\Models\Sede;

class BloqueController extends Controller {

    private $id_institucion = 1; // Simplificación temporal

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $bloques = Bloque::obtenerTodos($this->id_institucion);
        $this->render('admin/bloques/index', [
            'titulo' => 'Bloques',
            'bloques' => $bloques
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $sedes = Sede::obtenerTodas($this->id_institucion);
        $bloque = null;
        if (!empty($_GET['sede_id'])) {
            $bloque = ['id_sede' => (int)$_GET['sede_id']];
        }
        $this->render('admin/bloques/form', [
            'titulo' => 'Nuevo Bloque',
            'bloque' => $bloque,
            'sedes' => $sedes
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_sede' => (int)($_POST['id_sede'] ?? 0),
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (empty($datos['id_sede']) || empty($datos['codigo']) || empty($datos['nombre'])) {
                $sedes = Sede::obtenerTodas($this->id_institucion);
                $this->render('admin/bloques/form', [
                    'titulo' => 'Nuevo Bloque',
                    'bloque' => $datos,
                    'sedes' => $sedes,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
                return;
            }

            if (Bloque::existeCodigo($datos['codigo'], $datos['id_sede'])) {
                $sedes = Sede::obtenerTodas($this->id_institucion);
                $this->render('admin/bloques/form', [
                    'titulo' => 'Nuevo Bloque',
                    'bloque' => $datos,
                    'sedes' => $sedes,
                    'error' => 'Ese código ya existe para esta sede.'
                ]);
                return;
            }

            $idCreado = Bloque::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_cadena'] = [
                    'tipo' => 'bloque',
                    'id' => $idCreado,
                    'nombre' => $datos['nombre']
                ];
            }
            $this->redirect('?ruta=admin/espacios/bloques');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $bloque = Bloque::obtenerPorId($id);
        
        if (!$bloque) {
            $this->redirect('?ruta=admin/espacios/bloques');
        }

        $sedes = Sede::obtenerTodas($this->id_institucion);
        $this->render('admin/bloques/form', [
            'titulo' => 'Editar Bloque',
            'bloque' => $bloque,
            'sedes' => $sedes
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_bloque'] ?? 0;
            $datos = [
                'id_sede' => $_POST['id_sede'] ?? 0,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'estado' => $_POST['estado'] ?? 'ACTIVO'
            ];
            
            if (!empty($datos['id_sede']) && !empty($datos['codigo']) && !empty($datos['nombre'])) {
                Bloque::actualizar($id, $datos);
                $this->redirect('?ruta=admin/espacios/bloques');
            } else {
                $datos['id_bloque'] = $id;
                $sedes = Sede::obtenerTodas($this->id_institucion);
                $this->render('admin/bloques/form', [
                    'titulo' => 'Editar Bloque',
                    'bloque' => $datos,
                    'sedes' => $sedes,
                    'error' => 'Todos los campos son obligatorios.'
                ]);
            }
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Bloque::desactivar($id);
            $_SESSION['swal_alerta'] = [
                'title' => 'Registro desactivado',
                'text' => 'El bloque ha sido desactivado.',
                'icon' => 'success'
            ];
        }
        $this->redirect('?ruta=admin/espacios/bloques');
    }
}
