<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Docente;
use App\Models\Programa;
use App\Models\Asignatura;
use App\Models\NivelAcademico;
use Throwable;

class DocenteController extends Controller {
    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/docentes/index', [
            'titulo' => 'Docentes',
            'docentes' => Docente::obtenerTodos($this->id_institucion),
        ]);
    }

    public function crear() {
        $this->formulario();
    }

    public function editar() {
        $id = (int)($_GET['id'] ?? 0);
        $this->formulario($id);
    }

    private function formulario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        
        $docente = null;
        $perfilAcademico = [];

        if ($id) {
            $docente = Docente::obtenerPorId($id);
            if (!$docente) {
                $_SESSION['swal_alerta'] = [
                    'icon' => 'error',
                    'title' => 'Docente no encontrado',
                    'text' => 'El docente solicitado no existe en el sistema.'
                ];
                $this->redirect('?ruta=admin/academico/docentes');
                return;
            }
            $perfilAcademico = Docente::obtenerPerfilAcademico($id);
        }

        $programas = Programa::obtenerTodos($this->id_institucion);
        $nivelesAcademicos = NivelAcademico::obtenerTodos();

        $this->render('admin/docentes/form', [
            'titulo'            => $id ? 'Editar Docente' : 'Registrar Docente',
            'docente'           => $docente,
            'perfilAcademico'   => $perfilAcademico,
            'programas'         => $programas,
            'nivelesAcademicos' => $nivelesAcademicos,
        ]);
    }

    public function guardar() {
        $this->persistir();
    }

    public function actualizar() {
        $id = (int)($_POST['id_docente'] ?? $_GET['id'] ?? 0);
        $this->persistir($id ?: null);
    }

    private function persistir($id = null) {
        Auth::requerirRol('ADMINISTRADOR');

        $esAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/academico/docentes');
            return;
        }

        // Obtener datos del cuerpo o POST
        $payloadRaw = file_get_contents('php://input');
        $jsonPayload = json_decode($payloadRaw, true);

        if (is_array($jsonPayload) && isset($jsonPayload['nombre'])) {
            $nombre = trim($jsonPayload['nombre'] ?? '');
            $apellido = trim($jsonPayload['apellido'] ?? '');
            $correo = trim($jsonPayload['correo'] ?? '');
            $codigo = trim($jsonPayload['codigo'] ?? '');
            $estado = $jsonPayload['estado'] ?? 'ACTIVO';
            $perfilProgramas = (array)($jsonPayload['programas'] ?? []);
            if (!$id && !empty($jsonPayload['id_docente'])) {
                $id = (int)$jsonPayload['id_docente'];
            }
        } else {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $codigo = trim($_POST['codigo'] ?? '');
            $estado = $_POST['estado'] ?? 'ACTIVO';

            // El perfil académico se puede enviar serializado en JSON desde el campo oculto
            $perfilJson = $_POST['perfil_academico'] ?? '';
            if (!empty($perfilJson)) {
                $perfilProgramas = json_decode($perfilJson, true);
                if (!is_array($perfilProgramas)) {
                    $perfilProgramas = [];
                }
            } else {
                $perfilProgramas = (array)($_POST['programas'] ?? []);
            }
        }

        $datos = [
            'id_institucion' => $this->id_institucion,
            'nombre'         => $nombre,
            'apellido'       => $apellido,
            'correo'         => $correo,
            'codigo'         => $codigo,
            'estado'         => $estado,
        ];

        try {
            $idDocenteGuardado = Docente::guardarConPerfil($datos, $perfilProgramas, $id);

            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'id_docente' => $idDocenteGuardado,
                    'message' => 'Docente guardado correctamente con su configuración académica.'
                ]);
                exit;
            }

            $_SESSION['swal_alerta'] = [
                'icon' => 'success',
                'title' => $id ? 'Docente actualizado' : 'Docente registrado',
                'text' => 'La información básica y el perfil académico quedaron guardados correctamente.'
            ];
            $this->redirect('?ruta=admin/academico/docentes');
        } catch (Throwable $e) {
            if ($esAjax) {
                header('Content-Type: application/json', true, 400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            }

            $_SESSION['swal_alerta'] = [
                'icon' => 'error',
                'title' => 'Error al guardar docente',
                'text' => $e->getMessage()
            ];
            if ($id) {
                $this->redirect('?ruta=admin/academico/docentes/editar&id=' . $id);
            } else {
                $this->redirect('?ruta=admin/academico/docentes/crear');
            }
        }
    }

    /**
     * Endpoint API JSON para consultar las asignaturas de un programa académico.
     */
    public function apiAsignaturas() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');

        $idPrograma = (int)($_GET['id_programa'] ?? 0);
        if ($idPrograma <= 0) {
            echo json_encode([]);
            exit;
        }

        $asignaturas = Asignatura::obtenerPorPrograma($idPrograma);
        echo json_encode($asignaturas);
        exit;
    }

    /**
     * Endpoint API JSON para generar o consultar el código sugerido en tiempo real.
     */
    public function apiGenerarCodigo() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');

        $nombre = $_GET['nombre'] ?? '';
        $apellido = $_GET['apellido'] ?? '';
        $idExcluir = !empty($_GET['id_docente']) ? (int)$_GET['id_docente'] : null;

        $codigo = Docente::generarCodigoSugerido($nombre, $apellido, $this->id_institucion, $idExcluir);
        echo json_encode([
            'success' => true,
            'codigo' => $codigo
        ]);
        exit;
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id > 0) {
            Docente::cambiarEstado($id);
            $_SESSION['swal_alerta'] = [
                'icon' => 'success',
                'title' => 'Estado actualizado',
                'text' => 'El estado del docente fue modificado exitosamente.'
            ];
        }
        $this->redirect('?ruta=admin/academico/docentes');
    }
}
