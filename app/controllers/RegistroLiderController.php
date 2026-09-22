<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Programa;
use App\Models\Usuario;

class RegistroLiderController extends Controller {
    private $id_institucion = 1;

    public function mostrar() {
        $habilitado = defined('App\Config\AUTORREGISTRO_LIDERES_HABILITADO')
            ? constant('App\Config\AUTORREGISTRO_LIDERES_HABILITADO')
            : false;

        $this->render('auth/registro_lider', [
            'titulo' => 'Solicitar acceso',
            'habilitado' => $habilitado,
            'programas' => $habilitado ? Programa::obtenerTodos($this->id_institucion) : []
        ]);
    }

    public function guardar() {
        Auth::init();
        $habilitado = defined('App\Config\AUTORREGISTRO_LIDERES_HABILITADO')
            ? constant('App\Config\AUTORREGISTRO_LIDERES_HABILITADO')
            : false;
        if (!$habilitado || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=registro/lider');
        }

        $passwordInterna = Usuario::generarPasswordTemporal();
        Usuario::guardar([
            'id_institucion' => $this->id_institucion,
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $passwordInterna,
            'password_temporal' => 1,
            'rol' => 'LIDER_PROGRAMA',
            'estado' => 'PENDIENTE',
        ], [(int)($_POST['id_programa'] ?? 0)]);

        $_SESSION['swal_alerta'] = [
            'icon' => 'success',
            'title' => 'Solicitud recibida',
            'text' => 'Tu solicitud quedo pendiente de aprobacion por el Administrador.'
        ];
        $this->redirect('?ruta=login');
    }
}
