<?php

namespace App\Core;

class Router {
    private $rutas = [];

    public function agregarRuta($ruta, $controlador, $metodo) {
        $this->rutas[$ruta] = ['controlador' => $controlador, 'metodo' => $metodo];
    }

    public function despachar($ruta_solicitada) {
        // Por defecto, si no hay ruta, ir a login
        $ruta_solicitada = empty($ruta_solicitada) ? 'login' : $ruta_solicitada;

        if (array_key_exists($ruta_solicitada, $this->rutas)) {
            $info_ruta = $this->rutas[$ruta_solicitada];
            $nombre_controlador = $info_ruta['controlador'];
            $nombre_metodo = $info_ruta['metodo'];

            if (class_exists($nombre_controlador)) {
                $controlador = new $nombre_controlador();
                if (method_exists($controlador, $nombre_metodo)) {
                    $controlador->$nombre_metodo();
                } else {
                    die("El método $nombre_metodo no existe en el controlador $nombre_controlador.");
                }
            } else {
                die("El controlador $nombre_controlador no existe.");
            }
        } else {
            // Ruta no encontrada, ir a login por seguridad o a un 404
            header('Location: ?ruta=login');
            exit;
        }
    }
}
