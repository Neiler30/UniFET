<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Historial de Auditorías']);
$this->render('elementos_compartidos/sidebar_lider');

$diasEsp = [
    'LUNES' => 'Lunes', 'MARTES' => 'Martes', 'MIERCOLES' => 'Miércoles',
    'JUEVES' => 'Jueves', 'VIERNES' => 'Viernes', 'SABADO' => 'Sábado', 'DOMINGO' => 'Domingo'
];
?>

<style>
.historial-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 60%, #1a0a3e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.historial-hero::before {
    content: '';
    position: absolute;
    right: -20px; bottom: -20px;
    width: 160px; height: 160px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.historial-hero h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.historial-hero p { color: rgba(255,255,255,.75); font-size: 13.5px; margin: 0; }
.historial-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    text-decoration: none;
    margin-top: 10px;
    transition: gap .2s;
}
.historial-back-link:hover { gap: 10px; color: #fff; text-decoration: none; }

.historial-filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 22px;
    box-shadow: var(--sombra-tarjeta);
}
.historial-filter-bar .form-group { margin-bottom: 0; min-width: 160px; flex: 1; }
.historial-filter-bar label { font-size: 12px; font-weight: 600; color: var(--color-texto-secundario); margin-bottom: 4px; }

/* Table container */
.historial-table-wrap {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--sombra-tarjeta);
}
.historial-table-wrap .table { margin: 0; }
.historial-table-wrap .table th {
    background: var(--color-superficie);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--color-texto-secundario);
    padding: 12px 16px;
    border-bottom: 1px solid var(--color-borde);
}
.historial-table-wrap .table td {
    padding: 12px 16px;
    font-size: 13.5px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.historial-table-wrap .table tr:last-child td { border-bottom: none; }
.historial-table-wrap .table tr:hover td { background: rgba(59,13,143,.02); }

.badge-resultado {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.badge-resultado.sin-novedad { background: #d1fae5; color: #065f46; }
.badge-resultado.con-novedad { background: #fee2e2; color: #991b1b; }
.badge-resultado.verificada { background: #dbeafe; color: #1e40af; }
.badge-resultado.pendiente { background: #fef3c7; color: #b45309; }

.btn-ver-mini {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    border: 1px solid var(--color-primario);
    border-radius: 6px;
    color: var(--color-primario);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}
.btn-ver-mini:hover { background: var(--color-primario); color: #fff; text-decoration: none; }

.historial-empty {
    text-align: center;
    padding: 48px 20px;
    color: var(--color-texto-secundario);
}
.historial-empty i { font-size: 40px; opacity: .3; display: block; margin-bottom: 12px; }
.historial-empty h3 { font-size: 17px; color: var(--color-oscuro); margin-bottom: 4px; }

.total-count {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(59,13,143,.06);
    color: var(--color-primario);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 14px;
}

@media (max-width: 800px) {
    .historial-filter-bar { flex-direction: column; }
    .historial-filter-bar .form-group { min-width: 100%; }
}
</style>

<!-- Hero -->
<div class="historial-hero">
    <h1><i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px;"></i> Historial de Auditorías</h1>
    <p><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?> — Consulta todas las auditorías realizadas.</p>
    <a href="?ruta=lider/auditoria<?= $programaActivo ? '&programa_id='.(int)$programaActivo['id_programa'] : '' ?>" class="historial-back-link">
        <i class="fa-solid fa-arrow-left"></i> Volver a auditoría del día
    </a>
</div>

<!-- Filters -->
<form method="GET" class="historial-filter-bar">
    <input type="hidden" name="ruta" value="lider/auditoria/historial">
    <?php if ($programaActivo): ?>
        <input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>">
    <?php endif; ?>
    <div class="form-group">
        <label><i class="fa-solid fa-calendar-day"></i> Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtros['fecha'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label><i class="fa-solid fa-flag"></i> Resultado</label>
        <select name="resultado" class="form-control">
            <option value="">Todos</option>
            <option value="SIN_NOVEDAD" <?= ($filtros['resultado'] ?? '') === 'SIN_NOVEDAD' ? 'selected' : '' ?>>Sin novedad</option>
            <option value="CON_NOVEDAD" <?= ($filtros['resultado'] ?? '') === 'CON_NOVEDAD' ? 'selected' : '' ?>>Con novedad</option>
            <option value="VERIFICADA" <?= ($filtros['resultado'] ?? '') === 'VERIFICADA' ? 'selected' : '' ?>>Verificada</option>
        </select>
    </div>
    <div class="form-group">
        <label><i class="fa-solid fa-book"></i> Asignatura</label>
        <input type="text" name="asignatura" class="form-control" placeholder="Buscar asignatura..." value="<?= htmlspecialchars($filtros['asignatura'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label><i class="fa-solid fa-chalkboard-user"></i> Docente</label>
        <input type="text" name="docente" class="form-control" placeholder="Buscar docente..." value="<?= htmlspecialchars($filtros['docente'] ?? '') ?>">
    </div>
    <div class="form-group" style="flex: 0;">
        <button type="submit" class="btn-primario" style="padding: 10px 18px; font-size: 14px; white-space: nowrap;">
            <i class="fa-solid fa-search"></i> Filtrar
        </button>
    </div>
</form>

<!-- Results -->
<?php if (empty($auditorias)): ?>
    <div class="panel">
        <div class="historial-empty">
            <i class="fa-regular fa-folder-open"></i>
            <h3>No se encontraron auditorías</h3>
            <p>No hay registros de auditoría que coincidan con los filtros aplicados.</p>
        </div>
    </div>
<?php else: ?>
    <div class="total-count">
        <i class="fa-solid fa-list-check"></i> <?= count($auditorias) ?> auditoría<?= count($auditorias) !== 1 ? 's' : '' ?> encontrada<?= count($auditorias) !== 1 ? 's' : '' ?>
    </div>

    <div class="historial-table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Día</th>
                    <th>Asignatura</th>
                    <th>Docente</th>
                    <th>Horario</th>
                    <th>Espacio</th>
                    <th>Resultado</th>
                    <th>Auditor</th>
                    <th style="text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditorias as $a):
                    $resultBadge = match($a['resultado']) {
                        'SIN_NOVEDAD' => 'sin-novedad',
                        'CON_NOVEDAD' => 'con-novedad',
                        'VERIFICADA' => 'verificada',
                        default => 'pendiente'
                    };
                    $resultLabel = match($a['resultado']) {
                        'SIN_NOVEDAD' => 'Sin novedad',
                        'CON_NOVEDAD' => 'Con novedad',
                        'VERIFICADA' => 'Verificada',
                        default => $a['resultado']
                    };
                    $resultIcon = match($a['resultado']) {
                        'SIN_NOVEDAD' => 'fa-check-circle',
                        'CON_NOVEDAD' => 'fa-exclamation-triangle',
                        'VERIFICADA' => 'fa-badge-check',
                        default => 'fa-clock'
                    };
                    $diaEsp = $diasEsp[$a['dia_semana']] ?? $a['dia_semana'];
                ?>
                <tr>
                    <td><strong><?= date('d/m/Y', strtotime($a['fecha_auditoria'])) ?></strong></td>
                    <td><?= $diaEsp ?></td>
                    <td>
                        <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($a['asignatura_nombre']) ?></strong>
                        <br><span style="font-size: 12px; color: var(--color-texto-secundario);"><?= htmlspecialchars($a['asignatura_codigo']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($a['docente_nombre']) ?></td>
                    <td><?= date('H:i', strtotime($a['hora_inicio'])) ?> – <?= date('H:i', strtotime($a['hora_fin'])) ?></td>
                    <td>
                        <?= htmlspecialchars($a['espacio_nombre']) ?>
                        <br><span style="font-size: 12px; color: var(--color-texto-secundario);"><?= htmlspecialchars($a['sede_nombre']) ?> / <?= htmlspecialchars($a['bloque_nombre']) ?></span>
                    </td>
                    <td>
                        <span class="badge-resultado <?= $resultBadge ?>">
                            <i class="fa-solid <?= $resultIcon ?>"></i> <?= $resultLabel ?>
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 12.5px; color: var(--color-texto-secundario);">
                            <i class="fa-solid fa-user-shield" style="margin-right: 3px;"></i><?= htmlspecialchars($a['auditor_nombre']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a class="btn-ver-mini" href="?ruta=lider/auditoria/detalle&id=<?= (int)$a['id_auditoria'] ?>">
                            <i class="fa-solid fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php $this->render('elementos_compartidos/footer'); ?>
