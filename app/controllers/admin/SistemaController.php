<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Programa;
use App\Services\NotificacionService;

class SistemaController extends Controller {
    private $id_institucion = 1;

    public function personalizacion() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/sistema/personalizacion', [
            'titulo' => 'Sistema',
            'institucion' => Institucion::obtener($this->id_institucion),
        ]);
    }

    public function guardarPersonalizacion() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Institucion::actualizar($this->id_institucion, [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'nombre_sistema' => trim($_POST['nombre_sistema'] ?? ''),
                'lema' => trim($_POST['lema'] ?? ''),
                'logo_url' => trim($_POST['logo_url'] ?? ''),
                'favicon_url' => trim($_POST['favicon_url'] ?? ''),
                'color_principal' => trim($_POST['color_principal'] ?? '#3B0D8F'),
                'color_secundario' => trim($_POST['color_secundario'] ?? '#0F9D58'),
                'color_acento' => trim($_POST['color_acento'] ?? '#F5B301'),
            ]);
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Identidad actualizada', 'text' => 'La personalizacion institucional fue guardada.'];
        }
        $this->redirect('?ruta=admin/sistema/personalizacion');
    }

    public function usuarios() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/sistema/usuarios_index', [
            'titulo' => 'Usuarios',
            'usuarios' => Usuario::obtenerTodos($this->id_institucion),
            'pendientes' => Usuario::pendientes($this->id_institucion),
        ]);
    }

    public function lideres() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/sistema/lideres', [
            'titulo' => 'Lideres de Programa',
            'usuarios' => Usuario::obtenerTodos($this->id_institucion),
            'programas' => Programa::obtenerTodos($this->id_institucion),
        ]);
    }

    public function guardarLider() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=admin/sistema/lideres');

        $programa = (int)($_POST['id_programa'] ?? 0);
        if (!$programa) {
            $_SESSION['swal_alerta'] = ['icon' => 'error', 'title' => 'Programa requerido', 'text' => 'Debes asociar el lider a un programa.'];
            $this->redirect('?ruta=admin/sistema/lideres');
        }

        $passwordTemporal = Usuario::generarPasswordTemporal();
        $usuario = [
            'id_institucion' => $this->id_institucion,
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $passwordTemporal,
            'password_temporal' => 1,
            'rol' => 'LIDER_PROGRAMA',
            'estado' => 'ACTIVO',
        ];
        Usuario::guardar($usuario, [$programa]);
        NotificacionService::enviarCredencialesLider($usuario, $passwordTemporal);
        $_SESSION['password_temporal_generada'] = [
            'correo' => $usuario['correo'],
            'password' => $passwordTemporal
        ];
        $this->redirect('?ruta=admin/sistema/lideres');
    }

    public function crearUsuario() { $this->formUsuario(); }
    public function editarUsuario() { $this->formUsuario((int)($_GET['id'] ?? 0)); }

    private function formUsuario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/sistema/usuarios_form', [
            'titulo' => $id ? 'Editar Usuario' : 'Nuevo Usuario',
            'usuario' => $id ? Usuario::obtenerPorId($id) : null,
            'programasAsignados' => $id ? Usuario::programas($id) : [],
            'programas' => Programa::obtenerTodos($this->id_institucion),
        ]);
    }

    public function guardarUsuario() { $this->persistirUsuario(); }
    public function actualizarUsuario() { $this->persistirUsuario((int)($_POST['id_usuario'] ?? 0)); }

    private function persistirUsuario($id = null) {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?ruta=admin/sistema/usuarios');
        $rol = $_POST['rol'] ?? 'LIDER_PROGRAMA';
        $passwordTemporal = null;
        $password = trim($_POST['password'] ?? '');
        $esNuevoLider = !$id && $rol === 'LIDER_PROGRAMA';
        if ($esNuevoLider) {
            $passwordTemporal = Usuario::generarPasswordTemporal();
            $password = $passwordTemporal;
        }

        Usuario::guardar([
            'id_institucion' => $this->id_institucion,
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $password,
            'password_temporal' => $passwordTemporal ? 1 : 0,
            'rol' => $rol,
            'estado' => $_POST['estado'] ?? 'ACTIVO',
        ], $_POST['programas'] ?? [], $id ?: null);
        if ($passwordTemporal) {
            $usuarioCreado = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
            ];
            NotificacionService::enviarCredencialesLider($usuarioCreado, $passwordTemporal);
            $_SESSION['password_temporal_generada'] = [
                'correo' => $usuarioCreado['correo'],
                'password' => $passwordTemporal
            ];
        } else {
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Usuario guardado', 'text' => 'La cuenta y sus permisos quedaron actualizados.'];
        }
        $this->redirect('?ruta=admin/sistema/usuarios');
    }

    public function desactivarUsuario() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') Usuario::cambiarEstado((int)($_GET['id'] ?? 0));
        $this->redirect('?ruta=admin/sistema/usuarios');
    }

    public function aprobarUsuario() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_GET['id'] ?? 0);
            $usuario = Usuario::obtenerPorId($id);
            if ($usuario && $usuario['estado'] === 'PENDIENTE') {
                $passwordTemporal = Usuario::generarPasswordTemporal();
                Usuario::cambiarPassword($id, $passwordTemporal, true);
                Usuario::cambiarEstadoDirecto($id, 'ACTIVO');
                NotificacionService::enviarCredencialesLider($usuario, $passwordTemporal);
                $_SESSION['password_temporal_generada'] = [
                    'correo' => $usuario['correo'],
                    'password' => $passwordTemporal
                ];
            }
        }
        $this->redirect('?ruta=admin/sistema/usuarios&tab=pendientes');
    }

    public function rechazarUsuario() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Usuario::cambiarEstadoDirecto((int)($_GET['id'] ?? 0), 'INACTIVO');
            $_SESSION['swal_alerta'] = ['icon' => 'success', 'title' => 'Solicitud rechazada', 'text' => 'El usuario quedo inactivo.'];
        }
        $this->redirect('?ruta=admin/sistema/usuarios&tab=pendientes');
    }
}
