<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Detalle de Auditoría']);
$this->render('elementos_compartidos/sidebar_lider');

$diasEsp = [
    'LUNES' => 'Lunes', 'MARTES' => 'Martes', 'MIERCOLES' => 'Miércoles',
    'JUEVES' => 'Jueves', 'VIERNES' => 'Viernes', 'SABADO' => 'Sábado', 'DOMINGO' => 'Domingo'
];
$diaLabel = $diasEsp[$auditoria['dia_semana']] ?? $auditoria['dia_semana'];
$fechaFormateada = date('d/m/Y', strtotime($auditoria['fecha_auditoria']));
$esConNovedad = ($auditoria['resultado'] === 'CON_NOVEDAD');
$esSinNovedad = ($auditoria['resultado'] === 'SIN_NOVEDAD');
?>

<style>
.detalle-container {
    max-width: 820px;
    margin: 0 auto;
    position: relative;
}
.detalle-container::before {
    content: '';
    position: fixed;
    right: 40px; bottom: 40px;
    width: 280px; height: 280px;
    background: url('assets/img/isotipo-color.png') no-repeat center/contain;
    opacity: 0.03;
    pointer-events: none;
}
.detalle-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--color-primario);
    margin-bottom: 18px;
    text-decoration: none;
    transition: gap .2s;
}
.detalle-back:hover { gap: 10px; text-decoration: none; }

