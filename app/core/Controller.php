<?php

namespace App\Core;

class Controller {
    protected function render($vista, $datos = []) {
        // Extraer variables para que estén disponibles en la vista
        extract($datos);
        
        $archivo_vista = __DIR__ . '/../views/' . $vista . '.php';
        
        if (file_exists($archivo_vista)) {
            require $archivo_vista;
        } else {
            die("La vista '$vista' no existe.");
        }
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}
