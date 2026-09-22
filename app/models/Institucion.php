<?php

namespace App\Models;

use App\Config\Database;

class Institucion {
    public static function obtener($id = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM institucion WHERE id_institucion = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function actualizar($id, $datos) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            UPDATE institucion
            SET nombre = :nombre, nombre_sistema = :nombre_sistema, lema = :lema,
                logo_url = :logo_url, favicon_url = :favicon_url,
                color_principal = :color_principal, color_secundario = :color_secundario, color_acento = :color_acento
            WHERE id_institucion = :id
        ");
        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':nombre_sistema' => $datos['nombre_sistema'],
            ':lema' => $datos['lema'],
            ':logo_url' => $datos['logo_url'],
            ':favicon_url' => $datos['favicon_url'],
            ':color_principal' => $datos['color_principal'],
            ':color_secundario' => $datos['color_secundario'],
            ':color_acento' => $datos['color_acento'],
            ':id' => $id,
        ]);
    }
}
