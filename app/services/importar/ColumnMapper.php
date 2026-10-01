<?php

namespace App\Services\Importar;

class ColumnMapper {
    
    /**
     * Mapea los encabezados de un Excel a los campos internos de una entidad.
     * @param string $entityKey La entidad detectada o seleccionada (ej. 'ASIGNATURA')
     * @param array $headers Encabezados originales del excel ['A' => 'Cód Asig', 'B' => 'Nombre']
     * @return array Retorna la configuración de mapeo
     */
    public static function map(string $entityKey, array $headers): array {
        $entities = Catalog::getEntities();
        $entityConfig = $entities[$entityKey] ?? null;
        
        if (!$entityConfig) {
            throw new \Exception("Entidad no encontrada en el catálogo: {$entityKey}");
        }

        $mapping = [];
        $unmappedHeaders = [];

        foreach ($headers as $col => $originalHeader) {
            $normalizedHeader = HeaderNormalizer::normalizar($originalHeader);
            $matchedField = null;

            foreach ($entityConfig['campos'] as $fieldKey => $fieldConfig) {
                if (in_array($normalizedHeader, $fieldConfig['alias'])) {
                    $matchedField = $fieldKey;
                    break;
                }
            }

            if ($matchedField) {
                $mapping[$col] = [
                    'original' => $originalHeader,
                    'normalized' => $normalizedHeader,
                    'mapped_to' => $matchedField,
                    'label' => $entityConfig['campos'][$matchedField]['label'],
                    'status' => 'DETECTED'
                ];
            } else {
                $mapping[$col] = [
                    'original' => $originalHeader,
                    'normalized' => $normalizedHeader,
                    'mapped_to' => null,
                    'label' => null,
                    'status' => 'UNMAPPED'
                ];
                $unmappedHeaders[$col] = $originalHeader;
            }
        }

        return [
            'entity' => $entityKey,
            'mapping' => $mapping,
            'unmapped' => $unmappedHeaders
        ];
    }
}
