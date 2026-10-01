<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Sede;
use App\Models\Bloque;
use App\Models\Espacio;

class EspaciosUnificadosController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $sedes = Sede::obtenerTodas($this->id_institucion);
        $bloques = Bloque::obtenerTodos($this->id_institucion);
        $espacios = Espacio::obtenerTodos($this->id_institucion);

        $this->render('admin/espacios_unificados/index', [
            'titulo' => 'Gestión de Espacios Físicos',
            'sedes' => $sedes,
            'bloques' => $bloques,
            'espacios' => $espacios
        ]);
    }

    public function crear() {
        Auth::requerirRol('ADMINISTRADOR');
        $paso = (int)($_GET['paso'] ?? 1);
        $id_sede = (int)($_GET['id_sede'] ?? 0);
        $id_bloque = (int)($_GET['id_bloque'] ?? 0);

        $sedes = Sede::obtenerTodas($this->id_institucion);
        $bloques = $id_sede ? Bloque::obtenerTodosPorSede($id_sede) : [];
        $espacio = null;

        // Pre-cargar datos si venimos de un paso anterior
        if ($paso === 2 && $id_sede) {
            $sede = Sede::obtenerPorId($id_sede, $this->id_institucion);
        }
        if ($paso === 3 && $id_bloque) {
            $bloque = Bloque::obtenerPorId($id_bloque);
            $sede = $bloque ? Sede::obtenerPorId($bloque['id_sede'], $this->id_institucion) : null;
        }

        // Generar códigos automáticos sugeridos
        $codigoSedeSugerido = $this->generarCodigoSedeSugerido($sedes);
        $codigoBloqueSugerido = $id_sede ? $this->generarCodigoBloqueSugerido($id_sede, $bloques) : null;
        $codigoEspacioSugerido = $id_bloque ? $this->generarCodigoEspacioSugerido($id_bloque, $espacios) : null;

        $this->render('admin/espacios_unificados/crear', [
            'titulo' => 'Nueva Sede / Bloque / Espacio',
            'paso' => $paso,
            'sedes' => $sedes,
            'bloques' => $bloques,
            'id_sede' => $id_sede,
            'id_bloque' => $id_bloque,
            'sede' => $sede ?? null,
            'bloque' => $bloque ?? null,
            'espacio' => $espacio,
            'codigoSedeSugerido' => $codigoSedeSugerido,
            'codigoBloqueSugerido' => $codigoBloqueSugerido,
            'codigoEspacioSugerido' => $codigoEspacioSugerido,
            'error' => null
        ]);
    }

    public function guardarSede() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/espacios/unificados');
        }

        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($codigo) || empty($nombre)) {
            $this->redirect('?ruta=admin/espacios/unificados/crear&paso=1&error=' . urlencode('Código y Nombre son obligatorios'));
        }

        if (Sede::existeCodigo($codigo, $this->id_institucion)) {
            $this->redirect('?ruta=admin/espacios/unificados/crear&paso=1&error=' . urlencode('Ese código ya existe para esta institución'));
        }

        $idCreado = Sede::crear([
            'id_institucion' => $this->id_institucion,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'estado' => $estado
        ]);

        if ($idCreado) {
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Sede creada', 'text' => 'La sede se registró correctamente. Ahora puedes agregar bloques.'];
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=2&id_sede={$idCreado}");
        } else {
            $this->redirect('?ruta=admin/espacios/unificados/crear&paso=1&error=' . urlencode('Error al crear la sede'));
        }
    }

    public function guardarBloque() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/espacios/unificados');
        }

        $id_sede = (int)($_POST['id_sede'] ?? 0);
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($id_sede) || empty($codigo) || empty($nombre)) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=2&id_sede={$id_sede}&error=" . urlencode('Todos los campos son obligatorios'));
        }

        if (Bloque::existeCodigo($codigo, $id_sede)) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=2&id_sede={$id_sede}&error=" . urlencode('Ese código ya existe para esta sede'));
        }

        $idCreado = Bloque::crear([
            'id_sede' => $id_sede,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'estado' => $estado
        ]);

        if ($idCreado) {
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Bloque creado', 'text' => 'El bloque se registró correctamente. Ahora puedes agregar espacios.'];
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$idCreado}");
        } else {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=2&id_sede={$id_sede}&error=" . urlencode('Error al crear el bloque'));
        }
    }

    public function guardarEspacio() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/espacios/unificados');
        }

        $id_bloque = (int)($_POST['id_bloque'] ?? 0);
        $id_sede = (int)($_POST['id_sede'] ?? 0);
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = $_POST['tipo'] ?? 'AULA';
        $capacidad = (int)($_POST['capacidad'] ?? 0);
        $caracteristicas = $_POST['caracteristicas'] ?? '[]';

        if (empty($id_bloque) || empty($codigo) || empty($nombre)) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}&error=" . urlencode('Bloque, Código y Nombre son obligatorios'));
        }

        if (Espacio::existeCodigo($codigo, $id_bloque)) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}&error=" . urlencode('Ese código ya existe para este bloque'));
        }

        // Validar JSON de características
        $caracteristicas = trim($caracteristicas);
        if ($caracteristicas === '' || $caracteristicas === '[]') {
            $caracteristicas = '[]';
        } else {
            json_decode($caracteristicas);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}&error=" . urlencode('Las características deben ser un JSON válido'));
            }
        }

        $idCreado = Espacio::crear([
            'id_bloque' => $id_bloque,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'capacidad' => $capacidad,
            'caracteristicas' => $caracteristicas
        ]);

        if ($idCreado) {
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Espacio creado', 'text' => 'El espacio físico se registró correctamente.'];
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}&creado=1");
        } else {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}&error=" . urlencode('Error al crear el espacio'));
        }
    }

    public function agregarOtroEspacio() {
        Auth::requerirRol('ADMINISTRADOR');
        $id_sede = (int)($_GET['id_sede'] ?? 0);
        $id_bloque = (int)($_GET['id_bloque'] ?? 0);
        if ($id_sede && $id_bloque) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=3&id_sede={$id_sede}&id_bloque={$id_bloque}");
        } else {
            $this->redirect('?ruta=admin/espacios/unificados/crear&paso=1');
        }
    }

    public function agregarOtroBloque() {
        Auth::requerirRol('ADMINISTRADOR');
        $id_sede = (int)($_GET['id_sede'] ?? 0);
        if ($id_sede) {
            $this->redirect("?ruta=admin/espacios/unificados/crear&paso=2&id_sede={$id_sede}");
        } else {
            $this->redirect('?ruta=admin/espacios/unificados/crear&paso=1');
        }
    }

    // API para generar códigos automáticos
    public function apiGenerarCodigoSede() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $sedes = Sede::obtenerTodas($this->id_institucion);
        echo json_encode(['codigo' => $this->generarCodigoSedeSugerido($sedes)]);
    }

    public function apiGenerarCodigoBloque() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $id_sede = (int)($_GET['id_sede'] ?? 0);
        $bloques = $id_sede ? Bloque::obtenerTodosPorSede($id_sede) : [];
        echo json_encode(['codigo' => $this->generarCodigoBloqueSugerido($id_sede, $bloques)]);
    }

    public function apiGenerarCodigoEspacio() {
        Auth::requerirRol('ADMINISTRADOR');
        header('Content-Type: application/json');
        $id_bloque = (int)($_GET['id_bloque'] ?? 0);
        $espacios = $id_bloque ? Espacio::obtenerTodos(1) : []; // Filtrar por bloque en el método
        // Filtrar espacios del bloque
        $espaciosBloque = array_filter($espacios, fn($e) => (int)$e['id_bloque'] === $id_bloque);
        echo json_encode(['codigo' => $this->generarCodigoEspacioSugerido($id_bloque, $espaciosBloque)]);
    }

    private function generarCodigoSedeSugerido($sedes) {
        $maxNum = 0;
        foreach ($sedes as $sede) {
            if (preg_match('/^SED-(\d+)$/', $sede['codigo'], $matches)) {
                $maxNum = max($maxNum, (int)$matches[1]);
            }
        }
        return 'SED-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generarCodigoBloqueSugerido($id_sede, $bloques) {
        $sede = Sede::obtenerPorId($id_sede, $this->id_institucion);
        if (!$sede) return 'BLQ-001';
        
        $prefijo = substr($sede['codigo'], 0, 4) . '-';
        $maxNum = 0;
        foreach ($bloques as $bloque) {
            if (preg_match('/^' . preg_quote($prefijo, '/') . '(\d+)$/', $bloque['codigo'], $matches)) {
                $maxNum = max($maxNum, (int)$matches[1]);
            }
        }
        return $prefijo . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generarCodigoEspacioSugerido($id_bloque, $espacios) {
        $bloque = Bloque::obtenerPorId($id_bloque);
        if (!$bloque) return 'ESP-001';
        
        $prefijo = substr($bloque['codigo'], 0, 4) . '-';
        $maxNum = 0;
        foreach ($espacios as $espacio) {
            if (preg_match('/^' . preg_quote($prefijo, '/') . '(\d+)$/', $espacio['codigo'], $matches)) {
                $maxNum = max($maxNum, (int)$matches[1]);
            }
        }
        return $prefijo . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }
}
