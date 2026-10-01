<?php
$activo = $activo ?? '';

$tabs = [
    [
        'etiqueta' => 'Facultades',
        'ruta'     => 'admin/institucion/facultades',
        'activa'   => $activo === 'facultades',
        'icono'    => 'fa-solid fa-building-columns'
    ],
    [
        'etiqueta' => 'Programas Académicos',
        'ruta'     => 'admin/institucion/programas',
        'activa'   => $activo === 'programas',
        'icono'    => 'fa-solid fa-graduation-cap'
    ],
];
?>

<style>
/* =========================================================
   MÓDULO INSTITUCIÓN — UniFET
   Arquitectura Visual y Main Estructurado
   Tokens: #3B0D8F | #0F9D58 | #F5B301 | #0B1F33 | #F4F6F8
   ========================================================= */

.institucion-hero-banner {
    background: linear-gradient(135deg, var(--color-primario, #3B0D8F) 0%, #24114f 65%, #180936 100%);
    color: #fff;
    border-radius: 14px;
    padding: 22px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);
    font-family: 'Poppins', sans-serif;
}
.institucion-hero-banner::before {
    content: '';
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 175px;
    height: 175px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.institucion-hero-content {
    position: relative;
    z-index: 2;
}
.institucion-hero-tag {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    padding: 3px 10px;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}
.institucion-hero-content h1 {
    font-size: 21px;
    font-weight: 700;
    margin: 0 0 5px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.institucion-hero-content p {
    color: rgba(255, 255, 255, 0.82);
    font-size: 13.5px;
    margin: 0;
    line-height: 1.4;
}
.institucion-hero-pills {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}
.institucion-stat-pill {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 8px 14px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    color: #fff;
    backdrop-filter: blur(4px);
}

/* Tarjeta de Encabezado de Sección */
.header-acciones {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 22px;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    font-family: 'Poppins', sans-serif;
    gap: 14px;
    flex-wrap: wrap;
}
.header-acciones h1 {
    font-size: 19px;
    font-weight: 800;
    color: #0B1F33;
    margin: 0 0 2px;
}
.header-acciones p {
    font-size: 13px;
    color: #64748B;
    margin: 0;
}

/* KPI Cards Ligeras para Institución */
.inst-kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
    font-family: 'Poppins', sans-serif;
}
.inst-kpi-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.inst-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.inst-kpi-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.inst-kpi-icon.facultades { background: rgba(59,13,143,0.08); color: var(--color-primario, #3B0D8F); }
.inst-kpi-icon.programas  { background: rgba(14,165,233,0.09); color: #0284c7; }
.inst-kpi-icon.activos    { background: rgba(15,157,88,0.09); color: #0F9D58; }

.inst-kpi-info span {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748B;
    display: block;
    margin-bottom: 2px;
}
.inst-kpi-info strong {
    font-size: 20px;
    font-weight: 800;
    color: #0B1F33;
    line-height: 1;
}

/* Panel de Tablas */
.panel.table-panel,
.panel {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 18px rgba(0,0,0,0.03);
    font-family: 'Poppins', sans-serif;
    background: #fff;
}
.table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    padding: 13px 16px;
}
.table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.table tbody tr:hover td {
    background-color: #f8fafc;
}
.code-pill {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 12px;
    background: #f1f5f9;
    color: var(--color-primario, #3B0D8F);
    padding: 3px 8px;
    border-radius: 5px;
    letter-spacing: 0.5px;
}

/* Badges con dot para estado */
.badge.activo    { background: #dcfce7; color: #166534; font-weight: 600; padding: 3px 9px; border-radius: 99px; }
.badge.inactivo  { background: #f1f5f9; color: #64748b; font-weight: 600; padding: 3px 9px; border-radius: 99px; }
</style>

<!-- Main Estructurado: Banner de Gestión Institucional -->
<div class="institucion-hero-banner">
    <div class="institucion-hero-content">
        <span class="institucion-hero-tag">
            <i class="fa-solid fa-building-columns"></i> Estructura Universitaria UniFET
        </span>
        <h1>Gestión Institucional</h1>
        <p>Administración de la estructura orgánica universitaria: facultades decanales y programas académicos.</p>
    </div>
    <div class="institucion-hero-pills">
        <div class="institucion-stat-pill">
            <i class="fa-solid fa-sitemap" style="color: #6ee7b7;"></i>
            <span>Nivel: <strong>Facultades &bull; Programas</strong></span>
        </div>
    </div>
</div>

<!-- Tabs Profesionales y Dinámicos con Iconos y Estados -->
<?php $this->render('elementos_compartidos/tabs', ['tabs' => $tabs]); ?>
