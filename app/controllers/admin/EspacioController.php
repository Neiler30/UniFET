<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Espacio;
use App\Models\Bloque;

class EspacioController extends Controller {

    private $id_institucion = 1; // Simplificación temporal

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $espacios = Espacio::obtenerTodos($this->id_institucion);
        $this->render('admin/espacios/index', [
            'titulo' => 'Espacios',
            'espacios' => $espacios
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $bloques = Bloque::obtenerTodos($this->id_institucion);
        $espacio = null;
        if (!empty($_GET['bloque_id'])) {
            $espacio = ['id_bloque' => (int)$_GET['bloque_id']];
        }
        $this->render('admin/espacios/form', [
            'titulo' => 'Nuevo Espacio',
            'espacio' => $espacio,
            'bloques' => $bloques
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_bloque' => (int)($_POST['id_bloque'] ?? 0),
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'AULA',
                'capacidad' => (int)($_POST['capacidad'] ?? 0),
                'caracteristicas' => $_POST['caracteristicas'] ?? '[]'
            ];
            
            if (empty($datos['id_bloque']) || empty($datos['codigo']) || empty($datos['nombre'])) {
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Nuevo Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Bloque, Código y Nombre son obligatorios.'
                ]);
                return;
            }

            if (Espacio::existeCodigo($datos['codigo'], $datos['id_bloque'])) {
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Nuevo Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Ese código ya existe para este bloque.'
                ]);
                return;
            }

            $idCreado = Espacio::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'Espacio creado con éxito',
                    'text' => 'El espacio físico se registró correctamente en la institución.',
                    'icon' => 'success'
                ];
            }
            $this->redirect('?ruta=admin/espacios/espacios');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $espacio = Espacio::obtenerPorId($id);
        
        if (!$espacio) {
            $this->redirect('?ruta=admin/espacios/espacios');
        }

        $bloques = Bloque::obtenerTodos($this->id_institucion);
        $this->render('admin/espacios/form', [
            'titulo' => 'Editar Espacio',
            'espacio' => $espacio,
            'bloques' => $bloques
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_espacio'] ?? 0);
            $datos = [
                'id_bloque' => (int)($_POST['id_bloque'] ?? 0),
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'AULA',
                'capacidad' => (int)($_POST['capacidad'] ?? 0),
                'caracteristicas' => $_POST['caracteristicas'] ?? '[]'
            ];
            
            if (empty($datos['id_bloque']) || empty($datos['codigo']) || empty($datos['nombre'])) {
                $datos['id_espacio'] = $id;
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Editar Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Bloque, Código y Nombre son obligatorios.'
                ]);
                return;
            }

            if (Espacio::existeCodigo($datos['codigo'], $datos['id_bloque'], $id)) {
                $datos['id_espacio'] = $id;
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Editar Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Ese código ya existe para este bloque.'
                ]);
                return;
            }

            Espacio::actualizar($id, $datos);
            $_SESSION['swal_alerta'] = [
                'title' => 'Espacio actualizado',
                'text' => 'Los cambios se guardaron con éxito.',
                'icon' => 'success'
            ];
            $this->redirect('?ruta=admin/espacios/espacios');
        }
    }

    public function cambiar_estado() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $estado = $_GET['estado'] ?? 'DISPONIBLE'; // INACTIVO, MANTENIMIENTO, DISPONIBLE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Espacio::cambiarEstado($id, $estado);
            $_SESSION['swal_alerta'] = [
                'title' => 'Estado actualizado',
                'text' => 'El espacio ahora se encuentra ' . strtolower($estado) . '.',
                'icon' => 'success'
            ];
        }
        $this->redirect('?ruta=admin/espacios/espacios');
    }
}
