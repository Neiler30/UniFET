<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'unifet';
    const DB_USER = 'root';
    const DB_PASS = '';

    private static $conexion = null;

    public static function getConexion() {
        if (self::$conexion === null) {
            try {
                $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
                self::$conexion = new PDO($dsn, self::DB_USER, self::DB_PASS);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$conexion;
    }
}
