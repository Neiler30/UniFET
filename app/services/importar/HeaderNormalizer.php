<?php

namespace App\Services\Importar;

class HeaderNormalizer {
    
    /**
     * Normaliza un texto para ser comparado de forma flexible.
     * Convierte a minúsculas, elimina acentos, caracteres especiales y espacios extra.
     */
    public static function normalizar(string $texto): string {
        // A minúsculas
        $texto = strtolower(trim($texto));
        
        // Quitar acentos
        $texto = self::quitarAcentos($texto);
        
        // Reemplazar guiones o puntos por espacios para limpiar después
        $texto = str_replace(['-', '_', '.'], ' ', $texto);
        
        // Eliminar caracteres especiales (deja solo letras, números y espacios)
        $texto = preg_replace('/[^a-z0-9\s]/', '', $texto);
        
        // Reemplazar múltiples espacios por uno solo
        $texto = preg_replace('/\s+/', ' ', $texto);
        
        return trim($texto);
    }

    private static function quitarAcentos(string $cadena): string {
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
        return utf8_encode($cadena);
    }
}
