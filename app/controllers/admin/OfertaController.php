<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\OfertaAcademica;
use App\Models\PeriodoAcademico;
use App\Models\Programa;
use App\Config\Database;
use PDO;

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
            'titulo' => 'Oferta Académica',
            'periodos' => $periodos,
            'programas' => $programas,
            'periodo_seleccionado' => $id_periodo,
            'programa_seleccionado' => $id_programa,
            'ofertas' => $ofertas
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $periodos = PeriodoAcademico::obtenerTodos($this->id_institucion);
        $programas = Programa::obtenerTodos($this->id_institucion);

        $periodo_id = (int)($_GET['periodo_id'] ?? 0);
        if (!$periodo_id) {
            $activo = PeriodoAcademico::getActivo();
            $periodo_id = $activo['id_periodo'] ?? ($periodos[0]['id_periodo'] ?? 0);
        }

        $programa_id = (int)($_GET['programa_id'] ?? ($programas[0]['id_programa'] ?? 0));

        // Obtener asignaturas disponibles
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT a.* FROM asignatura a
            WHERE a.id_institucion = :inst AND a.estado = 'ACTIVO'
            ORDER BY a.nombre ASC
        ");
        $stmt->execute([':inst' => $this->id_institucion]);
        $asignaturas = $stmt->fetchAll();

        $this->render('admin/oferta/form', [
            'titulo' => 'Cargar Oferta Académica',
            'periodos' => $periodos,
            'programas' => $programas,
            'asignaturas' => $asignaturas,
            'periodo_id' => $periodo_id,
            'programa_id' => $programa_id
        ]);
    }

    public function guardar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_periodo = (int)($_POST['id_periodo'] ?? 0);
            $id_programa = (int)($_POST['id_programa'] ?? 0);
            $id_asignatura = (int)($_POST['id_asignatura'] ?? 0);

            if (!$id_periodo || !$id_programa || !$id_asignatura) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'No se pudo guardar',
                    'text' => 'Todos los campos son obligatorios.',
                    'icon' => 'error'
                ];
                $this->redirect("?ruta=admin/academico/oferta/crear&periodo_id=$id_periodo&programa_id=$id_programa");
                return;
            }

            if (OfertaAcademica::existeOferta($id_periodo, $id_programa, $id_asignatura)) {
                $_SESSION['swal_alerta'] = [
                    'title' => 'Asignatura ya ofertada',
                    'text' => 'Esta asignatura ya se encuentra ofertada para este programa en el periodo seleccionado.',
                    'icon' => 'warning'
                ];
                $this->redirect("?ruta=admin/academico/oferta/crear&periodo_id=$id_periodo&programa_id=$id_programa");
                return;
            }

            OfertaAcademica::crear([
                'id_periodo' => $id_periodo,
                'id_programa' => $id_programa,
                'id_asignatura' => $id_asignatura,
                'estado' => 'ACTIVA'
            ]);

            $_SESSION['swal_alerta'] = [
                'title' => 'Oferta agregada',
                'text' => 'La asignatura fue incorporada a la oferta académica del periodo.',
                'icon' => 'success'
            ];

            $this->redirect("?ruta=admin/academico/oferta&periodo_id=$id_periodo&programa_id=$id_programa");
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? 0);
        $periodo_id = (int)($_GET['periodo_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            OfertaAcademica::desactivar($id);
            $_SESSION['swal_alerta'] = [
                'title' => 'Oferta desactivada',
                'text' => 'La asignatura ya no se encuentra activa en la oferta del periodo.',
                'icon' => 'success'
            ];
        }
        $this->redirect("?ruta=admin/academico/oferta&periodo_id=$periodo_id");
    }
}

