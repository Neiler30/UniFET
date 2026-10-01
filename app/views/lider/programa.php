<?php
$this->render('elementos_compartidos/header', ['titulo' => 'Mi Programa — UniFET']);
$this->render('elementos_compartidos/sidebar_lider');

$totalAsignaturas = (int)($kpis['total_asignaturas'] ?? count($asignaturas));
$totalDocentes = (int)($kpis['docentes'] ?? count($docentes));
$totalOfertadas = (int)($kpis['asignaturas'] ?? 0);
$totalAsignaciones = (int)($kpis['asignaciones'] ?? count($asignaciones));
$totalClases = (int)($kpis['clases'] ?? 0);
$totalEspacios = (int)($kpis['espacios'] ?? 0);
$docentesConClase = (int)($kpis['docentes_con_clase'] ?? 0);

// Cálculos de porcentaje de avance real
$pctAsignacion = $totalOfertadas > 0 ? min(100, round(($totalAsignaciones / $totalOfertadas) * 100)) : ($totalAsignaturas > 0 ? min(100, round(($totalAsignaciones / $totalAsignaturas) * 100)) : 0);
$pctDocentes = $totalDocentes > 0 ? min(100, round(($docentesConClase / $totalDocentes) * 100)) : 0;
?>

<style>
/* =========================================================
   MI PROGRAMA — PANEL ACADÉMICO INTEGRADO (UniFET)
   ========================================================= */

.prog-bg-watermark {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.prog-bg-watermark img {
    width: min(55vw, 480px);
    opacity: 0.025;
    user-select: none;
}

.prog-wrap {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 22px;
}

/* 1. Header principal */
.prog-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 60%, #1a0a3e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(59,13,143,.15);
}
.prog-hero::before {
    content: '';
    position: absolute;
    right: -25px; bottom: -25px;
    width: 170px; height: 170px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.07;
    pointer-events: none;
}
.prog-hero-main {
    position: relative;
    z-index: 2;
}
.prog-hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--color-dorado);
    margin-bottom: 4px;
}
.prog-hero h1 {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 6px;
    color: #fff;
    line-height: 1.2;
}
.prog-hero-sub {
    color: rgba(255,255,255,.82);
    font-size: 13.5px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.prog-hero-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}
.btn-hero-action {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 15px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
}
.btn-hero-action:hover {
    background: rgba(255,255,255,.25);
    color: #fff;
    transform: translateY(-2px);
    text-decoration: none;
}
.btn-hero-action.primary {
    background: #fff;
    color: var(--color-primario);
    border-color: #fff;
    font-weight: 700;
}
.btn-hero-action.primary:hover {
    background: #f1f5f9;
    color: var(--color-primario);
}

/* Selector de programa si tiene múltiples */
.prog-selector-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.1);
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,.2);
    font-size: 13px;
    margin-top: 10px;
    width: fit-content;
}
.prog-selector-box select {
    background: transparent;
    color: #fff;
    border: none;
    outline: none;
    font-weight: 600;
    cursor: pointer;
    font-size: 13px;
}
.prog-selector-box select option {
    background: #0B1F33;
    color: #fff;
}

/* 2. KPIs Académicos */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(180px, 100%), 1fr));
    gap: 14px;
}
.kpi-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: var(--sombra-tarjeta);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform .2s, box-shadow .2s;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--sombra-hover);
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.kpi-icon.docentes   { background: rgba(59,13,143,.08); color: var(--color-primario); }
.kpi-icon.asig       { background: rgba(11,31,51,.08); color: #0B1F33; }
.kpi-icon.oferta     { background: rgba(245,179,1,.12); color: #b45309; }
.kpi-icon.asignac    { background: rgba(15,157,88,.09); color: #0F9D58; }
.kpi-icon.clases     { background: rgba(30,58,138,.08); color: #1e3a8a; }

.kpi-info span {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--color-texto-secundario);
    display: block;
    margin-bottom: 2px;
}
.kpi-info strong {
    font-size: 22px;
    font-weight: 800;
    color: var(--color-oscuro);
    line-height: 1;
}
.kpi-info small {
    font-size: 11px;
    color: var(--color-texto-secundario);
    display: block;
    margin-top: 3px;
}

/* 3. Ficha institucional y Avance de planificación (2 columnas) */
.dual-section-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 18px;
}
.academic-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    box-shadow: var(--sombra-tarjeta);
    overflow: hidden;
}
.academic-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--color-borde);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #fafbfc;
}
.academic-card-header h2 {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-oscuro);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.academic-card-body {
    padding: 20px;
}

