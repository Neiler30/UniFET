<?php
$activo = $activo ?? '';
$periodoActivo = \App\Models\PeriodoAcademico::getActivo();

$tabs = [
    [
        'etiqueta' => 'Periodos',
        'ruta'     => 'admin/academico/periodos',
        'activa'   => $activo === 'periodos',
        'icono'    => 'fa-solid fa-calendar-days'
    ],
    [
        'etiqueta' => 'Oferta Académica',
        'ruta'     => 'admin/academico/oferta',
        'activa'   => $activo === 'oferta',
        'icono'    => 'fa-solid fa-graduation-cap'
    ],
    [
        'etiqueta' => 'Áreas Académicas',
        'ruta'     => 'admin/academico/areas',
        'activa'   => $activo === 'areas',
        'icono'    => 'fa-solid fa-shapes'
    ],
    [
        'etiqueta' => 'Asignaturas',
        'ruta'     => 'admin/academico/asignaturas',
        'activa'   => $activo === 'asignaturas',
        'icono'    => 'fa-solid fa-book-bookmark'
    ],
    [
        'etiqueta' => 'Docentes',
        'ruta'     => 'admin/academico/docentes',
        'activa'   => $activo === 'docentes',
        'icono'    => 'fa-solid fa-chalkboard-user'
    ],
    [
        'etiqueta' => 'Restricciones',
        'ruta'     => 'admin/academico/restricciones',
        'activa'   => $activo === 'restricciones',
        'icono'    => 'fa-solid fa-clock-rotate-left'
    ],
];
?>

<style>
/* =========================================================
   MÓDULO ACADÉMICO — UniFET
   Arquitectura Visual y Main Estructurado
   ========================================================= */

.academico-hero-banner {
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
.academico-hero-banner::before {
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
.academico-hero-content {
    position: relative;
    z-index: 2;
}
.academico-hero-tag {
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
.academico-hero-content h1 {
    font-size: 21px;
    font-weight: 700;
    margin: 0 0 5px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
}
.academico-hero-content p {
    color: rgba(255, 255, 255, 0.82);
    font-size: 13.5px;
    margin: 0;
    line-height: 1.4;
}
.academico-hero-pills {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}
.academico-periodo-pill {
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
.academico-periodo-pill .dot-active {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 8px #10b981;
}

/* Elevación de las secciones del Módulo Académico */
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
</style>

<!-- Main Estructurado: Banner de Gestión Académica Institucional -->
<div class="academico-hero-banner">
    <div class="academico-hero-content">
        <span class="academico-hero-tag">
            <i class="fa-solid fa-graduation-cap"></i> Portal Curricular UniFET
        </span>
        <h1>Gestión Académica</h1>
        <p>Administra periodos, oferta académica, áreas, asignaturas, planta docente y reglas horarias.</p>
    </div>
    <div class="academico-hero-pills">
        <div class="academico-periodo-pill">
            <span class="dot-active"></span>
            <span>Ciclo Vigente: <strong><?= $periodoActivo ? htmlspecialchars($periodoActivo['codigo']) : 'Sin periodo activo' ?></strong></span>
        </div>
    </div>
</div>

<!-- Tabs Profesionales y Dinámicos con Iconos y Estados -->
<?php $this->render('elementos_compartidos/tabs', ['tabs' => $tabs]); ?>
