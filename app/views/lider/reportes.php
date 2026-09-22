<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Reportes']);
$this->render('shared/sidebar_lider');
?>
<style>.module-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px}.module-tab.active{background:var(--color-primario);border-color:var(--color-primario);color:#fff}.table-wrap{overflow-x:auto}</style>
<div class="panel"><h1 style="font-size:22px;margin-bottom:6px;">Reportes</h1><p style="color:var(--color-texto-secundario);margin:0;"><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?> - <?= htmlspecialchars($periodoActivo['codigo'] ?? 'Sin periodo') ?></p></div>
<nav class="module-tabs"><a class="module-tab <?= $tipo === 'programa' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=programa"><i class="fa-solid fa-calendar-week"></i> Horario de mi programa</a><a class="module-tab <?= $tipo === 'docente' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=docente"><i class="fa-solid fa-chalkboard-user"></i> Por docente</a><a class="module-tab <?= $tipo === 'auditorias' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=auditorias"><i class="fa-solid fa-clipboard-check"></i> Auditorias</a></nav>
<div class="panel table-wrap">
<?php if ($tipo === 'auditorias'): ?>
    <table class="table"><thead><tr><th>Fecha</th><th>Clase</th><th>Docente</th><th>Espacio</th><th>Resultado</th></tr></thead><tbody><?php if (empty($datos)): ?><tr><td colspan="5" style="text-align:center;padding:24px;">Sin datos.</td></tr><?php endif; ?><?php foreach ($datos as $a): ?><tr><td><?= htmlspecialchars($a['fecha_auditoria']) ?></td><td><?= htmlspecialchars($a['asignatura']) ?></td><td><?= htmlspecialchars($a['docente']) ?></td><td><?= htmlspecialchars($a['espacio']) ?></td><td><span class="badge <?= strtolower($a['resultado']) ?>"><?= htmlspecialchars($a['resultado']) ?></span></td></tr><?php endforeach; ?></tbody></table>
<?php else: ?>
    <table class="table"><thead><tr><th>Periodo</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Hora</th><th>Estado</th></tr></thead><tbody><?php if (empty($datos)): ?><tr><td colspan="7" style="text-align:center;padding:24px;">Sin datos.</td></tr><?php endif; ?><?php foreach ($datos as $h): ?><tr><td><?= htmlspecialchars($h['periodo']) ?></td><td><?= htmlspecialchars($h['asignatura']) ?></td><td><?= htmlspecialchars($h['docente']) ?></td><td><?= htmlspecialchars($h['espacio']) ?></td><td><?= htmlspecialchars($h['dia_semana']) ?></td><td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></td><td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td></tr><?php endforeach; ?></tbody></table>
<?php endif; ?>
</div>
<?php $this->render('shared/footer'); ?>
