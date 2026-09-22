<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Dashboard - UniFET']);
$this->render('shared/sidebar_admin');
$usuario = \App\Core\Auth::getUsuarioActual();
$periodoActivo = \App\Models\PeriodoAcademico::getActivo();
?>

<style>
.dashboard-welcome {
    background: linear-gradient(135deg, var(--color-primario) 0%, #200654 100%);
    border-radius: var(--radio-borde-lg, 12px);
    padding: 30px;
    color: var(--color-blanco);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    box-shadow: var(--sombra-tarjeta);
    position: relative;
    overflow: hidden;
}

.dashboard-welcome::after {
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

.welcome-text h1 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}

.welcome-text p {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.82);
    margin: 0;
}

.welcome-actions {
    display: flex;
    gap: 12px;
    z-index: 2;
    flex-wrap: wrap;
}

.welcome-actions .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 10px 16px;
    border-radius: var(--radio-borde);
    font-size: 13.5px;
    font-weight: 500;
    backdrop-filter: blur(4px);
    transition: all 0.2s ease;
}

.welcome-actions .btn-action:hover {
    background: rgba(255, 255, 255, 0.28);
    text-decoration: none;
    transform: translateY(-1px);
}

.welcome-actions .btn-action.highlight {
    background: var(--color-secundario);
    border-color: var(--color-secundario);
}

.welcome-actions .btn-action.highlight:hover {
    background: #0b8046;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.kpi-card {
    background: var(--color-blanco);
    border-radius: var(--radio-borde-lg, 12px);
    padding: 22px 20px;
    box-shadow: var(--sombra-tarjeta);
    border: 1px solid var(--color-borde, #E2E5E9);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--sombra-hover, 0 8px 24px rgba(59, 13, 143, 0.1));
}

.kpi-info h3 {
    font-size: 13px;
    color: var(--color-texto-secundario);
    margin-bottom: 6px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kpi-info .valor {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-oscuro);
    line-height: 1;
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.kpi-icon.purple { background: rgba(59, 13, 143, 0.1); color: var(--color-primario); }
.kpi-icon.green { background: rgba(15, 157, 88, 0.1); color: var(--color-secundario); }
.kpi-icon.yellow { background: rgba(245, 179, 1, 0.15); color: #d09400; }
.kpi-icon.blue { background: rgba(11, 31, 51, 0.08); color: var(--color-oscuro); }
.kpi-icon.red { background: rgba(220, 53, 69, 0.1); color: #dc3545; }

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--color-borde, #E2E5E9);
}

.panel-header h2 {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-oscuro);
    display: flex;
    align-items: center;
    gap: 8px;
}

.panel-header a {
    font-size: 13px;
    font-weight: 500;
}

@media (max-width: 900px) {
    .dashboard-welcome { flex-direction: column; align-items: flex-start; gap: 18px; }
    .dashboard-grid { grid-template-columns: 1fr; }
}
</style>

<div class="dashboard-welcome">
    <div class="welcome-text">
        <h1>Bienvenido al Portal de Gestión, <?= htmlspecialchars($usuario['nombre']) ?></h1>
        <p>
            Institución Universitaria Unicaribe &bull; Periodo: 
            <strong><?= $periodoActivo ? htmlspecialchars($periodoActivo['codigo'] . ' (' . ($periodoActivo['nombre'] ?? 'Activo') . ')') : 'Sin periodo activo' ?></strong>
        </p>
    </div>
    <div class="welcome-actions">
        <a href="?ruta=admin/horarios" class="btn-action highlight">
            <i class="fa-solid fa-calendar-plus"></i> Asignar Clase
        </a>
        <a href="?ruta=admin/horarios&tab=fet" class="btn-action">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Simular FET
        </a>
        <a href="?ruta=admin/institucion/facultades/crear" class="btn-action">
            <i class="fa-solid fa-plus"></i> Nueva Facultad
        </a>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Facultades</h3>
            <div class="valor"><?= $kpis['facultades'] ?></div>
        </div>
        <div class="kpi-icon purple">
            <i class="fa-solid fa-building-columns"></i>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Programas</h3>
            <div class="valor"><?= $kpis['programas'] ?></div>
        </div>
        <div class="kpi-icon blue">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Docentes</h3>
            <div class="valor"><?= $kpis['docentes'] ?></div>
        </div>
        <div class="kpi-icon green">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Espacios Libres</h3>
            <div class="valor"><?= $kpis['espacios'] ?></div>
        </div>
        <div class="kpi-icon yellow">
            <i class="fa-solid fa-door-open"></i>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Auditorías Hoy</h3>
            <div class="valor"><?= $kpis['auditorias_pendientes'] ?></div>
        </div>
        <div class="kpi-icon red">
            <i class="fa-solid fa-clipboard-check"></i>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Horarios Planificados Recientes</h2>
            <a href="?ruta=admin/horarios">Ver todos &rarr;</a>
        </div>
        
        <?php if (empty($ultimos_horarios)): ?>
            <div class="empty-state">
                <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
                <h3>No hay horarios registrados todavía</h3>
                <p>Comienza a planificar asignando clases de forma manual o ejecutando la optimización con el motor FET.</p>
                <a href="?ruta=admin/horarios" class="btn-primario">+ Asignar Primera Clase</a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Día y Hora</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Espacio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_horarios as $h): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($h['dia_semana'] ?? '') ?></strong><br>
                                    <span style="font-size: 12.5px; color: var(--color-texto-secundario);">
                                        <?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($h['asignatura']) ?></strong><br>
                                    <span style="font-size: 12px; color: var(--color-texto-secundario);"><?= htmlspecialchars($h['programa']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($h['docente']) ?></td>
                                <td>
                                    <span style="font-size: 13px; font-weight: 500;"><?= htmlspecialchars($h['espacio']) ?></span><br>
                                    <span style="font-size: 12px; color: var(--color-texto-secundario);"><?= htmlspecialchars($h['bloque']) ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= strtolower($h['estado']) ?>">
                                        <?= htmlspecialchars($h['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-list-check"></i> Auditorías de Hoy</h2>
        </div>
        
        <?php if (empty($auditorias)): ?>
            <div style="text-align: center; padding: 40px 15px;">
                <i class="fa-solid fa-circle-check" style="font-size: 40px; color: var(--color-secundario); opacity: 0.8; margin-bottom: 12px;"></i>
                <h4 style="font-size: 15px; color: var(--color-oscuro); margin-bottom: 6px;">Sin auditorías pendientes</h4>
                <p style="font-size: 13px; color: var(--color-texto-secundario); margin: 0;">
                    No hay verificaciones de clase programadas para el día de hoy.
                </p>
            </div>
        <?php else: ?>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php foreach ($auditorias as $a): ?>
                    <li style="padding: 12px 0; border-bottom: 1px solid var(--color-borde); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 14px; color: var(--color-oscuro);"><?= htmlspecialchars($a['espacio']) ?></strong><br>
                            <span style="font-size: 13px; color: var(--color-texto-secundario);">
                                <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($a['hora_inicio'])) ?> &bull; <?= htmlspecialchars($a['docente']) ?>
                            </span>
                        </div>
                        <span class="badge borrador">Pendiente</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php $this->render('shared/footer'); ?>
