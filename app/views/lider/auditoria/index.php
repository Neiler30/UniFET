<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Auditoría de Clases']);
$this->render('elementos_compartidos/sidebar_lider');

$diasEsp = [
    'LUNES' => 'Lunes', 'MARTES' => 'Martes', 'MIERCOLES' => 'Miércoles',
    'JUEVES' => 'Jueves', 'VIERNES' => 'Viernes', 'SABADO' => 'Sábado', 'DOMINGO' => 'Domingo'
];
$diaLabel = $diasEsp[$diaSemanaNombre] ?? $diaSemanaNombre;
$fechaFormateada = date('d/m/Y', strtotime($fechaSeleccionada));
?>

<style>
/* ============================== HERO ============================== */
.audit-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 60%, #1a0a3e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    position: relative;
    overflow: hidden;
}
.audit-hero::before {
    content: '';
    position: absolute;
    right: -30px; bottom: -30px;
    width: 220px; height: 220px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.audit-hero h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
.audit-hero p { color: rgba(255,255,255,.78); margin: 0; font-size: 14px; }

/* ============================== METRICS ============================== */
.audit-metrics {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    min-width: min(440px, 100%);
}
.audit-metric {
    border: 1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.08);
    border-radius: 10px;
    padding: 14px 16px;
    text-align: center;
    backdrop-filter: blur(4px);
    transition: background .2s;
}
.audit-metric:hover { background: rgba(255,255,255,.14); }
.audit-metric strong { display: block; font-size: 26px; font-weight: 700; }
.audit-metric span { font-size: 11px; color: rgba(255,255,255,.68); text-transform: uppercase; letter-spacing: .5px; }
.metric-pendientes strong { color: #fbbf24; }
.metric-sin strong { color: #34d399; }
.metric-con strong { color: #f87171; }
.metric-total strong { color: #fff; }

/* ============================== FILTER BAR ============================== */
.audit-filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
    margin-bottom: 22px;
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 18px 22px;
    box-shadow: var(--sombra-tarjeta);
}
.audit-filter-bar .form-group { margin-bottom: 0; min-width: 180px; }
.audit-filter-bar label { font-size: 12px; font-weight: 600; color: var(--color-texto-secundario); margin-bottom: 4px; }

/* ============================== STATE TABS ============================== */
.state-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.state-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border: 1px solid var(--color-borde);
    border-radius: 8px;
    background: #fff;
    color: var(--color-texto-secundario);
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    transition: all .2s;
}
.state-tab:hover { border-color: var(--color-primario); color: var(--color-primario); text-decoration: none; }
.state-tab.active { background: var(--color-primario); border-color: var(--color-primario); color: #fff; }
.state-tab .tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    background: rgba(0,0,0,.08);
    color: inherit;
}
.state-tab.active .tab-count { background: rgba(255,255,255,.22); }

/* ============================== CLASS CARDS ============================== */
.clases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(380px, 100%), 1fr));
    gap: 16px;
}
.clase-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: box-shadow .25s, transform .25s;
    position: relative;
    overflow: hidden;
}
.clase-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 5px; height: 100%;
    border-radius: 12px 0 0 12px;
}
.clase-card.estado-pendiente::before { background: #f59e0b; }
.clase-card.estado-sin-novedad::before { background: #10b981; }
.clase-card.estado-con-novedad::before { background: #ef4444; }
.clase-card:hover { box-shadow: var(--sombra-hover); transform: translateY(-2px); }

.clase-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
.clase-card-header h3 { font-size: 15px; font-weight: 700; color: var(--color-oscuro); margin: 0; }
.clase-card-header .badge-estado {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.badge-pendiente { background: #fef3c7; color: #b45309; }
.badge-sin-novedad { background: #d1fae5; color: #065f46; }
.badge-con-novedad { background: #fee2e2; color: #991b1b; }

.clase-card-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    font-size: 13px;
}
.meta-item { display: flex; align-items: center; gap: 7px; color: var(--color-texto-secundario); }
.meta-item i { width: 16px; text-align: center; color: var(--color-primario); opacity: .7; font-size: 13px; }
.meta-item strong { color: var(--color-oscuro); font-weight: 600; }

.clase-card-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}
.btn-auditar {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: var(--color-primario);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s, transform .15s;
}
.btn-auditar:hover { background: #2c096b; transform: translateY(-1px); text-decoration: none; }
.btn-ver-detalle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    color: var(--color-primario);
    border: 1px solid var(--color-primario);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}
.btn-ver-detalle:hover { background: rgba(59,13,143,.06); text-decoration: none; }
.btn-historial-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: rgba(59,13,143,.06);
    color: var(--color-primario);
    border: 1px solid rgba(59,13,143,.15);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}
.btn-historial-link:hover { background: rgba(59,13,143,.12); text-decoration: none; }

/* ============================== EMPTY STATE ============================== */
.audit-empty-state {
    text-align: center;
    padding: 48px 20px;
    color: var(--color-texto-secundario);
}
.audit-empty-state i { font-size: 48px; opacity: .3; margin-bottom: 14px; display: block; }
.audit-empty-state h3 { font-size: 18px; color: var(--color-oscuro); margin-bottom: 6px; }
.audit-empty-state p { font-size: 14px; max-width: 440px; margin: 0 auto; }

/* ============================== AUDITOR INFO ============================== */
.auditor-info {
    font-size: 12px;
    color: var(--color-texto-secundario);
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: auto;
}

/* ============================== RESPONSIVE ============================== */
@media (max-width: 900px) {
    .audit-hero { flex-direction: column; align-items: stretch; padding: 22px; }
    .audit-metrics { grid-template-columns: repeat(2, 1fr); min-width: 0; }
    .clases-grid { grid-template-columns: 1fr; }
    .clase-card-meta { grid-template-columns: 1fr; }
}
</style>

<!-- =================== HERO =================== -->
<div class="audit-hero">
    <div>
        <h1><i class="fa-solid fa-clipboard-check" style="margin-right: 8px;"></i> Auditoría de Clases</h1>
        <p><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?> — <?= $diaLabel ?>, <?= $fechaFormateada ?></p>
    </div>
    <div class="audit-metrics">
        <div class="audit-metric metric-total">
            <strong><?= $resumenEstados['total'] ?></strong>
            <span>Total</span>
        </div>
        <div class="audit-metric metric-pendientes">
            <strong><?= $resumenEstados['pendientes'] ?></strong>
            <span>Pendientes</span>
        </div>
        <div class="audit-metric metric-sin">
            <strong><?= $resumenEstados['sin_novedades'] ?></strong>
            <span>Sin novedad</span>
        </div>
        <div class="audit-metric metric-con">
            <strong><?= $resumenEstados['con_novedades'] ?></strong>
            <span>Con novedad</span>
        </div>
    </div>
</div>

<!-- =================== FILTER BAR =================== -->
<form method="GET" class="audit-filter-bar">
    <input type="hidden" name="ruta" value="lider/auditoria">
    <?php if ($programaActivo): ?>
        <input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>">
    <?php endif; ?>
    <div class="form-group">
        <label><i class="fa-solid fa-calendar-day"></i> Fecha de consulta</label>
        <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($fechaSeleccionada) ?>">
    </div>
    <div class="form-group">
        <button type="submit" class="btn-primario" style="padding: 10px 20px; font-size: 14px;">
            <i class="fa-solid fa-search"></i> Consultar
        </button>
    </div>
    <div style="margin-left: auto;">
        <a href="?ruta=lider/auditoria/historial<?= $programaActivo ? '&programa_id='.(int)$programaActivo['id_programa'] : '' ?>" class="btn-historial-link">
            <i class="fa-solid fa-clock-rotate-left"></i> Historial de Auditorías
        </a>
    </div>
</form>

<!-- =================== STATE TABS =================== -->
<?php if ($programaActivo): ?>
<nav class="state-tabs">
    <?php
    $baseUrl = "?ruta=lider/auditoria&fecha={$fechaSeleccionada}&programa_id=" . (int)$programaActivo['id_programa'];
    $estados = [
        ''             => ['Todas',       'fa-th-large',      $resumenEstados['total']],
        'PENDIENTE'    => ['Pendientes',  'fa-clock',         $resumenEstados['pendientes']],
        'SIN_NOVEDAD'  => ['Sin novedad', 'fa-check-circle',  $resumenEstados['sin_novedades']],
        'CON_NOVEDAD'  => ['Con novedad', 'fa-exclamation-triangle', $resumenEstados['con_novedades']],
    ];
    foreach ($estados as $key => $info):
    ?>
        <a class="state-tab <?= $filtroEstado === $key ? 'active' : '' ?>"
           href="<?= $baseUrl . ($key ? "&estado={$key}" : '') ?>">
            <i class="fa-solid <?= $info[1] ?>"></i> <?= $info[0] ?>
            <span class="tab-count"><?= $info[2] ?></span>
        </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<!-- =================== CLASSES GRID =================== -->
<?php if (!$programaActivo): ?>
    <div class="panel">
        <div class="audit-empty-state">
            <i class="fa-solid fa-graduation-cap"></i>
            <h3>Sin programa asignado</h3>
            <p>No tienes ningún programa académico asignado. Contacta al administrador para que te asigne uno.</p>
        </div>
    </div>
<?php elseif (empty($clases)): ?>
    <div class="panel">
        <div class="audit-empty-state">
            <i class="fa-regular fa-calendar-xmark"></i>
            <h3>No hay clases para esta fecha</h3>
            <p>No se encontraron clases programadas el <strong><?= $diaLabel ?></strong> (<?= $fechaFormateada ?>) para el programa <strong><?= htmlspecialchars($programaActivo['nombre']) ?></strong>.</p>
        </div>
    </div>
<?php else: ?>
    <div class="clases-grid">
        <?php foreach ($clases as $clase):
            $estado = $clase['estado_auditoria'];
            $cssEstado = match($estado) {
                'PENDIENTE' => 'estado-pendiente',
                'SIN_NOVEDAD' => 'estado-sin-novedad',
                'CON_NOVEDAD' => 'estado-con-novedad',
                default => 'estado-pendiente'
            };
            $badgeCss = match($estado) {
                'PENDIENTE' => 'badge-pendiente',
                'SIN_NOVEDAD' => 'badge-sin-novedad',
                'CON_NOVEDAD' => 'badge-con-novedad',
                default => 'badge-pendiente'
            };
            $badgeLabel = match($estado) {
                'PENDIENTE' => 'Pendiente',
                'SIN_NOVEDAD' => 'Sin novedad',
                'CON_NOVEDAD' => 'Con novedad',
                default => 'Pendiente'
            };
            $badgeIcon = match($estado) {
                'PENDIENTE' => 'fa-clock',
                'SIN_NOVEDAD' => 'fa-check-circle',
                'CON_NOVEDAD' => 'fa-exclamation-triangle',
                default => 'fa-clock'
            };
        ?>
        <div class="clase-card <?= $cssEstado ?>">
            <div class="clase-card-header">
                <h3><?= htmlspecialchars($clase['asignatura_nombre']) ?></h3>
                <span class="badge-estado <?= $badgeCss ?>">
                    <i class="fa-solid <?= $badgeIcon ?>"></i> <?= $badgeLabel ?>
                </span>
            </div>

            <div class="clase-card-meta">
                <div class="meta-item">
                    <i class="fa-solid fa-barcode"></i>
                    <span><strong><?= htmlspecialchars($clase['asignatura_codigo']) ?></strong></span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <span><?= htmlspecialchars($clase['docente_nombre']) ?></span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-clock"></i>
                    <span><?= date('H:i', strtotime($clase['hora_inicio'])) ?> – <?= date('H:i', strtotime($clase['hora_fin'])) ?></span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-door-open"></i>
                    <span><?= htmlspecialchars($clase['espacio_nombre']) ?> (<?= htmlspecialchars($clase['espacio_codigo']) ?>)</span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-building"></i>
                    <span><?= htmlspecialchars($clase['bloque_nombre']) ?> — <?= htmlspecialchars($clase['sede_nombre']) ?></span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <span><?= htmlspecialchars($clase['docente_codigo']) ?></span>
                </div>
            </div>

            <div class="clase-card-actions">
                <?php if ($estado === 'PENDIENTE'): ?>
                    <a class="btn-auditar" href="?ruta=lider/auditoria/auditar&id_horario=<?= (int)$clase['id_horario'] ?>&fecha=<?= urlencode($fechaSeleccionada) ?>">
                        <i class="fa-solid fa-clipboard-check"></i> Auditar esta clase
                    </a>
                <?php else: ?>
                    <a class="btn-ver-detalle" href="?ruta=lider/auditoria/detalle&id=<?= (int)$clase['id_auditoria'] ?>">
                        <i class="fa-solid fa-eye"></i> Ver auditoría
                    </a>
                    <a class="btn-auditar" href="?ruta=lider/auditoria/auditar&id_horario=<?= (int)$clase['id_horario'] ?>&fecha=<?= urlencode($fechaSeleccionada) ?>" style="background:rgba(59,13,143,.08); color:var(--color-primario); font-size:12px; padding:6px 12px;">
                        <i class="fa-solid fa-pen"></i> Re-auditar
                    </a>
                    <?php if (!empty($clase['auditor_nombre'])): ?>
                        <span class="auditor-info">
                            <i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($clase['auditor_nombre']) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $this->render('elementos_compartidos/footer'); ?>
