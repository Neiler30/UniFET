<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Disponibilidad']);
$this->render('shared/sidebar_lider');
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
        'lideres' => $h['lideres_programa'] ?: 'Sin lider asignado',
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
$eventosPorInicio = [];
$celdasOmitidas = [];
foreach ($ocupadoPorDia as $dia => $eventos) {
    foreach ($eventos as $evento) {
        $inicioHora = (int)date('H', strtotime($evento['hora_inicio']));
        $finTs = strtotime($evento['hora_fin']);
        $inicioTs = strtotime(sprintf('%02d:00:00', $inicioHora));
        $span = max(1, (int)ceil(($finTs - $inicioTs) / 3600));
        $span = min($span, 22 - $inicioHora);
        $evento['_rowspan'] = $span;
        $eventosPorInicio[$dia][$inicioHora] = $evento;
        for ($i = 1; $i < $span; $i++) {
            $celdasOmitidas[$dia][$inicioHora + $i] = true;
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
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(210px,100%),1fr));gap:14px;align-items:end}.availability{overflow-x:auto}.availability table{min-width:980px;border-collapse:collapse}.hour-col{width:140px;background:#f7f8fa;color:#0B1F33;font-weight:800;white-space:nowrap}.hour-cell{font-size:13px;color:#0B1F33;font-weight:700;background:#fbfbfc}.hour-row{height:64px}.midday-row td{height:30px;background:#fff8e6!important;color:#B78103!important;text-align:center;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;border-top:2px solid rgba(245,179,1,.45)!important;border-bottom:2px solid rgba(245,179,1,.45)!important}.time-cell{min-width:145px;vertical-align:middle}.cell-free{background:rgba(15,157,88,.06);color:#0b8046}.cell-busy{background:rgba(217,54,54,.08);color:#b42318;cursor:pointer;position:relative}.slot-cell{height:100%;min-height:52px;border-radius:8px;padding:10px;font-size:12px;border:1px solid transparent;display:flex;flex-direction:column;justify-content:center}.slot-cell:hover{border-color:rgba(59,13,143,.22)}.free-summary{height:100%;min-height:52px;border-radius:8px;padding:10px;background:rgba(15,157,88,.08);color:#0b8046;font-size:12px}.busy-tooltip{display:none;position:absolute;left:50%;bottom:calc(100% + 8px);transform:translateX(-50%);z-index:20;min-width:220px;background:#0B1F33;color:#fff;border-radius:8px;padding:10px;box-shadow:var(--sombra-hover);font-size:12px;text-align:left}.cell-busy:hover .busy-tooltip{display:block}.space-meta{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}.space-meta span{background:#f1f3f5;border-radius:999px;padding:5px 10px;font-size:12px;color:var(--color-texto-secundario)}.occupied-swal-container{text-align:left!important}.occupied-detail{text-align:left;color:#0B1F33}.occupied-header{display:grid;grid-template-columns:58px 1fr;gap:14px;align-items:center;padding-bottom:14px;border-bottom:1px solid #E2E5E9}.modal-logo{width:58px;height:58px;border-radius:12px;background:rgba(59,13,143,.08);display:grid;place-items:center}.modal-logo img{max-width:40px;max-height:40px}.occupied-header h2{font-size:20px;margin:0 0 4px;color:#0B1F33}.occupied-header p{margin:0;color:#637083;font-size:13px}.detail-chips{display:flex;gap:8px;flex-wrap:wrap;margin:14px 0}.detail-chip{display:inline-flex;align-items:center;gap:6px;background:#F6F7F9;border:1px solid #E2E5E9;border-radius:999px;padding:6px 10px;font-size:12px;color:#4B5563}.detail-sections{display:grid;grid-template-columns:1fr 1fr;gap:12px}.detail-section{border:1px solid #E2E5E9;border-radius:8px;padding:12px;background:#fff}.detail-section h3{font-size:13px;text-transform:uppercase;color:#637083;letter-spacing:.4px;margin:0 0 10px}.detail-list{display:grid;gap:9px}.detail-item{display:grid;gap:3px;font-size:13px}.detail-item span{color:#637083;font-size:12px}.detail-item strong{color:#0B1F33;font-weight:600;line-height:1.35}.occupied-modal-backdrop{position:fixed;inset:0;background:rgba(11,31,51,.46);z-index:2000;display:grid;place-items:center;padding:18px}.occupied-modal{width:min(720px,100%);background:#fff;border-radius:12px;padding:24px;box-shadow:0 18px 60px rgba(11,31,51,.22)}.occupied-modal-actions{display:flex;justify-content:flex-end;margin-top:18px}@media(max-width:700px){.detail-sections{grid-template-columns:1fr}.occupied-header{grid-template-columns:1fr}.detail-item{grid-template-columns:1fr}.busy-tooltip{display:none!important}}
</style>
<div class="panel">
    <h1 style="font-size:22px;margin-bottom:6px;">Disponibilidad de espacios</h1>
    <p style="color:var(--color-texto-secundario);">Consulta dinamica de ocupacion real. El calendario cruza contra horarios existentes del periodo activo.</p>
    <form method="GET" class="filter-grid" id="space-filter-form">
        <input type="hidden" name="ruta" value="lider/espacios">
        <div class="form-group"><label>Buscar</label><input type="search" id="space-search" class="form-control" placeholder="Aula, sede, bloque, tipo..."></div>
        <div class="form-group"><label>Sede</label><select id="sede-filter" class="form-control"><option value="">Todas</option><?php foreach ($sedes as $sede): ?><option value="<?= htmlspecialchars($sede) ?>"><?= htmlspecialchars($sede) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Bloque</label><select id="bloque-filter" class="form-control"><option value="">Todos</option><?php foreach ($bloques as $bloque): ?><option value="<?= htmlspecialchars($bloque) ?>"><?= htmlspecialchars($bloque) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Fecha de referencia</label><input type="date" name="fecha_ref" class="form-control" value="<?= htmlspecialchars($_GET['fecha_ref'] ?? date('Y-m-d')) ?>"></div>
        <div class="form-group"><label>Espacio</label><select name="espacio_id" id="space-select" class="form-control"><?php foreach ($espacios as $e): ?><option value="<?= (int)$e['id_espacio'] ?>" data-search="<?= htmlspecialchars(strtolower($e['sede_nombre'].' '.$e['bloque_nombre'].' '.$e['nombre'].' '.$e['tipo'])) ?>" data-sede="<?= htmlspecialchars($e['sede_nombre']) ?>" data-bloque="<?= htmlspecialchars($e['bloque_nombre']) ?>" <?= $idEspacio == $e['id_espacio'] ? 'selected' : '' ?>><?= htmlspecialchars($e['sede_nombre'].' / '.$e['bloque_nombre'].' / '.$e['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><button class="btn-primario" style="width:100%;">Consultar</button></div>
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
<div class="panel availability">
    <table class="table">
        <thead><tr><th class="hour-col">Hora</th><?php foreach ($dias as $dia): ?><th><?= $dia ?></th><?php endforeach; ?></tr></thead>
        <tbody>
            <?php foreach ($horas as $hora): $horaInt = (int)date('H', strtotime($hora)); ?>
                <?php if ($horaInt === 12): ?>
                    <tr class="midday-row"><td colspan="<?= count($dias) + 1 ?>">Mediodia</td></tr>
                <?php endif; ?>
                <tr class="hour-row">
                    <td class="hour-cell"><?= htmlspecialchars($formatoHora($hora)) ?></td>
                    <?php foreach ($dias as $dia): ?>
                        <?php if (!empty($celdasOmitidas[$dia][$horaInt])) continue; ?>
                        <?php $h = $eventosPorInicio[$dia][$horaInt] ?? null; ?>
                        <?php if ($h): $detalle = $detalleOcupacion($h); ?>
                            <td class="time-cell" rowspan="<?= (int)$h['_rowspan'] ?>">
                                <div class="slot-cell cell-busy" title="<?= htmlspecialchars($detalle['programa']) ?>" data-occupied-detail="<?= htmlspecialchars(json_encode($detalle), ENT_QUOTES, 'UTF-8') ?>">
                                    <strong>Ocupado</strong>
                                    <small><?= htmlspecialchars($detalle['hora']) ?></small>
                                    <?php if ((int)$h['_rowspan'] > 1): ?><small><?= (int)$h['_rowspan'] ?> horas</small><?php endif; ?>
                                    <div class="busy-tooltip">
                                        <strong><?= htmlspecialchars($detalle['programa']) ?></strong><br>
                                        <?= htmlspecialchars($detalle['espacio']) ?><br>
                                        <?= htmlspecialchars($detalle['hora']) ?>
                                    </div>
                                </div>
                            </td>
                        <?php else: ?>
                            <td class="time-cell"><div class="free-summary">Libre</div></td>
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
                        <h3>Ubicacion</h3>
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
                            <div class="detail-item"><span>Lider a cargo</span><strong>${esc(detail.lideres) || '-'}</strong></div>
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
                            <div class="detail-item"><span>Dia</span><strong>${esc(detail.dia) || '-'}</strong></div>
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
<?php $this->render('shared/footer'); ?>
