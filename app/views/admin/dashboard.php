<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Dashboard']);
$this->render('shared/sidebar_admin');
?>

<style>
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.kpi-card { background: var(--color-blanco); border-radius: var(--radio-borde); padding: 20px; box-shadow: var(--sombra-tarjeta); text-align: center; border-bottom: 4px solid var(--color-primario); }
.kpi-card h3 { font-size: 14px; color: var(--color-texto-secundario); margin-bottom: 10px; font-weight: 500; }
.kpi-card .valor { font-size: 32px; font-weight: 700; color: var(--color-primario); }
.kpi-card.verde { border-bottom-color: var(--color-secundario); }
.kpi-card.verde .valor { color: var(--color-secundario); }
.kpi-card.amarillo { border-bottom-color: var(--color-acento); }
.kpi-card.amarillo .valor { color: var(--color-acento); }
.dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
</style>

<div class="header-acciones">
    <h1>Panel de Administración</h1>
    <p>Bienvenido, <?= htmlspecialchars(\App\Core\Auth::getUsuarioActual()['nombre']) ?></p>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <h3>Total de Programas</h3>
        <div class="valor"><?= $kpis['programas'] ?></div>
    </div>
    <div class="kpi-card verde">
        <h3>Total de Docentes</h3>
        <div class="valor"><?= $kpis['docentes'] ?></div>
    </div>
    <div class="kpi-card amarillo">
        <h3>Espacios Disponibles</h3>
        <div class="valor"><?= $kpis['espacios'] ?></div>
    </div>
    <div class="kpi-card">
        <h3>Facultades Activas</h3>
        <div class="valor"><?= $kpis['facultades'] ?></div>
    </div>
    <div class="kpi-card verde">
        <h3>Auditorías Pendientes</h3>
        <div class="valor"><?= $kpis['auditorias_pendientes'] ?></div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="panel">
        <h2 style="margin-bottom: 15px; font-size: 18px;">Horarios recientes</h2>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Aula</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimos_horarios)): ?>
                        <tr><td colspan="5">No hay horarios recientes.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ultimos_horarios as $h): ?>
                            <tr>
                                <td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></td>
                                <td><?= htmlspecialchars($h['asignatura']) ?></td>
                                <td><?= htmlspecialchars($h['docente']) ?></td>
                                <td><?= htmlspecialchars($h['bloque'] . ' ' . $h['espacio']) ?></td>
                                <td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="panel">
        <h2 style="margin-bottom: 15px; font-size: 18px;">Auditorías de Hoy</h2>
        <ul style="list-style:none; padding:0;">
            <?php if (empty($auditorias)): ?>
                <li><p style="color:var(--color-texto-secundario);">No hay auditorías pendientes para hoy.</p></li>
            <?php else: ?>
                <?php foreach ($auditorias as $a): ?>
                    <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <strong><?= htmlspecialchars($a['espacio']) ?></strong><br>
                        <span style="font-size:13px; color:var(--color-texto-secundario);">
                            <?= date('H:i', strtotime($a['hora_inicio'])) ?> - <?= htmlspecialchars($a['docente']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php $this->render('shared/footer'); ?>
