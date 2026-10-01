<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Disponibilidad de Espacios — UniFET']);
$this->render('elementos_compartidos/sidebar_lider');
$dias = ['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO'];
$ocupadoPorDia = [];
foreach ($ocupacion as $h) {
    $ocupadoPorDia[$h['dia_semana']][] = $h;
}
foreach ($ocupadoPorDia as $dia => $items) {
    usort($items, fn($a, $b) => strcmp($a['hora_inicio'], $b['hora_inicio']));
    $ocupadoPorDia[$dia] = $items;
}
$detalleOcupacion = function ($h) {
    return [
        'programa' => trim(($h['programa_codigo'] ?? '') . ' - ' . ($h['programa_nombre'] ?? '')),
        'facultad' => $h['facultad_nombre'] ?? '',
        'lideres' => $h['lideres_programa'] ?: 'Sin líder asignado',
        'asignatura' => $h['asignatura_nombre'] ?? '',
        'docente' => $h['docente_nombre'] ?? '',
        'espacio' => ($h['sede_nombre'] ?? '') . ' / ' . ($h['bloque_nombre'] ?? '') . ' / ' . ($h['espacio_nombre'] ?? ''),
        'dia' => $h['dia_semana'] ?? '',
        'hora' => date('H:i', strtotime($h['hora_inicio'])) . ' - ' . date('H:i', strtotime($h['hora_fin'])),
        'estado' => $h['estado'] ?? '',
        'origen' => $h['origen'] ?? '',
    ];
};
$horas = [];
for ($h = 6; $h < 22; $h++) {
    $horas[] = sprintf('%02d:00:00', $h);
}

// Mapeo dinámico de ocupación continua por día y hora de inicio
$eventosPorInicio = [];
$celdasOmitidas = [];
foreach ($ocupadoPorDia as $dia => $eventos) {
    foreach ($eventos as $evento) {
        $iniTs = strtotime($evento['hora_inicio']);
        $finTs = strtotime($evento['hora_fin']);
        $duracionSeg = max(0, $finTs - $iniTs);
        $duracionHoras = $duracionSeg / 3600;

        // Número de filas (horas) continuas que debe abarcar
        // Ej: 08:00–09:00 -> 1 fila, 08:00–10:00 -> 2 filas, 15:00–19:00 -> 4 filas, 18:30–20:30 -> 2 filas
        $span = (int)round($duracionHoras);
        if ($span < 1) $span = 1;

        $inicioHora = (int)date('H', $iniTs);
        // Respetar escala de la grilla (hasta las 22:00)
        if ($inicioHora + $span > 22) {
            $span = max(1, 22 - $inicioHora);
        }

        $duracionTexto = ($span === 1) ? '1 hora' : $span . ' horas';
        $evento['_rowspan'] = $span;
        $evento['_duracion_texto'] = $duracionTexto;
        $evento['_hora_formato'] = date('H:i', $iniTs) . ' - ' . date('H:i', $finTs);

        if (!isset($eventosPorInicio[$dia][$inicioHora])) {
            $eventosPorInicio[$dia][$inicioHora] = $evento;
            for ($i = 1; $i < $span; $i++) {
                $celdasOmitidas[$dia][$inicioHora + $i] = true;
            }
        }
    }
}

$formatoHora = function ($hora) {
    $inicio = strtotime($hora);
    $fin = strtotime('+1 hour', $inicio);
    return date('g:00', $inicio) . ' a ' . date('g:00 A', $fin);
};
$sedes = array_values(array_unique(array_filter(array_column($espacios, 'sede_nombre'))));
$bloques = array_values(array_unique(array_filter(array_column($espacios, 'bloque_nombre'))));
$espacioSeleccionado = null;
foreach ($espacios as $e) {
    if ((int)$e['id_espacio'] === (int)$idEspacio) $espacioSeleccionado = $e;
}
?>

