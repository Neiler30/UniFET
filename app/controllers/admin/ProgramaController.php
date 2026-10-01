<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Programa;
use App\Models\Facultad;
use App\Models\NivelAcademico;

class ProgramaController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $programas = Programa::obtenerTodos($this->id_institucion);
        $this->render('admin/programas/index', [
            'titulo'    => 'Programas',
            'programas' => $programas
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $facultades = Facultad::obtenerTodas($this->id_institucion);
        $niveles    = NivelAcademico::obtenerActivos();
        $programa   = null;
        if (!empty($_GET['facultad_id'])) {
            $programa = ['id_facultad' => (int)$_GET['facultad_id']];
        }
        $this->render('admin/programas/form', [
            'titulo'     => 'Nuevo Programa',
            'programa'   => $programa,
            'facultades' => $facultades,
            'niveles'    => $niveles,
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_facultad'        => (int)($_POST['id_facultad']        ?? 0),
                'id_nivel_academico' => (int)($_POST['id_nivel_academico'] ?? 0) ?: null,
                'codigo'             => trim($_POST['codigo']    ?? ''),
                'nombre'             => trim($_POST['nombre']    ?? ''),
                'numero_semestres'   => (int)($_POST['numero_semestres']   ?? 0),
                'estado'             => $_POST['estado'] ?? 'ACTIVO',
            ];

            if (empty($datos['id_facultad']) || empty($datos['codigo']) || empty($datos['nombre'])) {
                $this->render('admin/programas/form', [
                    'titulo'     => 'Nuevo Programa',
                    'programa'   => $datos,
                    'facultades' => Facultad::obtenerTodas($this->id_institucion),
                    'niveles'    => NivelAcademico::obtenerActivos(),
                    'error'      => 'Todos los campos obligatorios deben estar completos.',
                ]);
                return;
            }

            if (Programa::existeCodigo($datos['codigo'], $datos['id_facultad'])) {
                $this->render('admin/programas/form', [
                    'titulo'     => 'Nuevo Programa',
                    'programa'   => $datos,
                    'facultades' => Facultad::obtenerTodas($this->id_institucion),
                    'niveles'    => NivelAcademico::obtenerActivos(),
                    'error'      => 'Ese código ya existe para esta facultad.',
                ]);
                return;
            }

            $idCreado = Programa::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'Programa creado',
                    'text'  => 'El programa académico se registró con éxito.',
                    'icon'  => 'success',
                ];
            }
            $this->redirect('?ruta=admin/institucion/programas');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id      = $_GET['id'] ?? 0;
        $programa = Programa::obtenerPorId($id);

        if (!$programa) {
            $this->redirect('?ruta=admin/institucion/programas');
        }

        $this->render('admin/programas/form', [
            'titulo'     => 'Editar Programa',
            'programa'   => $programa,
            'facultades' => Facultad::obtenerTodas($this->id_institucion),
            'niveles'    => NivelAcademico::obtenerActivos(),
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id   = $_POST['id_programa'] ?? 0;
            $datos = [
                'id_facultad'        => (int)($_POST['id_facultad']        ?? 0),
                'id_nivel_academico' => (int)($_POST['id_nivel_academico'] ?? 0) ?: null,
                'codigo'             => trim($_POST['codigo']    ?? ''),
                'nombre'             => trim($_POST['nombre']    ?? ''),
                'numero_semestres'   => (int)($_POST['numero_semestres']   ?? 0),
                'estado'             => $_POST['estado'] ?? 'ACTIVO',
            ];

            if (!empty($datos['id_facultad']) && !empty($datos['codigo']) && !empty($datos['nombre'])) {
                Programa::actualizar($id, $datos);
                $_SESSION['swal_alerta'] = [
                    'title' => 'Programa actualizado',
                    'text'  => 'Los cambios se guardaron con éxito.',
                    'icon'  => 'success',
                ];
                $this->redirect('?ruta=admin/institucion/programas');
            } else {
                $datos['id_programa'] = $id;
                $this->render('admin/programas/form', [
                    'titulo'     => 'Editar Programa',
                    'programa'   => $datos,
                    'facultades' => Facultad::obtenerTodas($this->id_institucion),
                    'niveles'    => NivelAcademico::obtenerActivos(),
                    'error'      => 'Todos los campos obligatorios deben estar completos.',
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
                'title' => 'Registro actualizado',
                'text'  => 'El estado del programa ha sido cambiado.',
                'icon'  => 'success',
            ];
        }
        $this->redirect('?ruta=admin/institucion/programas');
    }
}
