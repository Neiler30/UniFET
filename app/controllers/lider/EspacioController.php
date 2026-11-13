<?php

namespace App\Controllers\Lider;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\LiderPanel;
use App\Models\PeriodoAcademico;
use App\Config\Database;
use PDO;

class EspacioController extends Controller {
    public function index() {
        Auth::requerirRol('LIDER_PROGRAMA');
        $usuario = Auth::getUsuarioActual();
        $programaActivo = LiderPanel::resolverProgramaActivo($usuario['id_usuario'], $_GET['programa_id'] ?? null);
        $periodoActivo = PeriodoAcademico::getActivo();
        $espacios = LiderPanel::espacios();

        // Buscar espacios donde el programa del líder tiene clases activas
        $espaciosDelPrograma = [];
        $ultimoEspacioConClase = null;
        if ($programaActivo && $periodoActivo) {
            $db = Database::getConexion();
            $stmt = $db->prepare("
                SELECT DISTINCT e.id_espacio, e.nombre, b.nombre as bloque_nombre, s.nombre as sede_nombre,
                       (SELECT COUNT(*) FROM horario h2 WHERE h2.id_espacio = e.id_espacio AND h2.id_programa = :programa_cnt AND h2.id_periodo = :periodo_cnt AND h2.estado != 'ELIMINADO') as total_clases
                FROM horario h
                JOIN espacio e ON e.id_espacio = h.id_espacio
                JOIN bloque b ON b.id_bloque = e.id_bloque
                JOIN sede s ON s.id_sede = b.id_sede
                WHERE h.id_programa = :programa 
                  AND h.id_periodo = :periodo 
                  AND h.estado != 'ELIMINADO'
                ORDER BY h.id_horario DESC
            ");
            $stmt->execute([
                ':programa' => $programaActivo['id_programa'],
                ':periodo' => $periodoActivo['id_periodo'],
                ':programa_cnt' => $programaActivo['id_programa'],
                ':periodo_cnt' => $periodoActivo['id_periodo']
            ]);
            $espaciosDelPrograma = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($espaciosDelPrograma)) {
                $ultimoEspacioConClase = (int)$espaciosDelPrograma[0]['id_espacio'];
            }
        }

        $sedeFiltro = !empty($_GET['sede']) ? trim($_GET['sede']) : '';
        $bloqueFiltro = !empty($_GET['bloque']) ? trim($_GET['bloque']) : '';

        // Si se especificó por GET, usarlo. Si no se especificó o es 0, mostrar todos los espacios.
        if (isset($_GET['espacio_id'])) {
            $idEspacio = (int)$_GET['espacio_id'];
        } else {
            // Por defecto mostrar todos los espacios para no tener que buscar salón por salón
            $idEspacio = 0;
        }

        $ocupacion = [];
        if ($programaActivo && $periodoActivo) {
            $ocupacion = LiderPanel::ocupacionEspacio(
                $programaActivo['id_programa'],
                $periodoActivo['id_periodo'],
                $idEspacio,
                $sedeFiltro,
                $bloqueFiltro
            );
        }

        $this->render('lider/espacios', [
            'titulo' => 'Disponibilidad de Espacios',
            'programaActivo' => $programaActivo,
            'periodoActivo' => $periodoActivo,
            'espacios' => $espacios,
            'espaciosDelPrograma' => $espaciosDelPrograma,
            'idEspacio' => $idEspacio,
            'sedeFiltro' => $sedeFiltro,
            'bloqueFiltro' => $bloqueFiltro,
            'ocupacion' => $ocupacion
        ]);
    }
}
