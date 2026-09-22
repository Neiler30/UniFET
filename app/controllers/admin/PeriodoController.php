<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\PeriodoAcademico;

class PeriodoController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $periodos = PeriodoAcademico::obtenerTodos($this->id_institucion);
        $this->render('admin/periodos/index', [
            'titulo' => 'Periodos Académicos',
            'periodos' => $periodos
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/periodos/form', [
            'titulo' => 'Nuevo Periodo Académico',
            'periodo' => null,
            'periodos' => PeriodoAcademico::obtenerTodos($this->id_institucion)
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_institucion' => $this->id_institucion,
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                'fecha_fin' => $_POST['fecha_fin'] ?? '',
                'estado' => $_POST['estado'] ?? 'PLANIFICACION',
                'id_periodo_base' => (int)($_POST['id_periodo_base'] ?? 0)
            ];

            if (empty($datos['codigo']) || empty($datos['nombre'])) {
                $this->render('admin/periodos/form', [
                    'titulo' => 'Nuevo Periodo Académico',
                    'periodo' => $datos,
                    'error' => 'Código y Nombre son obligatorios.'
                ]);
                return;
            }

            if (PeriodoAcademico::existeCodigo($datos['codigo'], $this->id_institucion)) {
                $this->render('admin/periodos/form', [
                    'titulo' => 'Nuevo Periodo Académico',
                    'periodo' => $datos,
                    'error' => 'Ese código de periodo ya existe.'
                ]);
                return;
            }

            $idCreado = PeriodoAcademico::crear($datos);
            if ($idCreado) {
                $_SESSION['swal_cadena'] = [
                    'tipo' => 'periodo',
                    'id' => $idCreado,
                    'nombre' => $datos['codigo']
                ];
            }
            $this->redirect('?ruta=admin/academico/periodos');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? 0);
        $periodo = PeriodoAcademico::obtenerPorId($id);

        if (!$periodo) {
            $this->redirect('?ruta=admin/academico/periodos');
        }

        $this->render('admin/periodos/form', [
            'titulo' => 'Editar Periodo Académico',
            'periodo' => $periodo,
            'periodos' => PeriodoAcademico::obtenerTodos($this->id_institucion)
        ]);
    }

    public function actualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_periodo'] ?? 0);
            $datos = [
                'codigo' => trim($_POST['codigo'] ?? ''),
                'nombre' => trim($_POST['nombre'] ?? ''),
                'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                'fecha_fin' => $_POST['fecha_fin'] ?? '',
                'estado' => $_POST['estado'] ?? 'PLANIFICACION'
            ];

            if (empty($datos['codigo']) || empty($datos['nombre'])) {
                $datos['id_periodo'] = $id;
                $this->render('admin/periodos/form', [
                    'titulo' => 'Editar Periodo Académico',
                    'periodo' => $datos,
                    'error' => 'Código y Nombre son obligatorios.'
                ]);
                return;
            }

            if (PeriodoAcademico::existeCodigo($datos['codigo'], $this->id_institucion, $id)) {
                $datos['id_periodo'] = $id;
                $this->render('admin/periodos/form', [
                    'titulo' => 'Editar Periodo Académico',
                    'periodo' => $datos,
                    'error' => 'Ese código de periodo ya existe.'
                ]);
                return;
            }

            PeriodoAcademico::actualizar($id, $datos);
            $_SESSION['swal_alerta'] = [
                'title' => 'Periodo actualizado',
                'text' => 'Los cambios se guardaron con éxito.',
                'icon' => 'success'
            ];
            $this->redirect('?ruta=admin/academico/periodos');
        }
    }
}
