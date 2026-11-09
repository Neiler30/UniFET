<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Horarios']);
$this->render('elementos_compartidos/sidebar_admin');

$tab = $tab ?? 'general';
$diasSemana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
$estadosHorario = ['BORRADOR', 'PROPUESTA', 'CONFIRMADO', 'CERRADO', 'MODIFICADO'];
$origenes = ['MANUAL', 'FET', 'HIBRIDO'];
$tabsHorario = [
    ['id' => 'general', 'label' => 'Listado y Asignación', 'icon' => 'fa-table-list'],
    ['id' => 'ver_horario', 'label' => 'Ver Horario Semanal', 'icon' => 'fa-calendar-week'],
    ['id' => 'fet', 'label' => 'Generar con FET', 'icon' => 'fa-wand-magic-sparkles'],
    ['id' => 'propuestas', 'label' => 'Propuestas FET', 'icon' => 'fa-layer-group'],
];
?>

<style>
    .horarios-hero {
        background: linear-gradient(135deg, var(--color-primario), #24114f);
        color: #fff;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: center;
    }

    .horarios-hero h1 {
        font-size: 24px;
        margin-bottom: 6px;
    }

    .horarios-hero p {
        color: rgba(255, 255, 255, .82);
        font-size: 14px;
        max-width: 760px;
    }

    .metric-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        min-width: min(340px, 100%);
    }

    .metric-tile {
        border: 1px solid rgba(255, 255, 255, .18);
        background: rgba(255, 255, 255, .09);
        border-radius: 8px;
        padding: 12px;
    }

    .metric-tile strong {
        display: block;
        font-size: 22px;
    }

    .metric-tile span {
        font-size: 12px;
        color: rgba(255, 255, 255, .72);
    }

    .module-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .module-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border: 1px solid var(--color-borde);
        border-radius: 8px;
        background: #fff;
        color: var(--color-texto-secundario);
        font-weight: 600;
        font-size: 13px;
    }

    .module-tab:hover {
        text-decoration: none;
        background: rgba(59, 13, 143, .04);
    }

    .module-tab.active {
        background: var(--color-primario);
        border-color: var(--color-primario);
        color: #fff;
    }

    .filter-grid,
    .assignment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
        gap: 14px;
        align-items: end;
    }

    .filter-grid>*,
    .assignment-grid>* {
        min-width: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .section-title h2 {
        font-size: 18px;
        color: var(--color-oscuro);
    }

    .section-title p {
        font-size: 13px;
        color: var(--color-texto-secundario);
        margin-top: 3px;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .code-pill {
        font-family: monospace;
        background: #f1f3f5;
        padding: 3px 7px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .actions-inline {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .audit-form {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        min-width: 360px;
    }

    @media (max-width: 1000px) {
        .horarios-hero {
            flex-direction: column;
            align-items: stretch;
        }

        .metric-strip,
        .filter-grid,
        .assignment-grid {
            grid-template-columns: 1fr;
            min-width: 0;
        }

        .audit-form {
            grid-template-columns: 1fr;
            min-width: 240px;
        }
    }

    /* Estilos Cuadrícula de Horarios */
    .schedule-grid-wrap {
        overflow-x: auto;
        background: #fff;
        border-radius: 10px;
        border: 1px solid var(--color-borde);
        padding: 14px;
        margin-top: 14px;
    }
    .schedule-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 5px;
        table-layout: fixed;
    }
    .schedule-table thead th {
        background: var(--color-oscuro, #0B1F33);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 8px;
        text-align: center;
        border-radius: 6px;
        letter-spacing: .3px;
        border: none;
    }
    .schedule-table thead th.hour-header {
        width: 135px;
        background: #1e1b4b;
    }
    .schedule-time-cell {
        font-size: 11.5px;
        color: var(--color-oscuro);
        font-weight: 700;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        text-align: center;
        padding: 6px;
        vertical-align: middle;
        white-space: nowrap;
        width: 135px;
    }
    .schedule-slot {
        vertical-align: top;
        padding: 2px !important;
        height: 64px;
        box-sizing: border-box;
    }
    .schedule-slot-free {
        height: 100%;
        min-height: 58px;
        border-radius: 6px;
        border: 1px dashed #e2e8f0;
        background: #fafbfc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #94a3b8;
        transition: all .15s ease;
    }
    .schedule-slot-free:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .schedule-card {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        background: #f5f3ff;
        border: 1.5px solid #ddd6fe;
        border-left: 4px solid var(--color-primario);
        border-radius: 8px;
        padding: 8px 10px;
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 4px;
        transition: all .2s ease;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        position: relative;
    }
    .schedule-card:hover {
        background: #ede9fe;
        border-color: var(--color-primario);
        box-shadow: 0 6px 16px rgba(59,13,143,.15);
        transform: translateY(-1px);
    }
    .schedule-card.tipo-lab {
        background: #f0fdf4;
        border-color: #bbf7d0;
        border-left-color: #10b981;
    }
    .schedule-card.tipo-lab:hover {
        background: #dcfce7;
    }
    .schedule-card-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--color-oscuro);
        line-height: 1.3;
        margin-bottom: 3px;
    }
    .schedule-card-meta {
        font-size: 11px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .schedule-card-badge {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        background: rgba(59,13,143,0.1);
        color: var(--color-primario);
        width: fit-content;
    }
    .schedule-card.tipo-lab .schedule-card-badge {
        background: rgba(16,185,129,0.15);
        color: #047857;
    }
</style>

<div class="horarios-hero">
    <div>
        <h1>Gestion y planificacion de horarios</h1>
        <p>Administra clases manuales, propuestas simuladas de FET y visualiza la cuadrícula de horarios por día.</p>
    </div>
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
        <div class="metric-strip">
            <div class="metric-tile"><strong><?= count($horarios ?? []) ?></strong><span>Horarios filtrados</span></div>
            <div class="metric-tile"><strong><?= count($propuestasFetGeneradas ?? []) ?></strong><span>Propuestas FET</span></div>
        </div>
        <a class="btn-primario" href="?ruta=admin/horarios&tab=ver_horario" style="background:#fff; color:var(--color-primario); font-weight:700; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.15); display:inline-flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-calendar-days" style="color:var(--color-primario);"></i> Ver Horario Semanal
        </a>
    </div>
</div>

<nav class="module-tabs" aria-label="Tabs de horarios">
    <?php foreach ($tabsHorario as $item): ?>
        <a class="module-tab <?= $tab === $item['id'] ? 'active' : '' ?>"
            href="?ruta=admin/horarios&tab=<?= htmlspecialchars($item['id']) ?>">
            <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i>
            <?= htmlspecialchars($item['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($tab === 'general'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Asignar clase</h2>
                <p>Selectores en cascada para asignaturas ofertadas, docentes compatibles y espacios libres.</p>
            </div>
        </div>
        <form action="?ruta=admin/horarios/guardar" method="POST" class="assignment-grid">
            <div class="form-group">
                <label>Periodo academico</label>
                <select name="id_periodo" id="id_periodo" class="form-control" required>
                    <?php foreach ($periodos as $p): ?>
                        <option value="<?= (int) $p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Programa</label>
                <select name="id_programa" id="id_programa" class="form-control" required>
                    <option value="">Seleccionar programa</option>
                    <?php foreach ($programas as $p): ?>
                        <option value="<?= (int) $p['id_programa'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Asignatura ofertada</label>
                <select name="id_asignatura" id="id_asignatura" class="form-control" required>
                    <option value="">Selecciona periodo y programa</option>
                </select>
            </div>
            <div class="form-group">
                <label>Docente compatible</label>
                <select name="id_docente" id="id_docente" class="form-control" required>
                    <option value="">Selecciona una asignatura</option>
                </select>
            </div>
            <div class="form-group">
                <label>Dia</label>
                <select name="dia_semana" id="dia_semana" class="form-control" required>
                    <option value="">Seleccionar dia</option>
                    <?php foreach ($diasSemana as $dia): ?>
                        <option value="<?= $dia ?>"><?= $dia ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hora inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hora fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Espacio disponible</label>
                <select name="id_espacio" id="id_espacio" class="form-control" required>
                    <option value="">Completa dia y horas</option>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <?php foreach ($estadosHorario as $estado): ?>
                        <option value="<?= $estado ?>"><?= $estado ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Origen</label>
                <select name="origen" class="form-control">
                    <?php foreach ($origenes as $origen): ?>
                        <option value="<?= $origen ?>"><?= $origen ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="display:flex; align-items:end;">
                <button type="submit" class="btn-primario" style="width:100%;"><i class="fa-solid fa-calendar-plus"></i>
                    Guardar clase</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Listado general</h2>
                <p>Vista consolidada desde <span class="code-pill">vista_horario_completo</span>.</p>
            </div>
            <a href="?ruta=admin/horarios&tab=ver_horario" class="btn-primario" style="font-size:13px; padding:8px 14px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-calendar-week"></i> Ver en Cuadrícula Semanal
            </a>
        </div>
        <form action="" method="GET" class="filter-grid" style="margin-bottom:16px;">
            <input type="hidden" name="ruta" value="admin/horarios">
            <input type="hidden" name="tab" value="general">
            <div class="form-group"><label>Programa</label><input type="text" name="programa" class="form-control"
                    value="<?= htmlspecialchars($filtros['programa'] ?? '') ?>"></div>
            <div class="form-group"><label>Docente</label><input type="text" name="docente" class="form-control"
                    value="<?= htmlspecialchars($filtros['docente'] ?? '') ?>"></div>
            <div class="form-group"><label>Espacio</label><input type="text" name="espacio" class="form-control"
                    value="<?= htmlspecialchars($filtros['espacio'] ?? '') ?>"></div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <?php foreach ($estadosHorario as $estado): ?>
                        <option value="<?= $estado ?>" <?= (($filtros['estado'] ?? '') === $estado) ? 'selected' : '' ?>>
                            <?= $estado ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><button class="btn-primario" type="submit">Filtrar</button> <a
                    href="?ruta=admin/horarios" class="btn-demo">Limpiar</a></div>
        </form>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Facultad</th>
                        <th>Programa</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Sede</th>
                        <th>Bloque</th>
                        <th>Espacio</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Origen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($horarios)): ?>
                        <tr>
                            <td colspan="13" style="text-align:center; padding:24px;">No hay horarios con los filtros
                                seleccionados.</td>
                        </tr>
                    <?php else:
                        foreach ($horarios as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['periodo'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['facultad'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['programa'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['asignatura'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['docente'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['sede'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['bloque'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['espacio'] ?? '') ?></td>
                                <td><?= htmlspecialchars($h['dia_semana'] ?? '') ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($h['hora_inicio']))) ?> -
                                    <?= htmlspecialchars(date('H:i', strtotime($h['hora_fin']))) ?></td>
                                <td><span
                                        class="badge <?= strtolower($h['estado'] ?? '') ?>"><?= htmlspecialchars($h['estado'] ?? '') ?></span>
                                </td>
                                <td><?= htmlspecialchars($h['origen'] ?? '') ?></td>
                                <td>
                                    <div class="actions-inline">
                                        <a href="?ruta=admin/horarios&tab=ver_horario&periodo_id=<?= (int)($h['id_periodo'] ?? ($periodoActivo['id_periodo'] ?? 0)) ?>&programa_id=<?= (int)($h['id_programa'] ?? 0) ?>"
                                            class="btn-accion btn-accion-editar" title="Ver horario semanal en cuadrícula">
                                            <i class="fa-solid fa-calendar-days"></i>
                                        </a>
                                        <?php if (($h['estado'] ?? '') !== 'CERRADO'): ?>
                                            <a href="?ruta=admin/horarios/cerrar&id=<?= (int) $h['id_horario'] ?>"
                                                class="btn-accion btn-accion-desactivar" title="Cerrar"
                                                onclick="return confirmarCerrarHorario(event, this.href);">
                                                <i class="fa-solid fa-lock"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'ver_horario'): ?>
    <?php
    $diasCuadricula = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
    $horasCuadricula = [];
    for ($hr = 6; $hr < 22; $hr++) {
        $horasCuadricula[] = sprintf('%02d:00:00', $hr);
    }

    $eventosPorDia = [];
    foreach ($horariosCuadricula ?? [] as $hc) {
        $eventosPorDia[$hc['dia_semana']][] = $hc;
    }
    foreach ($eventosPorDia as $dia => $items) {
        usort($items, fn($a, $b) => strcmp($a['hora_inicio'], $b['hora_inicio']));
        $eventosPorDia[$dia] = $items;
    }

    $eventosPorInicio = [];
    $celdasOmitidas = [];
    foreach ($eventosPorDia as $dia => $eventos) {
        foreach ($eventos as $ev) {
            $iniTs = strtotime($ev['hora_inicio']);
            $finTs = strtotime($ev['hora_fin']);
            $duracionSeg = max(0, $finTs - $iniTs);
            $duracionHoras = $duracionSeg / 3600;
            $span = (int)round($duracionHoras);
            if ($span < 1) $span = 1;

            $inicioHora = (int)date('H', $iniTs);
            if ($inicioHora + $span > 22) {
                $span = max(1, 22 - $inicioHora);
            }

            $ev['_rowspan'] = $span;
            $ev['_duracion_texto'] = ($span === 1) ? '1 hora' : $span . ' horas';
            $ev['_hora_formato'] = date('H:i', $iniTs) . ' - ' . date('H:i', $finTs);

            if (!isset($eventosPorInicio[$dia][$inicioHora])) {
                $eventosPorInicio[$dia][$inicioHora] = $ev;
                for ($i = 1; $i < $span; $i++) {
                    $celdasOmitidas[$dia][$inicioHora + $i] = true;
                }
            }
        }
    }

    $fnFormatoHora = function ($hora) {
        $inicio = strtotime($hora);
        $fin = strtotime('+1 hour', $inicio);
        return date('g:00', $inicio) . ' - ' . date('g:00 A', $fin);
    };

    $progSeleccionadoObj = null;
    foreach ($programas as $prg) {
        if ((int)$prg['id_programa'] === (int)$idProgramaHorario) {
            $progSeleccionadoObj = $prg;
            break;
        }
    }
    ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Horario Semanal — Cuadrícula por Días</h2>
                <p>Visualización interactiva tipo calendario de clases programadas de Lunes a Sábado.</p>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn-secundario" onclick="window.print();" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-print"></i> Imprimir Horario
                </button>
                <a href="?ruta=admin/horarios&tab=general" class="btn-primario" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-plus"></i> Asignar Nueva Clase
                </a>
            </div>
        </div>

        <!-- Filtros de Cuadrícula -->
        <form method="GET" class="filter-grid" style="margin-bottom: 16px;">
            <input type="hidden" name="ruta" value="admin/horarios">
            <input type="hidden" name="tab" value="ver_horario">

            <div class="form-group">
                <label>Periodo Académico</label>
                <select name="periodo_id" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($periodos as $p): ?>
                        <option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$idPeriodoHorario === (int)$p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Programa Académico</label>
                <select name="programa_id" class="form-control" onchange="this.form.submit()">
                    <option value="0" <?= ($idProgramaHorario === null || $idProgramaHorario === 0) ? 'selected' : '' ?>>Todos los programas</option>
                    <?php foreach ($programas as $prog): ?>
                        <option value="<?= (int)$prog['id_programa'] ?>" <?= ((int)$idProgramaHorario === (int)$prog['id_programa']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($prog['codigo'] ? $prog['codigo'].' - ' : '').$prog['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Docente</label>
                <select name="docente_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los docentes</option>
                    <?php foreach ($docentes as $doc): ?>
                        <option value="<?= (int)$doc['id_docente'] ?>" <?= ((int)$idDocenteHorario === (int)$doc['id_docente']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($doc['apellido'] . ' ' . $doc['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Espacio Físico</label>
                <select name="espacio_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los espacios</option>
                    <?php foreach ($espacios as $esp): ?>
                        <option value="<?= (int)$esp['id_espacio'] ?>" <?= ((int)$idEspacioHorario === (int)$esp['id_espacio']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($esp['sede_nombre'] . ' / ' . $esp['bloque_nombre'] . ' / ' . $esp['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Semestre</label>
                <select name="semestre" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los semestres</option>
                    <?php for ($s = 1; $s <= 10; $s++): ?>
                        <option value="<?= $s ?>" <?= ((int)$semestreHorario === $s) ? 'selected' : '' ?>>Semestre <?= $s ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primario" style="width:100%;">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <!-- Indicador de contexto -->
        <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; padding:10px 14px; border-radius:8px; margin-bottom:12px; font-size:13px;">
            <div>
                <strong>Programa:</strong> <?= htmlspecialchars($progSeleccionadoObj['nombre'] ?? 'Todos los programas') ?>
                <?php if (!empty($semestreHorario)): ?>
                    <span class="badge confirmado" style="margin-left:6px;">Semestre <?= (int)$semestreHorario ?></span>
                <?php endif; ?>
            </div>
            <div>
                <span style="color:var(--color-texto-secundario);">Total clases en cuadrícula:</span> <strong><?= count($horariosCuadricula ?? []) ?></strong>
            </div>
        </div>

        <!-- Tabla Cuadrícula Semanal -->
        <div class="schedule-grid-wrap">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="hour-header"><i class="fa-regular fa-clock"></i> Hora</th>
                        <?php foreach ($diasCuadricula as $dia): ?>
                            <th><?= $dia ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horasCuadricula as $hora): $horaInt = (int)date('H', strtotime($hora)); ?>
                        <tr style="height: 64px;">
                            <td class="schedule-time-cell">
                                <div><?= htmlspecialchars($fnFormatoHora($hora)) ?></div>
                                <?php if ($horaInt === 12): ?>
                                    <span style="display:block; background:#FFF8E6; color:#B78103; border:1px solid rgba(245,179,1,0.45); border-radius:999px; padding:1px 6px; font-size:10px; font-weight:700; margin:3px auto 0; text-transform:uppercase; width:fit-content;">Mediodía</span>
                                <?php endif; ?>
                            </td>
                            <?php foreach ($diasCuadricula as $dia): ?>
                                <?php if (!empty($celdasOmitidas[$dia][$horaInt])) continue; ?>
                                <?php $ev = $eventosPorInicio[$dia][$horaInt] ?? null; ?>
                                <?php if ($ev): $span = (int)$ev['_rowspan']; $isLab = (strtoupper($ev['espacio_tipo'] ?? '') === 'LABORATORIO'); ?>
                                    <td class="schedule-slot" rowspan="<?= $span ?>">
                                        <div class="schedule-card <?= $isLab ? 'tipo-lab' : '' ?>" style="min-height: calc(<?= $span ?> * 64px - 6px);">
                                            <div>
                                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:4px; margin-bottom:3px;">
                                                    <span class="schedule-card-badge">
                                                        <?= htmlspecialchars($ev['asignatura_codigo'] ?? '') ?>
                                                        <?= !empty($ev['semestre']) ? '• Sem. ' . $ev['semestre'] : '' ?>
                                                    </span>
                                                    <span style="font-size:10.5px; font-weight:700; color:var(--color-primario);">
                                                        <?= htmlspecialchars($ev['_hora_formato']) ?>
                                                    </span>
                                                </div>
                                                <strong class="schedule-card-title" title="<?= htmlspecialchars($ev['asignatura_nombre']) ?>">
                                                    <?= htmlspecialchars($ev['asignatura_nombre']) ?>
                                                </strong>
                                            </div>
                                            <div>
                                                <div class="schedule-card-meta" title="Docente: <?= htmlspecialchars($ev['docente_nombre']) ?>">
                                                    <i class="fa-solid fa-chalkboard-user"></i>
                                                    <span><?= htmlspecialchars($ev['docente_nombre']) ?></span>
                                                </div>
                                                <div class="schedule-card-meta" title="Espacio: <?= htmlspecialchars($ev['sede_nombre'].' - '.$ev['bloque_nombre'].' - '.$ev['espacio_nombre']) ?>">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    <span><?= htmlspecialchars($ev['espacio_nombre']) ?> (<?= htmlspecialchars($ev['espacio_tipo']) ?>)</span>
                                                </div>
                                                <?php if (empty($idProgramaHorario)): ?>
                                                    <div class="schedule-card-meta" style="font-size:10px; color:#64748b;" title="Programa: <?= htmlspecialchars($ev['programa_nombre']) ?>">
                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                        <span><?= htmlspecialchars($ev['programa_nombre']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td class="schedule-slot">
                                        <div class="schedule-slot-free">Libre</div>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'fet'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Simular generacion FET</h2>
                <p>Genera una propuesta coherente en <span class="code-pill">propuesta_fet</span> y <span
                        class="code-pill">propuesta_fet_detalle</span>. Integracion real pendiente.</p>
            </div>
        </div>
        <form action="?ruta=admin/horarios/generar_fet" method="POST" class="assignment-grid">
            <div class="form-group">
                <label>Periodo</label>
                <select name="id_periodo" class="form-control" required>
                    <?php foreach ($periodos as $p): ?>
                        <option value="<?= (int) $p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Programa</label>
                <select name="id_programa" class="form-control" required>
                    <option value="">Seleccionar programa</option>
                    <?php foreach ($programas as $p): ?>
                        <option value="<?= (int) $p['id_programa'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tipo de generacion</label>
                <select name="franja" class="form-control">
                    <option value="DIURNA">Diurna balanceada</option>
                    <option value="NOCTURNA">Nocturna intensiva</option>
                    <option value="MIXTA">Mixta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Peso docente</label>
                <select name="peso_docente" class="form-control">
                    <option value="ALTO">Alto</option>
                    <option value="MEDIO">Medio</option>
                    <option value="BAJO">Bajo</option>
                </select>
            </div>
            <div class="form-group" style="grid-column:1 / -1;">
                <label>Dias lectivos</label>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <?php foreach (array_slice($diasSemana, 0, 6) as $dia): ?>
                        <label style="display:inline-flex; gap:6px; align-items:center; font-size:13px;"><input type="checkbox"
                                name="dias[]" value="<?= $dia ?>" checked> <?= $dia ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group"><button type="submit" class="btn-primario"><i class="fa-solid fa-play"></i> Simular
                    FET</button></div>
        </form>
    </div>

    <?php if (!empty($propuestaFet)): ?>
        <div class="panel">
            <div class="section-title">
                <div>
                    <h2>Ultima propuesta generada</h2>
                    <p><?= htmlspecialchars($propuestaFet['programa_nombre'] ?? '') ?> -
                        <?= htmlspecialchars($propuestaFet['periodo_codigo'] ?? '') ?></p>
                </div><span
                    class="badge <?= strtolower($propuestaFet['estado']) ?>"><?= htmlspecialchars($propuestaFet['estado']) ?></span>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Espacio</th>
                            <th>Dia</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detallesFet as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '') . ' ' . ($d['asignatura_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['docente_nombre'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($d['sede_nombre'] ?? '') . ' / ' . ($d['bloque_nombre'] ?? '') . ' / ' . ($d['espacio_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['dia_semana'] ?? '') ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($d['hora_inicio']))) ?> -
                                    <?= htmlspecialchars(date('H:i', strtotime($d['hora_fin']))) ?></td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php elseif ($tab === 'propuestas'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Propuestas FET</h2>
                <p>Acepta lineas para crear horarios reales con origen FET, o descarta propuestas completas.</p>
            </div>
        </div>
        <div class="filter-grid" style="margin-bottom:16px;">
            <div class="form-group">
                <label>Propuesta</label>
                <select class="form-control"
                    onchange="if (this.value) window.location='?ruta=admin/horarios&tab=propuestas&propuesta_id=' + this.value">
                    <option value="">Seleccionar propuesta</option>
                    <?php foreach ($propuestasFetGeneradas as $pf): ?>
                        <option value="<?= (int) $pf['id_propuesta'] ?>"
                            <?= ((int) $propuestaSeleccionada === (int) $pf['id_propuesta']) ? 'selected' : '' ?>>
                            #<?= (int) $pf['id_propuesta'] ?> - <?= htmlspecialchars($pf['programa_nombre']) ?> -
                            <?= htmlspecialchars($pf['estado']) ?> - <?= (int) $pf['total_detalles'] ?> lineas
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($propuestaSeleccionada): ?>
                <div class="form-group" style="display:flex; align-items:end;">
                    <form action="?ruta=admin/horarios/descartar_propuesta_fet" method="POST"
                        onsubmit="return confirmarDesactivacion(event, this, 'La propuesta completa quedara marcada como descartada.');">
                        <input type="hidden" name="id_propuesta" value="<?= (int) $propuestaSeleccionada ?>">
                        <button class="btn-demo" type="submit"><i class="fa-solid fa-ban"></i> Descartar propuesta</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detallesPropuestaSeleccionada)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:24px;">No hay lineas para revisar.</td>
                        </tr>
                    <?php else:
                        foreach ($detallesPropuestaSeleccionada as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '') . ' ' . ($d['asignatura_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['docente_nombre'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($d['sede_nombre'] ?? '') . ' / ' . ($d['bloque_nombre'] ?? '') . ' / ' . ($d['espacio_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['dia_semana'] ?? '') ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($d['hora_inicio']))) ?> -
                                    <?= htmlspecialchars(date('H:i', strtotime($d['hora_fin']))) ?></td>
                                <td><?= !empty($d['id_horario_resultante']) ? '<span class="badge aceptada">ACEPTADA</span>' : '<span class="badge generada">GENERADA</span>' ?>
                                </td>
                                <td style="text-align:right;">
                                    <span class="actions-inline">
                                        <?php if (empty($d['id_horario_resultante'])): ?>
                                            <a class="btn-accion btn-accion-aceptar" title="Aceptar"
                                                href="?ruta=admin/horarios/aceptar_propuesta_fet&id=<?= (int) $d['id_detalle'] ?>&propuesta_id=<?= (int) $propuestaSeleccionada ?>"><i
                                                    class="fa-solid fa-check"></i></a>
                                            <form action="?ruta=admin/horarios/eliminar_detalle_fet" method="POST"
                                                onsubmit="return confirmarEliminacion(event, this, 'esta linea FET');">
                                                <input type="hidden" name="id_detalle" value="<?= (int) $d['id_detalle'] ?>">
                                                <input type="hidden" name="propuesta_id" value="<?= (int) $propuestaSeleccionada ?>">
                                                <button class="btn-accion btn-accion-eliminar" title="Descartar linea" type="submit"><i
                                                        class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'auditoria'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Auditoria de clases</h2>
                <p>Filtra clases pendientes y registra el resultado de verificacion.</p>
            </div>
        </div>
        <form action="" method="GET" class="filter-grid" style="margin-bottom:16px;">
            <input type="hidden" name="ruta" value="admin/horarios"><input type="hidden" name="tab" value="auditoria">
            <div class="form-group"><label>Fecha</label><input type="date" name="fecha" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['fecha'] ?? '') ?>"></div>
            <div class="form-group"><label>Sede</label><input name="sede" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['sede'] ?? '') ?>"></div>
            <div class="form-group"><label>Bloque</label><input name="bloque" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['bloque'] ?? '') ?>"></div>
            <div class="form-group"><label>Espacio</label><input name="espacio_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['espacio'] ?? '') ?>"></div>
            <div class="form-group"><label>Docente</label><input name="docente_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['docente'] ?? '') ?>"></div>
            <div class="form-group"><label>Programa</label><input name="programa_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['programa'] ?? '') ?>"></div>
            <div class="form-group"><label>Resultado</label><select name="resultado" class="form-control">
                    <option value="">Todos</option><?php foreach (['PENDIENTE', 'VERIFICADA', 'NO_REALIZADA'] as $res): ?>
                        <option value="<?= $res ?>" <?= (($filtrosAuditoria['resultado'] ?? '') === $res) ? 'selected' : '' ?>>
                            <?= $res ?></option><?php endforeach; ?>
                </select></div>
            <div class="form-group"><button class="btn-primario" type="submit">Filtrar</button> <a
                    href="?ruta=admin/horarios&tab=auditoria" class="btn-demo">Limpiar</a></div>
        </form>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Clase</th>
                        <th>Docente</th>
                        <th>Programa</th>
                        <th>Espacio</th>
                        <th>Horario</th>
                        <th>Resultado</th>
                        <th>Verificacion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditorias)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:24px;">No hay auditorias con estos filtros.</td>
                        </tr>
                    <?php else:
                        foreach ($auditorias as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['fecha_auditoria'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['asignatura'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['docente'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['programa'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($a['sede'] ?? '') . ' / ' . ($a['bloque'] ?? '') . ' / ' . ($a['espacio'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars(($a['dia_semana'] ?? '') . ' ' . date('H:i', strtotime($a['hora_inicio'])) . '-' . date('H:i', strtotime($a['hora_fin']))) ?>
                                </td>
                                <td><span
                                        class="badge <?= strtolower($a['resultado'] ?? '') ?>"><?= htmlspecialchars($a['resultado'] ?? '') ?></span>
                                </td>
                                <td>
                                    <form class="audit-form" action="?ruta=admin/horarios/verificar_auditoria" method="POST">
                                        <input type="hidden" name="id_auditoria" value="<?= (int) $a['id_auditoria'] ?>">
                                        <input type="text" name="observacion" class="form-control" placeholder="Observacion">
                                        <button class="btn-accion btn-accion-aceptar" name="resultado" value="VERIFICADA"
                                            title="Verificada"><i class="fa-solid fa-check"></i></button>
                                        <button class="btn-accion btn-accion-eliminar" name="resultado" value="NO_REALIZADA"
                                            title="No realizada"><i class="fa-solid fa-xmark"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const periodo = document.getElementById('id_periodo');
        const programa = document.getElementById('id_programa');
        const asignatura = document.getElementById('id_asignatura');
        const docente = document.getElementById('id_docente');
        const dia = document.getElementById('dia_semana');
        const inicio = document.getElementById('hora_inicio');
        const fin = document.getElementById('hora_fin');
        const espacio = document.getElementById('id_espacio');

        const fillSelect = (select, rows, placeholder, mapper) => {
            if (!select) return;
            select.innerHTML = `<option value="">${placeholder}</option>`;
            rows.forEach(row => {
                const option = document.createElement('option');
                const mapped = mapper(row);
                option.value = mapped.value;
                option.textContent = mapped.label;
                select.appendChild(option);
            });
        };

        const loadAsignaturas = async () => {
            if (!periodo || !programa || !periodo.value || !programa.value) return;
            const res = await fetch(`?ruta=admin/horarios/api_asignaturas&periodo_id=${periodo.value}&programa_id=${programa.value}`);
            fillSelect(asignatura, await res.json(), 'Seleccionar asignatura', row => ({ value: row.id_asignatura, label: `${row.codigo} - ${row.nombre}` }));
            fillSelect(docente, [], 'Selecciona una asignatura', row => row);
        };
        const loadDocentes = async () => {
            if (!asignatura || !asignatura.value) return;
            const res = await fetch(`?ruta=admin/horarios/api_docentes&asignatura_id=${asignatura.value}`);
            fillSelect(docente, await res.json(), 'Seleccionar docente', row => ({ value: row.id_docente, label: `${row.apellido} ${row.nombre}` }));
        };
        const loadEspacios = async () => {
            if (!periodo || !dia || !inicio || !fin || !periodo.value || !dia.value || !inicio.value || !fin.value) return;
            const res = await fetch(`?ruta=admin/horarios/api_espacios&periodo_id=${periodo.value}&dia_semana=${dia.value}&hora_inicio=${inicio.value}&hora_fin=${fin.value}`);
            fillSelect(espacio, await res.json(), 'Seleccionar espacio libre', row => ({ value: row.id_espacio, label: `${row.sede_nombre} / ${row.bloque_nombre} / ${row.nombre} (${row.tipo}, ${row.capacidad})` }));
        };
        [periodo, programa].forEach(el => el && el.addEventListener('change', loadAsignaturas));
        asignatura && asignatura.addEventListener('change', loadDocentes);
        [periodo, dia, inicio, fin].forEach(el => el && el.addEventListener('change', loadEspacios));
    });
</script>

<?php $this->render('elementos_compartidos/footer'); ?>