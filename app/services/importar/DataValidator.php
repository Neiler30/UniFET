<?php

namespace App\Services\Importar;

class DataValidator {
    
    /**
     * Valida los datos mapeados contra las reglas de la entidad.
     * @param string $entityKey
     * @param array $mapeoUsuario [ 'A' => 'codigo', 'B' => 'nombre', ... ]
     * @param array $dataRows Filas de excel
     * @return array
     */
    public static function validate(string $entityKey, array $mapeoUsuario, array $dataRows): array {
        $catalogo = Catalog::getEntities()[$entityKey];
        $filas = [];
        $stats = [
            'total'      => count($dataRows),
            'nuevos'     => 0,
            'completar'  => 0,
            'actualizar' => 0,
            'duplicados' => 0,
            'errores'    => 0
        ];

        foreach ($dataRows as $index => $row) {
            $erroresFila = [];
            $filaEstructurada = [
                'fila_excel' => $index
            ];

            // 1. Extraer los datos según el mapeo
            foreach ($mapeoUsuario as $col => $campoInterno) {
                if (!empty($campoInterno)) {
                    $filaEstructurada[$campoInterno] = $row[$col] ?? null;
                }
            }

            // 2. Validar campos requeridos y tipos (basado en catálogo)
            foreach ($catalogo['campos'] as $campoKey => $config) {
                $valor = $filaEstructurada[$campoKey] ?? null;
                
                // Chequear requerido
                if ($config['requerido'] && (trim((string)$valor) === '')) {
                    $erroresFila[] = "El campo '{$config['label']}' es obligatorio.";
                }

                // Validaciones adicionales
                if ($campoKey === 'creditos' && $valor !== null && !is_numeric($valor)) {
                    $erroresFila[] = "El campo 'Créditos' debe ser un valor numérico.";
                }
            }

            // 3. Resolver Relaciones
            $erroresRelacion = RelationshipResolver::resolve($entityKey, $filaEstructurada);
            $erroresFila = array_merge($erroresFila, $erroresRelacion);

            // 4. Establecer estado final
            if (count($erroresFila) > 0) {
                $filaEstructurada['estado'] = 'ERROR';
                $filaEstructurada['detalle'] = implode(' | ', $erroresFila);
                $stats['errores']++;
            } else {
                // Chequeo técnico en Base de Datos (NUEVO, EXISTENTE, ACTUALIZAR, CONFLICTO)
                $checkDB = ExistenceChecker::check($entityKey, $filaEstructurada);
                $filaEstructurada['estado'] = $checkDB['estado'];
                $filaEstructurada['detalle'] = $checkDB['detalle'];
                if (!empty($checkDB['id_existente'])) {
                    $filaEstructurada['id_existente'] = $checkDB['id_existente'];
                }
                if (!empty($checkDB['codigo_bd'])) {
                    $filaEstructurada['codigo_bd'] = $checkDB['codigo_bd'];
                }

                if ($checkDB['estado'] === 'NUEVO') {
                    $stats['nuevos']++;
                } elseif ($checkDB['estado'] === 'ACTUALIZAR') {
                    $stats['actualizar']++;
                } elseif ($checkDB['estado'] === 'COMPLETAR') {
                    $stats['completar']++;
                } elseif ($checkDB['estado'] === 'EXISTENTE' || $checkDB['estado'] === 'DUPLICADO') {
                    $stats['duplicados']++;
                } elseif ($checkDB['estado'] === 'CONFLICTO' || $checkDB['estado'] === 'ERROR') {
                    $stats['errores']++;
                }
            }

            $filas[] = $filaEstructurada;
        }

        return [
            'stats' => $stats,
            'filas' => $filas
        ];
    }
}
