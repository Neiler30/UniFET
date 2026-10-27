<?php

namespace App\Services\Importar;

class DataValidator {
    
    /**
     * Valida los datos mapeados contra las reglas de la entidad.
     * @param string $entityKey
     * @param array $mapeoUsuario [ 'A' => 'codigo', 'B' => 'nombre', ... ]
     * @param array $dataRows Filas de excel
     * @param int $id_institucion ID de la institución
     * @param string $modoDuplicados 'omitir', 'actualizar', 'estricto'
     * @return array
     */
    public static function validate(string $entityKey, array $mapeoUsuario, array $dataRows, int $id_institucion = 1, string $modoDuplicados = 'omitir'): array {
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

        // Rastreo de códigos o identificadores repetidos en el mismo archivo
        $clavesVistasEnLote = [];

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
            $erroresRelacion = RelationshipResolver::resolve($entityKey, $filaEstructurada, $id_institucion);
            $erroresFila = array_merge($erroresFila, $erroresRelacion);

            // 4. Establecer estado final
            if (count($erroresFila) > 0) {
                $filaEstructurada['estado'] = 'ERROR';
                $filaEstructurada['detalle'] = implode(' | ', $erroresFila);
                $stats['errores']++;
            } else {
                // Chequeo de duplicados dentro del mismo archivo Excel
                $claveIdentificadora = '';
                if ($entityKey === 'ASIGNATURA') {
                    $cod = trim((string)($filaEstructurada['codigo'] ?? ''));
                    $prog = !empty($filaEstructurada['id_programa']) ? (string)$filaEstructurada['id_programa'] : trim((string)($filaEstructurada['programa'] ?? ''));
                    if ($cod !== '') {
                        $claveIdentificadora = $prog !== '' ? ($cod . '||' . $prog) : $cod;
                    }
                } elseif ($entityKey === 'PROGRAMA') {
                    $claveIdentificadora = trim((string)($filaEstructurada['codigo'] ?? ''));
                } elseif ($entityKey === 'DOCENTE') {
                    $claveIdentificadora = trim((string)($filaEstructurada['documento'] ?? ''));
                }

                if ($claveIdentificadora !== '' && isset($clavesVistasEnLote[strtoupper($claveIdentificadora)])) {
                    $filaOriginal = $clavesVistasEnLote[strtoupper($claveIdentificadora)];
                    if ($modoDuplicados === 'omitir') {
                        $filaEstructurada['estado'] = 'DUPLICADO';
                        $filaEstructurada['detalle'] = "Registro repetido en el archivo para la misma asignatura/programa (primera aparición en fila #{$filaOriginal}). Omitido sin error.";
                        $stats['duplicados']++;
                        $filas[] = $filaEstructurada;
                        continue;
                    }
                } elseif ($claveIdentificadora !== '') {
                    $clavesVistasEnLote[strtoupper($claveIdentificadora)] = $index;
                }

                // Chequeo técnico en Base de Datos (NUEVO, EXISTENTE, ACTUALIZAR, CONFLICTO)
                $checkDB = ExistenceChecker::check($entityKey, $filaEstructurada, $id_institucion, $modoDuplicados);
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
