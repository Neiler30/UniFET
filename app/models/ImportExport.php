<?php

namespace App\Models;

class ImportExport {
    public static function phpspreadsheetDisponible() {
        return class_exists('\PhpOffice\PhpSpreadsheet\IOFactory');
    }

    public static function validarFilas($tipo, $filas) {
        $requeridos = [
            'DOCENTE' => ['codigo', 'nombre', 'apellido'],
            'ASIGNATURA' => ['codigo', 'nombre', 'creditos'],
            'ESPACIO' => ['codigo', 'nombre', 'tipo', 'capacidad'],
            'PROGRAMA' => ['codigo', 'nombre'],
        ][$tipo] ?? [];

        $resultado = [];
        foreach ($filas as $i => $fila) {
            $errores = [];
            foreach ($requeridos as $campo) {
                if (!isset($fila[$campo]) || trim((string)$fila[$campo]) === '') {
                    $errores[] = "Falta {$campo}";
                }
            }
            $resultado[] = [
                'numero' => $i + 2,
                'datos' => $fila,
                'errores' => $errores,
                'valida' => empty($errores),
            ];
        }
        return $resultado;
    }
}
