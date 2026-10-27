<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ImportExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Services\Importar\ExcelReader;
use App\Services\Importar\EntityDetector;
use App\Services\Importar\ColumnMapper;
use App\Services\Importar\Catalog;
use Throwable;

class ImportExportController extends Controller {

    private $id_institucion = 1;

    public function index() {
        Auth::requerirRol('ADMINISTRADOR');

        $resultado = $_SESSION['importacion_resultado'] ?? null;
        unset($_SESSION['importacion_resultado']);

        $this->render('admin/importar/index', [
            'titulo'         => 'Importar / Exportar Datos',
            'phpspreadsheet' => ImportExport::phpspreadsheetDisponible(),
            'resultado'      => $resultado,
        ]);
    }

    public function validar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/importar');
            return;
        }

        $tipo = $_POST['tipo_entidad'] ?? 'AUTO';

        if (empty($_FILES['archivo']['tmp_name']) || !is_uploaded_file($_FILES['archivo']['tmp_name'])) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Archivo no seleccionado',
                'text'  => 'Por favor selecciona un archivo Excel (.xlsx o .xls) válido para importar.',
            ];
            $this->redirect('?ruta=admin/importar');
            return;
        }

        $nombreOriginal = $_FILES['archivo']['name'] ?? 'archivo.xlsx';
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if (!in_array($extension, ['xlsx', 'xls'], true)) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Formato no soportado',
                'text'  => 'El archivo debe tener extensión .xlsx o .xls de Microsoft Excel.',
            ];
            $this->redirect('?ruta=admin/importar');
            return;
        }

        try {
            // 1. Leer el archivo Excel
            $excelData = ExcelReader::read($_FILES['archivo']['tmp_name']);
            $headers = $excelData['headers'];
            $data = $excelData['data'];

            if (empty($headers)) {
                throw new \Exception("El archivo no tiene encabezados en la primera fila.");
            }

            // 2. Detección automática si es necesario
            if ($tipo === 'AUTO') {
                $tipoDetectado = EntityDetector::detect(array_values($headers));
                if (!$tipoDetectado) {
                    throw new \Exception("No se pudo detectar automáticamente la entidad. Por favor selecciónala manualmente.");
                }
                $tipo = $tipoDetectado;
            }

            // 3. Mapear las columnas automáticamente identificando los datos específicos
            $mapeo = ColumnMapper::map($tipo, $headers);
            $modoDuplicados = $_POST['modo_duplicados'] ?? 'omitir';

            // Extraer el mapeo automático directamente sin pantalla intermedia
            $mapeoUsuario = [];
            foreach ($mapeo['mapping'] as $col => $info) {
                if (!empty($info['mapped_to'])) {
                    $mapeoUsuario[$col] = $info['mapped_to'];
                }
            }

            // Validar directamente usando DataValidator con id_institucion y modo_duplicados
            $procesado = \App\Services\Importar\DataValidator::validate(
                $tipo, 
                $mapeoUsuario, 
                $data, 
                $this->id_institucion, 
                $modoDuplicados
            );

            // Guardar directamente en sesión para ejecución final
            $_SESSION['importacion_previa'] = [
                'tipo'            => $tipo,
                'nombre_archivo'  => $nombreOriginal,
                'fecha'           => date('Y-m-d H:i:s'),
                'stats'           => $procesado['stats'],
                'filas'           => $procesado['filas'],
                'mapeo_usuario'   => $mapeoUsuario,
                'modo_duplicados' => $modoDuplicados
            ];

            // Renderizar directamente la Vista Previa
            $this->render('admin/importar/preview', [
                'titulo'          => 'Vista Previa de Importación',
                'tipo'            => $tipo,
                'nombre_archivo'  => $nombreOriginal,
                'stats'           => $procesado['stats'],
                'filas'           => $procesado['filas'],
                'modo_duplicados' => $modoDuplicados
            ]);
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error al leer el archivo',
                'text'  => $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/importar');
        }
    }

    public function previsualizar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/importar');
            return;
        }

        $sessionDatos = $_SESSION['importacion_datos'] ?? null;
        if (!$sessionDatos) {
            $this->redirect('?ruta=admin/importar');
            return;
        }

        $tipo = $_POST['tipo_entidad'] ?? $sessionDatos['tipo'];
        $mapeoUsuario = $_POST['mapeo'] ?? []; // ['A' => 'codigo', 'B' => 'nombre', ...]
        $modoDuplicados = $_POST['modo_duplicados'] ?? $sessionDatos['modo_duplicados'] ?? 'omitir';

        try {
            // Validar usando el nuevo DataValidator con soporte de id_institucion y modo_duplicados
            $procesado = \App\Services\Importar\DataValidator::validate(
                $tipo, 
                $mapeoUsuario, 
                $sessionDatos['data'], 
                $this->id_institucion, 
                $modoDuplicados
            );

            $_SESSION['importacion_previa'] = [
                'tipo'            => $tipo,
                'nombre_archivo'  => $sessionDatos['nombre_archivo'],
                'fecha'           => date('Y-m-d H:i:s'),
                'stats'           => $procesado['stats'],
                'filas'           => $procesado['filas'],
                'mapeo_usuario'   => $mapeoUsuario, // Guardamos el mapeo para la ejecución final
                'modo_duplicados' => $modoDuplicados
            ];

            $this->render('admin/importar/preview', [
                'titulo'          => 'Vista Previa de Importación',
                'tipo'            => $tipo,
                'nombre_archivo'  => $sessionDatos['nombre_archivo'],
                'stats'           => $procesado['stats'],
                'filas'           => $procesado['filas'],
                'modo_duplicados' => $modoDuplicados
            ]);
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error en la validación',
                'text'  => $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/importar');
        }
    }

    public function ejecutar() {
        Auth::requerirRol('ADMINISTRADOR');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?ruta=admin/importar');
            return;
        }

        $datosPrevios = $_SESSION['importacion_previa'] ?? null;
        if (!$datosPrevios || empty($datosPrevios['filas'])) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'warning',
                'title' => 'Sin datos para importar',
                'text'  => 'No hay un archivo validado recientemente para ejecutar la carga.',
            ];
            $this->redirect('?ruta=admin/importar');
            return;
        }

        try {
            $accion = $_POST['accion'] ?? 'cargar_datos';
            $modoDuplicados = $_POST['modo_duplicados'] ?? $datosPrevios['modo_duplicados'] ?? 'omitir';
            $filasParaProcesar = $datosPrevios['filas'];

            if ($accion === 'cargar_semestre') {
                $filasParaProcesar = array_filter($filasParaProcesar, fn($f) => $f['estado'] === 'COMPLETAR');
            } elseif ($accion === 'cargar_nuevos') {
                $filasParaProcesar = array_filter($filasParaProcesar, fn($f) => $f['estado'] === 'NUEVO');
            }

            // Llamada al nuevo ImportProcessor con el parámetro modo_duplicados
            $res = \App\Services\Importar\ImportProcessor::process(
                $datosPrevios['tipo'], 
                $filasParaProcesar, 
                $this->id_institucion, 
                $modoDuplicados
            );

            // Guardar reporte para posible descarga de inconsistencias
            $filasNoImportadas = array_values(array_filter($datosPrevios['filas'], function ($f) {
                return in_array($f['estado'], ['ERROR', 'CONFLICTO'], true);
            }));

            $filasExistentes = array_values(array_filter($datosPrevios['filas'], function ($f) {
                return in_array($f['estado'], ['EXISTENTE', 'DUPLICADO'], true);
            }));

            $entidades = \App\Services\Importar\Catalog::getEntities();
            $nombreAmigable = $entidades[$datosPrevios['tipo']]['nombre_amigable'] ?? 'Registros';

            $omitidosTotal = count($filasExistentes) + ($res['omitidos'] ?? 0);

            $_SESSION['importacion_resultado'] = [
                'nombre_archivo'       => $datosPrevios['nombre_archivo'],
                'total_procesadas'     => count($datosPrevios['filas']),
                'entity_tipo'          => $datosPrevios['tipo'],
                'entity_nombre'        => $nombreAmigable,
                'modo_duplicados'      => $modoDuplicados,
                // Claves genéricas (usadas por la vista)
                'creados'              => $res['creados'],
                'actualizados'         => $res['actualizados'],
                'duplicados_omitidos'  => $res['omitidos'] ?? 0,
                'existentes_omitidas'  => $omitidosTotal,
                'filas_omitidas'       => count($filasNoImportadas),
                'errores'              => $filasNoImportadas,
                // Compat. legacy (no se quita para no romper nada)
                'asignaturas_creadas'  => $res['creados'],
                'asociaciones_creadas' => $res['actualizados'],
            ];

            unset($_SESSION['importacion_previa']);

            $detalleTexto = "{$nombreAmigable}: {$res['creados']} nuevo(s) importado(s)";
            if ($res['actualizados'] > 0) {
                $detalleTexto .= ", {$res['actualizados']} actualizado(s)";
            }
            if (($res['omitidos'] ?? 0) > 0) {
                $detalleTexto .= ", {$res['omitidos']} duplicado(s) omitido(s) sin error";
            }
            if (count($filasExistentes) > 0) {
                $detalleTexto .= ", " . count($filasExistentes) . " sincronizado(s) en BD";
            }
            $detalleTexto .= ".";

            $_SESSION['swal_alerta'] = [
                'icon'  => 'success',
                'title' => '✅ Importación Completada',
                'text'  => $detalleTexto,
            ];

            $this->redirect('?ruta=admin/importar');
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error durante la carga',
                'text'  => 'La transacción fue revertida debido a un error: ' . $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/importar');
        }
    }

    public function plantilla() {
        Auth::requerirRol('ADMINISTRADOR');
        try {
            $spreadsheet = ImportExport::generarPlantillaExcel();
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="plantilla_asignaturas_unifet.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error al generar plantilla',
                'text'  => $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/importar');
        }
    }

    public function exportar() {
        Auth::requerirRol('ADMINISTRADOR');
        try {
            $spreadsheet = ImportExport::exportarAsignaturas($this->id_institucion);
            $writer = new Xlsx($spreadsheet);

            $nombre = "asignaturas_unifet_" . date('Y-m-d_His') . ".xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nombre . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (Throwable $e) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'error',
                'title' => 'Error al exportar',
                'text'  => $e->getMessage(),
            ];
            $this->redirect('?ruta=admin/importar');
        }
    }

    public function reporteErrores() {
        Auth::requerirRol('ADMINISTRADOR');
        $resultado = $_SESSION['importacion_resultado']['errores'] ?? [];
        if (empty($resultado)) {
            $_SESSION['swal_alerta'] = [
                'icon'  => 'info',
                'title' => 'Sin inconsistencias',
                'text'  => 'No hay reporte de errores pendiente de descarga.',
            ];
            $this->redirect('?ruta=admin/importar');
            return;
        }

        try {
            $spreadsheet = new Spreadsheet();
            $hoja = $spreadsheet->getActiveSheet();
            $hoja->setTitle('Inconsistencias');

            $headers = ['FILA EXCEL', 'CODIGO', 'ASIGNATURA', 'PROGRAMA', 'SEMESTRE', 'ESTADO', 'MOTIVO'];
            $col = 'A';
            foreach ($headers as $h) {
                $hoja->setCellValue("{$col}1", $h);
                $col++;
            }

            $f = 2;
            foreach ($resultado as $err) {
                $hoja->setCellValue("A{$f}", $err['fila_excel'] ?? '');
                $hoja->setCellValue("B{$f}", $err['codigo'] ?? '');
                $hoja->setCellValue("C{$f}", $err['asignatura'] ?? '');
                $hoja->setCellValue("D{$f}", $err['programa_texto'] ?? '');
                $hoja->setCellValue("E{$f}", $err['semestre_texto'] ?? '');
                $hoja->setCellValue("F{$f}", $err['estado'] ?? '');
                $hoja->setCellValue("G{$f}", $err['detalle'] ?? '');
                $f++;
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_errores_importacion_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (Throwable $e) {
            $this->redirect('?ruta=admin/importar');
        }
    }
}
