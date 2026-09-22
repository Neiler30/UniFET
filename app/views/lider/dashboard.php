<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Dashboard Lider']);
$this->render('shared/sidebar_lider');
$usuario = \App\Core\Auth::getUsuarioActual();
?>
<style>
.lider-hero{background:linear-gradient(135deg,var(--color-primario),#24114f);color:#fff;border-radius:8px;padding:26px;margin-bottom:18px;display:flex;justify-content:space-between;gap:18px;align-items:center}
.lider-hero h1{font-size:24px;margin-bottom:6px}.lider-hero p{color:rgba(255,255,255,.82);margin:0}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap}.hero-actions a{display:inline-flex;gap:8px;align-items:center;padding:10px 14px;border:1px solid rgba(255,255,255,.25);border-radius:8px;color:#fff;background:rgba(255,255,255,.12);font-weight:600;font-size:13px}
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:18px}.kpi-card{background:#fff;border:1px solid var(--color-borde);border-radius:8px;padding:18px;box-shadow:var(--sombra-tarjeta);display:flex;justify-content:space-between;align-items:center;text-decoration:none}.kpi-card:hover{text-decoration:none;transform:translateY(-1px);box-shadow:var(--sombra-hover)}.kpi-card h3{font-size:12px;color:var(--color-texto-secundario);text-transform:uppercase;margin-bottom:6px}.kpi-card .valor{font-size:28px;font-weight:800;color:var(--color-oscuro)}.kpi-icon{width:44px;height:44px;border-radius:8px;display:grid;place-items:center;background:rgba(59,13,143,.1);color:var(--color-primario)}
.lider-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px}.table-wrap{overflow-x:auto}.section-title{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}.section-title h2{font-size:17px}
@media(max-width:900px){.lider-hero{flex-direction:column;align-items:flex-start}.lider-grid{grid-template-columns:1fr}}
</style>
<div class="lider-hero">
    <div>
        <h1><?= htmlspecialchars($programaActivo['nombre'] ?? 'Panel del Lider') ?></h1>
        <p>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>. Gestiona tu programa con informacion academica filtrada por tu alcance.</p>
    </div>
    <div class="hero-actions">
        <a href="?ruta=lider/horarios"><i class="fa-solid fa-calendar-plus"></i> Construir horario</a>
        <a href="?ruta=lider/horarios&tab=fet"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar con FET</a>
        <a href="?ruta=lider/horarios&tab=auditoria"><i class="fa-solid fa-clipboard-check"></i> Auditar hoy</a>
    </div>
</div>
<?php if (!$programaActivo): ?>
    <div class="panel"><strong>No tienes programas asignados.</strong> Un administrador debe vincular tu usuario en programa_lider.</div>
<?php else: ?>
<div class="kpi-grid">
    <a class="kpi-card" href="?ruta=lider/programa&tab=asignaturas"><div><h3>Asignaturas ofertadas</h3><div class="valor"><?= (int)$kpis['asignaturas'] ?></div></div><div class="kpi-icon"><i class="fa-solid fa-book"></i></div></a>
    <a class="kpi-card" href="?ruta=lider/programa&tab=docentes"><div><h3>Docentes asignados</h3><div class="valor"><?= (int)$kpis['docentes'] ?></div></div><div class="kpi-icon"><i class="fa-solid fa-chalkboard-user"></i></div></a>
    <a class="kpi-card" href="?ruta=lider/horarios"><div><h3>Clases programadas</h3><div class="valor"><?= (int)$kpis['clases'] ?></div></div><div class="kpi-icon"><i class="fa-solid fa-calendar-days"></i></div></a>
    <a class="kpi-card" href="?ruta=lider/horarios&tab=propuestas"><div><h3>Propuestas FET</h3><div class="valor"><?= (int)$kpis['propuestas'] ?></div></div><div class="kpi-icon"><i class="fa-solid fa-layer-group"></i></div></a>
</div>
<div class="lider-grid">
    <div class="panel">
        <div class="section-title"><h2>Proximos horarios de mi programa</h2><a href="?ruta=lider/horarios">Ver todos</a></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Dia</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Estado</th></tr></thead><tbody>
        <?php if (empty($horarios)): ?><tr><td colspan="5" style="text-align:center;padding:22px;">Aun no hay clases programadas.</td></tr><?php endif; ?>
        <?php foreach ($horarios as $h): ?><tr><td><?= htmlspecialchars($h['dia_semana']) ?><br><small><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></small></td><td><?= htmlspecialchars($h['asignatura']) ?></td><td><?= htmlspecialchars($h['docente']) ?></td><td><?= htmlspecialchars($h['espacio']) ?></td><td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td></tr><?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="panel">
        <div class="section-title"><h2>Estado FET</h2></div>
        <?php if (empty($propuestas)): ?><p style="color:var(--color-texto-secundario);">Sin propuestas para el periodo activo.</p><?php endif; ?>
        <?php foreach ($propuestas as $p): ?><div style="padding:12px 0;border-bottom:1px solid var(--color-borde);"><strong>#<?= (int)$p['id_propuesta'] ?></strong> <?= htmlspecialchars($p['periodo_codigo']) ?><br><span class="badge <?= strtolower($p['estado']) ?>"><?= htmlspecialchars($p['estado']) ?></span> <small><?= (int)$p['total_detalles'] ?> lineas</small></div><?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<?php $this->render('shared/footer'); ?>
