<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Horarios']);
$this->render('shared/sidebar_lider');
$diasSemana = ['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'];
$tabs = [
    'general' => ['Mi horario', 'fa-table-list'],
    'fet' => ['Generar con FET', 'fa-wand-magic-sparkles'],
    'propuestas' => ['Propuestas FET', 'fa-layer-group'],
    'auditoria' => ['Auditoria', 'fa-clipboard-check']
];
?>
<style>
.horarios-hero{background:linear-gradient(135deg,var(--color-primario),#24114f);color:#fff;border-radius:8px;padding:24px;margin-bottom:18px;display:flex;justify-content:space-between;gap:18px;align-items:center}.horarios-hero h1{font-size:24px;margin-bottom:6px}.horarios-hero p{color:rgba(255,255,255,.82);margin:0}.metric-strip{display:grid;grid-template-columns:repeat(auto-fit,minmax(115px,1fr));gap:10px;min-width:min(340px,100%)}.metric-tile{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.09);border-radius:8px;padding:12px}.metric-tile strong{display:block;font-size:22px}.metric-tile span{font-size:12px;color:rgba(255,255,255,.72)}
.module-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px}.module-tab.active{background:var(--color-primario);border-color:var(--color-primario);color:#fff}.assignment-grid,.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(220px,100%),1fr));gap:14px;align-items:end}.assignment-grid>*{min-width:0}.section-title{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px}.section-title h2{font-size:18px}.section-title p{color:var(--color-texto-secundario);font-size:13px;margin:3px 0 0}.table-wrap{overflow-x:auto}.actions-inline{display:inline-flex;align-items:center;justify-content:flex-end;gap:6px}.audit-form{display:grid;grid-template-columns:1fr auto auto;gap:8px;min-width:340px}
@media(max-width:1000px){.horarios-hero{flex-direction:column;align-items:stretch}.metric-strip,.assignment-grid,.filter-grid{grid-template-columns:1fr;min-width:0}.audit-form{grid-template-columns:1fr;min-width:230px}}
</style>
<div class="horarios-hero">
    <div><h1>Gestion y planificacion de horarios</h1><p><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?>. Construccion manual, FET y auditoria con alcance seguro.</p></div>
    <div class="metric-strip"><div class="metric-tile"><strong><?= count($horarios) ?></strong><span>Clases</span></div><div class="metric-tile"><strong><?= count($propuestas) ?></strong><span>Propuestas</span></div><div class="metric-tile"><strong><?= count($auditorias) ?></strong><span>Auditorias</span></div></div>
</div>
<nav class="module-tabs">
    <?php foreach ($tabs as $id => $item): ?><a class="module-tab <?= $tab === $id ? 'active' : '' ?>" href="?ruta=lider/horarios&tab=<?= $id ?>"><i class="fa-solid <?= $item[1] ?>"></i><?= $item[0] ?></a><?php endforeach; ?>
</nav>
<?php if (!$programaActivo): ?>
    <div class="panel">No tienes programas asignados.</div>
<?php elseif ($tab === 'general'): ?>
    <div class="panel">
        <div class="section-title"><div><h2>Asignar clase</h2><p>Selectores en cascada para oferta, docentes compatibles y espacios libres.</p></div></div>
        <?php if ($cerrado): ?>
            <div style="padding:14px;border:1px solid #f4c2c2;background:#fff5f5;border-radius:8px;color:#b42318;">Horario cerrado. Solo el Administrador puede modificar este periodo.</div>
        <?php else: ?>
        <form action="?ruta=lider/horarios/guardar" method="POST" class="assignment-grid">
            <input type="hidden" name="id_programa" id="id_programa" value="<?= (int)$programaActivo['id_programa'] ?>">
            <input type="hidden" name="id_detalle" value="<?= (int)($detalleEditar['id_detalle'] ?? 0) ?>">
            <input type="hidden" name="origen" value="<?= $detalleEditar ? 'FET' : 'MANUAL' ?>">
            <div class="form-group"><label>Periodo academico</label><select name="id_periodo" id="id_periodo" class="form-control" required><?php foreach ($periodos as $p): ?><option value="<?= (int)$p['id_periodo'] ?>" <?= (($detalleEditar['id_periodo'] ?? $idPeriodo) == $p['id_periodo']) ? 'selected' : '' ?>><?= htmlspecialchars($p['codigo'].' - '.($p['nombre'] ?? '')) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Asignatura ofertada</label><select name="id_asignatura" id="id_asignatura" class="form-control" required><option value="">Seleccionar asignatura</option></select></div>
            <div class="form-group"><label>Docente compatible</label><select name="id_docente" id="id_docente" class="form-control" required><option value="">Selecciona una asignatura</option></select></div>
            <div class="form-group"><label>Dia</label><select name="dia_semana" id="dia_semana" class="form-control" required><option value="">Seleccionar dia</option><?php foreach ($diasSemana as $dia): ?><option value="<?= $dia ?>" <?= (($detalleEditar['dia_semana'] ?? '') === $dia) ? 'selected' : '' ?>><?= $dia ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Hora inicio</label><input type="time" name="hora_inicio" id="hora_inicio" class="form-control" value="<?= htmlspecialchars(substr($detalleEditar['hora_inicio'] ?? '',0,5)) ?>" required></div>
            <div class="form-group"><label>Hora fin</label><input type="time" name="hora_fin" id="hora_fin" class="form-control" value="<?= htmlspecialchars(substr($detalleEditar['hora_fin'] ?? '',0,5)) ?>" required></div>
            <div class="form-group"><label>Espacio disponible</label><select name="id_espacio" id="id_espacio" class="form-control" required><option value="">Completa dia y horas</option></select></div>
            <div class="form-group"><button type="submit" class="btn-primario" style="width:100%;"><i class="fa-solid fa-calendar-plus"></i> <?= $detalleEditar ? 'Confirmar propuesta' : 'Guardar clase' ?></button></div>
        </form>
        <?php endif; ?>
    </div>
    <div class="panel">
        <div class="section-title"><div><h2>Horario actual</h2><p>Vista filtrada por tu programa activo.</p></div><?php if (!$cerrado && $idPeriodo): ?><a href="?ruta=lider/horarios/cerrar&periodo_id=<?= (int)$idPeriodo ?>&programa_id=<?= (int)$programaActivo['id_programa'] ?>" class="btn-demo" onclick="return confirmarCerrarHorario(event, this.href);"><i class="fa-solid fa-lock"></i> Cerrar horario</a><?php endif; ?></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Periodo</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Hora</th><th>Estado</th><th>Origen</th><th>Acciones</th></tr></thead><tbody>
        <?php if (empty($horarios)): ?><tr><td colspan="9" style="text-align:center;padding:24px;">No hay horarios registrados.</td></tr><?php endif; ?>
        <?php foreach ($horarios as $h): ?><tr><td><?= htmlspecialchars($h['periodo']) ?></td><td><?= htmlspecialchars($h['asignatura']) ?></td><td><?= htmlspecialchars($h['docente']) ?></td><td><?= htmlspecialchars($h['espacio']) ?></td><td><?= htmlspecialchars($h['dia_semana']) ?></td><td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></td><td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td><td><?= htmlspecialchars($h['origen']) ?></td><td><?= ($h['estado'] === 'CERRADO') ? '<span class="badge cerrado">Cerrado</span>' : '<span style="color:var(--color-texto-secundario);font-size:12px;">Admin edita</span>' ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
<?php elseif ($tab === 'fet'): ?>
    <div class="panel"><div class="section-title"><div><h2>Generar con FET</h2><p>Simulacion scoped al programa activo. La integracion real queda marcada como TODO.</p></div></div>
        <form action="?ruta=lider/horarios/generar_fet" method="POST" class="assignment-grid">
            <input type="hidden" name="id_programa" value="<?= (int)$programaActivo['id_programa'] ?>">
            <div class="form-group"><label>Periodo</label><select name="id_periodo" class="form-control" required><?php foreach ($periodos as $p): ?><option value="<?= (int)$p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>><?= htmlspecialchars($p['codigo'].' - '.($p['nombre'] ?? '')) ?></option><?php endforeach; ?></select></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Dias lectivos</label><div style="display:flex;gap:12px;flex-wrap:wrap;"><?php foreach (array_slice($diasSemana,0,6) as $dia): ?><label style="display:inline-flex;gap:6px;align-items:center;font-size:13px;"><input type="checkbox" name="dias[]" value="<?= $dia ?>" checked> <?= $dia ?></label><?php endforeach; ?></div></div>
            <div class="form-group"><button class="btn-primario" type="submit"><i class="fa-solid fa-play"></i> Simular FET</button></div>
        </form>
    </div>
<?php elseif ($tab === 'propuestas'): ?>
    <div class="panel"><div class="section-title"><div><h2>Propuestas FET</h2><p>Acepta, ajusta o descarta cada linea antes de crear horario real.</p></div></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Hora</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>
        <?php if (empty($detallesPropuestas)): ?><tr><td colspan="7" style="text-align:center;padding:24px;">No hay lineas FET para revisar.</td></tr><?php endif; ?>
        <?php foreach ($detallesPropuestas as $d): ?><tr><td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '').' '.($d['asignatura_nombre'] ?? '')) ?></td><td><?= htmlspecialchars($d['docente_nombre']) ?></td><td><?= htmlspecialchars($d['sede_nombre'].' / '.$d['bloque_nombre'].' / '.$d['espacio_nombre']) ?></td><td><?= htmlspecialchars($d['dia_semana']) ?></td><td><?= date('H:i', strtotime($d['hora_inicio'])) ?> - <?= date('H:i', strtotime($d['hora_fin'])) ?></td><td><?= !empty($d['id_horario_resultante']) ? '<span class="badge aceptada">ACEPTADA</span>' : '<span class="badge generada">GENERADA</span>' ?></td><td style="text-align:right;"><span class="actions-inline"><?php if (empty($d['id_horario_resultante'])): ?><a class="btn-accion btn-accion-aceptar" title="Aceptar" href="?ruta=lider/horarios/aceptar_fet&id=<?= (int)$d['id_detalle'] ?>&programa_id=<?= (int)$programaActivo['id_programa'] ?>"><i class="fa-solid fa-check"></i></a><a class="btn-accion btn-accion-editar" title="Editar" href="?ruta=lider/horarios&tab=general&editar_detalle=<?= (int)$d['id_detalle'] ?>"><i class="fa-solid fa-pen"></i></a><form action="?ruta=lider/horarios/descartar_fet" method="POST" onsubmit="return confirmarEliminacion(event, this, 'esta linea FET');"><input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>"><input type="hidden" name="id_detalle" value="<?= (int)$d['id_detalle'] ?>"><button class="btn-accion btn-accion-eliminar" title="Descartar linea"><i class="fa-solid fa-trash-can"></i></button></form><?php endif; ?></span></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
