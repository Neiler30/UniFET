<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ImportExport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExportController extends Controller {
    public function index() {
        Auth::requerirRol('ADMINISTRADOR');
        $this->render('admin/importar/index', [
            'titulo' => 'Importar/Exportar',
            'phpspreadsheet' => ImportExport::phpspreadsheetDisponible(),
        ]);
    }

    public function validar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/importar');
        }

        $tipo = $_POST['tipo_entidad'] ?? 'DOCENTE';
        $filas = [];
        $mensaje = '';

        if (!ImportExport::phpspreadsheetDisponible()) {
            $mensaje = 'PhpSpreadsheet no esta instalado. Ejecuta composer require phpoffice/phpspreadsheet para leer Excel real.';
            $filas = $this->filaEjemplo($tipo);
        } else {
            $archivo = $_FILES['archivo']['tmp_name'] ?? '';
            if ($archivo && is_uploaded_file($archivo)) {
                $spreadsheet = IOFactory::load($archivo);
                $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $headers = [];

                foreach ($rows as $index => $row) {
                    if ((int)$index === 1) {
                        foreach ($row as $col => $value) {
                            $headers[$col] = strtolower(trim((string)$value));
                        }
                        continue;
                    }

                    $fila = [];
                    foreach ($headers as $col => $header) {
                        if ($header !== '') {
                            $fila[$header] = $row[$col] ?? '';
                        }
                    }
                    if (array_filter($fila, fn($value) => trim((string)$value) !== '')) {
                        $filas[] = $fila;
                    }
                }
                $mensaje = 'Archivo leido correctamente. Revisa los errores por fila antes de confirmar la carga.';
            } else {
                $mensaje = 'No se recibio archivo. Se muestra una fila de ejemplo para validar el formato esperado.';
                $filas = $this->filaEjemplo($tipo);
            }
        }

        $this->render('admin/importar/preview', [
            'titulo' => 'Vista previa de importacion',
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'preview' => ImportExport::validarFilas($tipo, $filas),
        ]);
    }

    private function filaEjemplo($tipo) {
        return [[
            'codigo' => 'EJ-001',
            'nombre' => 'Registro de ejemplo',
            'apellido' => $tipo === 'DOCENTE' ? 'Validado' : '',
            'creditos' => $tipo === 'ASIGNATURA' ? 3 : '',
            'tipo' => $tipo === 'ESPACIO' ? 'AULA' : '',
            'capacidad' => $tipo === 'ESPACIO' ? 30 : '',
        ]];
    }
}