/* Info grid inside Ficha */
.prog-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
.prog-info-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 11px 13px;
}
.prog-info-item span {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--color-texto-secundario);
    letter-spacing: .4px;
    display: block;
    margin-bottom: 3px;
}
.prog-info-item strong {
    font-size: 13.5px;
    color: var(--color-oscuro);
    font-weight: 600;
}

/* Barras de avance / planificación */
.progress-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.progress-item-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 6px;
}
.progress-item-head span {
    font-weight: 600;
    color: var(--color-oscuro);
    display: flex;
    align-items: center;
    gap: 6px;
}
.progress-item-head strong {
    font-weight: 700;
    color: var(--color-primario);
}
.progress-bar-bg {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 999px;
    transition: width .5s ease;
}
.progress-bar-fill.purple { background: var(--color-primario); }
.progress-bar-fill.green  { background: #0F9D58; }
.progress-bar-fill.blue   { background: #1e3a8a; }
.progress-bar-fill.yellow { background: #F5B301; }

.plan-stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px dashed var(--color-borde);
    text-align: center;
}
.plan-stat-col span {
    font-size: 11px;
    color: var(--color-texto-secundario);
    display: block;
}
.plan-stat-col strong {
    font-size: 17px;
    font-weight: 800;
    color: var(--color-oscuro);
}

/* 4. Tablas y Secciones Principales */
.section-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    box-shadow: var(--sombra-tarjeta);
    overflow: hidden;
}
.section-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid var(--color-borde);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    background: #fafbfc;
}
.section-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
}
.section-title-icon.doc  { background: linear-gradient(135deg, var(--color-primario), #4c1d95); }
.section-title-icon.asig { background: linear-gradient(135deg, #0B1F33, #1e3a8a); }
.section-title-icon.asigdoc { background: linear-gradient(135deg, #0F9D58, #059669); }

.section-title-wrap h2 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-oscuro);
    margin: 0 0 2px;
}
.section-title-wrap p {
    font-size: 12.5px;
    color: var(--color-texto-secundario);
    margin: 0;
}
.section-search-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}
.table-search-input {
    padding: 7px 12px 7px 32px;
    border: 1px solid var(--color-borde);
    border-radius: 7px;
    font-size: 12.5px;
    outline: none;
    width: 220px;
    transition: all .2s;
}
.table-search-input:focus {
    border-color: var(--color-primario);
    box-shadow: 0 0 0 3px rgba(59,13,143,.1);
    width: 260px;
}

/* Tablas estilizadas */
.modern-table-wrap {
    overflow-x: auto;
}
.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
}
.modern-table thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    padding: 12px 16px;
    border-bottom: 1px solid var(--color-borde);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.modern-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
}
.modern-table tbody tr:hover {
    background: #f8fafc;
}
.modern-table td {
    padding: 12px 16px;
    vertical-align: middle;
}
.code-badge {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 12px;
    background: #f1f5f9;
    color: var(--color-oscuro);
    padding: 3px 8px;
    border-radius: 5px;
}
.chip-compatibles {
    background: rgba(59,13,143,.08);
    color: var(--color-primario);
    padding: 3px 9px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Formulario colapsable para asignar docente */
.asignar-drawer {
    display: none;
    background: #f8fafc;
    border-bottom: 1px solid var(--color-borde);
    padding: 18px 22px;
}
.asignar-drawer.open {
    display: block;
    animation: fadeIn .2s ease;
}
.asignar-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 900px) {
    .dual-section-grid { grid-template-columns: 1fr; }
    .prog-hero { flex-direction: column; align-items: flex-start; }
    .asignar-form-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Watermark -->
<div class="prog-bg-watermark">
    <img src="assets/img/isotipo-color.png" alt="">
</div>

<div class="prog-wrap">

    <?php if (!$programaActivo): ?>
        <div class="panel empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No tienes programas académicos asignados</h3>
            <p>Contacta al Administrador del sistema para que asigne tu usuario a la coordinación o liderazgo de un programa.</p>
        </div>
    <?php else: ?>

        <!-- =========================================================
             1. HEADER: IDENTIFICACIÓN DEL PROGRAMA & ACCIONES
             ========================================================= -->
        <div class="prog-hero">
            <div class="prog-hero-main">
                <span class="prog-hero-tag"><i class="fa-solid fa-graduation-cap"></i> Panel Académico del Líder</span>
                <h1><?= htmlspecialchars($programaActivo['nombre']) ?></h1>
                <div class="prog-hero-sub">
                    <span><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($programaActivo['facultad_nombre']) ?></span>
                    <span>&bull;</span>
                    <span><i class="fa-regular fa-calendar-check"></i> Periodo Académico: <?= htmlspecialchars($periodoActivo['codigo'] ?? 'Activo') ?></span>
                    <span>&bull;</span>
                    <span>Código: <strong><?= htmlspecialchars($programaActivo['codigo']) ?></strong></span>
                </div>

                <?php if (count($programasLider) > 1): ?>
                    <div class="prog-selector-box">
                        <i class="fa-solid fa-arrows-rotate" style="opacity: .8;"></i>
                        <label for="selector-programa" style="margin: 0; font-size: 12px; opacity: .85;">Cambiar programa:</label>
                        <select id="selector-programa" onchange="window.location.href='?ruta=lider/programa&programa_id=' + this.value">
                            <?php foreach ($programasLider as $pl): ?>
                                <option value="<?= (int)$pl['id_programa'] ?>" <?= ((int)$pl['id_programa'] === (int)$programaActivo['id_programa']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pl['nombre']) ?> (<?= htmlspecialchars($pl['codigo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Acciones principales del Líder -->
            <div class="prog-hero-actions">
                <a href="?ruta=lider/horarios&tab=general" class="btn-hero-action primary" title="Construir y planificar horarios">
                    <i class="fa-solid fa-calendar-plus"></i> Construir horario
                </a>
                <a href="?ruta=lider/horarios&tab=fet" class="btn-hero-action" title="Generación algorítmica con motor FET">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generar con FET
                </a>
                <a href="?ruta=lider/horarios" class="btn-hero-action" title="Ver el horario consolidado del programa">
                    <i class="fa-solid fa-calendar-week"></i> Ver horario
                </a>
            </div>
        </div>

        <!-- =========================================================
             2. RESUMEN ACADÉMICO (KPIs)
             ========================================================= -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon docentes"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="kpi-info">
                    <span>Docentes</span>
                    <strong><?= $totalDocentes ?></strong>
                    <small>Asociados al programa</small>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon asig"><i class="fa-solid fa-book-open"></i></div>
                <div class="kpi-info">
                    <span>Asignaturas</span>
                    <strong><?= $totalAsignaturas ?></strong>
                    <small>Plan de estudios</small>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon oferta"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="kpi-info">
                    <span>Oferta</span>
                    <strong style="color: #b45309;"><?= $totalOfertadas ?></strong>
                    <small>Periodo <?= htmlspecialchars($periodoActivo['codigo'] ?? 'Activo') ?></small>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon asignac"><i class="fa-solid fa-user-check"></i></div>
                <div class="kpi-info">
                    <span>Asignaciones</span>
                    <strong style="color: #0F9D58;"><?= $totalAsignaciones ?></strong>
                    <small>Docente / Materia</small>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon clases"><i class="fa-solid fa-clock"></i></div>
                <div class="kpi-info">
                    <span>Clases</span>
                    <strong style="color: #1e3a8a;"><?= $totalClases ?></strong>
                    <small>En el horario</small>
                </div>
            </div>
        </div>

        <!-- =========================================================
             3. INFORMACIÓN INSTITUCIONAL & PLANIFICACIÓN DEL PERIODO
             ========================================================= -->
        <div class="dual-section-grid">
            <!-- Ficha Institucional -->
            <div class="academic-card">
                <div class="academic-card-header">
                    <h2><i class="fa-solid fa-circle-info" style="color: var(--color-primario);"></i> Información del Programa</h2>
                    <span class="badge <?= strtolower($programaActivo['estado']) ?>"><?= htmlspecialchars($programaActivo['estado']) ?></span>
                </div>
                <div class="academic-card-body">
                    <div class="prog-info-grid">
                        <div class="prog-info-item">
                            <span>Nombre Oficial</span>
                            <strong><?= htmlspecialchars($programaActivo['nombre']) ?></strong>
                        </div>
                        <div class="prog-info-item">
                            <span>Código SNIES / Prog</span>
                            <strong style="font-family: monospace;"><?= htmlspecialchars($programaActivo['codigo']) ?></strong>
                        </div>
                        <div class="prog-info-item">
                            <span>Facultad Adscrita</span>
                            <strong><?= htmlspecialchars($programaActivo['facultad_nombre']) ?></strong>
                        </div>
                        <div class="prog-info-item">
                            <span>Periodo Académico</span>
                            <strong><?= htmlspecialchars($periodoActivo['codigo'] ?? 'Sin asignar') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de Planificación del Periodo -->
            <div class="academic-card">
                <div class="academic-card-header">
                    <h2><i class="fa-solid fa-chart-pie" style="color: #0F9D58;"></i> Planificación del Periodo <?= htmlspecialchars($periodoActivo['codigo'] ?? '') ?></h2>
                    <span style="font-size: 12px; color: var(--color-texto-secundario); font-weight: 600;">Avance Global: <strong><?= $pctAsignacion ?>%</strong></span>
                </div>
                <div class="academic-card-body">
                    <div class="progress-list">
                        <!-- Asignaturas ofertadas vs asignadas -->
                        <div>
                            <div class="progress-item-head">
                                <span><i class="fa-solid fa-book-bookmark" style="color: var(--color-primario);"></i> Cobertura Docente en Asignaturas</span>
                                <strong><?= $totalAsignaciones ?> / <?= $totalOfertadas ?: $totalAsignaturas ?> (<?= $pctAsignacion ?>%)</strong>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill purple" style="width: <?= $pctAsignacion ?>%;"></div>
                            </div>
                        </div>

                        <!-- Docentes activos con carga -->
                        <div>
                            <div class="progress-item-head">
                                <span><i class="fa-solid fa-chalkboard-user" style="color: #0F9D58;"></i> Docentes con Clases Programadas</span>
                                <strong><?= $docentesConClase ?> / <?= $totalDocentes ?> (<?= $pctDocentes ?>%)</strong>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill green" style="width: <?= $pctDocentes ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas resumen -->
                    <div class="plan-stats-strip">
                        <div class="plan-stat-col">
                            <span>Ofertadas</span>
                            <strong><?= $totalOfertadas ?></strong>
                        </div>
                        <div class="plan-stat-col">
                            <span>Asignadas</span>
                            <strong style="color: #0F9D58;"><?= $totalAsignaciones ?></strong>
                        </div>
                        <div class="plan-stat-col">
                            <span>Clases Horario</span>
                            <strong style="color: var(--color-primario);"><?= $totalClases ?></strong>
                        </div>
                        <div class="plan-stat-col">
                            <span>Espacios Usados</span>
                            <strong style="color: #1e3a8a;"><?= $totalEspacios ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             4. DOCENTES DEL PROGRAMA
             ========================================================= -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-title-wrap">
                    <div class="section-title-icon doc"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div>
                        <h2>Docentes Asociados</h2>
                        <p><?= count($docentes) ?> docentes vinculados al programa académico.</p>
                    </div>
                </div>
                <div class="section-search-wrap">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--color-texto-secundario);"></i>
                        <input type="text" class="table-search-input" id="search-docentes" placeholder="Buscar docente..." oninput="filtrarDocentes()">
                    </div>
                </div>
            </div>

            <div class="modern-table-wrap">
                <table class="modern-table" id="tabla-docentes">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Código</th>
                            <th>Docente</th>
                            <th>Correo Electrónico</th>
                            <th style="text-align: center;">Asignaturas Compatibles</th>
                            <th style="width: 120px; text-align: center;">Estado</th>
                            <th style="width: 100px; text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($docentes)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 24px; color: var(--color-texto-secundario);">
                                    No hay docentes vinculados a este programa académico.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($docentes as $d): ?>
                                <tr class="fila-docente" data-search="<?= htmlspecialchars(strtolower($d['nombre'].' '.$d['apellido'].' '.$d['codigo'].' '.$d['correo'])) ?>">
                                    <td>
                                        <span class="code-badge"><?= htmlspecialchars($d['codigo']) ?></span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($d['nombre'] . ' ' . $d['apellido']) ?></strong>
                                    </td>
                                    <td>
                                        <span style="color: var(--color-texto-secundario); font-size: 12.5px;">
                                            <i class="fa-regular fa-envelope" style="margin-right: 4px;"></i><?= htmlspecialchars($d['correo']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="chip-compatibles">
                                            <i class="fa-solid fa-book"></i> <?= (int)($d['total_compatibles'] ?? 0) ?> materia<?= ($d['total_compatibles'] ?? 0) != 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge <?= strtolower($d['estado']) ?>">
                                            <?= htmlspecialchars($d['estado']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn-node-action"
                                                onclick="verFichaDocente(<?= htmlspecialchars(json_encode([
                                                    'codigo' => $d['codigo'],
                                                    'nombre' => $d['nombre'] . ' ' . $d['apellido'],
                                                    'correo' => $d['correo'],
                                                    'estado' => $d['estado'],
                                                    'compatibles' => (int)($d['total_compatibles'] ?? 0)
                                                ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Ver ficha del docente">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             5. ASIGNATURAS DEL PROGRAMA
             ========================================================= -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-title-wrap">
                    <div class="section-title-icon asig"><i class="fa-solid fa-book-open"></i></div>
                    <div>
                        <h2>Asignaturas del Programa</h2>
                        <p><?= count($asignaturas) ?> asignaturas estructuradas en el plan de estudios.</p>
                    </div>
                </div>
                <div class="section-search-wrap">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--color-texto-secundario);"></i>
                        <input type="text" class="table-search-input" id="search-asignaturas" placeholder="Buscar asignatura..." oninput="filtrarAsignaturas()">
                    </div>
                </div>
            </div>

            <div class="modern-table-wrap">
                <table class="modern-table" id="tabla-asignaturas">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Código</th>
                            <th>Asignatura</th>
                            <th style="text-align: center; width: 120px;">Semestre</th>
                            <th>Área Académica</th>
                            <th style="text-align: center; width: 100px;">Créditos</th>
                            <th style="width: 110px; text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asignaturas)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 24px; color: var(--color-texto-secundario);">
                                    No hay asignaturas vinculadas a este programa académico.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($asignaturas as $a): ?>
                                <tr class="fila-asignatura" data-search="<?= htmlspecialchars(strtolower($a['nombre'].' '.$a['codigo'].' '.($a['area_nombre'] ?? ''))) ?>">
                                    <td>
                                        <span class="code-badge"><?= htmlspecialchars($a['codigo']) ?></span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($a['nombre']) ?></strong>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (!empty($a['semestre'])): ?>
                                            <span style="display:inline-block; padding:2px 8px; background:#ede9fe; color:#4c1d95; font-size:12px; font-weight:700; border-radius:12px;">
                                                Semestre <?= (int)$a['semestre'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:var(--color-texto-secundario);">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-size: 12.5px; color: var(--color-texto-secundario);">
                                            <?= htmlspecialchars($a['area_nombre'] ?? 'Área general') ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 700; color: var(--color-oscuro);"><?= (int)$a['creditos'] ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge <?= strtolower($a['estado']) ?>">
                                            <?= htmlspecialchars($a['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             6. ASIGNACIONES DOCENTES
             ========================================================= -->
        <div class="section-card" id="seccion-asignaciones">
            <div class="section-card-header">
                <div class="section-title-wrap">
                    <div class="section-title-icon asigdoc"><i class="fa-solid fa-user-check"></i></div>
                    <div>
                        <h2>Asignaciones Docentes</h2>
                        <p>Relación directa Docente &harr; Asignatura para el periodo académico <?= htmlspecialchars($periodoActivo['codigo'] ?? '') ?>.</p>
                    </div>
                </div>
                <div class="section-search-wrap">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--color-texto-secundario);"></i>
                        <input type="text" class="table-search-input" id="search-asignaciones" placeholder="Buscar asignación..." oninput="filtrarAsignaciones()">
                    </div>
                    <button type="button" class="btn-node-action primary" onclick="toggleFormAsignacion()">
                        <i class="fa-solid fa-plus"></i> Nueva Asignación
                    </button>
                </div>
            </div>

            <!-- Formulario contextual desplegable -->
            <div class="asignar-drawer" id="drawer-asignacion">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <strong style="font-size: 14px; color: var(--color-oscuro);">
                        <i class="fa-solid fa-link" style="color: var(--color-primario); margin-right: 5px;"></i> Asignar Docente a Asignatura
                    </strong>
                    <button type="button" onclick="toggleFormAsignacion()" style="background: none; border: none; cursor: pointer; color: var(--color-texto-secundario);">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form action="?ruta=lider/programa/asignar_docente" method="POST" class="asignar-form-grid">
                    <input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>">

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; font-weight: 600; color: var(--color-oscuro); margin-bottom: 4px; display: block;">
                            Asignatura del Programa *
                        </label>
                        <select name="id_asignatura" class="form-control" required style="font-size: 13px; height: 38px;">
                            <option value="">— Seleccionar Asignatura —</option>
                            <?php foreach ($asignaturas as $a): ?>
                                <option value="<?= (int)$a['id_asignatura'] ?>">
                                    <?= htmlspecialchars($a['codigo'] . ' - ' . $a['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; font-weight: 600; color: var(--color-oscuro); margin-bottom: 4px; display: block;">
                            Docente Asociado *
                        </label>
                        <select name="id_docente" class="form-control" required style="font-size: 13px; height: 38px;">
                            <option value="">— Seleccionar Docente —</option>
                            <?php foreach ($docentes as $d): ?>
                                <option value="<?= (int)$d['id_docente'] ?>">
                                    <?= htmlspecialchars($d['nombre'] . ' ' . $d['apellido']) ?> (<?= htmlspecialchars($d['codigo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="btn-primario" style="height: 38px; padding: 0 18px; font-size: 13px; font-weight: 700;">
                            <i class="fa-solid fa-check"></i> Confirmar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de asignaciones -->
            <div class="modern-table-wrap">
                <table class="modern-table" id="tabla-asignaciones">
                    <thead>
                        <tr>
                            <th>Asignatura</th>
                            <th>Docente Asignado</th>
                            <th style="width: 140px; text-align: center;">Periodo</th>
                            <th style="width: 120px; text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asignaciones)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 24px; color: var(--color-texto-secundario);">
                                    No hay asignaciones docentes registradas para este programa y periodo académico.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($asignaciones as $asig): ?>
                                <tr class="fila-asignacion" data-search="<?= htmlspecialchars(strtolower($asig['asignatura_nombre'].' '.$asig['asignatura_codigo'].' '.$asig['docente_nombre'].' '.$asig['docente_codigo'])) ?>">
                                    <td>
                                        <span class="code-badge" style="margin-right: 6px;"><?= htmlspecialchars($asig['asignatura_codigo']) ?></span>
                                        <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($asig['asignatura_nombre']) ?></strong>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: var(--color-oscuro);">
                                            <i class="fa-solid fa-user-tie" style="color: var(--color-primario); margin-right: 6px;"></i>
                                            <?= htmlspecialchars($asig['docente_nombre']) ?>
                                        </span>
                                        <small style="color: var(--color-texto-secundario); margin-left: 4px;">(<?= htmlspecialchars($asig['docente_codigo']) ?>)</small>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 700; color: var(--color-oscuro); font-size: 12px; background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">
                                            <?= htmlspecialchars($asig['periodo_codigo']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge <?= strtolower($asig['estado'] ?? 'activa') ?>">
                                            <?= htmlspecialchars($asig['estado'] ?? 'ACTIVA') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>
</div>

<script>
// =========================================================
// FILTROS EN TIEMPO REAL
// =========================================================
function filtrarDocentes() {
    const q = document.getElementById('search-docentes').value.toLowerCase().trim();
    document.querySelectorAll('#tabla-docentes .fila-docente').forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

function filtrarAsignaturas() {
    const q = document.getElementById('search-asignaturas').value.toLowerCase().trim();
    document.querySelectorAll('#tabla-asignaturas .fila-asignatura').forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

function filtrarAsignaciones() {
    const q = document.getElementById('search-asignaciones').value.toLowerCase().trim();
    document.querySelectorAll('#tabla-asignaciones .fila-asignacion').forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

function toggleFormAsignacion() {
    const drawer = document.getElementById('drawer-asignacion');
    if (drawer) drawer.classList.toggle('open');
}

// Modal Ficha Docente
function verFichaDocente(doc) {
    Swal.fire({
        title: null,
        html: `
            <div style="text-align:left; font-family:'Poppins',sans-serif;">
                <div style="display:flex; align-items:center; gap:14px; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:16px;">
                    <div style="width:48px; height:48px; border-radius:50%; background:rgba(59,13,143,.1); color:var(--color-primario); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700;">
                        ${doc.nombre.charAt(0)}
                    </div>
                    <div>
                        <h3 style="font-size:18px; font-weight:700; color:var(--color-oscuro); margin:0;">${doc.nombre}</h3>
                        <span style="font-family:monospace; font-size:12px; font-weight:700; color:var(--color-primario);">${doc.codigo}</span>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                        <span style="font-size:11px; text-transform:uppercase; color:var(--color-texto-secundario); font-weight:700;">Correo Institucional</span>
                        <div style="font-size:13px; font-weight:600; color:var(--color-oscuro); margin-top:2px; word-break:break-all;">
                            ${doc.correo}
                        </div>
                    </div>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                        <span style="font-size:11px; text-transform:uppercase; color:var(--color-texto-secundario); font-weight:700;">Materias Compatibles</span>
                        <div style="font-size:13px; font-weight:600; color:var(--color-oscuro); margin-top:2px;">
                            ${doc.compatibles} asignaturas asociadas
                        </div>
                    </div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px;">
                    <span style="font-size:11px; text-transform:uppercase; color:var(--color-texto-secundario); font-weight:700; display:block; margin-bottom:4px;">Estado en el Sistema</span>
                    <span class="badge ${doc.estado.toLowerCase()}">${doc.estado}</span>
                </div>
            </div>
        `,
        showCloseButton: true,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#3B0D8F',
        width: 480
    });
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
