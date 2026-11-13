<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Reportes']);
$this->render('elementos_compartidos/sidebar_lider');
?>
<style>
.reportes-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #200654 100%);
    color: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);
}
.reportes-hero::after {
    content: '';
    position: absolute;
    right: -20px;
    bottom: -30px;
    width: 220px;
    height: 220px;
    background-image: url('assets/img/isotipo-blanco.png');
    background-repeat: no-repeat;
    background-size: contain;
    opacity: 0.08;
    pointer-events: none;
}
.reportes-hero h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; position: relative; z-index: 1; }
.reportes-hero p { color: rgba(255,255,255,.82); font-size: 13.5px; margin: 0; position: relative; z-index: 1; }
.reportes-hero .hero-stats { display: flex; gap: 12px; z-index: 2; }
.reportes-hero .hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px;
    padding: 10px 18px;
    text-align: center;
    backdrop-filter: blur(4px);
}
.reportes-hero .hero-stat strong { display: block; font-size: 20px; font-weight: 700; }
.reportes-hero .hero-stat span { font-size: 11px; color: rgba(255,255,255,.75); text-transform: uppercase; }

.module-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}
.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px;transition:all .2s;}
.module-tab:hover{background:rgba(59,13,143,.04);text-decoration:none;border-color:var(--color-primario);color:var(--color-primario);}
.module-tab.active{background:var(--color-primario);border-color:var(--color-primario);color:#fff}
.table-wrap{overflow-x:auto}
</style>

<div class="reportes-hero">
    <div>
        <h1><i class="fa-solid fa-chart-line" style="margin-right:10px;opacity:.85;"></i> Reportes Académicos</h1>
        <p><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?> &bull; Periodo: <?= htmlspecialchars($periodoActivo['codigo'] ?? 'Sin periodo') ?></p>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong><?= count($datos ?? []) ?></strong>
            <span>Registros</span>
        </div>
    </div>
</div>
<nav class="module-tabs"><a class="module-tab <?= $tipo === 'programa' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=programa"><i class="fa-solid fa-calendar-week"></i> Horario de mi programa</a><a class="module-tab <?= $tipo === 'docente' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=docente"><i class="fa-solid fa-chalkboard-user"></i> Por docente</a><a class="module-tab <?= $tipo === 'auditorias' ? 'active' : '' ?>" href="?ruta=lider/reportes&tipo=auditorias"><i class="fa-solid fa-clipboard-check"></i> Auditorias</a></nav>
<div class="panel table-wrap">
<?php if ($tipo === 'auditorias'): ?>
    <table class="table"><thead><tr><th>Fecha</th><th>Clase</th><th>Docente</th><th>Espacio</th><th>Resultado</th></tr></thead><tbody><?php if (empty($datos)): ?><tr><td colspan="5" style="text-align:center;padding:24px;">Sin datos.</td></tr><?php endif; ?><?php foreach ($datos as $a): ?><tr><td><?= htmlspecialchars($a['fecha_auditoria']) ?></td><td><?= htmlspecialchars($a['asignatura']) ?></td><td><?= htmlspecialchars($a['docente']) ?></td><td><?= htmlspecialchars($a['espacio']) ?></td><td><span class="badge <?= strtolower($a['resultado']) ?>"><?= htmlspecialchars($a['resultado']) ?></span></td></tr><?php endforeach; ?></tbody></table>
<?php else: ?>
    <table class="table"><thead><tr><th>Periodo</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Hora</th><th>Estado</th></tr></thead><tbody><?php if (empty($datos)): ?><tr><td colspan="7" style="text-align:center;padding:24px;">Sin datos.</td></tr><?php endif; ?><?php foreach ($datos as $h): ?><tr><td><?= htmlspecialchars($h['periodo']) ?></td><td><?= htmlspecialchars($h['asignatura']) ?></td><td><?= htmlspecialchars($h['docente']) ?></td><td><?= htmlspecialchars($h['espacio']) ?></td><td><?= htmlspecialchars($h['dia_semana']) ?></td><td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></td><td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td></tr><?php endforeach; ?></tbody></table>
<?php endif; ?>
</div>
<?php $this->render('elementos_compartidos/footer'); ?>
