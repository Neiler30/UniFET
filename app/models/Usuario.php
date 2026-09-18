<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Usuario {
    public static function buscarPorCorreo($correo) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM usuario WHERE correo = :correo AND estado = 'ACTIVO'");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
