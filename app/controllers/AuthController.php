<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Usuario;

class AuthController extends Controller {

    public function __construct() {
        Auth::init();
    }

    public function mostrarLogin() {
        if (Auth::isLogged()) {
            $this->redirigirPorRol(Auth::getRol());
        }
        $this->render('auth/login', ['error' => '']);
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->mostrarLogin();
            return;
        }

        $demo = $_POST['demo_rol'] ?? '';
        if ($demo === 'ADMINISTRADOR') {
            $correo = 'admin@unicaribe.edu';
            $password = '123456';
        } elseif ($demo === 'LIDER_PROGRAMA') {
            $correo = 'lider@unicaribe.edu';
            $password = '123456';
        } else {
            $correo = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';
        }

        if (empty($correo) || empty($password)) {
            $this->render('auth/login', ['error' => 'Por favor, ingrese sus credenciales.']);
            return;
        }

        $usuario = Usuario::buscarParaLogin($correo);
        if ($usuario && ($usuario['estado'] ?? '') === 'PENDIENTE') {
            $this->render('auth/login', ['error' => 'Tu solicitud esta pendiente de aprobacion.']);
            return;
        }
        if ($usuario && ($usuario['estado'] ?? '') !== 'ACTIVO') {
            $this->render('auth/login', ['error' => 'Tu usuario se encuentra inactivo.']);
            return;
        }

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            Auth::login($usuario);
            if (!empty($usuario['password_temporal'])) {
                $_SESSION['debe_cambiar_password'] = true;
            }
            $this->redirigirPorRol($usuario['rol']);
            return;
        }

        $this->render('auth/login', ['error' => 'Credenciales invalidas.']);
    }

    public function cambiarPasswordTemporal() {
        Auth::init();
        if (!Auth::isLogged()) {
            $this->redirect('?ruta=login');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->dashboardUrl());
        }

        $password = trim($_POST['password'] ?? '');
        $confirmacion = trim($_POST['password_confirmacion'] ?? '');
        if (strlen($password) < 8 || $password !== $confirmacion) {
            $_SESSION['swal_alerta'] = [
                'icon' => 'error',
                'title' => 'Contrasena invalida',
                'text' => 'La nueva contrasena debe tener minimo 8 caracteres y coincidir con la confirmacion.'
            ];
            $this->redirect($this->dashboardUrl());
        }

        Usuario::cambiarPassword($_SESSION['usuario_id'], $password, false);
        unset($_SESSION['debe_cambiar_password']);
        $_SESSION['swal_alerta'] = [
            'icon' => 'success',
            'title' => 'Contrasena actualizada',
            'text' => 'Ya puedes navegar normalmente.'
        ];
        $this->redirect($this->dashboardUrl());
    }

    public function logout() {
        Auth::logout();
        $this->redirect('?ruta=login');
    }

    private function redirigirPorRol($rol) {
        $this->redirect($rol === 'ADMINISTRADOR' ? '?ruta=admin/dashboard' : ($rol === 'LIDER_PROGRAMA' ? '?ruta=lider/dashboard' : '?ruta=logout'));
    }

    private function dashboardUrl() {
        return Auth::getRol() === 'ADMINISTRADOR' ? '?ruta=admin/dashboard' : '?ruta=lider/dashboard';
    }
}