<style>
.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(210px, 100%), 1fr));
    gap: 14px;
    align-items: end;
}
.availability {
    overflow-x: auto;
    border-radius: 12px;
    background: #fff;
    box-shadow: var(--sombra-tarjeta);
    border: 1px solid var(--color-borde);
    padding: 12px;
    margin-top: 16px;
}
.availability table {
    width: 100%;
    min-width: 980px;
    border-collapse: separate;
    border-spacing: 5px;
    table-layout: fixed;
}
.availability thead th {
    background: #0B1F33;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    padding: 12px 8px;
    text-align: center;
    border-radius: 6px;
    letter-spacing: .4px;
    border: none;
}
.hour-col {
    width: 145px;
    background: #0B1F33 !important;
    color: #fff !important;
}
.hour-row {
    height: 64px;
}
.hour-cell {
    font-size: 12px;
    color: #0B1F33;
    font-weight: 700;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    text-align: center;
    padding: 8px 6px;
    vertical-align: middle;
    white-space: nowrap;
}
.midday-pill {
    display: block;
    background: #FFF8E6;
    color: #B78103;
    border: 1px solid rgba(245, 179, 1, 0.45);
    border-radius: 999px;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: 700;
    margin: 3px auto 0;
    text-transform: uppercase;
    letter-spacing: .4px;
    width: fit-content;
}
.time-cell {
    min-width: 135px;
    vertical-align: top;
    padding: 2px !important;
    height: 64px;
    box-sizing: border-box;
}
.time-cell.cell-busy-td {
    vertical-align: top !important;
    height: 1px; /* Estiramiento vertical en tabla */
    cursor: pointer;
}
.free-summary {
    height: 100%;
    min-height: 58px;
    border-radius: 8px;
    padding: 8px;
    background: rgba(15, 157, 88, 0.05);
    border: 1px dashed rgba(15, 157, 88, 0.24);
    color: #0F9D58;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    transition: all .15s ease;
}
.free-summary:hover {
    background: rgba(15, 157, 88, 0.12);
    border-color: #0F9D58;
}

/* Tarjeta continua de horario ocupado */
.busy-block {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    background: rgba(217, 54, 54, 0.08);
    border: 1.5px solid rgba(217, 54, 54, 0.28);
    border-left: 4.5px solid #D93636;
    border-radius: 8px;
    color: #b42318;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
    transition: all .2s ease;
    box-shadow: 0 1px 3px rgba(217, 54, 54, 0.06);
}
.busy-block:hover {
    background: rgba(217, 54, 54, 0.14);
    border-color: #D93636;
    box-shadow: 0 6px 18px rgba(217, 54, 54, 0.18);
    transform: translateY(-1px);
}
.busy-block-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.busy-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #b42318;
    letter-spacing: .2px;
}
.busy-time {
    font-size: 12px;
    font-weight: 600;
    color: #991b1b;
}
.busy-duration {
    font-size: 11.5px;
    font-weight: 600;
    color: #7f1d1d;
    background: rgba(217, 54, 54, 0.12);
    padding: 2px 10px;
    border-radius: 999px;
    margin-top: 2px;
    display: inline-block;
}
.busy-tooltip {
    display: none;
    position: absolute;
    left: 50%;
    bottom: calc(100% + 8px);
    transform: translateX(-50%);
    z-index: 50;
    min-width: 230px;
    background: #0B1F33;
    color: #fff;
    border-radius: 8px;
    padding: 10px 12px;
    box-shadow: 0 10px 30px rgba(11,31,51,.3);
    font-size: 12px;
    text-align: left;
    pointer-events: none;
}
.busy-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: #0B1F33 transparent transparent transparent;
}
.hour-row:first-child .busy-tooltip,
.hour-row:nth-child(2) .busy-tooltip {
    bottom: auto;
    top: calc(100% + 8px);
}
.hour-row:first-child .busy-tooltip::after,
.hour-row:nth-child(2) .busy-tooltip::after {
    top: auto;
    bottom: 100%;
    border-color: transparent transparent #0B1F33 transparent;
}
.busy-block:hover .busy-tooltip {
    display: block;
}

