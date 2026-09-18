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
        $this->render('admin/espacios/form', [
            'titulo' => 'Nuevo Espacio',
            'espacio' => null,
            'bloques' => $bloques
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_bloque' => $_POST['id_bloque'] ?? 0,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'tipo' => $_POST['tipo'] ?? 'AULA',
                'capacidad' => $_POST['capacidad'] ?? 0,
                'caracteristicas' => $_POST['caracteristicas'] ?? '[]'
            ];
            
            if (!empty($datos['id_bloque']) && !empty($datos['codigo']) && !empty($datos['nombre'])) {
                Espacio::crear($datos);
                $this->redirect('?ruta=admin/espacios');
            } else {
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Nuevo Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Bloque, Código y Nombre son obligatorios.'
                ]);
            }
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $espacio = Espacio::obtenerPorId($id);
        
        if (!$espacio) {
            $this->redirect('?ruta=admin/espacios');
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
            $id = $_POST['id_espacio'] ?? 0;
            $datos = [
                'id_bloque' => $_POST['id_bloque'] ?? 0,
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'tipo' => $_POST['tipo'] ?? 'AULA',
                'capacidad' => $_POST['capacidad'] ?? 0,
                'caracteristicas' => $_POST['caracteristicas'] ?? '[]'
            ];
            
            if (!empty($datos['id_bloque']) && !empty($datos['codigo']) && !empty($datos['nombre'])) {
                Espacio::actualizar($id, $datos);
                $this->redirect('?ruta=admin/espacios');
            } else {
                $datos['id_espacio'] = $id;
                $bloques = Bloque::obtenerTodos($this->id_institucion);
                $this->render('admin/espacios/form', [
                    'titulo' => 'Editar Espacio',
                    'espacio' => $datos,
                    'bloques' => $bloques,
                    'error' => 'Bloque, Código y Nombre son obligatorios.'
                ]);
            }
        }
    }

    public function cambiar_estado() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $estado = $_GET['estado'] ?? 'DISPONIBLE'; // INACTIVO, MANTENIMIENTO, DISPONIBLE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Espacio::cambiarEstado($id, $estado);
        }
        $this->redirect('?ruta=admin/espacios');
    }
}
