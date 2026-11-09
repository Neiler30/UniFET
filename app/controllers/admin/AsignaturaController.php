<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Asignatura;
use App\Models\Programa;
use App\Models\AreaAcademica;
use Throwable;

class AsignaturaController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $areaFiltro = (int)($_GET['area_id'] ?? 0);
        $asignaturas = Asignatura::obtenerTodos($this->id_institucion);
        if ($areaFiltro) {
            $asignaturas = array_values(array_filter($asignaturas, function ($asignatura) use ($areaFiltro) {
                return (int)($asignatura['id_area_academica'] ?? 0) === $areaFiltro;
            }));
        }
        $this->render('admin/asignaturas/index', [
            'titulo'      => 'Asignaturas',
            'asignaturas' => $asignaturas,
            'areas'       => AreaAcademica::obtenerTodas($this->id_institucion, true),
            'areaFiltro'  => $areaFiltro,
            'programas'   => Programa::obtenerTodos($this->id_institucion),
        ]);
    }

    public function crear() { 
        $this->formulario(); 
    }

    public function editar() { 
        $this->formulario((int)($_GET['id'] ?? 0)); 
    }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/asignaturas/form', [
            'titulo'             => $id ? 'Editar Asignatura' : 'Nueva Asignatura',
            'asignatura'         => $id ? Asignatura::obtenerPorId($id) : null,
            'programasAsignados' => $id ? Asignatura::programasConSemestre($id) : [],
            'programas'          => Programa::obtenerTodos($this->id_institucion),
            'areas'              => AreaAcademica::obtenerTodas($this->id_institucion, true),
        ]);
    }

    public function guardar() { 
        $this->persistir(); 
    }

    public function actualizar() { 
        $this->persistir((int)($_POST['id_asignatura'] ?? 0)); 
    }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/academico/asignaturas');
        }

        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($codigo) || empty($nombre)) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Campos obligatorios',
                'text'  => 'El código y el nombre de la asignatura son obligatorios.',
            ];
            $this->redirect('?ruta=admin/academico/asignaturas/' . ($id ? 'editar&id=' . $id : 'crear'));
            return;
        }

        // Verificar duplicado de código antes de intentar guardar
        if (Asignatura::existeCodigo($codigo, $this->id_institucion, $id ?: null)) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'warning',
                'title' => 'Código duplicado',
                'text'  => "El código «{$codigo}» ya está en uso por otra asignatura. Por favor usa un código diferente.",
            ];
            $this->redirect('?ruta=admin/academico/asignaturas/' . ($id ? 'editar&id=' . $id : 'crear'));
            return;
        }

        // Procesar asociaciones programa + semestre
        $rawAsociaciones = $_POST['asociaciones'] ?? $_POST['programas'] ?? [];
        $asociacionesValidadas = [];
        $programasVistos = [];

        if (is_array($rawAsociaciones)) {
            foreach ($rawAsociaciones as $idx => $item) {
                // Soporta formato estructurado ['id_programa' => X, 'semestre' => Y]
                if (is_array($item)) {
                    $idPrograma = (int)($item['id_programa'] ?? 0);
                    $semestreVal = isset($item['semestre']) && trim((string)$item['semestre']) !== '' ? (int)$item['semestre'] : null;
                } else {
                    $idPrograma = (int)$item;
                    $semestreVal = null;
                }

                // Si la fila está vacía (sin programa seleccionado), la omitimos
                if ($idPrograma <= 0) {
                    continue;
                }

                // Validación 1: Programa duplicado en la misma asignatura
                if (isset($programasVistos[$idPrograma])) {
                    $_SESSION['swal_alerta'] = [
                        'icon'  => 'warning',
                        'title' => 'Programa duplicado',
                        'text'  => 'Este programa ya está asociado a la asignatura.',
                    ];
                    $this->redirect('?ruta=admin/academico/asignaturas/' . ($id ? 'editar&id=' . $id : 'crear'));
                    return;
                }
                $programasVistos[$idPrograma] = true;

                // Validación 2: Semestre obligatorio cuando se agrega un programa (entero positivo)
                if ($semestreVal === null || $semestreVal <= 0) {
                    $_SESSION['swal_alerta'] = [
                        'icon'  => 'error',
                        'title' => 'Semestre obligatorio',
                        'text'  => 'Selecciona el semestre correspondiente al programa.',
                    ];
                    $this->redirect('?ruta=admin/academico/asignaturas/' . ($id ? 'editar&id=' . $id : 'crear'));
                    return;
                }

                $asociacionesValidadas[] = [
                    'id_programa' => $idPrograma,
                    'semestre'    => $semestreVal,
                ];
            }
        }

        try {
            Asignatura::guardar([
                'id_institucion'    => $this->id_institucion,
                'id_area_academica' => (int)($_POST['id_area_academica'] ?? 0),
                'codigo'            => $codigo,
                'nombre'            => $nombre,
                'creditos'          => max(0, (int)($_POST['creditos'] ?? 0)),
                'estado'            => $_POST['estado'] ?? 'ACTIVO',
            ], $asociacionesValidadas, $id ?: null);

            $_SESSION['swal_alerta'] = [
                'icon'  => 'success', 
                'title' => 'Asignatura guardada', 
                'text'  => 'La asignatura y sus programas con semestre curricular quedaron actualizados.'
            ];
            $this->redirect('?ruta=admin/academico/asignaturas');
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error al guardar',
                'text'  => 'Ocurrió un error al registrar la información: ' . $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/academico/asignaturas/' . ($id ? 'editar&id=' . $id : 'crear'));
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Asignatura::cambiarEstado((int)($_GET['id'] ?? 0));
        }
        $this->redirect('?ruta=admin/academico/asignaturas');
    }
}