.space-meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}
.space-meta span {
    background: #f1f3f5;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 12px;
    color: var(--color-texto-secundario);
}
.occupied-swal-container { text-align: left !important; }
.occupied-detail { text-align: left; color: #0B1F33; }
.occupied-header {
    display: grid;
    grid-template-columns: 58px 1fr;
    gap: 14px;
    align-items: center;
    padding-bottom: 14px;
    border-bottom: 1px solid #E2E5E9;
}
.modal-logo {
    width: 58px; height: 58px;
    border-radius: 12px;
    background: rgba(59,13,143,.08);
    display: grid; place-items: center;
}
.modal-logo img { max-width: 40px; max-height: 40px; }
.occupied-header h2 { font-size: 20px; margin: 0 0 4px; color: #0B1F33; }
.occupied-header p { margin: 0; color: #637083; font-size: 13px; }
.detail-chips { display: flex; gap: 8px; flex-wrap: wrap; margin: 14px 0; }
.detail-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #F6F7F9;
    border: 1px solid #E2E5E9;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 12px;
    color: #4B5563;
}
.detail-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.detail-section {
    border: 1px solid #E2E5E9;
    border-radius: 8px;
    padding: 12px;
    background: #fff;
}
.detail-section h3 {
    font-size: 13px;
    text-transform: uppercase;
    color: #637083;
    letter-spacing: .4px;
    margin: 0 0 10px;
}
.detail-list { display: grid; gap: 9px; }
.detail-item { display: grid; gap: 3px; font-size: 13px; }
.detail-item span { color: #637083; font-size: 12px; }
.detail-item strong { color: #0B1F33; font-weight: 600; line-height: 1.35; }
.occupied-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(11,31,51,.46);
    z-index: 2000;
    display: grid; place-items: center;
    padding: 18px;
}
.occupied-modal {
    width: min(720px, 100%);
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 18px 60px rgba(11,31,51,.22);
}
.occupied-modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 18px;
}
@media (max-width: 700px) {
    .detail-sections { grid-template-columns: 1fr; }
    .occupied-header { grid-template-columns: 1fr; }
    .detail-item { grid-template-columns: 1fr; }
    .busy-tooltip { display: none !important; }
}
</style>

<div class="panel">
    <h1 style="font-size: 22px; margin-bottom: 6px; font-weight: 700; color: var(--color-oscuro);">Disponibilidad de Espacios</h1>
    <p style="color: var(--color-texto-secundario); font-size: 13.5px;">Consulta dinámica de ocupación real. El calendario cruza contra los horarios registrados en el periodo académico activo.</p>

    <form method="GET" class="filter-grid" id="space-filter-form">
        <input type="hidden" name="ruta" value="lider/espacios">
        <div class="form-group">
            <label>Buscar</label>
            <input type="search" id="space-search" class="form-control" placeholder="Aula, sede, bloque, tipo...">
        </div>
        <div class="form-group">
            <label>Sede</label>
            <select id="sede-filter" class="form-control">
                <option value="">Todas</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= htmlspecialchars($sede) ?>"><?= htmlspecialchars($sede) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Bloque</label>
            <select id="bloque-filter" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($bloques as $bloque): ?>
                    <option value="<?= htmlspecialchars($bloque) ?>"><?= htmlspecialchars($bloque) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de referencia</label>
            <input type="date" name="fecha_ref" class="form-control" value="<?= htmlspecialchars($_GET['fecha_ref'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="form-group">
            <label>Espacio</label>
            <select name="espacio_id" id="space-select" class="form-control">
                <?php foreach ($espacios as $e): ?>
                    <option value="<?= (int)$e['id_espacio'] ?>"
                            data-search="<?= htmlspecialchars(strtolower($e['sede_nombre'].' '.$e['bloque_nombre'].' '.$e['nombre'].' '.$e['tipo'])) ?>"
                            data-sede="<?= htmlspecialchars($e['sede_nombre']) ?>"
                            data-bloque="<?= htmlspecialchars($e['bloque_nombre']) ?>"
                            <?= $idEspacio == $e['id_espacio'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['sede_nombre'].' / '.$e['bloque_nombre'].' / '.$e['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <button class="btn-primario" style="width: 100%;">
                <i class="fa-solid fa-magnifying-glass"></i> Consultar
            </button>
        </div>
    </form>

    <?php if ($espacioSeleccionado): ?>
        <div class="space-meta">
            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($espacioSeleccionado['sede_nombre']) ?></span>
            <span><i class="fa-solid fa-building"></i> <?= htmlspecialchars($espacioSeleccionado['bloque_nombre']) ?></span>
            <span><i class="fa-solid fa-door-open"></i> <?= htmlspecialchars($espacioSeleccionado['nombre']) ?></span>
            <span><i class="fa-solid fa-users"></i> Capacidad <?= (int)$espacioSeleccionado['capacidad'] ?></span>
        </div>
    <?php endif; ?>
</div>

<div class="availability">
    <table>
        <thead>
            <tr>
                <th class="hour-col">Hora</th>
                <?php foreach ($dias as $dia): ?>
                    <th><?= $dia ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($horas as $hora): $horaInt = (int)date('H', strtotime($hora)); ?>
                <tr class="hour-row">
                    <td class="hour-cell">
                        <div><?= htmlspecialchars($formatoHora($hora)) ?></div>
                        <?php if ($horaInt === 12): ?>
                            <span class="midday-pill">Mediodía</span>
                        <?php endif; ?>
                    </td>
                    <?php foreach ($dias as $dia): ?>
                        <?php if (!empty($celdasOmitidas[$dia][$horaInt])) continue; ?>
                        <?php $h = $eventosPorInicio[$dia][$horaInt] ?? null; ?>
                        <?php if ($h): $detalle = $detalleOcupacion($h); $span = (int)$h['_rowspan']; ?>
                            <td class="time-cell cell-busy-td" rowspan="<?= $span ?>" data-occupied-detail="<?= htmlspecialchars(json_encode($detalle), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="busy-block" style="min-height: calc(<?= $span ?> * 64px - 8px);">
                                    <div class="busy-block-content">
                                        <strong class="busy-title"><i class="fa-solid fa-lock" style="font-size: 11px;"></i> Ocupado</strong>
                                        <span class="busy-time"><?= htmlspecialchars($h['_hora_formato']) ?></span>
                                        <span class="busy-duration"><?= htmlspecialchars($h['_duracion_texto']) ?></span>
                                    </div>
                                    <div class="busy-tooltip">
                                        <strong><?= htmlspecialchars($detalle['programa']) ?></strong><br>
                                        <?= htmlspecialchars($detalle['asignatura'] ?? '') ?><br>
                                        <span style="opacity: 0.8;"><?= htmlspecialchars($detalle['espacio']) ?></span><br>
                                        <strong><?= htmlspecialchars($h['_hora_formato']) ?></strong> (<?= htmlspecialchars($h['_duracion_texto']) ?>)
                                    </div>
                                </div>
                            </td>
                        <?php else: ?>
                            <td class="time-cell">
                                <div class="free-summary">Libre</div>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('space-search');
    const sede = document.getElementById('sede-filter');
    const bloque = document.getElementById('bloque-filter');
    const select = document.getElementById('space-select');
    const allOptions = [...select.options].map(option => option.cloneNode(true));
    const normalize = value => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const esc = value => String(value || '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    
    const applyFilters = () => {
        const text = normalize(search.value);
        const sedeValue = sede.value;
        const bloqueValue = bloque.value;
        const current = select.value;
        select.innerHTML = '';
        allOptions.forEach(option => {
            const matchesText = !text || normalize(option.dataset.search).includes(text);
            const matchesSede = !sedeValue || option.dataset.sede === sedeValue;
            const matchesBloque = !bloqueValue || option.dataset.bloque === bloqueValue;
            if (matchesText && matchesSede && matchesBloque) select.appendChild(option.cloneNode(true));
        });
        if ([...select.options].some(option => option.value === current)) select.value = current;
    };
    search.addEventListener('input', applyFilters);
    [sede, bloque].forEach(input => input.addEventListener('change', applyFilters));

    document.querySelectorAll('[data-occupied-detail]').forEach(cell => {
        cell.addEventListener('click', () => {
            const detail = JSON.parse(cell.dataset.occupiedDetail || '{}');
            const html = `
                <div class="occupied-detail">
                <div class="occupied-header">
                    <div class="modal-logo"><img src="assets/img/isotipo-color.png" alt="UniFET"></div>
                    <div>
                        <h2>Espacio ocupado</h2>
                        <p>${esc(detail.programa)}</p>
                    </div>
                </div>
                <div class="detail-chips">
                    <span class="detail-chip"><i class="fa-solid fa-calendar-day"></i>${esc(detail.dia) || '-'}</span>
                    <span class="detail-chip"><i class="fa-regular fa-clock"></i>${esc(detail.hora) || '-'}</span>
                    <span class="detail-chip"><i class="fa-solid fa-lock"></i>${esc(detail.estado) || '-'}</span>
                </div>
                <div class="detail-sections">
                    <section class="detail-section">
                        <h3>Ubicación</h3>
                        <div class="detail-list">
                            <div class="detail-item"><span>Espacio</span><strong>${esc(detail.espacio) || '-'}</strong></div>
                            <div class="detail-item"><span>Estado / origen</span><strong>${esc(detail.estado) || '-'} / ${esc(detail.origen) || '-'}</strong></div>
                        </div>
                    </section>
                    <section class="detail-section">
                        <h3>Programa</h3>
                        <div class="detail-list">
                            <div class="detail-item"><span>Programa</span><strong>${esc(detail.programa) || '-'}</strong></div>
                            <div class="detail-item"><span>Facultad</span><strong>${esc(detail.facultad) || '-'}</strong></div>
                            <div class="detail-item"><span>Líder a cargo</span><strong>${esc(detail.lideres) || '-'}</strong></div>
                        </div>
                    </section>
                    <section class="detail-section">
                        <h3>Clase</h3>
                        <div class="detail-list">
                            <div class="detail-item"><span>Asignatura</span><strong>${esc(detail.asignatura) || '-'}</strong></div>
                            <div class="detail-item"><span>Docente</span><strong>${esc(detail.docente) || '-'}</strong></div>
                        </div>
                    </section>
                    <section class="detail-section">
                        <h3>Horario</h3>
                        <div class="detail-list">
                            <div class="detail-item"><span>Día</span><strong>${esc(detail.dia) || '-'}</strong></div>
                            <div class="detail-item"><span>Hora</span><strong>${esc(detail.hora) || '-'}</strong></div>
                        </div>
                    </section>
                </div>
                </div>
            `;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    width: 760,
                    customClass: { htmlContainer: 'occupied-swal-container' },
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3B0D8F',
                    html
                });
                return;
            }
            const backdrop = document.createElement('div');
            backdrop.className = 'occupied-modal-backdrop';
            backdrop.innerHTML = `<div class="occupied-modal">${html}<div class="occupied-modal-actions"><button class="btn-primario" type="button">Cerrar</button></div></div>`;
            backdrop.querySelector('button').addEventListener('click', () => backdrop.remove());
            backdrop.addEventListener('click', event => { if (event.target === backdrop) backdrop.remove(); });
            document.body.appendChild(backdrop);
        });
    });
});
</script>
<?php $this->render('elementos_compartidos/footer'); ?>
