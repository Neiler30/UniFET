<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use InvalidArgumentException;

class Docente {

    /**
     * Obtiene todos los docentes de una institución con resumen de programas y asignaturas.
     */
    public static function obtenerTodos($id_institucion = 1) {
        $db = Database::getConexion();
        $stmt = $db->prepare("
            SELECT d.*,
                   COUNT(DISTINCT dp.id_programa) AS total_programas,
                   COUNT(DISTINCT da.id_asignatura) AS total_asignaturas,
                   GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') AS programas,
                   GROUP_CONCAT(DISTINCT a.nombre ORDER BY a.nombre SEPARATOR ', ') AS asignaturas
            FROM docente d
            LEFT JOIN docente_programa dp ON dp.id_docente = d.id_docente
            LEFT JOIN programa p ON p.id_programa = dp.id_programa
            LEFT JOIN docente_asignatura da ON da.id_docente = d.id_docente
            LEFT JOIN asignatura a ON a.id_asignatura = da.id_asignatura
            WHERE d.id_institucion = :inst
            GROUP BY d.id_docente
            ORDER BY d.apellido ASC, d.nombre ASC
        ");
        $stmt->execute([':inst' => (int)$id_institucion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un docente por su ID.
     */
    public static function obtenerPorId($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("SELECT * FROM docente WHERE id_docente = :id");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el perfil académico completo configurado para un docente:
     * Lista de programas asociados y para cada uno, las asignaturas seleccionadas.
     */
    public static function obtenerPerfilAcademico($id_docente) {
        $db = Database::getConexion();
        
        // 1. Obtener los programas vinculados al docente
        $stmt = $db->prepare("
            SELECT dp.id_programa, p.nombre AS programa_nombre, p.codigo AS programa_codigo,
                   p.id_nivel_academico, na.nombre AS nivel_nombre
            FROM docente_programa dp
            JOIN programa p ON p.id_programa = dp.id_programa
            LEFT JOIN nivel_academico na ON na.id_nivel_academico = p.id_nivel_academico
            WHERE dp.id_docente = :id
            ORDER BY p.nombre ASC
        ");
        $stmt->execute([':id' => (int)$id_docente]);
        $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($programas as $prog) {
            $idPrograma = (int)$prog['id_programa'];
            
            // 2. Obtener asignaturas que el docente puede dictar en este programa
            $stmtAsig = $db->prepare("
                SELECT a.id_asignatura, a.codigo, a.nombre, a.creditos, ap.semestre
                FROM docente_asignatura da
                JOIN asignatura a ON a.id_asignatura = da.id_asignatura
                JOIN asignatura_programa ap ON ap.id_asignatura = a.id_asignatura AND ap.id_programa = :id_prog
                WHERE da.id_docente = :id_docente
                ORDER BY ap.semestre ASC, a.nombre ASC
            ");
            $stmtAsig->execute([
                ':id_docente' => (int)$id_docente,
                ':id_prog'    => $idPrograma
            ]);
            $asignaturas = $stmtAsig->fetchAll(PDO::FETCH_ASSOC);

            $resultado[] = [
                'id_programa'        => $idPrograma,
                'programa_nombre'    => $prog['programa_nombre'],
                'programa_codigo'    => $prog['programa_codigo'],
                'id_nivel_academico' => (int)($prog['id_nivel_academico'] ?? 0),
                'nivel_nombre'       => $prog['nivel_nombre'] ?? 'General',
                'asignaturas'        => array_map(function ($a) {
                    return [
                        'id_asignatura' => (int)$a['id_asignatura'],
                        'codigo'        => $a['codigo'],
                        'nombre'        => $a['nombre'],
                        'creditos'      => (int)$a['creditos'],
                        'semestre'      => $a['semestre'] !== null ? (int)$a['semestre'] : null
                    ];
                }, $asignaturas)
            ];
        }

        return $resultado;
    }

    /**
     * Retorna IDs planos de programas y asignaturas vinculados (retrocompatibilidad).
     */
    public static function relaciones($id) {
        $db = Database::getConexion();
        $programas = $db->prepare("SELECT id_programa FROM docente_programa WHERE id_docente = :id");
        $programas->execute([':id' => (int)$id]);
        $asignaturas = $db->prepare("SELECT id_asignatura FROM docente_asignatura WHERE id_docente = :id");
        $asignaturas->execute([':id' => (int)$id]);
        return [
            'programas'   => array_map('intval', $programas->fetchAll(PDO::FETCH_COLUMN)),
            'asignaturas' => array_map('intval', $asignaturas->fetchAll(PDO::FETCH_COLUMN)),
        ];
    }

    /**
     * Genera un código automático para el docente a partir de su nombre y apellido + consecutivo de 3 dígitos.
     * Ejemplo: Juan Pérez -> JPE001 (o JPE002 si ya existe).
     *
     * @param string $nombre
     * @param string $apellido
     * @param int $id_institucion
     * @param int|null $id_excluir
     * @return string
     */
    public static function generarCodigoSugerido($nombre, $apellido, $id_institucion = 1, $id_excluir = null) {
        $nombreNorm = preg_replace('/[^A-Z]/', '', strtoupper(self::quitarAcentos(trim($nombre))));
        $apellidoNorm = preg_replace('/[^A-Z]/', '', strtoupper(self::quitarAcentos(trim($apellido))));
        
        $letraNombre = substr($nombreNorm, 0, 1) ?: 'D';
        $letrasApellido = substr($apellidoNorm, 0, 2);
        if (strlen($letrasApellido) < 2) {
            $letrasApellido = str_pad($letrasApellido, 2, 'X');
        }
        
        $prefijo = $letraNombre . $letrasApellido; // Ej: JPE, MGO, CRO, AMA
        
        $db = Database::getConexion();
        $sql = "SELECT codigo FROM docente WHERE id_institucion = :inst AND codigo LIKE :prefijo";
        if ($id_excluir) {
            $sql .= " AND id_docente != :excluir";
        }
        $stmt = $db->prepare($sql);
        $params = [
            ':inst'    => (int)$id_institucion,
            ':prefijo' => $prefijo . '%'
        ];
        if ($id_excluir) {
            $params[':excluir'] = (int)$id_excluir;
        }
        $stmt->execute($params);
        $codigosExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $numerosUsados = [];
        foreach ($codigosExistentes as $cod) {
            if (preg_match('/^' . preg_quote($prefijo, '/') . '(\d+)$/', $cod, $m)) {
                $numerosUsados[] = (int)$m[1];
            }
        }
        
        $consecutivo = 1;
        while (in_array($consecutivo, $numerosUsados, true)) {
            $consecutivo++;
        }
        
        return sprintf("%s%03d", $prefijo, $consecutivo);
    }

    /**
     * Verifica si un código de docente ya existe en la institución.
     */
    public static function existeCodigo($codigo, $id_institucion = 1, $id_excluir = null) {
        $db = Database::getConexion();
        $sql = "SELECT COUNT(*) FROM docente WHERE codigo = :codigo AND id_institucion = :inst";
        if ($id_excluir) {
            $sql .= " AND id_docente != :id_excluir";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':codigo', trim($codigo), PDO::PARAM_STR);
        $stmt->bindValue(':inst', (int)$id_institucion, PDO::PARAM_INT);
        if ($id_excluir) {
            $stmt->bindValue(':id_excluir', (int)$id_excluir, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Elimina tildes y caracteres especiales para procesamiento de código.
     */
    private static function quitarAcentos($str) {
        $acentos = [
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
            'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
            'ñ'=>'n', 'Ñ'=>'N', 'ü'=>'u', 'Ü'=>'U',
            'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u'
        ];
        return strtr($str, $acentos);
    }

    /**
     * Guarda o actualiza un docente junto con su perfil académico completo
     * respetando las relaciones docente_programa, docente_asignatura y asignatura_programa.
     *
     * @param array $datos ['nombre', 'apellido', 'correo', 'codigo', 'estado', 'id_institucion']
     * @param array $perfilProgramas Lista de programas configurados con sus asignaturas:
     *                              [['id_programa' => X, 'asignaturas' => [A, B, C]], ...]
     * @param int|null $id ID del docente si es edición
     * @return int ID del docente
     * @throws InvalidArgumentException|\Throwable
     */
    public static function guardarConPerfil($datos, $perfilProgramas = [], $id = null) {
        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $estado = in_array($datos['estado'] ?? '', ['ACTIVO', 'INACTIVO'], true) ? $datos['estado'] : 'ACTIVO';
        $idInstitucion = (int)($datos['id_institucion'] ?? 1);

        if ($nombre === '') {
            throw new InvalidArgumentException("El nombre del docente es obligatorio.");
        }
        if ($apellido === '') {
            throw new InvalidArgumentException("El apellido del docente es obligatorio.");
        }
        if ($correo === '') {
            throw new InvalidArgumentException("El correo institucional es obligatorio.");
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El correo institucional no tiene un formato válido.");
        }

        // Validación y generación de código definitivo
        $codigo = trim($datos['codigo'] ?? '');
        if ($codigo === '' || (!$id && $codigo === 'AUTO')) {
            $codigo = self::generarCodigoSugerido($nombre, $apellido, $idInstitucion, $id);
        } else {
            if (self::existeCodigo($codigo, $idInstitucion, $id)) {
                if (!$id) {
                    // Si es nuevo y colisiona, auto-generar el siguiente consecutivo libre
                    $codigo = self::generarCodigoSugerido($nombre, $apellido, $idInstitucion, $id);
                } else {
                    throw new InvalidArgumentException("El código '{$codigo}' ya está asignado a otro docente.");
                }
            }
        }

        // Validación de programas y asignaturas
        if (empty($perfilProgramas) || !is_array($perfilProgramas)) {
            throw new InvalidArgumentException("Debes asociar al menos un programa académico con sus asignaturas.");
        }

        $programasProcesados = [];
        $todasAsignaturasDocente = [];
        $db = Database::getConexion();

        // Validar cada programa y verificar que las asignaturas pertenezcan a dicho programa en asignatura_programa
        $validadorAsigProg = $db->prepare("
            SELECT COUNT(*) 
            FROM asignatura_programa 
            WHERE id_programa = :prog AND id_asignatura = :asig
        ");

        foreach ($perfilProgramas as $pConfig) {
            $idProg = (int)($pConfig['id_programa'] ?? 0);
            if ($idProg <= 0) {
                continue;
            }

            if (isset($programasProcesados[$idProg])) {
                throw new InvalidArgumentException("El programa seleccionado ya fue agregado. No se permiten programas duplicados.");
            }

            $asigsRaw = (array)($pConfig['asignaturas'] ?? []);
            $asigsIds = array_values(array_unique(array_filter(array_map('intval', $asigsRaw))));

            if (empty($asigsIds)) {
                throw new InvalidArgumentException("Cada programa configurado debe tener al menos una asignatura seleccionada.");
            }

            // Validar existencia real en asignatura_programa para evitar asociaciones inválidas
            foreach ($asigsIds as $idAsig) {
                $validadorAsigProg->execute([
                    ':prog' => $idProg,
                    ':asig' => $idAsig
                ]);
                if ((int)$validadorAsigProg->fetchColumn() === 0) {
                    throw new InvalidArgumentException("La asignatura ID {$idAsig} no pertenece al programa ID {$idProg}.");
                }
                $todasAsignaturasDocente[$idAsig] = true;
            }

            $programasProcesados[$idProg] = true;
        }

        if (empty($programasProcesados)) {
            throw new InvalidArgumentException("Debes asociar al menos un programa académico válido.");
        }

        // Iniciar transacción de persistencia
        $db->beginTransaction();
        try {
            if ($id) {
                $stmt = $db->prepare("
                    UPDATE docente
                    SET codigo = :codigo, nombre = :nombre, apellido = :apellido, correo = :correo, estado = :estado
                    WHERE id_docente = :id AND id_institucion = :inst
                ");
                $stmt->execute([
                    ':codigo'   => $codigo,
                    ':nombre'   => $nombre,
                    ':apellido' => $apellido,
                    ':correo'   => $correo,
                    ':estado'   => $estado,
                    ':id'       => (int)$id,
                    ':inst'     => $idInstitucion
                ]);
                $idDocente = (int)$id;
            } else {
                $stmt = $db->prepare("
                    INSERT INTO docente (id_institucion, codigo, nombre, apellido, correo, estado)
                    VALUES (:inst, :codigo, :nombre, :apellido, :correo, :estado)
                ");
                $stmt->execute([
                    ':inst'     => $idInstitucion,
                    ':codigo'   => $codigo,
                    ':nombre'   => $nombre,
                    ':apellido' => $apellido,
                    ':correo'   => $correo,
                    ':estado'   => $estado
                ]);
                $idDocente = (int)$db->lastInsertId();
            }

            // Sincronizar docente_programa
            $db->prepare("DELETE FROM docente_programa WHERE id_docente = :id")->execute([':id' => $idDocente]);
            $stmtDP = $db->prepare("INSERT INTO docente_programa (id_docente, id_programa) VALUES (:docente, :prog)");
            foreach (array_keys($programasProcesados) as $idProg) {
                $stmtDP->execute([
                    ':docente' => $idDocente,
                    ':prog'    => $idProg
                ]);
            }

            // Sincronizar docente_asignatura (conjunto único de asignaturas de sus programas)
            $db->prepare("DELETE FROM docente_asignatura WHERE id_docente = :id")->execute([':id' => $idDocente]);
            $stmtDA = $db->prepare("INSERT INTO docente_asignatura (id_docente, id_asignatura) VALUES (:docente, :asig)");
            foreach (array_keys($todasAsignaturasDocente) as $idAsig) {
                $stmtDA->execute([
                    ':docente' => $idDocente,
                    ':asig'    => $idAsig
                ]);
            }

            $db->commit();
            return $idDocente;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Adaptador para compatibilidad retroactiva con llamadas clásicas a guardar().
     */
    public static function guardar($datos, $programas = [], $asignaturas = [], $id = null) {
        // Si el segundo parámetro ya es una estructura de perfil académico (array de objetos/arrays)
        if (!empty($programas) && is_array($programas) && isset($programas[0]['id_programa'])) {
            return self::guardarConPerfil($datos, $programas, $id);
        }

        // Si se pasan arrays planos de programas y asignaturas
        $programasIds = array_filter(array_map('intval', (array)$programas));
        $asignaturasIds = array_filter(array_map('intval', (array)$asignaturas));

        // Reconstruir estructura de perfil académico validando contra asignatura_programa
        $db = Database::getConexion();
        $perfil = [];

        foreach ($programasIds as $pId) {
            if (empty($asignaturasIds)) {
                $perfil[] = ['id_programa' => $pId, 'asignaturas' => []];
                continue;
            }
            $inAsigs = implode(',', $asignaturasIds);
            $stmt = $db->query("SELECT id_asignatura FROM asignatura_programa WHERE id_programa = {$pId} AND id_asignatura IN ({$inAsigs})");
            $asigsEnPrograma = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
            $perfil[] = [
                'id_programa' => $pId,
                'asignaturas' => !empty($asigsEnPrograma) ? $asigsEnPrograma : $asignaturasIds
            ];
        }

        if (empty($perfil)) {
            // Si no se pasaron programas pero sí asignaturas
            $perfil = [['id_programa' => 1, 'asignaturas' => $asignaturasIds]];
        }

        return self::guardarConPerfil($datos, $perfil, $id);
    }

    /**
     * Alterna el estado de un docente entre ACTIVO e INACTIVO.
     */
    public static function cambiarEstado($id) {
        $db = Database::getConexion();
        $stmt = $db->prepare("UPDATE docente SET estado = IF(estado = 'ACTIVO', 'INACTIVO', 'ACTIVO') WHERE id_docente = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