/* Result banner */
.resultado-banner {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    border-radius: 12px;
    margin-bottom: 20px;
}
.resultado-banner.sin-novedad {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border: 1px solid #6ee7b7;
}
.resultado-banner.con-novedad {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border: 1px solid #fca5a5;
}
.resultado-banner .result-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.sin-novedad .result-icon { background: rgba(16,185,129,.18); color: #059669; }
.con-novedad .result-icon { background: rgba(239,68,68,.15); color: #dc2626; }
.resultado-banner h2 { font-size: 18px; font-weight: 700; margin: 0 0 2px; }
.resultado-banner p { font-size: 13px; margin: 0; }
.sin-novedad h2 { color: #065f46; }
.con-novedad h2 { color: #991b1b; }
.sin-novedad p { color: #047857; }
.con-novedad p { color: #b91c1c; }

/* Cards */
.detalle-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 22px 24px;
    margin-bottom: 18px;
    box-shadow: var(--sombra-tarjeta);
}
.detalle-card h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-oscuro);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
}
.detalle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.detalle-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
}
.detalle-item i {
    width: 28px; height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(59,13,143,.07);
    color: var(--color-primario);
    border-radius: 6px;
    font-size: 13px;
    flex-shrink: 0;
}
.detalle-item .d-label { font-size: 11px; color: var(--color-texto-secundario); }
.detalle-item .d-value { font-weight: 600; color: var(--color-oscuro); }

/* Checklist result */
.checklist-result-grid { display: flex; flex-direction: column; gap: 8px; }
.chk-result-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: 13.5px;
    background: var(--color-superficie);
}
.chk-result-item.cumple { background: rgba(16,185,129,.05); }
.chk-result-item.no-cumple { background: rgba(239,68,68,.04); }
.chk-status-icon {
    width: 24px; height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    margin-top: 1px;
}
.cumple .chk-status-icon { background: #d1fae5; color: #059669; }
.no-cumple .chk-status-icon { background: #fee2e2; color: #dc2626; }
.chk-result-info { flex: 1; }
.chk-result-info strong { color: var(--color-oscuro); }
.chk-result-info .obs-text { font-size: 12.5px; color: var(--color-texto-secundario); margin-top: 3px; font-style: italic; }

/* Observaciones */
.obs-block {
    padding: 16px 18px;
    background: var(--color-superficie);
    border-left: 4px solid var(--color-primario);
    border-radius: 0 8px 8px 0;
    font-size: 14px;
    color: var(--color-texto-principal);
    line-height: 1.6;
}

/* Meta info */
.meta-audit-info {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 13px;
    color: var(--color-texto-secundario);
    padding: 14px 0 0;
    border-top: 1px solid #f1f5f9;
}
.meta-audit-info i { margin-right: 4px; color: var(--color-primario); opacity: .6; }

.detalle-actions {
    display: flex;
    gap: 12px;
    padding-top: 8px;
}

@media (max-width: 700px) {
    .detalle-grid { grid-template-columns: 1fr; }
}
</style>

<div class="detalle-container">
    <a href="?ruta=lider/auditoria&fecha=<?= urlencode($auditoria['fecha_auditoria']) ?>&programa_id=<?= (int)$auditoria['id_programa'] ?>" class="detalle-back">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>

    <!-- Result banner -->
    <div class="resultado-banner <?= $esConNovedad ? 'con-novedad' : 'sin-novedad' ?>">
        <div class="result-icon">
            <i class="fa-solid <?= $esConNovedad ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
        </div>
        <div>
            <h2><?= $esConNovedad ? 'Auditoría con Novedades' : 'Auditoría Sin Novedades' ?></h2>
            <p><?= $esConNovedad ? 'Se encontraron incidencias durante la verificación de esta clase.' : 'La clase se desarrolló con total normalidad según la auditoría.' ?></p>
        </div>
    </div>

    <!-- Class info -->
    <div class="detalle-card">
        <h3><i class="fa-solid fa-info-circle" style="color: var(--color-primario);"></i> Información de la Clase</h3>
        <div class="detalle-grid">
            <div class="detalle-item">
                <i class="fa-solid fa-book"></i>
                <div><div class="d-label">Asignatura</div><div class="d-value"><?= htmlspecialchars($auditoria['asignatura_nombre']) ?> (<?= htmlspecialchars($auditoria['asignatura_codigo']) ?>)</div></div>
            </div>
            <div class="detalle-item">
                <i class="fa-solid fa-chalkboard-user"></i>
                <div><div class="d-label">Docente</div><div class="d-value"><?= htmlspecialchars($auditoria['docente_nombre']) ?></div></div>
            </div>
            <div class="detalle-item">
                <i class="fa-solid fa-clock"></i>
                <div><div class="d-label">Horario</div><div class="d-value"><?= date('H:i', strtotime($auditoria['hora_inicio'])) ?> – <?= date('H:i', strtotime($auditoria['hora_fin'])) ?></div></div>
            </div>
            <div class="detalle-item">
                <i class="fa-solid fa-door-open"></i>
                <div><div class="d-label">Espacio</div><div class="d-value"><?= htmlspecialchars($auditoria['espacio_nombre']) ?> (<?= htmlspecialchars($auditoria['espacio_codigo']) ?>)</div></div>
            </div>
            <div class="detalle-item">
                <i class="fa-solid fa-building"></i>
                <div><div class="d-label">Ubicación</div><div class="d-value"><?= htmlspecialchars($auditoria['sede_nombre']) ?> — <?= htmlspecialchars($auditoria['bloque_nombre']) ?></div></div>
            </div>
            <div class="detalle-item">
                <i class="fa-solid fa-calendar"></i>
                <div><div class="d-label">Fecha</div><div class="d-value"><?= $diaLabel ?>, <?= $fechaFormateada ?></div></div>
            </div>
        </div>
    </div>

    <!-- Checklist -->
    <?php if (!empty($auditoria['detalles'])): ?>
    <div class="detalle-card">
        <h3><i class="fa-solid fa-list-check" style="color: var(--color-secundario);"></i> Lista de Verificación</h3>
        <div class="checklist-result-grid">
            <?php foreach ($auditoria['detalles'] as $det): ?>
                <div class="chk-result-item <?= !empty($det['cumple']) ? 'cumple' : 'no-cumple' ?>">
                    <div class="chk-status-icon">
                        <i class="fa-solid <?= !empty($det['cumple']) ? 'fa-check' : 'fa-xmark' ?>"></i>
                    </div>
                    <div class="chk-result-info">
                        <strong><?= htmlspecialchars($det['criterio']) ?></strong>
                        <?php if (!empty($det['observacion'])): ?>
                            <div class="obs-text"><i class="fa-solid fa-comment-dots"></i> <?= htmlspecialchars($det['observacion']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Observaciones -->
    <?php if (!empty($auditoria['observacion'])): ?>
    <div class="detalle-card">
        <h3><i class="fa-solid fa-comment-dots" style="color: var(--color-acento);"></i> Observaciones Generales</h3>
        <div class="obs-block">
            <?= nl2br(htmlspecialchars($auditoria['observacion'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Audit metadata -->
    <div class="detalle-card">
        <h3><i class="fa-solid fa-user-shield" style="color: var(--color-primario);"></i> Información del Auditor</h3>
        <div class="meta-audit-info">
            <span><i class="fa-solid fa-user"></i> <strong><?= htmlspecialchars($auditoria['auditor_nombre']) ?></strong></span>
            <?php if (!empty($auditoria['auditor_correo'])): ?>
                <span><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($auditoria['auditor_correo']) ?></span>
            <?php endif; ?>
            <span><i class="fa-solid fa-calendar-check"></i> <?= htmlspecialchars($auditoria['fecha_auditoria']) ?></span>
            <?php if (!empty($auditoria['hora_verificacion'])): ?>
                <span><i class="fa-solid fa-clock"></i> Hora: <?= date('H:i', strtotime($auditoria['hora_verificacion'])) ?></span>
            <?php endif; ?>
            <span><i class="fa-solid fa-server"></i> Registrada: <?= htmlspecialchars($auditoria['fecha_registro']) ?></span>
        </div>
    </div>

    <!-- Actions -->
    <div class="detalle-actions">
        <a href="?ruta=lider/auditoria&fecha=<?= urlencode($auditoria['fecha_auditoria']) ?>&programa_id=<?= (int)$auditoria['id_programa'] ?>" class="btn-demo" style="padding: 10px 18px; font-size: 14px;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
        <a href="?ruta=lider/auditoria/auditar&id_horario=<?= (int)$auditoria['id_horario'] ?>&fecha=<?= urlencode($auditoria['fecha_auditoria']) ?>" class="btn-primario" style="padding: 10px 18px; font-size: 14px;">
            <i class="fa-solid fa-pen"></i> Re-auditar
        </a>
    </div>
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
