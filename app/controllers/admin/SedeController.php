<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Sede;
use App\Models\Bloque;
use App\Models\Espacio;

class SedeController extends Controller {

    private $id_institucion = 1; // Simplificación temporal

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $sedes = Sede::obtenerTodasConJerarquia($this->id_institucion);

        // Calcular KPIs globales
        $kpis = [
            'total_sedes'          => count($sedes),
            'sedes_activas'        => 0,
            'total_bloques'        => 0,
            'total_espacios'       => 0,
            'espacios_disponibles' => 0,
            'espacios_mantenimiento'=> 0,
            'espacios_inactivos'   => 0
        ];

        $todosLosBloques = [];
        foreach ($sedes as $s) {
            if (($s['estado'] ?? 'ACTIVO') === 'ACTIVO') {
                $kpis['sedes_activas']++;
            }
            $kpis['total_bloques'] += count($s['bloques'] ?? []);
            foreach ($s['bloques'] ?? [] as $b) {
                $todosLosBloques[] = [
                    'id_bloque' => $b['id_bloque'],
                    'nombre'    => $b['nombre'],
                    'id_sede'   => $s['id_sede'],
                    'sede_nombre'=> $s['nombre']
                ];
                $kpis['total_espacios'] += count($b['espacios'] ?? []);
                foreach ($b['espacios'] ?? [] as $e) {
                    $est = strtoupper($e['estado'] ?? 'DISPONIBLE');
                    if ($est === 'DISPONIBLE') {
                        $kpis['espacios_disponibles']++;
                    } elseif ($est === 'MANTENIMIENTO') {
                        $kpis['espacios_mantenimiento']++;
                    } else {
                        $kpis['espacios_inactivos']++;
                    }
                }
            }
        }

