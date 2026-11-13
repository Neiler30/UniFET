<?php 
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Reportes']); 
$this->render('elementos_compartidos/sidebar_admin'); 
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

.module-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
.module-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 1px solid var(--color-borde);
    border-radius: 8px;
    background: #fff;
    color: var(--color-texto-secundario);
    font-weight: 600;
    font-size: 13px;
    transition: all .2s;
    text-decoration: none;
}
.module-tab:hover { background: rgba(59,13,143,.04); border-color: var(--color-primario); color: var(--color-primario); text-decoration: none; }
.module-tab.active { background: var(--color-primario); border-color: var(--color-primario); color: #fff; }

.reportes-panel {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 14px;
    padding: 24px;
    box-shadow: var(--sombra-tarjeta);
}
.reportes-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
}
.reportes-table thead th {
    background: #F8FAFC;
    color: var(--color-oscuro);
    font-weight: 700;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 12px 14px;
    border-bottom: 2px solid var(--color-borde);
}
.reportes-table tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #F1F5F9;
    font-size: 13.5px;
    color: var(--color-oscuro);
    vertical-align: middle;
}
.reportes-table tbody tr:hover td {
    background: rgba(59,13,143,.02);
}
</style>

<div class="reportes-hero">
    <div>
        <h1><i class="fa-solid fa-chart-pie" style="margin-right:10px;opacity:.85;"></i> Reportes Académicos</h1>
        <p>Generación y visualización consolidada de horarios, docentes, espacios y resultados de auditoría.</p>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong><?= count($datos ?? []) ?></strong>
            <span>Resultados</span>
        </div>
    </div>
</div>

<nav class="module-tabs">
    <?php foreach (['programa' => ['Horario por programa', 'fa-calendar-week'], 'docente' => ['Horario por docente', 'fa-chalkboard-user'], 'espacio' => ['Horario por espacio', 'fa-door-open'], 'auditorias' => ['Resultados de auditoría', 'fa-clipboard-check']] as $k => $info): ?>
        <a class="module-tab <?= $tipo === $k ? 'active' : '' ?>" href="?ruta=admin/reportes&tipo=<?= $k ?>">
            <i class="fa-solid <?= $info[1] ?>"></i> <?= htmlspecialchars($info[0]) ?>
        </a>
    <?php endforeach; ?>
</nav>

<div class="reportes-panel">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:20px;">
        <input type="hidden" name="ruta" value="admin/reportes">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
        <?php if ($tipo === 'auditorias'): ?>
            <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px;font-weight:600;color:var(--color-texto-secundario);margin-bottom:4px;display:block;">Resultado</label>
                <select class="form-control" name="resultado" style="height:38px;">
                    <option value="">Todos los resultados</option>
                    <?php foreach (['PENDIENTE','VERIFICADA','NO_REALIZADA'] as $r): ?>
                        <option value="<?= $r ?>" <?= $resultado === $r ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else: ?>
            <div class="form-group" style="margin-bottom:0;min-width:240px;">
                <label style="font-size:12px;font-weight:600;color:var(--color-texto-secundario);margin-bottom:4px;display:block;">Búsqueda</label>
                <input class="form-control" name="filtro" value="<?= htmlspecialchars($filtro) ?>" placeholder="Buscar por <?= htmlspecialchars($tipo) ?>..." style="height:38px;">
            </div>
        <?php endif; ?>
        <button class="btn-primario" style="height:38px;padding:0 18px;font-size:13px;font-weight:600;">
            <i class="fa-solid fa-magnifying-glass"></i> Generar Reporte
        </button>
        <button class="btn-demo" type="button" style="height:38px;padding:0 16px;font-size:13px;" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir / PDF
        </button>
    </form>

    <div style="overflow-x:auto;">
        <table class="reportes-table">
            <?php if ($tipo === 'auditorias'): ?>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Programa</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Resultado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($datos)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--color-texto-secundario);">Sin resultados para este filtro.</td></tr>
                <?php endif; ?>
                <?php foreach ($datos as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['fecha_auditoria'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($d['programa'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['asignatura'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['docente'] ?? '') ?></td>
                        <td><?= htmlspecialchars(($d['sede'] ?? '') . ' / ' . ($d['espacio'] ?? '')) ?></td>
                        <td><span class="badge <?= strtolower($d['resultado'] ?? '') ?>"><?= htmlspecialchars($d['resultado'] ?? '') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Programa</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Día</th>
                        <th>Horario</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($datos)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--color-texto-secundario);">Sin horarios registrados para este criterio de reporte.</td></tr>
                <?php endif; ?>
                <?php foreach ($datos as $d): ?>
                    <tr>
                        <td><span class="code-pill"><?= htmlspecialchars($d['periodo'] ?? '') ?></span></td>
                        <td><?= htmlspecialchars($d['programa'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($d['asignatura'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($d['docente'] ?? '') ?></td>
                        <td><?= htmlspecialchars(($d['sede'] ?? '') . ' / ' . ($d['bloque'] ?? '') . ' / ' . ($d['espacio'] ?? '')) ?></td>
                        <td><strong><?= htmlspecialchars($d['dia_semana'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars(($d['hora_inicio'] ?? '') . ' - ' . ($d['hora_fin'] ?? '')) ?></td>
                        <td><span class="badge <?= strtolower($d['estado'] ?? '') ?>"><?= htmlspecialchars($d['estado'] ?? '') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php $this->render('elementos_compartidos/footer'); ?>
