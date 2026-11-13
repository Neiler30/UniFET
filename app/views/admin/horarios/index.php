<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Horarios']);
$this->render('elementos_compartidos/sidebar_admin');

$tab = $tab ?? 'general';
$diasSemana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
$estadosHorario = ['BORRADOR', 'PROPUESTA', 'CONFIRMADO', 'CERRADO', 'MODIFICADO'];
$origenes = ['MANUAL', 'FET', 'HIBRIDO'];
$tabsHorario = [
    ['id' => 'general', 'label' => 'Listado y Asignación', 'icon' => 'fa-table-list'],
    ['id' => 'ver_horario', 'label' => 'Ver Horario Semanal', 'icon' => 'fa-calendar-week'],
    ['id' => 'fet', 'label' => 'Generar con FET', 'icon' => 'fa-wand-magic-sparkles'],
    ['id' => 'propuestas', 'label' => 'Propuestas FET', 'icon' => 'fa-layer-group'],
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
        font-size: 13.5px;
        max-width: 760px;
        position: relative;
        z-index: 1;
        margin: 0;
    }

    .metric-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        min-width: min(340px, 100%);
    }

    .metric-tile {
        border: 1px solid rgba(255, 255, 255, .18);
        background: rgba(255, 255, 255, .09);
        border-radius: 8px;
        padding: 12px;
    }

    .metric-tile strong {
        display: block;
        font-size: 22px;
    }

    .metric-tile span {
        font-size: 12px;
        color: rgba(255, 255, 255, .72);
    }

    .module-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
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
        font-size: 13px;
    }

    .module-tab:hover {
        text-decoration: none;
        background: rgba(59, 13, 143, .04);
    }

    .module-tab.active {
        background: var(--color-primario);
        border-color: var(--color-primario);
        color: #fff;
    }

    .filter-grid,
    .assignment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
        gap: 14px;
        align-items: end;
    }

    .filter-grid>*,
    .assignment-grid>* {
        min-width: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .section-title h2 {
        font-size: 18px;
        color: var(--color-oscuro);
    }

    .section-title p {
        font-size: 13px;
        color: var(--color-texto-secundario);
        margin-top: 3px;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .code-pill {
        font-family: monospace;
        background: #f1f3f5;
        padding: 3px 7px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .actions-inline {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .audit-form {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        min-width: 360px;
    }

    @media (max-width: 1000px) {
        .horarios-hero {
            flex-direction: column;
            align-items: stretch;
        }

        .metric-strip,
        .filter-grid,
        .assignment-grid {
            grid-template-columns: 1fr;
            min-width: 0;
        }

        .audit-form {
            grid-template-columns: 1fr;
            min-width: 240px;
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

    /* Grilla de 3 Bloques */
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

    /* Selector Visual de Días */
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

    /* Rango de Horas */
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

    /* Feedback de Duración */
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

    /* Bloque 3: Estado y Origen */
    .config-row-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .field-helper {
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
        display: block;
    }

    /* Botón Guardar Clase */
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

    /* Responsividad */
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

    /* Estilos Cuadrícula de Horarios */
    .schedule-grid-wrap {
        overflow-x: auto;
        background: #fff;
        border-radius: 10px;
        border: 1px solid var(--color-borde);
        padding: 14px;
        margin-top: 14px;
    }
    .schedule-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 5px;
        table-layout: fixed;
    }
    .schedule-table thead th {
        background: var(--color-oscuro, #0B1F33);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 8px;
        text-align: center;
        border-radius: 6px;
        letter-spacing: .3px;
        border: none;
    }
    .schedule-table thead th.hour-header {
        width: 135px;
        background: #1e1b4b;
    }
    .schedule-time-cell {
        font-size: 11.5px;
        color: var(--color-oscuro);
        font-weight: 700;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        text-align: center;
        padding: 6px;
        vertical-align: middle;
        white-space: nowrap;
        width: 135px;
    }
    .schedule-slot {
        vertical-align: top;
        padding: 2px !important;
        height: 64px;
        box-sizing: border-box;
    }
    .schedule-slot-free {
        height: 100%;
        min-height: 58px;
        border-radius: 6px;
        border: 1px dashed #e2e8f0;
        background: #fafbfc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #94a3b8;
        transition: all .15s ease;
    }
    .schedule-slot-free:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .schedule-card {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        background: #f5f3ff;
        border: 1.5px solid #ddd6fe;
        border-left: 4px solid var(--color-primario);
        border-radius: 8px;
        padding: 8px 10px;
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 4px;
        transition: all .2s ease;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        position: relative;
    }
    .schedule-card:hover {
        background: #ede9fe;
        border-color: var(--color-primario);
        box-shadow: 0 6px 16px rgba(59,13,143,.15);
        transform: translateY(-1px);
    }
    .schedule-card.tipo-lab {
        background: #f0fdf4;
        border-color: #bbf7d0;
        border-left-color: #10b981;
    }
    .schedule-card.tipo-lab:hover {
        background: #dcfce7;
    }
    .schedule-card-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--color-oscuro);
        line-height: 1.3;
        margin-bottom: 3px;
    }
    .schedule-card-meta {
        font-size: 11px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .schedule-card-badge {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        background: rgba(59,13,143,0.1);
        color: var(--color-primario);
        width: fit-content;
    }
    .schedule-card.tipo-lab .schedule-card-badge {
        background: rgba(16,185,129,0.15);
        color: #047857;
    }

    /* Contenedor de ranura con múltiples clases */
    .schedule-multi-container {
        display: flex;
        flex-direction: column;
        gap: 6px;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }
    .schedule-simultaneous-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .schedule-simultaneous-header:hover {
        background: #fde68a;
        transform: translateY(-1px);
    }
    .schedule-simultaneous-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .schedule-card.clickable-card {
        cursor: pointer;
    }
    .schedule-card.clickable-card:hover {
        transform: translateY(-2px);
    }
    .click-hint {
        font-size: 9.5px;
        color: var(--color-texto-secundario);
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0.85;
    }

    /* Modal de detalle de asignaturas / clases */
    .occupied-swal-container { text-align: left !important; }
    .occupied-detail { text-align: left; color: #0B1F33; font-family: inherit; }
    .occupied-header {
        display: grid;
        grid-template-columns: 56px 1fr;
        gap: 14px;
        align-items: center;
        padding-bottom: 14px;
        border-bottom: 1px solid #E2E5E9;
    }
    .modal-logo {
        width: 56px; height: 56px;
        border-radius: 12px;
        background: rgba(59,13,143,.08);
        display: grid; place-items: center;
    }
    .modal-logo img { max-width: 38px; max-height: 38px; }
    .occupied-header h2 { font-size: 19px; margin: 0 0 4px; color: #0B1F33; line-height: 1.25; font-weight: 700; }
    .occupied-header p { margin: 0; color: #637083; font-size: 13px; font-weight: 500; }
    .detail-chips { display: flex; gap: 8px; flex-wrap: wrap; margin: 14px 0; }
    .detail-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F6F7F9;
        border: 1px solid #E2E5E9;
        border-radius: 999px;
        padding: 5px 11px;
        font-size: 12px;
        font-weight: 600;
        color: #4B5563;
    }
    .detail-chip.chip-primary {
        background: rgba(59,13,143,0.08);
        border-color: rgba(59,13,143,0.25);
        color: var(--color-primario);
    }
    .detail-chip.chip-success {
        background: rgba(16,185,129,0.1);
        border-color: rgba(16,185,129,0.25);
        color: #047857;
    }
    .detail-sections {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .detail-section {
        border: 1px solid #E2E5E9;
        border-radius: 8px;
        padding: 12px 14px;
        background: #fff;
    }
    .detail-section h3 {
        font-size: 12px;
        text-transform: uppercase;
        color: #637083;
        letter-spacing: .5px;
        margin: 0 0 10px;
        font-weight: 700;
    }
    .detail-list { display: grid; gap: 9px; }
    .detail-item { display: grid; gap: 2px; font-size: 13px; }
    .detail-item span { color: #637083; font-size: 11.5px; }
    .detail-item strong { color: #0B1F33; font-weight: 600; line-height: 1.35; }
    .occupied-modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(11,31,51,.46);
        z-index: 2000;
        display: grid; place-items: center;
        padding: 18px;
    }
    .occupied-modal {
        width: min(740px, 100%);
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 18px 60px rgba(11,31,51,.22);
    }
    .occupied-modal-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    @media (max-width: 700px) {
        .detail-sections { grid-template-columns: 1fr; }
        .occupied-header { grid-template-columns: 1fr; }
    }
</style>

<div class="horarios-hero">
    <div>
        <h1>Gestion y planificacion de horarios</h1>
        <p>Administra clases manuales, propuestas simuladas de FET y visualiza la cuadrícula de horarios por día.</p>
    </div>
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
        <div class="metric-strip">
            <div class="metric-tile"><strong><?= count($horarios ?? []) ?></strong><span>Horarios filtrados</span></div>
            <div class="metric-tile"><strong><?= count($propuestasFetGeneradas ?? []) ?></strong><span>Propuestas FET</span></div>
        </div>
        <a class="btn-primario" href="?ruta=admin/horarios&tab=ver_horario" style="background:#fff; color:var(--color-primario); font-weight:700; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.15); display:inline-flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-calendar-days" style="color:var(--color-primario);"></i> Ver Horario Semanal
        </a>
    </div>
</div>

<nav class="module-tabs" aria-label="Tabs de horarios">
    <?php foreach ($tabsHorario as $item): ?>
        <a class="module-tab <?= $tab === $item['id'] ? 'active' : '' ?>"
            href="?ruta=admin/horarios&tab=<?= htmlspecialchars($item['id']) ?>">
            <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i>
            <?= htmlspecialchars($item['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($tab === 'general'): ?>
    <div class="class-assigner-panel">
        <div class="assigner-header">
            <div class="assigner-header-info">
                <span class="assigner-eyebrow"><i class="fa-solid fa-graduation-cap"></i> Planificación Académica</span>
                <h2><i class="fa-solid fa-calendar-plus" style="color:var(--color-primario);"></i> Asignar Nueva Clase</h2>
                <p>Configura y programa la sesión académica seleccionando la oferta curricular, asignando el docente compatible y reservando el espacio libre correspondiente.</p>
            </div>
        </div>

        <form action="?ruta=admin/horarios/guardar" method="POST" id="form-asignar-clase" class="assigner-form">
            <div class="assigner-blocks-grid">
                
                <!-- BLOQUE 1 — INFORMACIÓN ACADÉMICA -->
                <div class="assigner-block block-academico">
                    <div>
                        <div class="block-header">
                            <div class="block-badge">1</div>
                            <div class="block-title-wrap">
                                <h3>Información Académica</h3>
                                <p>Periodo, programa y cátedra ofertada</p>
                            </div>
                        </div>

                        <div class="block-fields">
                            <div class="field-item">
                                <label for="id_periodo">
                                    <span><i class="fa-regular fa-calendar"></i> Periodo Académico <span class="req">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="id_periodo" id="id_periodo" class="form-control" required>
                                        <?php foreach ($periodos as $p): ?>
                                            <option value="<?= (int) $p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-chevron-down select-chevron"></i>
                                </div>
                            </div>

                            <div class="field-item">
                                <label for="id_programa">
                                    <span><i class="fa-solid fa-folder-tree"></i> Programa Académico <span class="req">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="id_programa" id="id_programa" class="form-control" required>
                                        <option value="">Seleccionar programa</option>
                                        <?php foreach ($programas as $p): ?>
                                            <option value="<?= (int) $p['id_programa'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-chevron-down select-chevron"></i>
                                </div>
                            </div>

                            <div class="field-item">
                                <label for="id_asignatura">
                                    <span><i class="fa-solid fa-book-open"></i> Asignatura Ofertada <span class="req">*</span></span>
                                    <span class="field-spinner" id="spinner-asignatura" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="id_asignatura" id="id_asignatura" class="form-control" required>
                                        <option value="">Selecciona periodo y programa</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down select-chevron"></i>
                                </div>
                            </div>

                            <div class="field-item">
                                <label for="id_docente">
                                    <span><i class="fa-solid fa-chalkboard-user"></i> Docente Compatible <span class="req">*</span></span>
                                    <span class="field-spinner" id="spinner-docente" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
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
                                <p>Día lectivo, franja horaria y duración</p>
                            </div>
                        </div>

                        <div class="block-fields">
                            <div class="field-item">
                                <label>
                                    <span><i class="fa-regular fa-calendar-check"></i> Día de la Semana <span class="req">*</span></span>
                                </label>
                                <select name="dia_semana" id="dia_semana" class="form-control hidden-select-accessible" required>
                                    <option value="">Seleccionar día</option>
                                    <?php foreach ($diasSemana as $dia): ?>
                                        <option value="<?= $dia ?>"><?= $dia ?></option>
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
                                        if ($dia === 'DOMINGO') continue; // Lunes a Sábado institucional
                                    ?>
                                        <button type="button" class="day-pill-btn" data-day="<?= $dia ?>">
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
                                    <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" required>
                                </div>
                                <div class="time-separator">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                                <div class="field-item">
                                    <label for="hora_fin">
                                        <span><i class="fa-regular fa-clock"></i> Fin <span class="req">*</span></span>
                                    </label>
                                    <input type="time" name="hora_fin" id="hora_fin" class="form-control" required>
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
                                <h3>Espacio y Configuración</h3>
                                <p>Infraestructura y estado del registro</p>
                            </div>
                        </div>

                        <div class="block-fields">
                            <div class="field-item">
                                <label for="id_espacio">
                                    <span><i class="fa-solid fa-door-open"></i> Espacio Físico Libre <span class="req">*</span></span>
                                    <span class="field-spinner" id="spinner-espacio" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="id_espacio" id="id_espacio" class="form-control" required>
                                        <option value="">Completa día y horas para filtrar</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down select-chevron"></i>
                                </div>
                                <small class="field-helper" id="espacio-helper">
                                    <i class="fa-solid fa-circle-info"></i> Disponibilidad en tiempo real sin cruces.
                                </small>
                            </div>

                            <div class="config-row-2col">
                                <div class="field-item">
                                    <label for="estado">
                                        <span><i class="fa-solid fa-shield-halved"></i> Estado</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="estado" id="estado" class="form-control">
                                            <?php foreach ($estadosHorario as $estado): ?>
                                                <option value="<?= $estado ?>" <?= $estado === 'CONFIRMADO' ? 'selected' : '' ?>><?= $estado ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                </div>

                                <div class="field-item">
                                    <label for="origen">
                                        <span><i class="fa-solid fa-tag"></i> Origen</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="origen" id="origen" class="form-control">
                                            <?php foreach ($origenes as $origen): ?>
                                                <option value="<?= $origen ?>"><?= $origen ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fa-solid fa-chevron-down select-chevron"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-wrapper">
                        <button type="submit" class="btn-submit-class" id="btn-submit-class">
                            <span class="btn-text" id="btn-submit-text">
                                <i class="fa-solid fa-calendar-plus"></i> Guardar y Asignar Clase
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

    <div class="panel" style="border-radius:14px; box-shadow:var(--sombra-tarjeta); border:1px solid var(--color-borde); padding:24px;">
        <div class="section-title" style="margin-bottom:18px;">
            <div>
                <h2 style="font-size:18px; font-weight:700; color:var(--color-oscuro); margin:0 0 4px 0;">
                    <i class="fa-solid fa-list-check" style="color:var(--color-primario); margin-right:8px;"></i> Listado General de Clases
                </h2>
                <p style="color:var(--color-texto-secundario); font-size:13px; margin:0;">
                    Planificación académica consolidada en tiempo real.
                </p>
            </div>
            <a href="?ruta=admin/horarios&tab=ver_horario" class="btn-primario" style="font-size:13px; padding:9px 16px; display:inline-flex; align-items:center; gap:8px; border-radius:8px;">
                <i class="fa-solid fa-calendar-week"></i> Ver en Cuadrícula Semanal
            </a>
        </div>

        <form action="" method="GET" class="filter-grid" style="margin-bottom:20px; background:#F8FAFC; padding:16px; border-radius:10px; border:1px solid #E2E8F0;">
            <input type="hidden" name="ruta" value="admin/horarios">
            <input type="hidden" name="tab" value="general">
            <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px; font-weight:600; color:var(--color-texto-secundario); margin-bottom:4px; display:block;">Programa</label>
                <input type="text" name="programa" class="form-control" placeholder="Nombre o código..." value="<?= htmlspecialchars($filtros['programa'] ?? '') ?>" style="height:38px;">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px; font-weight:600; color:var(--color-texto-secundario); margin-bottom:4px; display:block;">Docente</label>
                <input type="text" name="docente" class="form-control" placeholder="Docente..." value="<?= htmlspecialchars($filtros['docente'] ?? '') ?>" style="height:38px;">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px; font-weight:600; color:var(--color-texto-secundario); margin-bottom:4px; display:block;">Espacio</label>
                <input type="text" name="espacio" class="form-control" placeholder="Aula, bloque o sede..." value="<?= htmlspecialchars($filtros['espacio'] ?? '') ?>" style="height:38px;">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px; font-weight:600; color:var(--color-texto-secundario); margin-bottom:4px; display:block;">Estado</label>
                <select name="estado" class="form-control" style="height:38px;">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estadosHorario as $estado): ?>
                        <option value="<?= $estado ?>" <?= (($filtros['estado'] ?? '') === $estado) ? 'selected' : '' ?>>
                            <?= $estado ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0; display:flex; gap:8px;">
                <button class="btn-primario" type="submit" style="height:38px; padding:0 18px; font-size:13px; font-weight:600;">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <a href="?ruta=admin/horarios" class="btn-demo" style="height:38px; padding:0 14px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </form>

        <div class="table-wrap">
            <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
                <thead>
                    <tr style="background:#F8FAFC;">
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Asignatura</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Programa / Facultad</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Docente</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Espacio y Ubicación</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Horario</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde);">Estado</th>
                        <th style="padding:12px 14px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; color:var(--color-oscuro); border-bottom:2px solid var(--color-borde); text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($horarios)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:36px; color:var(--color-texto-secundario);">
                                <i class="fa-regular fa-calendar-xmark" style="font-size:32px; opacity:.4; display:block; margin-bottom:10px;"></i>
                                No se encontraron clases con los filtros seleccionados.
                            </td>
                        </tr>
                    <?php else:
                        foreach ($horarios as $h): ?>
                            <tr style="transition:background .15s;">
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9;">
                                    <strong><?= htmlspecialchars($h['asignatura'] ?? '') ?></strong>
                                    <div style="font-size:11px; color:var(--color-texto-secundario); margin-top:2px;">
                                        Periodo: <span class="code-pill" style="font-size:11px; padding:1px 6px;"><?= htmlspecialchars($h['periodo'] ?? '') ?></span>
                                    </div>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9;">
                                    <div style="font-weight:600; color:var(--color-oscuro);"><?= htmlspecialchars($h['programa'] ?? '') ?></div>
                                    <div style="font-size:11.5px; color:var(--color-texto-secundario);"><?= htmlspecialchars($h['facultad'] ?? '') ?></div>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9;">
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <i class="fa-solid fa-chalkboard-user" style="color:var(--color-primario); font-size:12px;"></i>
                                        <span><?= htmlspecialchars($h['docente'] ?? 'Sin docente asignado') ?></span>
                                    </div>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9;">
                                    <div style="font-weight:600; color:var(--color-oscuro);">
                                        <i class="fa-solid fa-door-open" style="color:var(--color-secundario); font-size:12px; margin-right:4px;"></i>
                                        <?= htmlspecialchars($h['espacio'] ?? '') ?>
                                    </div>
                                    <div style="font-size:11.5px; color:var(--color-texto-secundario);">
                                        <?= htmlspecialchars(($h['sede'] ?? '') . ' / ' . ($h['bloque'] ?? '')) ?>
                                    </div>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9; white-space:nowrap;">
                                    <span style="display:inline-block; font-weight:700; color:var(--color-primario); font-size:12px;">
                                        <?= htmlspecialchars($h['dia_semana'] ?? '') ?>
                                    </span>
                                    <div style="font-size:12px; color:var(--color-oscuro); font-weight:500;">
                                        <?= htmlspecialchars(date('H:i', strtotime($h['hora_inicio']))) ?> – <?= htmlspecialchars(date('H:i', strtotime($h['hora_fin']))) ?>
                                    </div>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9;">
                                    <span class="badge <?= strtolower($h['estado'] ?? '') ?>"><?= htmlspecialchars($h['estado'] ?? '') ?></span>
                                    <?php if (!empty($h['origen'])): ?>
                                        <div style="font-size:10.5px; color:var(--color-texto-secundario); margin-top:3px; text-transform:uppercase;">
                                            <?= htmlspecialchars($h['origen']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 14px; border-bottom:1px solid #F1F5F9; text-align:right;">
                                    <div class="actions-inline">
                                        <a href="?ruta=admin/horarios&tab=ver_horario&periodo_id=<?= (int)($h['id_periodo'] ?? ($periodoActivo['id_periodo'] ?? 0)) ?>&programa_id=<?= (int)($h['id_programa'] ?? 0) ?>"
                                            class="btn-accion btn-accion-editar" title="Ver horario semanal en cuadrícula">
                                            <i class="fa-solid fa-calendar-days"></i>
                                        </a>
                                        <?php if (($h['estado'] ?? '') !== 'CERRADO'): ?>
                                            <a href="?ruta=admin/horarios/cerrar&id=<?= (int) $h['id_horario'] ?>"
                                                class="btn-accion btn-accion-desactivar" title="Cerrar clase"
                                                onclick="return confirmarCerrarHorario(event, this.href);">
                                                <i class="fa-solid fa-lock"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'ver_horario'): ?>
    <?php
    $diasCuadricula = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
    $horasCuadricula = [];
    for ($hr = 6; $hr < 22; $hr++) {
        $horasCuadricula[] = sprintf('%02d:00:00', $hr);
    }

    $eventosPorDia = [];
    foreach ($horariosCuadricula ?? [] as $hc) {
        $eventosPorDia[$hc['dia_semana']][] = $hc;
    }
    foreach ($eventosPorDia as $dia => $items) {
        usort($items, fn($a, $b) => strcmp($a['hora_inicio'], $b['hora_inicio']));
        $eventosPorDia[$dia] = $items;
    }

    $detalleEvento = function ($ev) {
        return [
            'id_horario' => (int)($ev['id_horario'] ?? 0),
            'asignatura_codigo' => $ev['asignatura_codigo'] ?? '',
            'asignatura_nombre' => $ev['asignatura_nombre'] ?? '',
            'creditos' => (int)($ev['creditos'] ?? 0),
            'semestre' => $ev['semestre'] ?? '',
            'docente' => $ev['docente_nombre'] ?? 'Sin docente',
            'espacio' => $ev['espacio_nombre'] ?? '',
            'espacio_tipo' => $ev['espacio_tipo'] ?? 'AULA',
            'bloque' => $ev['bloque_nombre'] ?? '',
            'sede' => $ev['sede_nombre'] ?? '',
            'programa' => ($ev['programa_codigo'] ? $ev['programa_codigo'] . ' - ' : '') . ($ev['programa_nombre'] ?? ''),
            'facultad' => $ev['facultad_nombre'] ?? 'Facultad Institucional',
            'dia' => $ev['dia_semana'] ?? '',
            'hora_inicio' => date('H:i', strtotime($ev['hora_inicio'])),
            'hora_fin' => date('H:i', strtotime($ev['hora_fin'])),
            'hora' => date('H:i', strtotime($ev['hora_inicio'])) . ' - ' . date('H:i', strtotime($ev['hora_fin'])),
            'duracion' => $ev['_duracion_texto'] ?? '',
            'estado' => $ev['estado'] ?? 'CONFIRMADO',
            'origen' => $ev['origen'] ?? 'MANUAL'
        ];
    };

    // Agrupar eventos de cada día en bloques continuos o simultáneos
    $bloquesPorDia = [];
    $celdasOmitidas = [];

    foreach ($diasCuadricula as $dia) {
        $itemsDia = $eventosPorDia[$dia] ?? [];
        $bloques = [];

        foreach ($itemsDia as $ev) {
            $iniTs = strtotime($ev['hora_inicio']);
            $finTs = strtotime($ev['hora_fin']);
            $iniH = (int)date('H', $iniTs);
            $finH = (int)date('H', $finTs);
            if ((int)date('i', $finTs) > 0) {
                $finH++;
            }
            $iniH = max(6, min(21, $iniH));
            $finH = max($iniH + 1, min(22, $finH));

            $duracionSeg = max(0, $finTs - $iniTs);
            $duracionHoras = max(1, round($duracionSeg / 3600, 1));
            $ev['_hora_formato'] = date('H:i', $iniTs) . ' - ' . date('H:i', $finTs);
            $ev['_duracion_texto'] = $duracionHoras . ($duracionHoras == 1 ? ' hora' : ' horas');
            $ev['_iniH'] = $iniH;
            $ev['_finH'] = $finH;

            // Verificar si este evento solapa con algún bloque ya iniciado
            $asig = false;
            foreach ($bloques as &$b) {
                if ($iniH < $b['finH'] && $finH > $b['iniH']) {
                    $b['eventos'][] = $ev;
                    $b['iniH'] = min($b['iniH'], $iniH);
                    $b['finH'] = max($b['finH'], $finH);
                    $asig = true;
                    break;
                }
            }
            unset($b);

            if (!$asig) {
                $bloques[] = [
                    'iniH' => $iniH,
                    'finH' => $finH,
                    'eventos' => [$ev]
                ];
            }
        }

        // Ordenar bloques por hora de inicio
        usort($bloques, fn($a, $b) => $a['iniH'] <=> $b['iniH']);

        // Indexar por hora de inicio y marcar celdas continuas omitidas
        foreach ($bloques as $bl) {
            $inicioHora = $bl['iniH'];
            $span = $bl['finH'] - $bl['iniH'];
            $bl['span'] = $span;
            $bloquesPorDia[$dia][$inicioHora] = $bl;

            for ($i = 1; $i < $span; $i++) {
                $celdasOmitidas[$dia][$inicioHora + $i] = true;
            }
        }
    }

    $fnFormatoHora = function ($hora) {
        $inicio = strtotime($hora);
        $fin = strtotime('+1 hour', $inicio);
        return date('g:00', $inicio) . ' - ' . date('g:00 A', $fin);
    };

    $progSeleccionadoObj = null;
    foreach ($programas as $prg) {
        if ((int)$prg['id_programa'] === (int)$idProgramaHorario) {
            $progSeleccionadoObj = $prg;
            break;
        }
    }
    ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Horario Semanal — Cuadrícula por Días</h2>
                <p>Visualización interactiva tipo calendario de clases programadas de Lunes a Sábado.</p>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn-secundario" onclick="window.print();" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-print"></i> Imprimir Horario
                </button>
                <a href="?ruta=admin/horarios&tab=general" class="btn-primario" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-plus"></i> Asignar Nueva Clase
                </a>
            </div>
        </div>

        <!-- Filtros de Cuadrícula -->
        <form method="GET" class="filter-grid" style="margin-bottom: 16px;">
            <input type="hidden" name="ruta" value="admin/horarios">
            <input type="hidden" name="tab" value="ver_horario">

            <div class="form-group">
                <label>Periodo Académico</label>
                <select name="periodo_id" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($periodos as $p): ?>
                        <option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$idPeriodoHorario === (int)$p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Programa Académico</label>
                <select name="programa_id" class="form-control" onchange="this.form.submit()">
                    <option value="" <?= empty($idProgramaHorario) ? 'selected' : '' ?>>Todos los programas</option>
                    <?php foreach ($programas as $prog): ?>
                        <option value="<?= (int)$prog['id_programa'] ?>" <?= ((int)$idProgramaHorario === (int)$prog['id_programa']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($prog['codigo'] ? $prog['codigo'].' - ' : '').$prog['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Docente</label>
                <select name="docente_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los docentes</option>
                    <?php foreach ($docentes as $doc): ?>
                        <option value="<?= (int)$doc['id_docente'] ?>" <?= ((int)$idDocenteHorario === (int)$doc['id_docente']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($doc['apellido'] . ' ' . $doc['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Espacio Físico</label>
                <select name="espacio_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los espacios</option>
                    <?php foreach ($espacios as $esp): ?>
                        <option value="<?= (int)$esp['id_espacio'] ?>" <?= ((int)$idEspacioHorario === (int)$esp['id_espacio']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($esp['sede_nombre'] . ' / ' . $esp['bloque_nombre'] . ' / ' . $esp['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Semestre</label>
                <select name="semestre" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los semestres</option>
                    <?php for ($s = 1; $s <= 10; $s++): ?>
                        <option value="<?= $s ?>" <?= ((int)$semestreHorario === $s) ? 'selected' : '' ?>>Semestre <?= $s ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primario" style="width:100%;">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <!-- Indicador de contexto -->
        <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; padding:10px 14px; border-radius:8px; margin-bottom:12px; font-size:13px;">
            <div>
                <strong>Programa:</strong> <?= htmlspecialchars($progSeleccionadoObj['nombre'] ?? 'Todos los programas') ?>
                <?php if (!empty($semestreHorario)): ?>
                    <span class="badge confirmado" style="margin-left:6px;">Semestre <?= (int)$semestreHorario ?></span>
                <?php endif; ?>
            </div>
            <div>
                <span style="color:var(--color-texto-secundario);">Total clases en cuadrícula:</span> <strong><?= count($horariosCuadricula ?? []) ?></strong>
            </div>
        </div>

        <!-- Tabla Cuadrícula Semanal -->
        <div class="schedule-grid-wrap">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="hour-header"><i class="fa-regular fa-clock"></i> Hora</th>
                        <?php foreach ($diasCuadricula as $dia): ?>
                            <th><?= $dia ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horasCuadricula as $hora): $horaInt = (int)date('H', strtotime($hora)); ?>
                        <tr style="height: 64px;">
                            <td class="schedule-time-cell">
                                <div><?= htmlspecialchars($fnFormatoHora($hora)) ?></div>
                                <?php if ($horaInt === 12): ?>
                                    <span style="display:block; background:#FFF8E6; color:#B78103; border:1px solid rgba(245,179,1,0.45); border-radius:999px; padding:1px 6px; font-size:10px; font-weight:700; margin:3px auto 0; text-transform:uppercase; width:fit-content;">Mediodía</span>
                                <?php endif; ?>
                            </td>
                            <?php foreach ($diasCuadricula as $dia): ?>
                                <?php if (!empty($celdasOmitidas[$dia][$horaInt])) continue; ?>
                                <?php $bloque = $bloquesPorDia[$dia][$horaInt] ?? null; ?>
                                <?php if ($bloque): 
                                    $span = (int)$bloque['span'];
                                    $eventos = $bloque['eventos'];
                                    $totalEventos = count($eventos);
                                    $minHeightPx = $span * 64 - 6;
                                    $detallesList = array_map($detalleEvento, $eventos);
                                ?>
                                    <td class="schedule-slot" rowspan="<?= $span ?>">
                                        <div class="schedule-multi-container" style="min-height: <?= $minHeightPx ?>px;">
                                            <?php if ($totalEventos > 1): ?>
                                                <div class="schedule-simultaneous-header" 
                                                     title="Haz clic para ver las <?= $totalEventos ?> asignaturas programadas"
                                                     data-multi-detail='<?= htmlspecialchars(json_encode($detallesList), ENT_QUOTES, 'UTF-8') ?>'
                                                     data-dia="<?= htmlspecialchars($dia) ?>"
                                                     data-hora-texto="<?= sprintf('%02d:00 - %02d:00', $bloque['iniH'], $bloque['finH']) ?>">
                                                    <span class="schedule-simultaneous-pill">
                                                        <i class="fa-solid fa-layer-group"></i> <?= $totalEventos ?> Asignaturas Simultáneas
                                                    </span>
                                                    <span style="font-size: 10px; opacity: 0.9;">Ver todas <i class="fa-solid fa-chevron-right"></i></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php foreach ($eventos as $ev): 
                                                $isLab = (strtoupper($ev['espacio_tipo'] ?? '') === 'LABORATORIO');
                                                $det = $detalleEvento($ev);
                                            ?>
                                                <div class="schedule-card clickable-card <?= $isLab ? 'tipo-lab' : '' ?>" 
                                                     data-class-detail='<?= htmlspecialchars(json_encode($det), ENT_QUOTES, 'UTF-8') ?>'
                                                     title="Haz clic para ver detalles completos de <?= htmlspecialchars($ev['asignatura_nombre']) ?>"
                                                     style="flex: 1;">
                                                    <div>
                                                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:4px; margin-bottom:3px;">
                                                            <span class="schedule-card-badge">
                                                                <?= htmlspecialchars($ev['asignatura_codigo'] ?? '') ?>
                                                                <?= !empty($ev['semestre']) ? '• Sem. ' . $ev['semestre'] : '' ?>
                                                            </span>
                                                            <span style="font-size:10.5px; font-weight:700; color:var(--color-primario);">
                                                                <?= htmlspecialchars($ev['_hora_formato']) ?>
                                                            </span>
                                                        </div>
                                                        <strong class="schedule-card-title">
                                                            <?= htmlspecialchars($ev['asignatura_nombre']) ?>
                                                        </strong>
                                                    </div>
                                                    <div>
                                                        <div class="schedule-card-meta">
                                                            <i class="fa-solid fa-chalkboard-user"></i>
                                                            <span><?= htmlspecialchars($ev['docente_nombre']) ?></span>
                                                        </div>
                                                        <div class="schedule-card-meta">
                                                            <i class="fa-solid fa-location-dot"></i>
                                                            <span><?= htmlspecialchars($ev['espacio_nombre']) ?> (<?= htmlspecialchars($ev['espacio_tipo']) ?>)</span>
                                                        </div>
                                                        <?php if (empty($idProgramaHorario)): ?>
                                                            <div class="schedule-card-meta" style="font-size:10px; color:#64748b;">
                                                                <i class="fa-solid fa-graduation-cap"></i>
                                                                <span><?= htmlspecialchars($ev['programa_nombre']) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="click-hint">
                                                            <i class="fa-solid fa-circle-info"></i> Haz clic para ver detalles
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td class="schedule-slot">
                                        <div class="schedule-slot-free">Libre</div>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'fet'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Simular generacion FET</h2>
                <p>Genera una propuesta coherente en <span class="code-pill">propuesta_fet</span> y <span
                        class="code-pill">propuesta_fet_detalle</span>. Integracion real pendiente.</p>
            </div>
        </div>
        <form action="?ruta=admin/horarios/generar_fet" method="POST" class="assignment-grid">
            <div class="form-group">
                <label>Periodo</label>
                <select name="id_periodo" class="form-control" required>
                    <?php foreach ($periodos as $p): ?>
                        <option value="<?= (int) $p['id_periodo'] ?>" <?= (($periodoActivo['id_periodo'] ?? null) == $p['id_periodo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Programa</label>
                <select name="id_programa" class="form-control" required>
                    <option value="">Seleccionar programa</option>
                    <?php foreach ($programas as $p): ?>
                        <option value="<?= (int) $p['id_programa'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tipo de generacion</label>
                <select name="franja" class="form-control">
                    <option value="DIURNA">Diurna balanceada</option>
                    <option value="NOCTURNA">Nocturna intensiva</option>
                    <option value="MIXTA">Mixta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Peso docente</label>
                <select name="peso_docente" class="form-control">
                    <option value="ALTO">Alto</option>
                    <option value="MEDIO">Medio</option>
                    <option value="BAJO">Bajo</option>
                </select>
            </div>
            <div class="form-group" style="grid-column:1 / -1;">
                <label>Dias lectivos</label>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <?php foreach (array_slice($diasSemana, 0, 6) as $dia): ?>
                        <label style="display:inline-flex; gap:6px; align-items:center; font-size:13px;"><input type="checkbox"
                                name="dias[]" value="<?= $dia ?>" checked> <?= $dia ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group"><button type="submit" class="btn-primario"><i class="fa-solid fa-play"></i> Simular
                    FET</button></div>
        </form>
    </div>

    <?php if (!empty($propuestaFet)): ?>
        <div class="panel">
            <div class="section-title">
                <div>
                    <h2>Ultima propuesta generada</h2>
                    <p><?= htmlspecialchars($propuestaFet['programa_nombre'] ?? '') ?> -
                        <?= htmlspecialchars($propuestaFet['periodo_codigo'] ?? '') ?></p>
                </div><span
                    class="badge <?= strtolower($propuestaFet['estado']) ?>"><?= htmlspecialchars($propuestaFet['estado']) ?></span>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detallesFet as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '') . ' ' . ($d['asignatura_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['docente_nombre'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($d['sede_nombre'] ?? '') . ' / ' . ($d['bloque_nombre'] ?? '') . ' / ' . ($d['espacio_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['dia_semana'] ?? '') ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($d['hora_inicio']))) ?> -
                                    <?= htmlspecialchars(date('H:i', strtotime($d['hora_fin']))) ?></td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php elseif ($tab === 'propuestas'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Propuestas FET</h2>
                <p>Acepta lineas para crear horarios reales con origen FET, o descarta propuestas completas.</p>
            </div>
        </div>
        <div class="filter-grid" style="margin-bottom:16px;">
            <div class="form-group">
                <label>Propuesta</label>
                <select class="form-control"
                    onchange="if (this.value) window.location='?ruta=admin/horarios&tab=propuestas&propuesta_id=' + this.value">
                    <option value="">Seleccionar propuesta</option>
                    <?php foreach ($propuestasFetGeneradas as $pf): ?>
                        <option value="<?= (int) $pf['id_propuesta'] ?>"
                            <?= ((int) $propuestaSeleccionada === (int) $pf['id_propuesta']) ? 'selected' : '' ?>>
                            #<?= (int) $pf['id_propuesta'] ?> - <?= htmlspecialchars($pf['programa_nombre']) ?> -
                            <?= htmlspecialchars($pf['estado']) ?> - <?= (int) $pf['total_detalles'] ?> lineas
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($propuestaSeleccionada): ?>
                <div class="form-group" style="display:flex; align-items:end;">
                    <form action="?ruta=admin/horarios/descartar_propuesta_fet" method="POST"
                        onsubmit="return confirmarDesactivacion(event, this, 'La propuesta completa quedara marcada como descartada.');">
                        <input type="hidden" name="id_propuesta" value="<?= (int) $propuestaSeleccionada ?>">
                        <button class="btn-demo" type="submit"><i class="fa-solid fa-ban"></i> Descartar propuesta</button>
                    </form>
                </div>
            <?php endif; ?>
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
                    <?php if (empty($detallesPropuestaSeleccionada)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:24px;">No hay lineas para revisar.</td>
                        </tr>
                    <?php else:
                        foreach ($detallesPropuestaSeleccionada as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars(($d['asignatura_codigo'] ?? '') . ' ' . ($d['asignatura_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['docente_nombre'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($d['sede_nombre'] ?? '') . ' / ' . ($d['bloque_nombre'] ?? '') . ' / ' . ($d['espacio_nombre'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars($d['dia_semana'] ?? '') ?></td>
                                <td><?= htmlspecialchars(date('H:i', strtotime($d['hora_inicio']))) ?> -
                                    <?= htmlspecialchars(date('H:i', strtotime($d['hora_fin']))) ?></td>
                                <td><?= !empty($d['id_horario_resultante']) ? '<span class="badge aceptada">ACEPTADA</span>' : '<span class="badge generada">GENERADA</span>' ?>
                                </td>
                                <td style="text-align:right;">
                                    <span class="actions-inline">
                                        <?php if (empty($d['id_horario_resultante'])): ?>
                                            <a class="btn-accion btn-accion-aceptar" title="Aceptar"
                                                href="?ruta=admin/horarios/aceptar_propuesta_fet&id=<?= (int) $d['id_detalle'] ?>&propuesta_id=<?= (int) $propuestaSeleccionada ?>"><i
                                                    class="fa-solid fa-check"></i></a>
                                            <form action="?ruta=admin/horarios/eliminar_detalle_fet" method="POST"
                                                onsubmit="return confirmarEliminacion(event, this, 'esta linea FET');">
                                                <input type="hidden" name="id_detalle" value="<?= (int) $d['id_detalle'] ?>">
                                                <input type="hidden" name="propuesta_id" value="<?= (int) $propuestaSeleccionada ?>">
                                                <button class="btn-accion btn-accion-eliminar" title="Descartar linea" type="submit"><i
                                                        class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($tab === 'auditoria'): ?>
    <div class="panel">
        <div class="section-title">
            <div>
                <h2>Auditoria de clases</h2>
                <p>Filtra clases pendientes y registra el resultado de verificacion.</p>
            </div>
        </div>
        <form action="" method="GET" class="filter-grid" style="margin-bottom:16px;">
            <input type="hidden" name="ruta" value="admin/horarios"><input type="hidden" name="tab" value="auditoria">
            <div class="form-group"><label>Fecha</label><input type="date" name="fecha" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['fecha'] ?? '') ?>"></div>
            <div class="form-group"><label>Sede</label><input name="sede" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['sede'] ?? '') ?>"></div>
            <div class="form-group"><label>Bloque</label><input name="bloque" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['bloque'] ?? '') ?>"></div>
            <div class="form-group"><label>Espacio</label><input name="espacio_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['espacio'] ?? '') ?>"></div>
            <div class="form-group"><label>Docente</label><input name="docente_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['docente'] ?? '') ?>"></div>
            <div class="form-group"><label>Programa</label><input name="programa_aud" class="form-control"
                    value="<?= htmlspecialchars($filtrosAuditoria['programa'] ?? '') ?>"></div>
            <div class="form-group"><label>Resultado</label><select name="resultado" class="form-control">
                    <option value="">Todos</option><?php foreach (['PENDIENTE', 'VERIFICADA', 'NO_REALIZADA'] as $res): ?>
                        <option value="<?= $res ?>" <?= (($filtrosAuditoria['resultado'] ?? '') === $res) ? 'selected' : '' ?>>
                            <?= $res ?></option><?php endforeach; ?>
                </select></div>
            <div class="form-group"><button class="btn-primario" type="submit">Filtrar</button> <a
                    href="?ruta=admin/horarios&tab=auditoria" class="btn-demo">Limpiar</a></div>
        </form>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Clase</th>
                        <th>Docente</th>
                        <th>Programa</th>
                        <th>Espacio</th>
                        <th>Horario</th>
                        <th>Resultado</th>
                        <th>Verificacion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditorias)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:24px;">No hay auditorias con estos filtros.</td>
                        </tr>
                    <?php else:
                        foreach ($auditorias as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['fecha_auditoria'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['asignatura'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['docente'] ?? '') ?></td>
                                <td><?= htmlspecialchars($a['programa'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($a['sede'] ?? '') . ' / ' . ($a['bloque'] ?? '') . ' / ' . ($a['espacio'] ?? '')) ?>
                                </td>
                                <td><?= htmlspecialchars(($a['dia_semana'] ?? '') . ' ' . date('H:i', strtotime($a['hora_inicio'])) . '-' . date('H:i', strtotime($a['hora_fin']))) ?>
                                </td>
                                <td><span
                                        class="badge <?= strtolower($a['resultado'] ?? '') ?>"><?= htmlspecialchars($a['resultado'] ?? '') ?></span>
                                </td>
                                <td>
                                    <form class="audit-form" action="?ruta=admin/horarios/verificar_auditoria" method="POST">
                                        <input type="hidden" name="id_auditoria" value="<?= (int) $a['id_auditoria'] ?>">
                                        <input type="text" name="observacion" class="form-control" placeholder="Observacion">
                                        <button class="btn-accion btn-accion-aceptar" name="resultado" value="VERIFICADA"
                                            title="Verificada"><i class="fa-solid fa-check"></i></button>
                                        <button class="btn-accion btn-accion-eliminar" name="resultado" value="NO_REALIZADA"
                                            title="No realizada"><i class="fa-solid fa-xmark"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const periodo = document.getElementById('id_periodo');
        const programa = document.getElementById('id_programa');
        const asignatura = document.getElementById('id_asignatura');
        const docente = document.getElementById('id_docente');
        const dia = document.getElementById('dia_semana');
        const inicio = document.getElementById('hora_inicio');
        const fin = document.getElementById('hora_fin');
        const espacio = document.getElementById('id_espacio');
        const formAsignar = document.getElementById('form-asignar-clase');
        const btnSubmit = document.getElementById('btn-submit-class');
        const btnText = document.getElementById('btn-submit-text');
        const btnLoading = document.getElementById('btn-submit-loading');

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
            // Si ya tiene valor, sincronizar pills
            if (dia.value) {
                dayPills.forEach(p => {
                    if (p.dataset.day === dia.value) p.classList.add('active');
                });
            }
        }

        // Calculador de duración de clase
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

        const fillSelect = (select, rows, placeholder, mapper) => {
            if (!select) return;
            select.innerHTML = `<option value="">${placeholder}</option>`;
            rows.forEach(row => {
                const option = document.createElement('option');
                const mapped = mapper(row);
                option.value = mapped.value;
                option.textContent = mapped.label;
                select.appendChild(option);
            });
        };

        const loadAsignaturas = async () => {
            if (!periodo || !programa || !periodo.value || !programa.value) return;
            if (spinAsig) spinAsig.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=admin/horarios/api_asignaturas&periodo_id=${periodo.value}&programa_id=${programa.value}`);
                fillSelect(asignatura, await res.json(), 'Seleccionar asignatura', row => ({ value: row.id_asignatura, label: `${row.codigo} - ${row.nombre}` }));
                fillSelect(docente, [], 'Selecciona una asignatura', row => row);
            } finally {
                if (spinAsig) spinAsig.style.display = 'none';
            }
        };
        const loadDocentes = async () => {
            if (!asignatura || !asignatura.value) return;
            if (spinDoc) spinDoc.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=admin/horarios/api_docentes&asignatura_id=${asignatura.value}`);
                fillSelect(docente, await res.json(), 'Seleccionar docente', row => ({ value: row.id_docente, label: `${row.apellido} ${row.nombre}` }));
            } finally {
                if (spinDoc) spinDoc.style.display = 'none';
            }
        };
        const loadEspacios = async () => {
            if (!periodo || !dia || !inicio || !fin || !periodo.value || !dia.value || !inicio.value || !fin.value) return;
            if (spinEsp) spinEsp.style.display = 'inline-block';
            try {
                const res = await fetch(`?ruta=admin/horarios/api_espacios&periodo_id=${periodo.value}&dia_semana=${dia.value}&hora_inicio=${inicio.value}&hora_fin=${fin.value}`);
                fillSelect(espacio, await res.json(), 'Seleccionar espacio libre', row => ({ value: row.id_espacio, label: `${row.sede_nombre} / ${row.bloque_nombre} / ${row.nombre} (${row.tipo}, ${row.capacidad})` }));
            } finally {
                if (spinEsp) spinEsp.style.display = 'none';
            }
        };
        [periodo, programa].forEach(el => el && el.addEventListener('change', loadAsignaturas));
        asignatura && asignatura.addEventListener('change', loadDocentes);
        [periodo, dia, inicio, fin].forEach(el => el && el.addEventListener('change', loadEspacios));

        // Helpers para modales de horarios en cuadrícula
        const esc = value => String(value || '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));

        const showSingleClassModal = (detail) => {
            const isLab = String(detail.espacio_tipo || '').toUpperCase() === 'LABORATORIO';
            const chipTypeClass = isLab ? 'chip-success' : 'chip-primary';
            const html = `
                <div class="occupied-detail">
                    <div class="occupied-header">
                        <div class="modal-logo"><img src="assets/img/isotipo-color.png" alt="UniFET" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%233b0d8f\'><rect width=\'24\' height=\'24\' rx=\'6\'/><text x=\'50%25\' y=\'65%25\' font-size=\'14\' font-weight=\'bold\' fill=\'%23ffffff\' text-anchor=\'middle\'>U</text></svg>'"></div>
                        <div>
                            <h2>${esc(detail.asignatura_nombre || 'Asignatura')}</h2>
                            <p>${esc(detail.programa || 'Programa Académico')}</p>
                        </div>
                    </div>
                    <div class="detail-chips">
                        <span class="detail-chip"><i class="fa-solid fa-calendar-day"></i> ${esc(detail.dia) || '-'}</span>
                        <span class="detail-chip"><i class="fa-regular fa-clock"></i> ${esc(detail.hora) || '-'} ${detail.duracion ? '(' + esc(detail.duracion) + ')' : ''}</span>
                        <span class="detail-chip ${chipTypeClass}"><i class="fa-solid ${isLab ? 'fa-flask-vial' : 'fa-school'}"></i> ${esc(detail.espacio_tipo || 'AULA')}</span>
                        <span class="detail-chip"><i class="fa-solid fa-circle-check"></i> ${esc(detail.estado || 'CONFIRMADO')}</span>
                    </div>
                    <div class="detail-sections">
                        <section class="detail-section">
                            <h3>Asignatura</h3>
                            <div class="detail-list">
                                <div class="detail-item"><span>Código</span><strong>${esc(detail.asignatura_codigo) || 'N/A'}</strong></div>
                                <div class="detail-item"><span>Nombre</span><strong>${esc(detail.asignatura_nombre) || '-'}</strong></div>
                                <div class="detail-item"><span>Créditos / Semestre</span><strong>${detail.creditos || 0} Créditos ${detail.semestre ? '• Semestre ' + esc(detail.semestre) : ''}</strong></div>
                            </div>
                        </section>
                        <section class="detail-section">
                            <h3>Docente</h3>
                            <div class="detail-list">
                                <div class="detail-item"><span>Docente a cargo</span><strong>${esc(detail.docente) || 'Sin docente'}</strong></div>
                                <div class="detail-item"><span>Programa</span><strong>${esc(detail.programa) || '-'}</strong></div>
                                <div class="detail-item"><span>Facultad</span><strong>${esc(detail.facultad) || 'Institucional'}</strong></div>
                            </div>
                        </section>
                        <section class="detail-section">
                            <h3>Ubicación Física</h3>
                            <div class="detail-list">
                                <div class="detail-item"><span>Espacio</span><strong>${esc(detail.espacio) || '-'} (${esc(detail.espacio_tipo || 'AULA')})</strong></div>
                                <div class="detail-item"><span>Bloque</span><strong>${esc(detail.bloque) || '-'}</strong></div>
                                <div class="detail-item"><span>Sede</span><strong>${esc(detail.sede) || 'Sede Principal'}</strong></div>
                            </div>
                        </section>
                        <section class="detail-section">
                            <h3>Planificación y Origen</h3>
                            <div class="detail-list">
                                <div class="detail-item"><span>Franja programada</span><strong>${esc(detail.dia)}: ${esc(detail.hora)}</strong></div>
                                <div class="detail-item"><span>Origen / Estado</span><strong>${esc(detail.origen) || 'MANUAL'} • ${esc(detail.estado) || 'CONFIRMADO'}</strong></div>
                            </div>
                        </section>
                    </div>
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    width: 760,
                    customClass: { htmlContainer: 'occupied-swal-container' },
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3B0D8F',
                    html
                });
                return;
            }

            const backdrop = document.createElement('div');
            backdrop.className = 'occupied-modal-backdrop';
            backdrop.innerHTML = `<div class="occupied-modal">${html}<div class="occupied-modal-actions"><button class="btn-primario" type="button">Cerrar</button></div></div>`;
            backdrop.querySelector('button').addEventListener('click', () => backdrop.remove());
            backdrop.addEventListener('click', e => { if (e.target === backdrop) backdrop.remove(); });
            document.body.appendChild(backdrop);
        };

        const showMultiClassModal = (dia, horaTexto, clases) => {
            let cardsHtml = '';
            clases.forEach((c) => {
                const isLab = String(c.espacio_tipo || '').toUpperCase() === 'LABORATORIO';
                cardsHtml += `
                    <div style="border:1.5px solid #E2E8F0; border-left:4.5px solid ${isLab ? '#10b981' : '#3b0d8f'}; border-radius:8px; padding:12px 14px; margin-bottom:10px; background:${isLab ? '#f0fdf4' : '#faf5ff'}; text-align:left;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px; flex-wrap:wrap; gap:6px;">
                            <div>
                                <span style="font-size:11px; font-weight:700; color:${isLab ? '#047857' : '#3b0d8f'}; background:rgba(0,0,0,0.06); padding:2px 7px; border-radius:4px;">${esc(c.asignatura_codigo)} ${c.semestre ? '• Sem. ' + esc(c.semestre) : ''}</span>
                                <strong style="font-size:14px; color:#0f172a; margin-left:6px;">${esc(c.asignatura_nombre)}</strong>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:var(--color-primario); background:#fff; padding:2px 8px; border-radius:4px; border:1px solid #e2e8f0;">${esc(c.hora)}</span>
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:6px; font-size:12px; color:#475569; margin-top:8px;">
                            <div><i class="fa-solid fa-chalkboard-user" style="color:var(--color-primario);"></i> <strong>Docente:</strong> ${esc(c.docente)}</div>
                            <div><i class="fa-solid fa-location-dot" style="color:#ef4444;"></i> <strong>Espacio:</strong> ${esc(c.espacio)} (${esc(c.espacio_tipo)})</div>
                            <div><i class="fa-solid fa-graduation-cap" style="color:#6366f1;"></i> <strong>Programa:</strong> ${esc(c.programa)}</div>
                            <div><i class="fa-solid fa-building" style="color:#64748b;"></i> <strong>Bloque/Sede:</strong> ${esc(c.bloque)} - ${esc(c.sede)}</div>
                        </div>
                    </div>
                `;
            });

            const html = `
                <div class="occupied-detail">
                    <div class="occupied-header">
                        <div class="modal-logo"><img src="assets/img/isotipo-color.png" alt="UniFET" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%233b0d8f\'><rect width=\'24\' height=\'24\' rx=\'6\'/><text x=\'50%25\' y=\'65%25\' font-size=\'14\' font-weight=\'bold\' fill=\'%23ffffff\' text-anchor=\'middle\'>U</text></svg>'"></div>
                        <div>
                            <h2>${clases.length} Asignaturas Simultáneas</h2>
                            <p>${esc(dia)} • Franja ${esc(horaTexto)}</p>
                        </div>
                    </div>
                    <div class="detail-chips">
                        <span class="detail-chip"><i class="fa-solid fa-calendar-day"></i> ${esc(dia)}</span>
                        <span class="detail-chip chip-primary"><i class="fa-solid fa-layer-group"></i> ${clases.length} Clases concurrentes</span>
                        <span class="detail-chip"><i class="fa-regular fa-clock"></i> ${esc(horaTexto)}</span>
                    </div>
                    <div style="margin-top:14px; max-height:420px; overflow-y:auto; padding-right:4px;">
                        ${cardsHtml}
                    </div>
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    width: 780,
                    customClass: { htmlContainer: 'occupied-swal-container' },
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3B0D8F',
                    html
                });
                return;
            }

            const backdrop = document.createElement('div');
            backdrop.className = 'occupied-modal-backdrop';
            backdrop.innerHTML = `<div class="occupied-modal">${html}<div class="occupied-modal-actions"><button class="btn-primario" type="button">Cerrar</button></div></div>`;
            backdrop.querySelector('button').addEventListener('click', () => backdrop.remove());
            backdrop.addEventListener('click', e => { if (e.target === backdrop) backdrop.remove(); });
            document.body.appendChild(backdrop);
        };

        // Event delegation para clic en tarjetas de clase y cabeceras simultáneas
        document.addEventListener('click', (e) => {
            const multiHeader = e.target.closest('[data-multi-detail]');
            if (multiHeader) {
                e.stopPropagation();
                try {
                    const clases = JSON.parse(multiHeader.dataset.multiDetail || '[]');
                    const dia = multiHeader.dataset.dia || '';
                    const horaTexto = multiHeader.dataset.horaTexto || '';
                    showMultiClassModal(dia, horaTexto, clases);
                } catch (err) {
                    console.error('Error parseando clases simultáneas:', err);
                }
                return;
            }

            const card = e.target.closest('[data-class-detail]');
            if (card) {
                e.stopPropagation();
                try {
                    const detail = JSON.parse(card.dataset.classDetail || '{}');
                    showSingleClassModal(detail);
                } catch (err) {
                    console.error('Error parseando detalle de clase:', err);
                }
            }
        });
    });
</script>

<?php $this->render('elementos_compartidos/footer'); ?>