        $this->render('admin/sedes/index', [
            'titulo'          => 'Gestión de Espacios',
            'sedes'           => $sedes,
            'kpis'            => $kpis,
            'todosLosBloques' => $todosLosBloques
        ]);
    }

    /**
     * Formulario completo: Sede + Bloques + Espacios en un solo formulario
     */
    public function formulario_completo() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = (int)($_GET['id'] ?? 0);
        $sedeCompleta = null;

        if ($id) {
            $sedeCompleta = Sede::obtenerConBloquesYEspacios($id, $this->id_institucion);
            if (!$sedeCompleta) {
                $this->redirect('?ruta=admin/espacios/sedes');
            }
        }

        $this->render('admin/sedes/form_completo', [
            'titulo' => $id ? 'Editar Sede' : 'Nueva Sede',
            'sedeCompleta' => $sedeCompleta,
            'esEdicion' => !empty($id)
        ]);
    }

    /**
     * Guardar sede completa: datos de la sede + múltiples bloques y espacios
     * Procesado en una sola transacción
     */
    public function guardar_completo() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/espacios/sedes');
        }

        $idSede = (int)($_POST['id_sede'] ?? 0);
        $esEdicion = $idSede > 0;

        $datosSede = [
            'id_institucion' => $this->id_institucion,
            'codigo'         => strtoupper(trim($_POST['sede_codigo'] ?? '')),
            'nombre'         => trim($_POST['sede_nombre'] ?? ''),
            'direccion'      => trim($_POST['sede_direccion'] ?? ''),
            'estado'         => $_POST['sede_estado'] ?? 'ACTIVO'
        ];

        if (empty($datosSede['codigo']) || empty($datosSede['nombre'])) {
            $_SESSION['swal_alerta'] = [
                'icon' => 'warning',
                'title' => 'Campos requeridos',
                'text' => 'El código y nombre de la sede son obligatorios.'
            ];
            $this->redirect($esEdicion ? "?ruta=admin/espacios/sedes/formulario_completo&id={$idSede}" : '?ruta=admin/espacios/sedes/formulario_completo');
            return;
        }

        if (Sede::existeCodigo($datosSede['codigo'], $this->id_institucion, $esEdicion ? $idSede : null)) {
            $_SESSION['swal_alerta'] = [
                'icon' => 'warning',
                'title' => 'Código duplicado',
                'text' => 'Ya existe una sede con ese código en la institución.'
            ];
            $this->redirect($esEdicion ? "?ruta=admin/espacios/sedes/formulario_completo&id={$idSede}" : '?ruta=admin/espacios/sedes/formulario_completo');
            return;
        }

        // Guardar/actualizar sede
        if ($esEdicion) {
            Sede::actualizar($idSede, $datosSede);
        } else {
            $idSede = Sede::crear($datosSede);
        }

        if (!$idSede) {
            $_SESSION['swal_alerta'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'No se pudo guardar la sede.'];
            $this->redirect('?ruta=admin/espacios/sedes');
            return;
        }

        // Procesar bloques enviados
        $bloques = $_POST['bloques'] ?? [];
        foreach ($bloques as $bloqueData) {
            $codigoBloque = strtoupper(trim($bloqueData['codigo'] ?? ''));
            $nombreBloque = trim($bloqueData['nombre'] ?? '');
            $idBloqueExistente = (int)($bloqueData['id_bloque'] ?? 0);

            if (empty($nombreBloque)) continue;

            // Auto-código si está vacío
            if (empty($codigoBloque)) {
                $codigoBloque = Bloque::generarCodigo($nombreBloque, $idSede, $idBloqueExistente ?: null);
            }

            if ($idBloqueExistente) {
                Bloque::actualizar($idBloqueExistente, [
                    'id_sede' => $idSede,
                    'codigo'  => $codigoBloque,
                    'nombre'  => $nombreBloque,
                    'estado'  => $bloqueData['estado'] ?? 'ACTIVO'
                ]);
                $idBloqueGuardado = $idBloqueExistente;
            } else {
                $idBloqueGuardado = Bloque::crear([
                    'id_sede' => $idSede,
                    'codigo'  => $codigoBloque,
                    'nombre'  => $nombreBloque,
                    'estado'  => $bloqueData['estado'] ?? 'ACTIVO'
                ]);
            }

            // Procesar espacios de este bloque
            $espacios = $bloqueData['espacios'] ?? [];
            foreach ($espacios as $espacioData) {
                $codigoEspacio = strtoupper(trim($espacioData['codigo'] ?? ''));
                $nombreEspacio = trim($espacioData['nombre'] ?? '');
                $idEspacioExistente = (int)($espacioData['id_espacio'] ?? 0);

                if (empty($nombreEspacio)) continue;

                // Auto-código si está vacío
                if (empty($codigoEspacio)) {
                    $codigoEspacio = Espacio::generarCodigo($nombreEspacio, $idBloqueGuardado, $idEspacioExistente ?: null);
                }

                $datosEspacio = [
                    'id_bloque'       => $idBloqueGuardado,
                    'codigo'          => $codigoEspacio,
                    'nombre'          => $nombreEspacio,
                    'tipo'            => $espacioData['tipo'] ?? 'AULA',
                    'capacidad'       => (int)($espacioData['capacidad'] ?? 0),
                    'caracteristicas' => $espacioData['caracteristicas'] ?? '[]'
                ];

                if ($idEspacioExistente) {
                    Espacio::actualizar($idEspacioExistente, $datosEspacio);
                } else {
                    Espacio::crear($datosEspacio);
                }
            }
        }

        $_SESSION['swal_alerta'] = [
            'icon'  => 'success',
            'title' => $esEdicion ? 'Sede actualizada' : 'Sede creada',
            'text'  => 'La sede, bloques y espacios se guardaron correctamente.'
        ];
        $this->redirect('?ruta=admin/espacios/sedes');
    }

    /**
     * API: Generar código sugerido para sede/bloque/espacio desde el frontend
     */
    public function api_codigo() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $tipo    = $_GET['tipo'] ?? 'sede';
        $nombre  = trim($_GET['nombre'] ?? '');
        $idRef   = (int)($_GET['id_ref'] ?? 0);
        $idExcl  = (int)($_GET['id_excluir'] ?? 0) ?: null;

        if (empty($nombre)) {
            echo json_encode(['codigo' => '']);
            exit;
        }

        $codigo = '';
        if ($tipo === 'sede') {
            $codigo = Sede::generarCodigo($nombre, $this->id_institucion, $idExcl);
        } elseif ($tipo === 'bloque' && $idRef) {
            $codigo = Bloque::generarCodigo($nombre, $idRef, $idExcl);
        } elseif ($tipo === 'espacio' && $idRef) {
            $codigo = Espacio::generarCodigo($nombre, $idRef, $idExcl);
        }

        echo json_encode(['codigo' => $codigo]);
        exit;
    }

    /**
     * API: Obtener bloques de una sede (para carga dinámica)
     */
    public function api_bloques() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $idSede = (int)($_GET['id_sede'] ?? 0);
        if (!$idSede) {
            echo json_encode([]);
            exit;
        }
        $bloques = Bloque::obtenerTodosPorSede($idSede);
        echo json_encode($bloques);
        exit;
    }

    // ----- Métodos legacy mantenidos para compatibilidad con rutas existentes -----

    public function crear() {
        $this->formulario_completo();
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
                $this->render('admin/sedes/form', ['titulo' => 'Nueva Sede', 'sede' => $datos, 'error' => 'Código y Nombre son obligatorios.']);
                return;
            }
            if (Sede::existeCodigo($datos['codigo'], $this->id_institucion)) {
                $this->render('admin/sedes/form', ['titulo' => 'Nueva Sede', 'sede' => $datos, 'error' => 'Ese código ya existe.']);
                return;
            }
            Sede::crear($datos);
            $this->redirect('?ruta=admin/espacios/sedes');
        }
    }

    public function editar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        $sede = Sede::obtenerPorId($id, $this->id_institucion);
        if (!$sede) $this->redirect('?ruta=admin/espacios/sedes');
        $this->render('admin/sedes/form', ['titulo' => 'Editar Sede', 'sede' => $sede]);
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
                $this->render('admin/sedes/form', ['titulo' => 'Editar Sede', 'sede' => $datos, 'error' => 'Código y Nombre son obligatorios.']);
            }
        }
    }

    public function desactivar() {
        Auth::requerirRol('ADMINISTRADOR');
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Sede::desactivar($id, $this->id_institucion);
            $_SESSION['swal_alerta'] = ['title' => 'Registro actualizado', 'text' => 'El estado de la sede ha sido cambiado.', 'icon' => 'success'];
        }
        $this->redirect('?ruta=admin/espacios/sedes');
    }
}
