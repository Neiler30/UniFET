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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($correo) || empty($password)) {
                $this->render('auth/login', ['error' => 'Por favor, ingrese sus credenciales.']);
                return;
            }

            $usuario = Usuario::buscarPorCorreo($correo);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                Auth::login($usuario);
                $this->redirigirPorRol($usuario['rol']);
            } else {
                $this->render('auth/login', ['error' => 'Credenciales inválidas.']);
            }
        } else {
            $this->mostrarLogin();
        }
    }

    public function logout() {
        Auth::logout();
        $this->redirect('?ruta=login');
    }

    private function redirigirPorRol($rol) {
        if ($rol === 'ADMINISTRADOR') {
            $this->redirect('?ruta=admin/dashboard');
        } elseif ($rol === 'LIDER_PROGRAMA') {
            $this->redirect('?ruta=lider/dashboard');
        } else {
            $this->logout();
        }
    }
}
