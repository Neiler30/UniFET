<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\OfertaAcademica;
use App\Models\PeriodoAcademico;
use App\Models\Programa;
use App\Models\Asignatura;
use App\Models\NivelAcademico;

class OfertaController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $periodos = PeriodoAcademico::obtenerTodos($this->id_institucion);
        $periodoActivo = PeriodoAcademico::getActivo();

        $id_periodo = !empty($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : ($periodoActivo['id_periodo'] ?? ($periodos[0]['id_periodo'] ?? 0));
        $id_programa = !empty($_GET['programa_id']) ? (int)$_GET['programa_id'] : null;

        $programas = Programa::obtenerTodos($this->id_institucion);
        $ofertas = $id_periodo ? OfertaAcademica::obtenerPorPeriodo($id_periodo, $id_programa) : [];

        $this->render('admin/oferta/index', [
            'titulo'               => 'Oferta Académica',
            'periodos'             => $periodos,
            'programas'            => $programas,
            'periodo_seleccionado' => $id_periodo,
            'programa_seleccionado'=> $id_programa,
            'ofertas'              => $ofertas
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $periodos = PeriodoAcademico::obtenerTodos($this->id_institucion);
        $programas = Programa::obtenerTodos($this->id_institucion);
        $niveles   = NivelAcademico::obtenerActivos();

        $periodo_id = (int)($_GET['periodo_id'] ?? 0);
        if (!$periodo_id) {
            $activo = PeriodoAcademico::getActivo();
            $periodo_id = $activo['id_periodo'] ?? ($periodos[0]['id_periodo'] ?? 0);
        }

        $nivel_id = (int)($_GET['nivel_id'] ?? 0);
        $programa_id = (int)($_GET['programa_id'] ?? 0);

        // Si se envió un programa pero no nivel_id, detectar su nivel académico
        if (!$nivel_id && $programa_id) {
            foreach ($programas as $p) {
                if ((int)$p['id_programa'] === $programa_id) {
                    $nivel_id = (int)($p['id_nivel_academico'] ?? 0);
                    break;
                }
            }
        }

        // Si no hay programa seleccionado, tomar el primero disponible
        if (!$programa_id && !empty($programas)) {
            if ($nivel_id) {
                foreach ($programas as $p) {
                    if ((int)($p['id_nivel_academico'] ?? 0) === $nivel_id) {
                        $programa_id = (int)$p['id_programa'];
                        break;
                    }
                }
            }
            if (!$programa_id) {
                $programa_id = (int)($programas[0]['id_programa'] ?? 0);
            }
        }

        // Obtener solo las asignaturas que pertenezcan a este programa (con su semestre desde asignatura_programa)
        $asignaturas = $programa_id ? Asignatura::obtenerPorPrograma($programa_id) : [];

        // Identificar asignaturas que ya fueron ofertadas en este periodo para este programa
        $ofertadasIds = ($periodo_id && $programa_id) ? OfertaAcademica::obtenerIdsOfertados($periodo_id, $programa_id) : [];

        $this->render('admin/oferta/form', [
            'titulo'        => 'Cargar Oferta Académica',
            'periodos'      => $periodos,
            'programas'     => $programas,
            'niveles'       => $niveles,
            'nivel_id'      => $nivel_id,
            'asignaturas'   => $asignaturas,
            'ofertadasIds'  => $ofertadasIds,
            'periodo_id'    => $periodo_id,
            'programa_id'   => $programa_id
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_periodo  = (int)($_POST['id_periodo'] ?? 0);
            $id_programa = (int)($_POST['id_programa'] ?? 0);

            // Admite selección múltiple de checkboxes id_asignaturas[] o individual id_asignatura
            $asignaturasSeleccionadas = $_POST['id_asignaturas'] ?? [];
            if (!is_array($asignaturasSeleccionadas) && !empty($_POST['id_asignatura'])) {
                $asignaturasSeleccionadas = [(int)$_POST['id_asignatura']];
            }

            if (!$id_periodo || !$id_programa || empty($asignaturasSeleccionadas)) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'Selección incompleta',
                    'text'  => 'Debes seleccionar el periodo, programa y al menos una asignatura para agregar a la oferta.',
                    'icon'  => 'warning'
                ];
                $this->redirect("?ruta=admin/academico/oferta/crear&periodo_id=$id_periodo&programa_id=$id_programa");
                return;
            }

            $cantidad = OfertaAcademica::crearMultiples($id_periodo, $id_programa, $asignaturasSeleccionadas);

            $_SESSION['swal_alerta'] = [
                'title' => 'Oferta actualizada',
                'text'  => "Se agregaron {$cantidad} asignatura(s) a la oferta académica del periodo.",
                'icon'  => 'success'
            ];

            $this->redirect("?ruta=admin/academico/oferta&periodo_id=$id_periodo&programa_id=$id_programa");
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? 0);
        $periodo_id = (int)($_GET['periodo_id'] ?? 0);
        $programa_id = (int)($_GET['programa_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            OfertaAcademica::desactivar($id);
            $_SESSION['swal_alerta'] = [
                'title' => 'Oferta desactivada',
                'text'  => 'La asignatura ya no se encuentra activa en la oferta del periodo.',
                'icon'  => 'success'
            ];
        }
        $redirectUrl = "?ruta=admin/academico/oferta&periodo_id=$periodo_id";
        if ($programa_id) {
            $redirectUrl .= "&programa_id=$programa_id";
        }
        $this->redirect($redirectUrl);
    }
}
