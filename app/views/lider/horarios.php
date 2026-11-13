<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Horarios']);
$this->render('elementos_compartidos/sidebar_lider');
$diasSemana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
$tabs = [
    'general' => ['Mi horario', 'fa-table-list'],
    'fet' => ['Generar con FET', 'fa-wand-magic-sparkles'],
    'propuestas' => ['Propuestas FET', 'fa-layer-group'],
    'auditoria' => ['Auditoria', 'fa-clipboard-check']
];
?>
<style>
    .horarios-hero {
        background: linear-gradient(135deg, var(--color-primario) 0%, #200654 100%);
        color: #fff;
        border-radius: 14px;
        padding: 28px 32px;
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);
    }

    .horarios-hero::after {
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

    .horarios-hero h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
    }

    .horarios-hero p {
        color: rgba(255, 255, 255, .82);
        margin: 0;
        font-size: 13.5px;
        position: relative;
        z-index: 1;
    }

    .metric-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(115px, 1fr));
        gap: 10px;
        min-width: min(340px, 100%)
    }

    .metric-tile {
        border: 1px solid rgba(255, 255, 255, .18);
        background: rgba(255, 255, 255, .09);
        border-radius: 8px;
        padding: 12px
    }

    .metric-tile strong {
        display: block;
        font-size: 22px
    }

    .metric-tile span {
        font-size: 12px;
        color: rgba(255, 255, 255, .72)
    }

    .module-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px
    }

    .module-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border: 1px solid var(--color-borde);
        border-radius: 8px;
        background: #fff;
        color: var(--color-texto-secundario);
        font-weight: 600;
        font-size: 13px
    }

    .module-tab.active {
        background: var(--color-primario);
        border-color: var(--color-primario);
        color: #fff
    }

    .assignment-grid,
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
        gap: 14px;
        align-items: end
    }

    .assignment-grid>* {
        min-width: 0
    }

    .section-title {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px
    }

    .section-title h2 {
        font-size: 18px
    }

    .section-title p {
        color: var(--color-texto-secundario);
        font-size: 13px;
        margin: 3px 0 0
    }

    .table-wrap {
        overflow-x: auto
    }

    .actions-inline {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px
    }

    .audit-form {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        min-width: 340px
    }

    @media(max-width:1000px) {
        .horarios-hero {
            flex-direction: column;
            align-items: stretch
        }

        .metric-strip,
        .assignment-grid,
        .filter-grid {
            grid-template-columns: 1fr;
            min-width: 0
        }

        .audit-form {
            grid-template-columns: 1fr;
            min-width: 230px
        }
    }

    /* =========================================================
   REDISEÑO UI/UX: ASIGNAR CLASE (SAAS ACADÉMICO PREMIUM)
   ========================================================= */
    .class-assigner-panel {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--color-borde, #e2e8f0);
        box-shadow: 0 4px 20px rgba(11, 31, 51, 0.04);
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
    }

    .assigner-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .assigner-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--color-primario, #3b0d8f);
        background: rgba(59, 13, 143, 0.08);
        padding: 3px 10px;
        border-radius: 999px;
        margin-bottom: 8px;
    }

    .assigner-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-oscuro, #0b1f33);
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .assigner-header p {
        font-size: 13px;
        color: var(--color-texto-secundario, #5e6d7e);
        margin: 0;
        max-width: 720px;
        line-height: 1.5;
    }

    .assigner-program-badge {
        background: linear-gradient(135deg, rgba(59, 13, 143, 0.06), rgba(59, 13, 143, 0.02));
        border: 1px solid rgba(59, 13, 143, 0.18);
        border-radius: 10px;
        padding: 10px 14px;
        text-align: right;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .assigner-program-badge .apb-label {
        display: block;
        font-size: 11px;
        color: var(--color-texto-secundario, #5e6d7e);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 2px;
    }

    .assigner-program-badge strong {
        font-size: 13.5px;
        color: var(--color-primario, #3b0d8f);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .assigner-blocks-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .assigner-block {
        background: #fafbfd;
        border: 1px solid #eef2f6;
        border-radius: 10px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all .2s ease;
        box-sizing: border-box;
    }

    .assigner-block:hover {
        background: #ffffff;
        border-color: rgba(59, 13, 143, 0.22);
        box-shadow: 0 8px 24px rgba(59, 13, 143, 0.06);
    }

    .block-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf2f7;
    }

    .block-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--color-primario, #3b0d8f);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(59, 13, 143, 0.3);
    }

    .block-title-wrap h3 {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--color-oscuro, #0b1f33);
        margin: 0;
        line-height: 1.3;
    }

    .block-title-wrap p {
        font-size: 11.5px;
        color: var(--color-texto-secundario, #5e6d7e);
        margin: 2px 0 0 0;
    }

    .block-fields {
        display: flex;
        flex-direction: column;
        gap: 14px;
        flex: 1;
    }

    .field-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .field-item label {
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0;
    }

    .field-item label i {
        color: var(--color-primario, #3b0d8f);
        font-size: 12px;
        margin-right: 5px;
    }

    .field-item label .req {
        color: #ef4444;
        font-weight: 700;
        margin-left: 2px;
    }

    .field-spinner {
        font-size: 11px;
        color: var(--color-primario, #3b0d8f);
        margin-left: auto;
    }

    .select-wrapper {
        position: relative;
        width: 100%;
    }

    .select-wrapper select.form-control {
        width: 100%;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 32px;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        height: 40px;
        transition: all .15s ease;
    }

    .select-wrapper select.form-control:focus {
        border-color: var(--color-primario, #3b0d8f);
        box-shadow: 0 0 0 3px rgba(59, 13, 143, 0.12);
        outline: none;
    }

    .select-chevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 11px;
        color: #94a3b8;
        pointer-events: none;
    }

    .days-pills-container {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 5px;
        margin-top: 2px;
    }

    .day-pill-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 6px;
        padding: 7px 2px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        text-align: center;
        transition: all .15s ease;
        user-select: none;
    }

    .day-pill-btn:hover {
        border-color: var(--color-primario, #3b0d8f);
        background: rgba(59, 13, 143, 0.04);
        color: var(--color-primario, #3b0d8f);
    }

    .day-pill-btn.active {
        background: var(--color-primario, #3b0d8f);
        border-color: var(--color-primario, #3b0d8f);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(59, 13, 143, 0.25);
    }

    .hidden-select-accessible {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        padding: 0;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    .time-range-group {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 8px;
        align-items: flex-end;
    }

    .time-range-group input[type="time"].form-control {
        height: 40px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        padding: 6px 10px;
        color: #1e293b;
        background: #ffffff;
    }

    .time-separator {
        color: #94a3b8;
        padding-bottom: 10px;
        font-size: 12px;
        text-align: center;
    }

    .duration-feedback-wrapper {
        margin-top: 4px;
    }

    .duration-badge-idle {
        font-size: 11.5px;
        color: #64748b;
        background: #f1f5f9;
        padding: 8px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .duration-badge-active {
        font-size: 11.5px;
        color: #2e0870;
        background: rgba(59, 13, 143, 0.08);
        border: 1px solid rgba(59, 13, 143, 0.2);
        padding: 8px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .duration-badge-active strong {
        font-weight: 700;
        color: var(--color-primario, #3b0d8f);
    }

    .duration-badge-error {
        font-size: 11.5px;
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 8px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .field-helper {
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
        display: block;
    }

    .form-actions-wrapper {
        margin-top: auto;
        padding-top: 14px;
    }

    .btn-submit-class {
        width: 100%;
        background: linear-gradient(135deg, var(--color-primario, #3b0d8f), #240b5c);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 12px 18px;
        font-size: 13.5px;
        font-weight: 700;
        letter-spacing: .2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s ease;
        box-shadow: 0 4px 14px rgba(59, 13, 143, 0.25);
    }

    .btn-submit-class:hover {
        background: linear-gradient(135deg, #4c11b0, #2c0e70);
        box-shadow: 0 6px 20px rgba(59, 13, 143, 0.35);
        transform: translateY(-1px);
    }

    .btn-submit-class:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
    }

    @media (max-width: 1200px) {
        .assigner-blocks-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    @media (max-width: 680px) {
        .assigner-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .days-pills-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
<div class="horarios-hero">
    <div>
        <h1>Gestion y planificacion de horarios</h1>
        <p><?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?>. Construccion manual, FET y auditoria
            con alcance seguro.</p>
    </div>
    <div class="metric-strip">
        <div class="metric-tile"><strong><?= count($horarios) ?></strong><span>Clases</span></div>
        <div class="metric-tile"><strong><?= count($propuestas) ?></strong><span>Propuestas</span></div>
        <div class="metric-tile"><strong><?= count($auditorias) ?></strong><span>Auditorias</span></div>
    </div>
</div>
<nav class="module-tabs">
    <?php foreach ($tabs as $id => $item): ?><a class="module-tab <?= $tab === $id ? 'active' : '' ?>"
            href="?ruta=lider/horarios&tab=<?= $id ?>"><i
                class="fa-solid <?= $item[1] ?>"></i><?= $item[0] ?></a><?php endforeach; ?>
</nav>
<?php if (!$programaActivo): ?>
    <div class="panel">No tienes programas asignados.</div>
<?php elseif ($tab === 'general'): ?>
    <?php if ($cerrado): ?>
        <div class="panel"
            style="padding:16px;border:1px solid #fecaca;background:#fef2f2;border-radius:10px;color:#991b1b;display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <i class="fa-solid fa-lock" style="font-size:20px;"></i>
            <div>
                <strong style="font-size:14px;">Horario Cerrado para este Periodo</strong>
                <p style="margin:2px 0 0;font-size:13px;color:#b91c1c;">El periodo académico se encuentra formalmente cerrado
                    para el programa. Solo el Administrador puede realizar modificaciones o nuevas asignaciones.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="class-assigner-panel">
            <div class="assigner-header">
                <div class="assigner-header-info">
                    <span class="assigner-eyebrow"><i class="fa-solid fa-graduation-cap"></i> Líder de Programa</span>
                    <h2><i class="fa-solid fa-calendar-plus" style="color:var(--color-primario);"></i>
                        <?= $detalleEditar ? 'Ajustar y Confirmar Clase' : 'Asignar Clase a Horario' ?>
                    </h2>
                    <p>Planifica la sesión académica seleccionando asignaturas ofertadas en tu programa, docentes compatibles y
                        espacios físicos sin cruces de horario.</p>
                </div>
                <div class="assigner-program-badge">
                    <span class="apb-label">Programa Activo</span>
                    <strong><i class="fa-solid fa-bookmark"></i>
                        <?= htmlspecialchars($programaActivo['nombre'] ?? 'Sin programa') ?></strong>
                </div>
            </div>

            <form action="?ruta=lider/horarios/guardar" method="POST" id="form-asignar-clase" class="assigner-form">
                <input type="hidden" name="id_programa" id="id_programa" value="<?= (int) $programaActivo['id_programa'] ?>">
                <input type="hidden" name="id_detalle" value="<?= (int) ($detalleEditar['id_detalle'] ?? 0) ?>">
                <input type="hidden" name="origen" value="<?= $detalleEditar ? 'FET' : 'MANUAL' ?>">

                <div class="assigner-blocks-grid">

                    <!-- BLOQUE 1 — INFORMACIÓN ACADÉMICA -->
                    <div class="assigner-block block-academico">
                        <div>
                            <div class="block-header">
                                <div class="block-badge">1</div>
                                <div class="block-title-wrap">
                                    <h3>Información Académica</h3>
                                    <p>Periodo activo y cátedra ofertada</p>
                                </div>
                            </div>

                            <div class="block-fields">
                                <div class="field-item">
                                    <label for="id_periodo">
                                        <span><i class="fa-regular fa-calendar"></i> Periodo Académico <span
                                                class="req">*</span></span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="id_periodo" id="id_periodo" class="form-control" required>
                                            <?php foreach ($periodos as $p): ?>
                                                <option value="<?= (int) $p['id_periodo'] ?>" <?= (($detalleEditar['id_periodo'] ?? $idPeriodo) == $p['id_periodo']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                </div>

                                <div class="field-item">
                                    <label for="id_asignatura">
                                        <span><i class="fa-solid fa-book-open"></i> Asignatura Ofertada <span
                                                class="req">*</span></span>
                                        <span class="field-spinner" id="spinner-asignatura" style="display:none;"><i
                                                class="fa-solid fa-circle-notch fa-spin"></i></span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="id_asignatura" id="id_asignatura" class="form-control" required>
                                            <option value="">Seleccionar asignatura</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                </div>

                                <div class="field-item">
                                    <label for="id_docente">
                                        <span><i class="fa-solid fa-chalkboard-user"></i> Docente Compatible <span
                                                class="req">*</span></span>
                                        <span class="field-spinner" id="spinner-docente" style="display:none;"><i
                                                class="fa-solid fa-circle-notch fa-spin"></i></span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="id_docente" id="id_docente" class="form-control" required>
                                            <option value="">Selecciona una asignatura</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2 — PROGRAMACIÓN DEL HORARIO -->
                    <div class="assigner-block block-horario">
                        <div>
                            <div class="block-header">
                                <div class="block-badge">2</div>
                                <div class="block-title-wrap">
                                    <h3>Programación del Horario</h3>
                                    <p>Día lectivo, franja y duración</p>
                                </div>
                            </div>

                            <div class="block-fields">
                                <div class="field-item">
                                    <label>
                                        <span><i class="fa-regular fa-calendar-check"></i> Día de la Semana <span
                                                class="req">*</span></span>
                                    </label>
                                    <select name="dia_semana" id="dia_semana" class="form-control hidden-select-accessible"
                                        required>
                                        <option value="">Seleccionar día</option>
                                        <?php foreach ($diasSemana as $dia): ?>
                                            <option value="<?= $dia ?>" <?= (($detalleEditar['dia_semana'] ?? '') === $dia) ? 'selected' : '' ?>><?= $dia ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="days-pills-container" id="days-pills-wrapper">
                                        <?php
                                        $nombresCortos = [
                                            'LUNES' => 'LUN',
                                            'MARTES' => 'MAR',
                                            'MIERCOLES' => 'MIÉ',
                                            'JUEVES' => 'JUE',
                                            'VIERNES' => 'VIE',
                                            'SABADO' => 'SÁB',
                                            'DOMINGO' => 'DOM'
                                        ];
                                        foreach ($diasSemana as $dia):
                                            if ($dia === 'DOMINGO')
                                                continue;
                                            $activo = (($detalleEditar['dia_semana'] ?? '') === $dia);
                                            ?>
                                            <button type="button" class="day-pill-btn <?= $activo ? 'active' : '' ?>"
                                                data-day="<?= $dia ?>">
                                                <?= $nombresCortos[$dia] ?? substr($dia, 0, 3) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="time-range-group">
                                    <div class="field-item">
                                        <label for="hora_inicio">
                                            <span><i class="fa-regular fa-clock"></i> Inicio <span class="req">*</span></span>
                                        </label>
                                        <input type="time" name="hora_inicio" id="hora_inicio" class="form-control"
                                            value="<?= htmlspecialchars(substr($detalleEditar['hora_inicio'] ?? '', 0, 5)) ?>"
                                            required>
                                    </div>
                                    <div class="time-separator">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                    <div class="field-item">
                                        <label for="hora_fin">
                                            <span><i class="fa-regular fa-clock"></i> Fin <span class="req">*</span></span>
                                        </label>
                                        <input type="time" name="hora_fin" id="hora_fin" class="form-control"
                                            value="<?= htmlspecialchars(substr($detalleEditar['hora_fin'] ?? '', 0, 5)) ?>"
                                            required>
                                    </div>
                                </div>

                                <div class="duration-feedback-wrapper">
                                    <div class="duration-badge-idle" id="duration-idle">
                                        <i class="fa-regular fa-clock"></i> Selecciona hora inicio y fin
                                    </div>
                                    <div class="duration-badge-active" id="duration-active" style="display:none;">
                                        <i class="fa-solid fa-stopwatch"></i> Duración: <strong id="duration-text">-</strong>
                                    </div>
                                    <div class="duration-badge-error" id="duration-error" style="display:none;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> La hora fin debe ser posterior a inicio
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 3 — ESPACIO Y CONFIGURACIÓN -->
                    <div class="assigner-block block-espacio">
                        <div>
                            <div class="block-header">
                                <div class="block-badge">3</div>
                                <div class="block-title-wrap">
                                    <h3>Espacio y Registro</h3>
                                    <p>Reserva física sin cruces de horario</p>
                                </div>
                            </div>

                            <div class="block-fields">
                                <div class="field-item">
                                    <label for="id_espacio">
                                        <span><i class="fa-solid fa-door-open"></i> Espacio Físico Libre <span
                                                class="req">*</span></span>
                                        <span class="field-spinner" id="spinner-espacio" style="display:none;"><i
                                                class="fa-solid fa-circle-notch fa-spin"></i></span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="id_espacio" id="id_espacio" class="form-control" required>
                                            <option value="">Completa día y horas para filtrar</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                    <small class="field-helper" id="espacio-helper">
                                        <i class="fa-solid fa-circle-info"></i> Valida en tiempo real que el aula no esté
                                        ocupada.
                                    </small>
                                </div>

                                <?php if (!empty($detalleEditar)): ?>
                                    <div
                                        style="background: rgba(59, 13, 143, 0.05); border: 1px dashed rgba(59, 13, 143, 0.25); border-radius: 8px; padding: 10px 12px; font-size: 12px; color: #3b0d8f;">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> Editando propuesta generada por FET
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-actions-wrapper">
                            <button type="submit" class="btn-submit-class" id="btn-submit-class">
                                <span class="btn-text" id="btn-submit-text">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                    <?= $detalleEditar ? 'Confirmar Propuesta FET' : 'Guardar y Asignar Clase' ?>
                                </span>
                                <span class="btn-loading" id="btn-submit-loading" style="display:none;">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Guardando asignación...
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    <?php endif; ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Horario actual</h2>
                <p>Vista filtrada por tu programa activo.</p>
            </div><?php if (!$cerrado && $idPeriodo): ?><a
                    href="?ruta=lider/horarios/cerrar&periodo_id=<?= (int) $idPeriodo ?>&programa_id=<?= (int) $programaActivo['id_programa'] ?>"
                    class="btn-demo" onclick="return confirmarCerrarHorario(event, this.href);"><i class="fa-solid fa-lock"></i>
                    Cerrar horario</a><?php endif; ?>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Origen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($horarios)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:24px;">No hay horarios registrados.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($horarios as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['periodo']) ?></td>
                            <td><?= htmlspecialchars($h['asignatura']) ?></td>
                            <td><?= htmlspecialchars($h['docente']) ?></td>
                            <td><?= htmlspecialchars($h['espacio']) ?></td>
                            <td><?= htmlspecialchars($h['dia_semana']) ?></td>
                            <td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?>
                            </td>
                            <td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($h['origen']) ?></td>
                            <td><?= ($h['estado'] === 'CERRADO') ? '<span class="badge cerrado">Cerrado</span>' : '<span style="color:var(--color-texto-secundario);font-size:12px;">Admin edita</span>' ?>
                            </td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'fet'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Generar con FET</h2>
                <p>Simulacion scoped al programa activo. La integracion real queda marcada como TODO.</p>
            </div>
        </div>
        <form action="?ruta=lider/horarios/generar_fet" method="POST" class="assignment-grid">
            <input type="hidden" name="id_programa" value="<?= (int) $programaActivo['id_programa'] ?>">
            <div class="form-group"><label>Periodo</label><select name="id_periodo" class="form-control"
                    required><?php foreach ($periodos as $p): ?>
                        <option value="<?= (int) $p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                        </option><?php endforeach; ?>
                </select></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Dias lectivos</label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <?php foreach (array_slice($diasSemana, 0, 6) as $dia): ?><label
                            style="display:inline-flex;gap:6px;align-items:center;font-size:13px;"><input type="checkbox"
                                name="dias[]" value="<?= $dia ?>" checked> <?= $dia ?></label><?php endforeach; ?>
                </div>
            </div>
            <div class="form-group"><button class="btn-primario" type="submit"><i class="fa-solid fa-play"></i> Simular
                    FET</button></div>
        </form>
    </div>
<?php elseif ($tab === 'propuestas'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Propuestas FET</h2>
                <p>Acepta, ajusta o descarta cada linea antes de crear horario real.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detallesPropuestas)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:24px;">No hay lineas FET para revisar.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($detallesPropuestas as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '') . ' ' . ($d['asignatura_nombre'] ?? '')) ?>
                            </td>
                            <td><?= htmlspecialchars($d['docente_nombre']) ?></td>
                            <td><?= htmlspecialchars($d['sede_nombre'] . ' / ' . $d['bloque_nombre'] . ' / ' . $d['espacio_nombre']) ?>
                            </td>
                            <td><?= htmlspecialchars($d['dia_semana']) ?></td>
                            <td><?= date('H:i', strtotime($d['hora_inicio'])) ?> - <?= date('H:i', strtotime($d['hora_fin'])) ?>
                            </td>
                            <td><?= !empty($d['id_horario_resultante']) ? '<span class="badge aceptada">ACEPTADA</span>' : '<span class="badge generada">GENERADA</span>' ?>
                            </td>
                            <td style="text-align:right;"><span
                                    class="actions-inline"><?php if (empty($d['id_horario_resultante'])): ?><a
                                            class="btn-accion btn-accion-aceptar" title="Aceptar"
                                            href="?ruta=lider/horarios/aceptar_fet&id=<?= (int) $d['id_detalle'] ?>&programa_id=<?= (int) $programaActivo['id_programa'] ?>"><i
                                                class="fa-solid fa-check"></i></a><a class="btn-accion btn-accion-editar"
                                            title="Editar"
                                            href="?ruta=lider/horarios&tab=general&editar_detalle=<?= (int) $d['id_detalle'] ?>"><i
                                                class="fa-solid fa-pen"></i></a>
                                        <form action="?ruta=lider/horarios/descartar_fet" method="POST"
                                            onsubmit="return confirmarEliminacion(event, this, 'esta linea FET');"><input
                                                type="hidden" name="programa_id"
                                                value="<?= (int) $programaActivo['id_programa'] ?>"><input type="hidden"
                                                name="id_detalle" value="<?= (int) $d['id_detalle'] ?>"><button
                                                class="btn-accion btn-accion-eliminar" title="Descartar linea"><i
                                                    class="fa-solid fa-trash-can"></i></button></form><?php endif; ?>
                                </span></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Auditoria</h2>
                <p>Registra verificacion de clases de tu programa.</p>
            </div>
        </div>
        <form method="GET" class="filter-grid" style="margin-bottom:16px;"><input type="hidden" name="ruta"
                value="lider/horarios"><input type="hidden" name="tab" value="auditoria">
            <div class="form-group"><label>Fecha</label><input type="date" name="fecha" class="form-control"
                    value="<?= htmlspecialchars($_GET['fecha'] ?? '') ?>"></div>
            <div class="form-group"><label>Sede</label><input name="sede" class="form-control"
                    value="<?= htmlspecialchars($_GET['sede'] ?? '') ?>"></div>
            <div class="form-group"><label>Bloque</label><input name="bloque" class="form-control"
                    value="<?= htmlspecialchars($_GET['bloque'] ?? '') ?>"></div>
            <div class="form-group"><label>Espacio</label><input name="espacio" class="form-control"
                    value="<?= htmlspecialchars($_GET['espacio'] ?? '') ?>"></div>
            <div class="form-group"><label>Docente</label><input name="docente" class="form-control"
                    value="<?= htmlspecialchars($_GET['docente'] ?? '') ?>"></div>
            <div class="form-group"><button class="btn-primario">Filtrar</button></div>
        </form>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Clase</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Horario</th>
                        <th>Resultado</th>
                        <th>Verificacion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditorias)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:24px;">No hay auditorias con estos filtros.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($auditorias as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['fecha_auditoria']) ?></td>
                            <td><?= htmlspecialchars($a['asignatura']) ?></td>
                            <td><?= htmlspecialchars($a['docente']) ?></td>
                            <td><?= htmlspecialchars($a['sede'] . ' / ' . $a['bloque'] . ' / ' . $a['espacio']) ?></td>
                            <td><?= date('H:i', strtotime($a['hora_inicio'])) ?> - <?= date('H:i', strtotime($a['hora_fin'])) ?>
                            </td>
                            <td><span
                                    class="badge <?= strtolower($a['resultado']) ?>"><?= htmlspecialchars($a['resultado']) ?></span>
                            </td>
                            <td>
                                <form class="audit-form" action="?ruta=lider/horarios/verificar_auditoria" method="POST"><input
                                        type="hidden" name="programa_id"
                                        value="<?= (int) $programaActivo['id_programa'] ?>"><input type="hidden"
                                        name="id_auditoria" value="<?= (int) $a['id_auditoria'] ?>"><input class="form-control"
                                        name="observacion" placeholder="Observacion"><button
                                        class="btn-accion btn-accion-aceptar" name="resultado" value="VERIFICADA"
                                        title="Verificada"><i class="fa-solid fa-check"></i></button><button
                                        class="btn-accion btn-accion-eliminar" name="resultado" value="NO_REALIZADA"
                                        title="No realizada"><i class="fa-solid fa-xmark"></i></button></form>
                            </td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const prefill = <?= json_encode($detalleEditar ?: []) ?>;
        const periodo = document.getElementById('id_periodo'),
            programa = document.getElementById('id_programa'),
            asignatura = document.getElementById('id_asignatura'),
            docente = document.getElementById('id_docente'),
            dia = document.getElementById('dia_semana'),
            inicio = document.getElementById('hora_inicio'),
            fin = document.getElementById('hora_fin'),
            espacio = document.getElementById('id_espacio'),
            formAsignar = document.getElementById('form-asignar-clase'),
            btnSubmit = document.getElementById('btn-submit-class'),
            btnText = document.getElementById('btn-submit-text'),
            btnLoading = document.getElementById('btn-submit-loading');

        // Spinners
        const spinAsig = document.getElementById('spinner-asignatura');
        const spinDoc = document.getElementById('spinner-docente');
        const spinEsp = document.getElementById('spinner-espacio');

        // Duración
        const durIdle = document.getElementById('duration-idle');
        const durActive = document.getElementById('duration-active');
        const durError = document.getElementById('duration-error');
        const durText = document.getElementById('duration-text');

        // Day pills
        const dayPills = document.querySelectorAll('.day-pill-btn');
        dayPills.forEach(pill => {
            pill.addEventListener('click', () => {
                const targetDay = pill.dataset.day;
                if (dia) {
                    dia.value = targetDay;
                    dia.dispatchEvent(new Event('change'));
                }
                dayPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
            });
        });

        if (dia) {
            dia.addEventListener('change', () => {
                dayPills.forEach(p => {
                    if (p.dataset.day === dia.value) p.classList.add('active');
                    else p.classList.remove('active');
                });
            });
            if (dia.value) {
                dayPills.forEach(p => {
                    if (p.dataset.day === dia.value) p.classList.add('active');
                });
            }
        }

        // Calculador de duración de sesión
        const updateDuracion = () => {
            if (!inicio || !fin || !durIdle || !durActive || !durError || !durText) return;
            const vIni = inicio.value;
            const vFin = fin.value;
            if (!vIni || !vFin) {
                durIdle.style.display = 'flex';
                durActive.style.display = 'none';
                durError.style.display = 'none';
                return;
            }
            const [h1, m1] = vIni.split(':').map(Number);
            const [h2, m2] = vFin.split(':').map(Number);
            const t1 = h1 * 60 + m1;
            const t2 = h2 * 60 + m2;
            const diff = t2 - t1;

            if (diff <= 0) {
                durIdle.style.display = 'none';
                durActive.style.display = 'none';
                durError.style.display = 'flex';
            } else {
                durIdle.style.display = 'none';
                durError.style.display = 'none';
                durActive.style.display = 'flex';
                const horas = Math.floor(diff / 60);
                const mins = diff % 60;
                let texto = '';
                if (horas > 0 && mins === 0) {
                    texto = horas === 1 ? '1 hora académica' : `${horas} horas académicas`;
                } else if (horas === 0) {
                    texto = `${mins} minutos`;
                } else {
                    texto = `${horas} h ${mins} min`;
                }
                durText.textContent = texto;
            }
        };
        [inicio, fin].forEach(el => el && el.addEventListener('input', updateDuracion));
        [inicio, fin].forEach(el => el && el.addEventListener('change', updateDuracion));
        updateDuracion();

        // Prevención de envíos duplicados
        if (formAsignar) {
            formAsignar.addEventListener('submit', (e) => {
                if (!dia || !dia.value) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Día no seleccionado',
                            text: 'Por favor selecciona un día de la semana para la clase.',
                            confirmButtonColor: '#3B0D8F'
                        });
                    } else {
                        alert('Por favor selecciona un día de la semana para la clase.');
                    }
                    return;
                }
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    if (btnText) btnText.style.display = 'none';
                    if (btnLoading) btnLoading.style.display = 'inline-flex';
                }
            });
        }

        const fill = (select, rows, placeholder, mapper, selected) => {
            if (!select) return;
            select.innerHTML = `<option value="">${placeholder}</option>`;
            rows.forEach(row => {
                const data = mapper(row);
                const option = document.createElement('option');
                option.value = data.value;
                option.textContent = data.label;
                if (String(selected || '') === String(data.value)) option.selected = true;
                select.appendChild(option);
            });
        };

        const loadAsignaturas = async () => {
            if (!periodo || !programa || !periodo.value) return;
            if (spinAsig) spinAsig.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=lider/horarios/api_asignaturas&periodo_id=${periodo.value}&programa_id=${programa.value}`);
                fill(asignatura, await res.json(), 'Seleccionar asignatura', row => ({ value: row.id_asignatura, label: `${row.codigo} - ${row.nombre}` }), prefill.id_asignatura);
                await loadDocentes();
            } finally {
                if (spinAsig) spinAsig.style.display = 'none';
            }
        };

        const loadDocentes = async () => {
            if (!asignatura || !asignatura.value) return;
            if (spinDoc) spinDoc.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=lider/horarios/api_docentes&asignatura_id=${asignatura.value}&programa_id=${programa.value}`);
                fill(docente, await res.json(), 'Seleccionar docente', row => ({ value: row.id_docente, label: `${row.apellido} ${row.nombre}` }), prefill.id_docente);
            } finally {
                if (spinDoc) spinDoc.style.display = 'none';
            }
        };

        const loadEspacios = async () => {
            if (!periodo || !dia || !inicio || !fin || !periodo.value || !dia.value || !inicio.value || !fin.value) return;
            if (spinEsp) spinEsp.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=lider/horarios/api_espacios&periodo_id=${periodo.value}&dia_semana=${dia.value}&hora_inicio=${inicio.value}&hora_fin=${fin.value}`);
                fill(espacio, await res.json(), 'Seleccionar espacio libre', row => ({ value: row.id_espacio, label: `${row.sede_nombre} / ${row.bloque_nombre} / ${row.nombre}` }), prefill.id_espacio);
            } finally {
                if (spinEsp) spinEsp.style.display = 'none';
            }
        };

        periodo && periodo.addEventListener('change', loadAsignaturas);
        asignatura && asignatura.addEventListener('change', loadDocentes);
        [periodo, dia, inicio, fin].forEach(el => el && el.addEventListener('change', loadEspacios));
        loadAsignaturas().then(loadEspacios);
    });
</script>
<?php $this->render('elementos_compartidos/footer'); ?>