<?php else: ?>
    <div class="panel"><div class="section-title"><div><h2>Auditoria</h2><p>Registra verificacion de clases de tu programa.</p></div></div>
        <form method="GET" class="filter-grid" style="margin-bottom:16px;"><input type="hidden" name="ruta" value="lider/horarios"><input type="hidden" name="tab" value="auditoria"><div class="form-group"><label>Fecha</label><input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($_GET['fecha'] ?? '') ?>"></div><div class="form-group"><label>Sede</label><input name="sede" class="form-control" value="<?= htmlspecialchars($_GET['sede'] ?? '') ?>"></div><div class="form-group"><label>Bloque</label><input name="bloque" class="form-control" value="<?= htmlspecialchars($_GET['bloque'] ?? '') ?>"></div><div class="form-group"><label>Espacio</label><input name="espacio" class="form-control" value="<?= htmlspecialchars($_GET['espacio'] ?? '') ?>"></div><div class="form-group"><label>Docente</label><input name="docente" class="form-control" value="<?= htmlspecialchars($_GET['docente'] ?? '') ?>"></div><div class="form-group"><button class="btn-primario">Filtrar</button></div></form>
        <div class="table-wrap"><table class="table"><thead><tr><th>Fecha</th><th>Clase</th><th>Docente</th><th>Espacio</th><th>Horario</th><th>Resultado</th><th>Verificacion</th></tr></thead><tbody>
        <?php if (empty($auditorias)): ?><tr><td colspan="7" style="text-align:center;padding:24px;">No hay auditorias con estos filtros.</td></tr><?php endif; ?>
        <?php foreach ($auditorias as $a): ?><tr><td><?= htmlspecialchars($a['fecha_auditoria']) ?></td><td><?= htmlspecialchars($a['asignatura']) ?></td><td><?= htmlspecialchars($a['docente']) ?></td><td><?= htmlspecialchars($a['sede'].' / '.$a['bloque'].' / '.$a['espacio']) ?></td><td><?= date('H:i', strtotime($a['hora_inicio'])) ?> - <?= date('H:i', strtotime($a['hora_fin'])) ?></td><td><span class="badge <?= strtolower($a['resultado']) ?>"><?= htmlspecialchars($a['resultado']) ?></span></td><td><form class="audit-form" action="?ruta=lider/horarios/verificar_auditoria" method="POST"><input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>"><input type="hidden" name="id_auditoria" value="<?= (int)$a['id_auditoria'] ?>"><input class="form-control" name="observacion" placeholder="Observacion"><button class="btn-accion btn-accion-aceptar" name="resultado" value="VERIFICADA" title="Verificada"><i class="fa-solid fa-check"></i></button><button class="btn-accion btn-accion-eliminar" name="resultado" value="NO_REALIZADA" title="No realizada"><i class="fa-solid fa-xmark"></i></button></form></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const prefill = <?= json_encode($detalleEditar ?: []) ?>;
    const periodo = document.getElementById('id_periodo'), programa = document.getElementById('id_programa'), asignatura = document.getElementById('id_asignatura'), docente = document.getElementById('id_docente'), dia = document.getElementById('dia_semana'), inicio = document.getElementById('hora_inicio'), fin = document.getElementById('hora_fin'), espacio = document.getElementById('id_espacio');
    const fill = (select, rows, placeholder, mapper, selected) => { if (!select) return; select.innerHTML = `<option value="">${placeholder}</option>`; rows.forEach(row => { const data = mapper(row); const option = document.createElement('option'); option.value = data.value; option.textContent = data.label; if (String(selected || '') === String(data.value)) option.selected = true; select.appendChild(option); }); };
    const loadAsignaturas = async () => { if (!periodo || !programa || !periodo.value) return; const res = await fetch(`?ruta=lider/horarios/api_asignaturas&periodo_id=${periodo.value}&programa_id=${programa.value}`); fill(asignatura, await res.json(), 'Seleccionar asignatura', row => ({value: row.id_asignatura, label: `${row.codigo} - ${row.nombre}`}), prefill.id_asignatura); await loadDocentes(); };
    const loadDocentes = async () => { if (!asignatura || !asignatura.value) return; const res = await fetch(`?ruta=lider/horarios/api_docentes&asignatura_id=${asignatura.value}&programa_id=${programa.value}`); fill(docente, await res.json(), 'Seleccionar docente', row => ({value: row.id_docente, label: `${row.apellido} ${row.nombre}`}), prefill.id_docente); };
    const loadEspacios = async () => { if (!periodo || !dia || !inicio || !fin || !periodo.value || !dia.value || !inicio.value || !fin.value) return; const res = await fetch(`?ruta=lider/horarios/api_espacios&periodo_id=${periodo.value}&dia_semana=${dia.value}&hora_inicio=${inicio.value}&hora_fin=${fin.value}`); fill(espacio, await res.json(), 'Seleccionar espacio libre', row => ({value: row.id_espacio, label: `${row.sede_nombre} / ${row.bloque_nombre} / ${row.nombre}`}), prefill.id_espacio); };
    periodo && periodo.addEventListener('change', loadAsignaturas); asignatura && asignatura.addEventListener('change', loadDocentes); [periodo,dia,inicio,fin].forEach(el => el && el.addEventListener('change', loadEspacios)); loadAsignaturas().then(loadEspacios);
});
</script>
<?php $this->render('shared/footer'); ?>
