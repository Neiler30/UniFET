<?php

namespace App\Core;

class Auth {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($usuario) {
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];
        // Generar un token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function logout() {
        session_unset();
        session_destroy();
    }

    public static function isLogged() {
        return isset($_SESSION['usuario_id']);
    }

    public static function getRol() {
        return $_SESSION['usuario_rol'] ?? null;
    }

    public static function requerirRol($rol_requerido) {
        self::init();
        if (!self::isLogged()) {
            header('Location: ?ruta=login');
            exit;
        }

        if (self::getRol() !== $rol_requerido) {
            die("Acceso denegado. Se requiere rol: " . $rol_requerido);
        }

        if (!empty($_SESSION['debe_cambiar_password'])) {
            $ruta = $_GET['ruta'] ?? '';
            $permitidas = ['auth/cambiar_password_temporal', 'admin/dashboard', 'lider/dashboard'];
            if (!in_array($ruta, $permitidas, true)) {
                $destino = self::getRol() === 'ADMINISTRADOR' ? 'admin/dashboard' : 'lider/dashboard';
                header('Location: ?ruta=' . $destino);
                exit;
            }
        }
    }

    public static function getUsuarioActual() {
        if (!self::isLogged()) return null;
        return [
            'id_usuario' => $_SESSION['usuario_id'],
            'rol' => $_SESSION['usuario_rol'],
            'nombre' => $_SESSION['usuario_nombre'],
            'apellido' => $_SESSION['usuario_apellido']
        ];
    }
}
