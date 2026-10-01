<?php
$this->render('elementos_compartidos/header', ['titulo' => 'Gestión de Espacios — UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
?>

<style>
/* =========================================================
   GESTIÓN DE ESPACIOS — UniFET
   Arquitectura Visual Dinámica, Espaciosa y Universitaria
   Tokens: #3B0D8F | #0F9D58 | #F5B301 | #0B1F33 | #F4F6F8
   ========================================================= */

:root {
    --uf-primary: #3B0D8F;
    --uf-primary-light: #5219b5;
    --uf-primary-soft: rgba(59, 13, 143, 0.08);
    --uf-dark: #0B1F33;
    --uf-success: #0F9D58;
    --uf-success-soft: #dcfce7;
    --uf-warning: #F5B301;
    --uf-warning-soft: #fef3c7;
    --uf-gray-bg: #F4F6F8;
    --uf-card-border: #E2E8F0;
    --uf-text-muted: #64748B;
}

.espacios-wrap {
    position: relative;
    z-index: 1;
    font-family: 'Poppins', sans-serif;
    color: var(--uf-dark);
}

/* Watermark institucional sutil */
.espacios-bg-watermark {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.espacios-bg-watermark img {
    width: min(55vw, 460px);
    opacity: 0.02;
    user-select: none;
}

/* =========================================================
   1. BANNER PRINCIPAL (SE MANTIENE TAL CUAL LO SOLICITADO)
   ========================================================= */
.espacios-header {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 65%, #180936 100%);
    color: #fff;
    border-radius: 14px;
    padding: 22px 28px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(59, 13, 143, 0.18);
}
.espacios-header::before {
    content: '';
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 180px;
    height: 180px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.espacios-header h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 5px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.espacios-header p {
    color: rgba(255, 255, 255, 0.84);
    font-size: 13.5px;
    margin: 0;
    line-height: 1.4;
}
.espacios-header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}
.btn-header-action {
    background: #fff;
    color: var(--color-primario) !important;
    font-weight: 700;
    font-size: 13.5px;
    padding: 10px 18px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}
.btn-header-action:hover {
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

/* =========================================================
   2. KPI STRIP — MÉTRICAS RÁPIDAS Y LIVIANAS
   ========================================================= */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(170px, 100%), 1fr));
    gap: 14px;
    margin-bottom: 22px;
}
.kpi-box {
    background: #fff;
    border: 1px solid var(--uf-card-border);
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
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
.kpi-icon.sedes    { background: rgba(59,13,143,0.08); color: var(--uf-primary); }
.kpi-icon.bloques  { background: rgba(11,31,51,0.08);  color: var(--uf-dark); }
.kpi-icon.espacios { background: rgba(30,58,138,0.08); color: #1e3a8a; }
.kpi-icon.libres   { background: rgba(15,157,88,0.1);  color: var(--uf-success); }
.kpi-icon.mant     { background: rgba(245,179,1,0.14); color: #b45309; }

.kpi-content span {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--uf-text-muted);
    display: block;
    margin-bottom: 2px;
}
.kpi-content strong {
    font-size: 22px;
    font-weight: 800;
    color: var(--uf-dark);
    line-height: 1;
}
.kpi-content small {
    font-size: 11px;
    color: var(--uf-text-muted);
    display: block;
    margin-top: 3px;
}

/* =========================================================
   3. NAVEGACIÓN DINÁMICA POR SEDES (TABS / TARJETAS FLUIDAS)
   ========================================================= */
.sedes-nav-container {
    margin-bottom: 20px;
}
.sedes-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.sedes-nav-title {
    font-size: 13.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--uf-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.sedes-cards-strip {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
}
.sede-selector-card {
    background: #fff;
    border: 2px solid var(--uf-card-border);
    border-radius: 12px;
    padding: 14px 18px;
    cursor: pointer;
    transition: all 0.22s ease;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    user-select: none;
}
.sede-selector-card:hover {
    border-color: rgba(59, 13, 143, 0.4);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(59, 13, 143, 0.08);
}
.sede-selector-card.active {
    background: #fff;
    border-color: var(--uf-primary);
    box-shadow: 0 8px 24px rgba(59, 13, 143, 0.14);
}
.sede-selector-card.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--uf-primary), #6d28d9);
    border-radius: 10px 10px 0 0;
}
.sede-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.sede-card-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sede-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--uf-primary-soft);
    color: var(--uf-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    transition: all 0.2s;
}
.sede-selector-card.active .sede-card-icon {
    background: var(--uf-primary);
    color: #fff;
}
.sede-card-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--uf-dark);
    margin: 0;
    line-height: 1.3;
}
.sede-card-code {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    color: var(--uf-primary);
    padding: 1px 6px;
    border-radius: 4px;
}
.sede-card-info {
    font-size: 12px;
    color: var(--uf-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.sede-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #f1f5f9;
    padding-top: 8px;
    margin-top: 2px;
}
.sede-badge-pills {
    display: flex;
    gap: 6px;
    align-items: center;
}
.pill-count {
    font-size: 11.5px;
    font-weight: 600;
    background: #f1f5f9;
    color: #334155;
    padding: 2px 8px;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* =========================================================
   4. WORKSPACE DEL CAMPUS (SEDE ACTIVA & BLOQUES TABS)
   ========================================================= */
.campus-workspace {
    background: #fff;
    border: 1px solid var(--uf-card-border);
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    padding: 22px 24px;
    margin-bottom: 24px;
}

/* Cabecera del Espacio de Trabajo de la Sede */
.workspace-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 16px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.workspace-title-box h2 {
    font-size: 19px;
    font-weight: 800;
    margin: 0 0 4px;
    color: var(--uf-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}
.workspace-title-box p {
    font-size: 13px;
    color: var(--uf-text-muted);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.workspace-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Selector de Bloques estilo Pills */
.bloques-tabs-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 12px;
    margin-bottom: 18px;
    scrollbar-width: thin;
}
.bloque-tab-btn {
    border: 1.5px solid var(--uf-card-border);
    background: #fff;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.18s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: inherit;
}
.bloque-tab-btn:hover {
    border-color: var(--uf-primary);
    color: var(--uf-primary);
    background: rgba(59,13,143,0.03);
}
.bloque-tab-btn.active {
    background: var(--uf-primary);
    border-color: var(--uf-primary);
    color: #fff;
    box-shadow: 0 3px 10px rgba(59,13,143,0.25);
}
.bloque-tab-btn .badge-counter {
    background: rgba(0,0,0,0.08);
    color: inherit;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 99px;
    font-weight: 700;
}
.bloque-tab-btn.active .badge-counter {
    background: rgba(255,255,255,0.25);
    color: #fff;
}
.btn-tab-add {
    border: 1.5px dashed var(--uf-primary);
    color: var(--uf-primary);
    background: transparent;
}

/* =========================================================
   5. BARRA DE HERRAMIENTAS Y BÚSQUEDA FLUIDA
   ========================================================= */
.search-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: #f8fafc;
    border: 1px solid var(--uf-card-border);
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 240px;
}
.search-input-wrap input {
    width: 100%;
    padding: 9px 12px 9px 38px;
    border: 1px solid var(--uf-card-border);
    border-radius: 8px;
    font-size: 13px;
    background: #fff;
    outline: none;
    transition: all 0.2s;
    font-family: inherit;
}
.search-input-wrap input:focus {
    border-color: var(--uf-primary);
    box-shadow: 0 0 0 3px rgba(59,13,143,0.1);
}
.search-input-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--uf-text-muted);
    font-size: 13px;
}
.filter-dropdowns {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-dropdowns select {
    padding: 8px 12px;
    border: 1px solid var(--uf-card-border);
    border-radius: 8px;
    font-size: 12.5px;
    background: #fff;
    color: var(--uf-dark);
    font-weight: 500;
    outline: none;
    cursor: pointer;
    font-family: inherit;
}
.filter-dropdowns select:focus {
    border-color: var(--uf-primary);
}

/* =========================================================
   6. GRID DE ESPACIOS — TARJETAS AIREADAS Y MODERNAS
   ========================================================= */
.espacios-room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
    align-items: stretch;
}

