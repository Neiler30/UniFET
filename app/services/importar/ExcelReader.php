<?php

namespace App\Services\Importar;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader {
    
    /**
     * Lee la primera hoja de un archivo Excel.
     * @return array [ 'headers' => ['A' => 'Nombre'], 'data' => [ 2 => ['A' => 'Valor', ...], ... ] ]
     */
    public static function read(string $filePath): array {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $headers = [];
        $data = [];
        $headerRow = 1;

        // Buscar la fila de encabezados (la primera con más de 1 celda con contenido)
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            $nonEmptyCount = 0;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $val = $sheet->getCell($colLetter . $row)->getFormattedValue();
                if ($val !== null && trim($val) !== '') {
                    $nonEmptyCount++;
                }
            }
            if ($nonEmptyCount > 1) {
                $headerRow = $row;
                break;
            }
        }

        // Leer la fila detectada como encabezados
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $val = $sheet->getCell($colLetter . $headerRow)->getFormattedValue();
            if ($val !== null && trim($val) !== '') {
                $headers[$colLetter] = trim($val);
            }
        }

        // Leer datos desde la fila siguiente a los encabezados
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $rowData = [];
            $hasData = false;
            foreach ($headers as $colLetter => $headerName) {
                $val = $sheet->getCell($colLetter . $row)->getFormattedValue();
                $rowData[$colLetter] = $val;
                if ($val !== null && trim($val) !== '') {
                    $hasData = true;
                }
            }
            if ($hasData) {
                $data[$row] = $rowData;
            }
        }

        return [
            'headers' => $headers,
            'data' => $data
        ];
    }
}
