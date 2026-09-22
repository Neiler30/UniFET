<?php

namespace App\Services;

class NotificacionService {
    public static function enviarCredencialesLider(array $usuario, string $passwordTemporal): void {
        // TODO: integrar PHPMailer o similar para envio real por SMTP.
        $linea = sprintf(
            "[%s] [SIMULADO] Correo a %s: contrasena temporal %s%s",
            date('Y-m-d H:i:s'),
            $usuario['correo'] ?? 'sin-correo',
            $passwordTemporal,
            PHP_EOL
        );

        $directorio = __DIR__ . '/../../storage/logs';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }
        file_put_contents($directorio . '/notificaciones.log', $linea, FILE_APPEND);
        error_log(trim($linea));
    }
}