/* Tarjeta de Espacio Físico (Placa Universitaria Moderna) */
.room-card {
    background: #fff;
    border: 1.5px solid var(--uf-card-border);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 12px;
    transition: transform 0.2s ease, box-shadow 0.22s ease, border-color 0.2s ease;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.room-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(59, 13, 143, 0.1);
    border-color: rgba(59, 13, 143, 0.4);
}

/* Estado discreto mediante borde izquierdo y punto */
.room-card.status-disponible    { border-left: 4px solid #10b981; }
.room-card.status-mantenimiento { border-left: 4px solid #f59e0b; }
.room-card.status-inactivo      { border-left: 4px solid #94a3b8; opacity: 0.88; }

.room-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.room-code-tag {
    font-family: 'Courier New', monospace;
    font-size: 11.5px;
    font-weight: 800;
    background: #f1f5f9;
    color: var(--uf-primary);
    padding: 3px 8px;
    border-radius: 5px;
    letter-spacing: 0.5px;
}
.room-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 99px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.room-status-pill.disponible    { background: #dcfce7; color: #166534; }
.room-status-pill.mantenimiento { background: #fef3c7; color: #92400e; }
.room-status-pill.inactivo      { background: #f1f5f9; color: #64748b; }
.room-status-pill .pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.room-card-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.room-card-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--uf-dark);
    margin: 0;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.room-location-sub {
    font-size: 12px;
    color: var(--uf-text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

.room-specs-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 2px;
}
.spec-chip {
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.spec-chip.aula        { background: #eff6ff; color: #1d4ed8; }
.spec-chip.laboratorio { background: #fdf4ff; color: #86198f; }
.spec-chip.auditorio   { background: #fff7ed; color: #c2410c; }
.spec-chip.sala        { background: #f0fdf4; color: #15803d; }
.spec-chip.otro        { background: #f8fafc; color: #475569; }

.spec-capacity {
    font-size: 12px;
    color: var(--uf-text-muted);
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Tags de características */
.room-features-row {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    min-height: 22px;
}
.feature-tag {
    font-size: 10.5px;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: 2px 7px;
    border-radius: 4px;
}

/* Pie de tarjeta con acciones directas */
.room-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #f1f5f9;
    padding-top: 10px;
    margin-top: 4px;
}
.btn-room-detail {
    background: var(--uf-primary-soft);
    color: var(--uf-primary);
    border: 1px solid transparent;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: inherit;
}
.btn-room-detail:hover {
    background: var(--uf-primary);
    color: #fff;
}

.room-secondary-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}
.btn-room-tool {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid var(--uf-card-border);
    background: #fff;
    color: var(--uf-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-room-tool:hover {
    border-color: var(--uf-primary);
    color: var(--uf-primary);
    background: var(--uf-primary-soft);
}
.btn-room-tool.danger:hover {
    border-color: #dc2626;
    color: #dc2626;
    background: #fee2e2;
}

/* Estados Vacíos */
.empty-canvas {
    padding: 40px 20px;
    text-align: center;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px dashed #cbd5e1;
}
.empty-canvas i {
    font-size: 38px;
    color: #cbd5e1;
    margin-bottom: 12px;
    display: block;
}
.empty-canvas h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--uf-dark);
    margin: 0 0 6px;
}
.empty-canvas p {
    font-size: 13px;
    color: var(--uf-text-muted);
    margin: 0 0 16px;
}

/* Botones universales */
.btn-uf-primary {
    background: var(--uf-primary);
    color: #fff !important;
    border: 1px solid var(--uf-primary);
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s;
}
.btn-uf-primary:hover {
    background: #280868;
    border-color: #280868;
}
.btn-uf-secondary {
    background: #fff;
    color: var(--uf-dark);
    border: 1px solid var(--uf-card-border);
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s;
}
.btn-uf-secondary:hover {
    background: #f8fafc;
    border-color: var(--uf-primary);
    color: var(--uf-primary);
}

@media (max-width: 900px) {
    .espacios-header { flex-direction: column; align-items: flex-start; }
    .search-filter-bar { flex-direction: column; align-items: stretch; }
    .workspace-header { flex-direction: column; align-items: flex-start; }
    .espacios-room-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
}
@media (max-width: 600px) {
    .espacios-room-grid { grid-template-columns: 1fr; }
    .sedes-cards-strip { grid-template-columns: 1fr; }
}
</style>

<!-- Watermark sutil -->
<div class="espacios-bg-watermark">
    <img src="assets/img/isotipo-color.png" alt="">
</div>

<div class="espacios-wrap">

    <!-- 1. BANNER PRINCIPAL (PRESERVADO INTACTO CONTEXTUALMENTE) -->
    <div class="espacios-header">
        <div>
            <h1><i class="fa-solid fa-map-location-dot"></i> Gestión de Espacios</h1>
            <p>Administra sedes, bloques y espacios físicos de la institución en una sola vista jerárquica.</p>
        </div>
        <div class="espacios-header-actions">
            <a href="?ruta=admin/espacios/sedes/nueva" class="btn-header-action">
                <i class="fa-solid fa-plus"></i> Nueva Sede
            </a>
        </div>
    </div>

    <!-- 2. RESUMEN KPI GLOBAL -->
    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-icon sedes"><i class="fa-solid fa-building-columns"></i></div>
            <div class="kpi-content">
                <span>Sedes</span>
                <strong><?= (int)($kpis['total_sedes'] ?? 0) ?></strong>
                <small><?= (int)($kpis['sedes_activas'] ?? 0) ?> activas</small>
            </div>
        </div>

        <div class="kpi-box">
            <div class="kpi-icon bloques"><i class="fa-solid fa-cube"></i></div>
            <div class="kpi-content">
                <span>Bloques</span>
                <strong><?= (int)($kpis['total_bloques'] ?? 0) ?></strong>
                <small>Registrados</small>
            </div>
        </div>

        <div class="kpi-box">
            <div class="kpi-icon espacios"><i class="fa-solid fa-door-open"></i></div>
            <div class="kpi-content">
                <span>Espacios</span>
                <strong><?= (int)($kpis['total_espacios'] ?? 0) ?></strong>
                <small>Registrados</small>
            </div>
        </div>

        <div class="kpi-box">
            <div class="kpi-icon libres"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-content">
                <span>Disponibles</span>
                <strong style="color: var(--uf-success);"><?= (int)($kpis['espacios_disponibles'] ?? 0) ?></strong>
                <small>Operativos</small>
            </div>
        </div>

        <div class="kpi-box">
            <div class="kpi-icon mant"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="kpi-content">
                <span>Mantenimiento</span>
                <strong style="color: #b45309;"><?= (int)($kpis['espacios_mantenimiento'] ?? 0) ?></strong>
                <small><?= (int)($kpis['espacios_inactivos'] ?? 0) ?> inactivos</small>
            </div>
        </div>
    </div>

    <?php if (empty($sedes)): ?>
        <!-- Estado Inicial: Sin Sedes -->
        <div class="campus-workspace">
            <div class="empty-canvas">
                <i class="fa-solid fa-city"></i>
                <h3>No hay sedes registradas en el sistema</h3>
                <p>Comienza creando la primera sede institucional para configurar bloques y registrar aulas o laboratorios.</p>
                <a href="?ruta=admin/espacios/sedes/nueva" class="btn-uf-primary">
                    <i class="fa-solid fa-plus"></i> Crear Primera Sede
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- 3. SELECTOR DINÁMICO DE SEDES (TABS / CARDS HORIZONTALES) -->
        <div class="sedes-nav-container">
            <div class="sedes-nav-header">
                <span class="sedes-nav-title">
                    <i class="fa-solid fa-layer-group"></i> Seleccionar Sede Institucional
                </span>
                <span style="font-size: 12.5px; color: var(--uf-text-muted);">
                    <?= count($sedes) ?> sede<?= count($sedes) != 1 ? 's' : '' ?> disponible<?= count($sedes) != 1 ? 's' : '' ?>
                </span>
            </div>

            <div class="sedes-cards-strip">
                <!-- Botón Todas las Sedes -->
                <div class="sede-selector-card active" id="tab-sede-all" onclick="seleccionarSede('all', this)">
                    <div class="sede-card-top">
                        <div class="sede-card-brand">
                            <div class="sede-card-icon"><i class="fa-solid fa-globe"></i></div>
                            <div>
                                <h3 class="sede-card-name">Todas las Sedes</h3>
                                <span class="sede-card-code">CAMPUS GLOBAL</span>
                            </div>
                        </div>
                    </div>
                    <div class="sede-card-info">
                        <span>Vista consolidada de toda la infraestructura</span>
                    </div>
                    <div class="sede-card-footer">
                        <div class="sede-badge-pills">
                            <span class="pill-count"><i class="fa-solid fa-cube"></i> <?= (int)$kpis['total_bloques'] ?> bloques</span>
                            <span class="pill-count"><i class="fa-solid fa-door-open"></i> <?= (int)$kpis['total_espacios'] ?> espacios</span>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas para cada Sede -->
                <?php foreach ($sedes as $s): ?>
                    <div class="sede-selector-card" id="tab-sede-<?= (int)$s['id_sede'] ?>" onclick="seleccionarSede(<?= (int)$s['id_sede'] ?>, this)">
                        <div class="sede-card-top">
                            <div class="sede-card-brand">
                                <div class="sede-card-icon"><i class="fa-solid fa-building-columns"></i></div>
                                <div>
                                    <h3 class="sede-card-name"><?= htmlspecialchars($s['nombre']) ?></h3>
                                    <span class="sede-card-code"><?= htmlspecialchars($s['codigo']) ?></span>
                                </div>
                            </div>
                            <span class="pill-count" style="background: <?= $s['estado'] === 'ACTIVO' ? '#dcfce7; color:#166534;' : '#f1f5f9; color:#64748b;' ?> font-size:10.5px;">
                                <?= htmlspecialchars($s['estado'] ?? 'ACTIVO') ?>
                            </span>
                        </div>
                        <div class="sede-card-info">
                            <span><i class="fa-solid fa-location-dot" style="font-size:11px;"></i> <?= !empty($s['direccion']) ? htmlspecialchars($s['direccion']) : 'Sin dirección especificada' ?></span>
                        </div>
                        <div class="sede-card-footer">
                            <div class="sede-badge-pills">
                                <span class="pill-count"><i class="fa-solid fa-cube"></i> <?= (int)$s['num_bloques'] ?> blq.</span>
                                <span class="pill-count"><i class="fa-solid fa-door-open"></i> <?= (int)$s['num_espacios'] ?> esp.</span>
                            </div>
                            <div onclick="event.stopPropagation();">
                                <a href="?ruta=admin/espacios/sedes/editar_completo&id=<?= (int)$s['id_sede'] ?>" class="btn-room-tool" title="Editar sede">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. WORKSPACE DEL CAMPUS (SECCIÓN ACTIVA Y FILTROS) -->
        <div class="campus-workspace">

            <!-- Encabezado del Workspace dinámico -->
            <div class="workspace-header">
                <div class="workspace-title-box">
                    <h2 id="workspace-sede-title">
                        <i class="fa-solid fa-network-wired" style="color: var(--uf-primary);"></i> Todas las Sedes
                    </h2>
                    <p id="workspace-sede-sub">
                        Explorando todos los bloques y espacios físicos registrados en la institución.
                    </p>
                </div>
                <div class="workspace-actions">
                    <a href="#" id="btn-workspace-add-bloque" class="btn-uf-primary" style="display: none;">
                        <i class="fa-solid fa-plus"></i> Nuevo Bloque en esta Sede
                    </a>
                    <a href="?ruta=admin/espacios/espacios/crear" class="btn-uf-secondary">
                        <i class="fa-solid fa-plus"></i> Registrar Espacio Físico
                    </a>
                </div>
            </div>

            <!-- Subnavegación: Pills de Bloques -->
            <div class="bloques-tabs-strip" id="bloques-pills-bar">
                <button type="button" class="bloque-tab-btn active" data-bloque="all" onclick="filtrarPorBloque('all', this)">
                    <i class="fa-solid fa-layer-group"></i> Todos los Bloques
                    <span class="badge-counter" id="total-espacios-counter"><?= (int)$kpis['total_espacios'] ?></span>
                </button>

                <?php foreach ($todosLosBloques as $tb): ?>
                    <button type="button" class="bloque-tab-btn bloque-pill-item" 
                            data-bloque="<?= (int)$tb['id_bloque'] ?>" 
                            data-sede="<?= (int)$tb['id_sede'] ?>" 
                            onclick="filtrarPorBloque(<?= (int)$tb['id_bloque'] ?>, this)">
                        <i class="fa-solid fa-cube"></i> <?= htmlspecialchars($tb['nombre']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- 5. BARRA DE BÚSQUEDA Y FILTRADO INMEDIATO -->
            <div class="search-filter-bar">
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="filtro-busqueda-input" placeholder="Buscar por nombre, código de aula, laboratorio o capacidad..." oninput="ejecutarFiltroEspacios()">
                </div>

                <div class="filter-dropdowns">
                    <select id="filtro-tipo-select" onchange="ejecutarFiltroEspacios()">
                        <option value="">Todos los Tipos</option>
                        <option value="AULA">Aula</option>
                        <option value="LABORATORIO">Laboratorio</option>
                        <option value="SALA">Sala</option>
                        <option value="AUDITORIO">Auditorio</option>
                        <option value="OTRO">Otro</option>
                    </select>

                    <select id="filtro-estado-select" onchange="ejecutarFiltroEspacios()">
                        <option value="">Todos los Estados</option>
                        <option value="DISPONIBLE">Disponible</option>
                        <option value="MANTENIMIENTO">Mantenimiento</option>
                        <option value="INACTIVO">Inactivo</option>
                    </select>

                    <button type="button" class="btn-uf-secondary" onclick="limpiarTodosFiltros()" title="Restablecer filtros">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Contador de resultados -->
            <div style="font-size: 12.5px; color: var(--uf-text-muted); margin-bottom: 16px; font-weight: 500;">
                <span id="contador-espacios-visibles">Mostrando <?= (int)$kpis['total_espacios'] ?> espacios físicos</span>
            </div>

            <!-- 6. GRID DE ESPACIOS FÍSICOS (PLACA UNIVERSITARIA ELEGANTE) -->
            <div class="espacios-room-grid" id="espacios-grid-canvas">
                <?php 
                $hayEspacios = false;
                foreach ($sedes as $s):
                    foreach ($s['bloques'] as $b):
                        foreach ($b['espacios'] as $esp):
                            $hayEspacios = true;
                            $tipoLower = strtolower($esp['tipo'] ?? 'aula');
                            $tipoIcons = [
                                'aula'        => 'chalkboard',
                                'laboratorio' => 'flask',
                                'auditorio'   => 'users-rectangle',
                                'sala'        => 'laptop',
                                'otro'        => 'door-closed'
                            ];
                            $iconoTipo = $tipoIcons[$tipoLower] ?? 'door-open';
                            $estadoClass = strtolower($esp['estado'] ?? 'disponible');
                            $estadoNext = $esp['estado'] === 'DISPONIBLE' ? 'MANTENIMIENTO'
                                          : ($esp['estado'] === 'MANTENIMIENTO' ? 'INACTIVO' : 'DISPONIBLE');
                            $estadoLabel = match($estadoClass) {
                                'disponible'    => 'Disponible',
                                'mantenimiento' => 'Mantenimiento',
                                default         => 'Inactivo'
                            };

                            $caractArr = [];
                            if (!empty($esp['caracteristicas']) && $esp['caracteristicas'] !== '[]') {
                                $parsed = json_decode($esp['caracteristicas'], true);
                                if (is_array($parsed)) $caractArr = $parsed;
                            }
                ?>
                            <div class="room-card status-<?= $estadoClass ?> espacio-item-unit"
                                 id="espacio-card-<?= (int)$esp['id_espacio'] ?>"
                                 data-espacio-id="<?= (int)$esp['id_espacio'] ?>"
                                 data-sede-id="<?= (int)$s['id_sede'] ?>"
                                 data-bloque-id="<?= (int)$b['id_bloque'] ?>"
                                 data-tipo="<?= htmlspecialchars($esp['tipo']) ?>"
                                 data-estado="<?= htmlspecialchars($esp['estado']) ?>"
                                 data-keywords="<?= htmlspecialchars(strtolower($esp['nombre'].' '.$esp['codigo'].' '.$esp['tipo'].' '.$b['nombre'].' '.$s['nombre'])) ?>">

                                <div>
                                    <!-- Cabecera de la tarjeta: Código + Estado con pulso -->
                                    <div class="room-card-head">
                                        <span class="room-code-tag"><?= htmlspecialchars($esp['codigo']) ?></span>
                                        <span class="room-status-pill <?= $estadoClass ?>">
                                            <span class="pulse-dot"></span> <?= $estadoLabel ?>
                                        </span>
                                    </div>

                                    <!-- Cuerpo: Nombre, ubicación y especificaciones -->
                                    <div class="room-card-body" style="margin-top: 10px;">
                                        <h3 class="room-card-title" title="<?= htmlspecialchars($esp['nombre']) ?>">
                                            <?= htmlspecialchars($esp['nombre']) ?>
                                        </h3>
                                        <div class="room-location-sub">
                                            <i class="fa-solid fa-location-dot" style="font-size:11px; color: var(--uf-primary);"></i>
                                            <?= htmlspecialchars($s['nombre']) ?> &bull; <strong><?= htmlspecialchars($b['nombre']) ?></strong>
                                        </div>

                                        <div class="room-specs-row">
                                            <span class="spec-chip <?= $tipoLower ?>">
                                                <i class="fa-solid fa-<?= $iconoTipo ?>"></i> <?= htmlspecialchars(ucfirst($tipoLower)) ?>
                                            </span>
                                            <span class="spec-capacity">
                                                <i class="fa-solid fa-user-group" style="font-size:11px;"></i> <?= (int)$esp['capacidad'] ?> estudiantes
                                            </span>
                                        </div>

                                        <!-- Tags de Características -->
                                        <div class="room-features-row" style="margin-top: 6px;">
                                            <?php if (!empty($caractArr)): ?>
                                                <?php foreach (array_slice($caractArr, 0, 3) as $c): ?>
                                                    <span class="feature-tag"><?= htmlspecialchars($c) ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($caractArr) > 3): ?>
                                                    <span class="feature-tag">+<?= count($caractArr) - 3 ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="font-size:11px; color:#94a3b8; font-style:italic;">Equipamiento estándar</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones de la Tarjeta -->
                                <div class="room-card-actions">
                                    <button type="button" class="btn-room-detail"
                                            onclick="verDetalleEspacio(<?= htmlspecialchars(json_encode([
                                                'codigo'          => $esp['codigo'],
                                                'nombre'          => $esp['nombre'],
                                                'tipo'            => $esp['tipo'],
                                                'capacidad'       => $esp['capacidad'],
                                                'caracteristicas' => $esp['caracteristicas'],
                                                'estado'          => $esp['estado'],
                                                'bloque'          => $b['nombre'],
                                                'sede'            => $s['nombre']
                                            ]), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fa-solid fa-eye"></i> Ver Ficha
                                    </button>

                                    <div class="room-secondary-actions">
                                        <a href="?ruta=admin/espacios/espacios/editar&id=<?= (int)$esp['id_espacio'] ?>" class="btn-room-tool" title="Editar espacio">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="?ruta=admin/espacios/espacios/cambiar_estado&id=<?= (int)$esp['id_espacio'] ?>&estado=<?= $estadoNext ?>" 
                                           class="btn-room-tool"
                                           onclick="return confirmarAccion(event, this.href, '¿Deseas cambiar el estado de este espacio a <?= $estadoNext ?>?')"
                                           title="Cambiar estado a <?= $estadoNext ?>">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                <?php 
                        endforeach;
                    endforeach;
                endforeach; 
                ?>
            </div>

            <!-- Canvas cuando no hay espacios con los filtros actuales -->
            <div id="no-rooms-message" class="empty-canvas" style="display: none; margin-top: 14px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h3>No se encontraron espacios físicos</h3>
                <p>No hay aulas ni laboratorios que coincidan con la sede, bloque o filtros seleccionados.</p>
                <button type="button" class="btn-uf-secondary" onclick="limpiarTodosFiltros()">
                    <i class="fa-solid fa-rotate-left"></i> Restablecer Filtros
                </button>
            </div>

        </div>

    <?php endif; ?>

</div>

<script>
// =========================================================
// ESTADO Y NAVEGACIÓN DINÁMICA DEL CAMPUS
// =========================================================
let currentSede = 'all';
let currentBloque = 'all';

const sedesData = <?= json_encode(array_map(function($s) {
    return [
        'id'        => (int)$s['id_sede'],
        'nombre'    => $s['nombre'],
        'direccion' => $s['direccion'] ?? '',
        'bloques'   => (int)$s['num_bloques'],
        'espacios'  => (int)$s['num_espacios']
    ];
}, $sedes)) ?>;

function seleccionarSede(sedeId, el) {
    currentSede = sedeId;
    currentBloque = 'all';

    // Actualizar clase activa en cards de sedes
    document.querySelectorAll('.sede-selector-card').forEach(c => c.classList.remove('active'));
    if (el) el.classList.add('active');

    // Actualizar encabezado del workspace
    const titleEl = document.getElementById('workspace-sede-title');
    const subEl = document.getElementById('workspace-sede-sub');
    const btnAddBloque = document.getElementById('btn-workspace-add-bloque');

    if (sedeId === 'all') {
        titleEl.innerHTML = `<i class="fa-solid fa-network-wired" style="color: var(--uf-primary);"></i> Todas las Sedes`;
        subEl.textContent = `Explorando todos los bloques y espacios físicos de la institución.`;
        if (btnAddBloque) btnAddBloque.style.display = 'none';
    } else {
        const sedeObj = sedesData.find(s => s.id === parseInt(sedeId));
        if (sedeObj) {
            titleEl.innerHTML = `<i class="fa-solid fa-building-columns" style="color: var(--uf-primary);"></i> ${sedeObj.nombre}`;
            subEl.textContent = `${sedeObj.direccion ? sedeObj.direccion + ' • ' : ''}${sedeObj.bloques} bloques • ${sedeObj.espacios} espacios registrados`;
            if (btnAddBloque) {
                btnAddBloque.href = `?ruta=admin/espacios/bloques/crear&sede_id=${sedeId}`;
                btnAddBloque.style.display = 'inline-flex';
            }
        }
    }

    // Filtrar visibilidad de las pills de bloques
    document.querySelectorAll('.bloque-pill-item').forEach(pill => {
        const pillSede = pill.getAttribute('data-sede');
        if (sedeId === 'all' || pillSede === String(sedeId)) {
            pill.style.display = 'inline-flex';
        } else {
            pill.style.display = 'none';
        }
    });

    // Resetear pill activa de bloque a "Todos"
    document.querySelectorAll('.bloque-tab-btn').forEach(b => b.classList.remove('active'));
    const tabAllBloques = document.querySelector('.bloque-tab-btn[data-bloque="all"]');
    if (tabAllBloques) tabAllBloques.classList.add('active');

    ejecutarFiltroEspacios();
}

function filtrarPorBloque(bloqueId, el) {
    currentBloque = bloqueId;

    document.querySelectorAll('.bloque-tab-btn').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');

    ejecutarFiltroEspacios();
}

function ejecutarFiltroEspacios() {
    const busqueda = (document.getElementById('filtro-busqueda-input')?.value || '').trim().toLowerCase();
    const tipo = (document.getElementById('filtro-tipo-select')?.value || '').toUpperCase();
    const estado = (document.getElementById('filtro-estado-select')?.value || '').toUpperCase();

    let visibles = 0;
    const cards = document.querySelectorAll('.espacio-item-unit');

    cards.forEach(card => {
        const cardSede = card.getAttribute('data-sede-id');
        const cardBloque = card.getAttribute('data-bloque-id');
        const cardTipo = card.getAttribute('data-tipo') || '';
        const cardEstado = card.getAttribute('data-estado') || '';
        const cardKeywords = card.getAttribute('data-keywords') || '';

        // Comprobación de Sede
        const matchSede = (currentSede === 'all' || cardSede === String(currentSede));
        // Comprobación de Bloque
        const matchBloque = (currentBloque === 'all' || cardBloque === String(currentBloque));
        // Comprobación de Tipo
        const matchTipo = (!tipo || cardTipo === tipo);
        // Comprobación de Estado
        const matchEstado = (!estado || cardEstado === estado);
        // Comprobación de Búsqueda
        const matchTexto = (!busqueda || cardKeywords.includes(busqueda));

        if (matchSede && matchBloque && matchTipo && matchEstado && matchTexto) {
            card.style.display = 'flex';
            visibles++;
        } else {
            card.style.display = 'none';
        }
    });

    // Contador y estado vacío
    const contador = document.getElementById('contador-espacios-visibles');
    const noRoomsMsg = document.getElementById('no-rooms-message');
    const canvas = document.getElementById('espacios-grid-canvas');

    if (contador) {
        contador.textContent = `Mostrando ${visibles} espacio(s) físico(s) coincidente(s)`;
    }

    if (visibles === 0) {
        if (noRoomsMsg) noRoomsMsg.style.display = 'block';
    } else {
        if (noRoomsMsg) noRoomsMsg.style.display = 'none';
    }
}

function limpiarTodosFiltros() {
    if (document.getElementById('filtro-busqueda-input')) document.getElementById('filtro-busqueda-input').value = '';
    if (document.getElementById('filtro-tipo-select')) document.getElementById('filtro-tipo-select').value = '';
    if (document.getElementById('filtro-estado-select')) document.getElementById('filtro-estado-select').value = '';

    seleccionarSede('all', document.getElementById('tab-sede-all'));
}

// =========================================================
// MODAL DE DETALLE DEL ESPACIO (FICHA TÉCNICA)
// =========================================================
function verDetalleEspacio(data) {
    let caractHtml = '<span style="color:var(--uf-text-muted);">Sin características adicionales registradas</span>';
    if (data.caracteristicas && data.caracteristicas !== '[]') {
        try {
            const arr = typeof data.caracteristicas === 'string' ? JSON.parse(data.caracteristicas) : data.caracteristicas;
            if (Array.isArray(arr) && arr.length > 0) {
                caractHtml = arr.map(c => `<span style="display:inline-block; padding:4px 9px; background:#eff6ff; color:#1d4ed8; border-radius:6px; font-size:12px; margin:2px; font-weight:500;">${c}</span>`).join(' ');
            }
        } catch(e) {}
    }

    Swal.fire({
        title: null,
        html: `
            <div style="text-align:left; font-family:'Poppins',sans-serif;">
                <div style="display:flex; align-items:center; gap:14px; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:16px;">
                    <div style="width:48px; height:48px; border-radius:12px; background:rgba(59,13,143,0.08); color:#3B0D8F; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <h3 style="font-size:18px; font-weight:800; color:#0B1F33; margin:0 0 3px;">${data.nombre}</h3>
                        <span style="font-family:'Courier New',monospace; font-size:12px; font-weight:800; color:#3B0D8F; background:#f1f5f9; padding:2px 7px; border-radius:4px;">${data.codigo}</span>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                        <span style="font-size:10.5px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Ubicación Física</span>
                        <div style="font-size:13.5px; font-weight:700; color:#0B1F33; margin-top:3px;">
                            ${data.sede} &bull; ${data.bloque}
                        </div>
                    </div>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                        <span style="font-size:10.5px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Clasificación & Aforo</span>
                        <div style="font-size:13.5px; font-weight:700; color:#0B1F33; margin-top:3px;">
                            ${data.tipo} &bull; ${data.capacidad} estudiantes
                        </div>
                    </div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:14px;">
                    <span style="font-size:10.5px; text-transform:uppercase; color:#64748b; font-weight:700; display:block; margin-bottom:6px; letter-spacing:0.4px;">Estado Operativo</span>
                    <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:99px; background:${data.estado === 'DISPONIBLE' ? '#dcfce7; color:#166534;' : (data.estado === 'MANTENIMIENTO' ? '#fef3c7; color:#92400e;' : '#f1f5f9; color:#64748b;')}">
                        ● ${data.estado}
                    </span>
                </div>

                <div>
                    <span style="font-size:10.5px; text-transform:uppercase; color:#64748b; font-weight:700; display:block; margin-bottom:6px; letter-spacing:0.4px;">Equipamiento & Características</span>
                    <div>${caractHtml}</div>
                </div>
            </div>
        `,
        showCloseButton: true,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#3B0D8F',
        width: 520
    });
}

function confirmarAccion(event, url, mensaje) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirmar acción',
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B0D8F',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
