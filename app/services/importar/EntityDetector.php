<?php

namespace App\Services\Importar;

class EntityDetector {
    
    /**
     * Intenta detectar la entidad basándose en los encabezados provistos.
     * @param array $headers Encabezados normalizados.
     * @return string|null Retorna el key de la entidad (ej. 'ASIGNATURA') o null.
     */
    public static function detect(array $headers): ?string {
        $entities = Catalog::getEntities();
        $bestMatch = null;
        $maxScore = 0;

        foreach ($entities as $entityKey => $entityConfig) {
            $score = 0;
            $requiredFields = 0;
            $matchedRequiredFields = 0;

            foreach ($entityConfig['campos'] as $fieldKey => $fieldConfig) {
                if ($fieldConfig['requerido']) {
                    $requiredFields++;
                }

                foreach ($headers as $header) {
                    $normalizedHeader = HeaderNormalizer::normalizar($header);
                    if (in_array($normalizedHeader, $fieldConfig['alias'])) {
                        $score++;
                        if ($fieldConfig['requerido']) {
                            $matchedRequiredFields++;
                        }
                        break; // Si encontró coincidencia para este campo, pasa al siguiente campo
                    }
                }
            }

            // Una heurística simple: 
            // - Damos más peso si coinciden campos requeridos
            // - Puntuación base es la cantidad de campos que hicieron match
            $finalScore = $score + ($matchedRequiredFields * 2);

            if ($finalScore > $maxScore && $matchedRequiredFields >= ($requiredFields / 2)) {
                $maxScore = $finalScore;
                $bestMatch = $entityKey;
            }
        }

        return $bestMatch;
    }
}
