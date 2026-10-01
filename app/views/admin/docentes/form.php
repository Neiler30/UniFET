<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Docente']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'docentes']);

$esEdicion = !empty($docente['id_docente']);
$idDocente = $esEdicion ? (int)$docente['id_docente'] : 0;
?>

<style>
/* =========================================================
   Estilos especializados para el Módulo de Docentes UniFET
   ========================================================= */

.docente-form-container {
    max-width: 980px;
    margin: 0 auto;
    position: relative;
}

.docente-card {
    background: #ffffff;
    border-radius: var(--radio-borde-lg, 12px);
    box-shadow: var(--sombra-tarjeta, 0 4px 12px rgba(0,0,0,0.08));
    padding: 32px 28px;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

/* Marca de agua institucional (Escudo / Logo UniFET) de fondo en todo el formulario */
.docente-card::before {
    content: "";
    position: absolute;
    top: 50%;
    right: 4%;
    transform: translateY(-50%);
    width: 440px;
    height: 440px;
    max-width: 80vw;
    max-height: 80vw;
    background: url("assets/img/isotipo-color.png") center center / contain no-repeat;
    opacity: 0.045;
    pointer-events: none;
    z-index: 0;
    transition: opacity 0.3s ease;
}

.docente-card::after {
    content: "";
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 220px;
    height: 220px;
    background: url("assets/img/isotipo-color.png") center / contain no-repeat;
    opacity: 0.025;
    pointer-events: none;
    z-index: 0;
}

.docente-header {
    margin-bottom: 24px;
    border-bottom: 1px solid var(--color-borde, #E2E5E9);
    padding-bottom: 18px;
    position: relative;
    z-index: 1;
}

.docente-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-oscuro, #0B1F33);
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.docente-header p {
    color: var(--color-texto-secundario, #666666);
    font-size: 14px;
    margin: 0;
}

/* Títulos de sección */
.docente-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-oscuro, #0B1F33);
    margin: 28px 0 16px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(59, 13, 143, 0.08);
}

.docente-section-title i {
    color: var(--color-primario, #3B0D8F);
    font-size: 17px;
}

.docente-section-title .section-step {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--color-primario, #3B0D8F);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
}

/* =====================================================
   Layout de Información Básica + Tarjeta de Código con Logo
   ===================================================== */
.docente-info-wrapper {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: stretch;
    margin-bottom: 10px;
}

@media (max-width: 860px) {
    .docente-info-wrapper {
        grid-template-columns: 1fr;
    }
}

.docente-inputs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 580px) {
    .docente-inputs-grid {
        grid-template-columns: 1fr;
    }
}

/* Tarjeta Destacada de Código con Marca de Agua (Logo UniFET) */
.codigo-docente-card {
    position: relative;
    background: linear-gradient(135deg, #FBF9FF 0%, #F5F7FB 100%);
    border: 1.5px solid rgba(59, 13, 143, 0.16);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 14px rgba(59, 13, 143, 0.04);
    overflow: hidden;
    transition: all 0.25s ease;
}

.codigo-docente-card::before {
    content: "";
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 105px;
    height: 105px;
    background: url("assets/img/isotipo-color.png") center / contain no-repeat;
    opacity: 0.13;
    pointer-events: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.codigo-docente-card:hover::before {
    opacity: 0.20;
    transform: translateY(-50%) scale(1.05);
}

.codigo-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    z-index: 1;
}

.codigo-card-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--color-oscuro, #0B1F33);
    display: flex;
    align-items: center;
    gap: 6px;
}

.codigo-card-label i {
    color: var(--color-primario, #3B0D8F);
}

.badge-auto-pill {
    background: rgba(15, 157, 88, 0.12);
    color: #0d8047;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    letter-spacing: 0.3px;
}

.badge-auto-pill.modo-manual {
    background: rgba(185, 125, 2, 0.12);
    color: #B97D02;
}

.codigo-input-container {
    position: relative;
    z-index: 1;
    margin: 4px 0 10px 0;
}

.codigo-display-input {
    width: 100%;
    font-family: 'Poppins', 'Consolas', monospace;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 2px;
    color: var(--color-primario, #3B0D8F);
    background: rgba(255, 255, 255, 0.9);
    border: 1.5px solid rgba(59, 13, 143, 0.22);
    border-radius: 8px;
    padding: 8px 14px;
    text-align: center;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
}

.codigo-display-input:focus {
    outline: none;
    border-color: var(--color-primario, #3B0D8F);
    box-shadow: 0 0 0 3px rgba(59, 13, 143, 0.15);
    background: #ffffff;
}

.codigo-card-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    font-size: 11.5px;
    color: var(--color-texto-secundario, #666666);
    z-index: 1;
    border-top: 1px dashed rgba(59, 13, 143, 0.12);
    padding-top: 8px;
}

.codigo-card-bottom span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.codigo-card-bottom i {
    color: var(--color-acento, #F5B301);
    font-size: 12px;
}

.btn-codigo-toggle {
    background: transparent;
    border: none;
    color: var(--color-primario, #3B0D8F);
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
    white-space: nowrap;
    transition: color 0.15s;
}

.btn-codigo-toggle:hover {
    color: #2c096b;
}

/* Bloque Configurar Programa (Selector + Asignaturas) */
.configurador-programa-box {
    background: #FAFCFE;
    border: 1.5px solid #E2E8F0;
    border-radius: 12px;
    padding: 22px;
    margin-bottom: 24px;
    transition: all 0.25s ease;
}

.configurador-programa-box.en-edicion {
    border-color: var(--color-primario, #3B0D8F);
    background: #F8F5FF;
    box-shadow: 0 0 0 3px rgba(59, 13, 143, 0.08);
}

.programa-filters-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

@media (max-width: 768px) {
    .programa-filters-grid {
        grid-template-columns: 1fr;
    }
}

/* Tarjeta informativa del programa seleccionado */
.programa-preview-card {
    background: #ffffff;
    border: 1px solid #D8E0EA;
    border-left: 4px solid var(--color-primario, #3B0D8F);
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.programa-preview-card .prog-info h4 {
    margin: 0 0 4px 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--color-oscuro, #0B1F33);
}

.programa-preview-card .prog-meta {
    display: flex;
    gap: 8px;
    align-items: center;
    font-size: 12.5px;
    color: var(--color-texto-secundario, #666666);
}

/* Área de selección de asignaturas */
.asignaturas-selection-area {
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 12px;
}

.asignaturas-toolbar {
    background: #F4F6F8;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    border-bottom: 1px solid #E2E8F0;
}

.asignaturas-search-box {
    position: relative;
    max-width: 280px;
    flex: 1 1 200px;
}

.asignaturas-search-box i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-texto-secundario, #666);
    font-size: 12px;
}

.asignaturas-search-box input {
    padding-left: 30px;
    height: 34px;
    font-size: 13px;
}

.asignaturas-quick-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-toolbar-action {
    background: #ffffff;
    border: 1px solid #CBD5E1;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--color-texto-principal, #333);
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-toolbar-action:hover {
    background: #f1f5f9;
    border-color: #94A3B8;
}

.contador-badge {
    background: rgba(59, 13, 143, 0.1);
    color: var(--color-primario, #3B0D8F);
    padding: 5px 12px;
    border-radius: 14px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Lista de checkboxes */
.asignaturas-checkbox-list {
    max-height: 280px;
    overflow-y: auto;
    padding: 8px 12px;
}

.asignatura-item-row {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 4px;
    transition: background 0.15s;
    cursor: pointer;
    user-select: none;
}

.asignatura-item-row:hover {
    background: #F8FAFC;
}

.asignatura-item-row.selected {
    background: #F3EEFF;
}

.asignatura-item-row input[type="checkbox"] {
    width: 17px;
    height: 17px;
    margin-right: 12px;
    cursor: pointer;
    accent-color: var(--color-primario, #3B0D8F);
}

.asignatura-item-info {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.asignatura-item-nombre {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--color-oscuro, #0B1F33);
}

.asignatura-item-tags {
    display: flex;
    align-items: center;
    gap: 6px;
}

.creditos-tag {
    font-size: 11.5px;
    background: #EEF2F6;
    color: var(--color-texto-secundario, #555);
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 500;
}

.semestre-tag {
    font-size: 11.5px;
    background: rgba(15, 157, 88, 0.1);
    color: var(--color-secundario, #0F9D58);
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 600;
}

/* Tarjetas de Programas Configurados */
.programas-configurados-container {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 14px;
}

.programa-configurado-card {
    background: #ffffff;
    border: 1.5px solid #E2E8F0;
    border-radius: 10px;
    padding: 18px 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    transition: all 0.2s ease;
}

.programa-configurado-card:hover {
    border-color: #CBD5E1;
    box-shadow: 0 4px 12px rgba(59, 13, 143, 0.06);
}

.prog-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #E2E8F0;
}

.prog-card-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.prog-card-title {
    font-size: 15.5px;
    font-weight: 700;
    color: var(--color-oscuro, #0B1F33);
    margin: 0;
}

.prog-card-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-card-accion {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid transparent;
}

.btn-card-editar {
    background: rgba(59, 13, 143, 0.08);
    color: var(--color-primario, #3B0D8F);
    border-color: rgba(59, 13, 143, 0.2);
}

.btn-card-editar:hover {
    background: var(--color-primario, #3B0D8F);
    color: #ffffff;
}

.btn-card-eliminar {
    background: rgba(220, 53, 69, 0.08);
    color: #DC3545;
    border-color: rgba(220, 53, 69, 0.2);
}

.btn-card-eliminar:hover {
    background: #DC3545;
    color: #ffffff;
}

/* Lista de asignaturas seleccionadas en la tarjeta de resumen */
.prog-card-asigs-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.asig-chip {
    background: #F4F6F8;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12.5px;
    color: var(--color-texto-principal, #333);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.asig-chip strong {
    color: var(--color-primario, #3B0D8F);
    font-weight: 600;
}

/* Estado vacío de programas configurados */
.empty-configurados-state {
    text-align: center;
    padding: 32px 20px;
    background: #FAFAFB;
    border: 2px dashed #D8E0EA;
    border-radius: 10px;
    color: var(--color-texto-secundario, #666);
}

.empty-configurados-state i {
    font-size: 32px;
    color: #A0AEC0;
    margin-bottom: 8px;
    display: block;
}

.empty-configurados-state h4 {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-oscuro, #0B1F33);
    margin: 0 0 4px 0;
}

.empty-configurados-state p {
    font-size: 13px;
    margin: 0;
}

/* Acciones principales al pie del formulario */
.form-footer-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 32px;
    padding-top: 20px;
    border-top: 1px solid var(--color-borde, #E2E5E9);
}
</style>

<div class="form-shell docente-form-container">
    <div class="docente-card">
        
        <!-- Encabezado del Formulario -->
        <div class="docente-header">
            <div class="form-entity-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
            <h1><?= htmlspecialchars($titulo) ?></h1>
            <p>Configura la información básica del docente y sus asignaturas compatibles por programa académico en un solo paso antes de guardar.</p>
        </div>

        <form id="form-docente" method="POST" action="?ruta=admin/academico/docentes/<?= $esEdicion ? 'actualizar' : 'guardar' ?>">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_docente" id="id_docente" value="<?= (int)$docente['id_docente'] ?>">
            <?php endif; ?>

            <!-- Campo oculto para enviar todo el perfil académico como JSON -->
            <input type="hidden" name="perfil_academico" id="input_perfil_academico">

            <!-- ===================================================== -->
            <!-- 1. INFORMACIÓN DEL DOCENTE                            -->
            <!-- ===================================================== -->
            <div class="docente-section-title">
                <span class="section-step">1</span>
                <span>Información del docente</span>
            </div>

            <div class="docente-info-wrapper">
                <!-- Inputs Básicos (Grid 2x2) -->
                <div class="docente-inputs-grid">
                    <!-- Nombre -->
                    <div class="form-group input-icon">
                        <label>Nombre <span style="color:red;">*</span></label>
                        <i class="fa-solid fa-user"></i>
                        <input type="text" class="form-control" name="nombre" id="docente_nombre" required placeholder="Ej: Juan" value="<?= htmlspecialchars($docente['nombre'] ?? '') ?>" autocomplete="off">
                    </div>

                    <!-- Apellido -->
                    <div class="form-group input-icon">
                        <label>Apellido <span style="color:red;">*</span></label>
                        <i class="fa-solid fa-user-tag"></i>
                        <input type="text" class="form-control" name="apellido" id="docente_apellido" required placeholder="Ej: Pérez" value="<?= htmlspecialchars($docente['apellido'] ?? '') ?>" autocomplete="off">
                    </div>

                    <!-- Correo Institucional -->
                    <div class="form-group input-icon">
                        <label>Correo institucional <span style="color:red;">*</span></label>
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" class="form-control" name="correo" id="docente_correo" required placeholder="Ej: juan.perez@unifet.edu.co" value="<?= htmlspecialchars($docente['correo'] ?? '') ?>">
                    </div>

                    <!-- Estado -->
                    <div class="form-group input-icon">
                        <label>Estado</label>
                        <i class="fa-solid fa-toggle-on"></i>
                        <select class="form-control" name="estado" id="docente_estado">
                            <option value="ACTIVO" <?= (($docente['estado'] ?? 'ACTIVO') === 'ACTIVO') ? 'selected' : '' ?>>Activo</option>
                            <option value="INACTIVO" <?= (($docente['estado'] ?? '') === 'INACTIVO') ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Tarjeta Estética del Código con Marca de Agua (Isotipo UniFET) -->
                <div class="codigo-docente-card">
                    <div class="codigo-card-top">
                        <span class="codigo-card-label">
                            <i class="fa-solid fa-id-card-clip"></i> Código institucional
                        </span>
                        <span class="badge-auto-pill" id="badge_codigo_modo">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Automático
                        </span>
                    </div>

                    <div class="codigo-input-container">
                        <input type="text" class="codigo-display-input" name="codigo" id="docente_codigo" required readonly value="<?= htmlspecialchars($docente['codigo'] ?? '') ?>" placeholder="JPE001" autocomplete="off">
                    </div>

                    <div class="codigo-card-bottom">
                        <span id="code_hint_text">
                            <i class="fa-solid fa-shield-halved"></i> Consecutivo único institucional
                        </span>
                        <button type="button" class="btn-codigo-toggle" id="toggle_codigo_manual">
                            <i class="fa-solid fa-pen-to-square"></i> Modificar
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- 2. PERFIL ACADÉMICO DEL DOCENTE                       -->
            <!-- ===================================================== -->
            <div class="docente-section-title" style="margin-top: 36px;">
                <span class="section-step">2</span>
                <span>Perfil académico del docente</span>
            </div>
            <p style="font-size: 13px; color: var(--color-texto-secundario); margin: -8px 0 16px 0;">
                Asocia los programas académicos y selecciona las asignaturas específicas que el docente está habilitado para impartir en cada uno.
            </p>

            <!-- Bloque de Selección y Configuración de Programa -->
            <div class="configurador-programa-box" id="bloque_configurador_programa">
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
                    <h3 style="font-size:15px; font-weight:700; color:var(--color-oscuro); margin:0; display:flex; align-items:center; gap:8px;" id="titulo_bloque_programa">
                        <i class="fa-solid fa-plus-circle" style="color:var(--color-primario);"></i> Agregar Programa al Docente
                    </h3>
                    <span id="badge_estado_edicion_prog" style="display:none;" class="badge proceso">Editando programa</span>
                </div>

                <!-- Filtro por Nivel Académico y Selector de Programa -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-size:12.5px; font-weight:600; color:var(--color-texto-secundario);">Filtrar por nivel académico</label>
                    <select id="filtro_nivel_academico" class="form-control" style="max-width: 380px;">
                        <option value="">Todos los niveles académicos</option>
                        <?php foreach (($nivelesAcademicos ?? []) as $nivel): ?>
                            <option value="<?= (int)$nivel['id_nivel_academico'] ?>"><?= htmlspecialchars($nivel['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Selector de Programa -->
                <div class="form-group" style="margin-top:12px; margin-bottom:16px;">
                    <label style="font-size:13px; font-weight:600; color:var(--color-oscuro);">Selecciona el programa <span style="color:red;">*</span></label>
                    <select id="select_programa_activo" class="form-control" style="font-weight: 500;">
                        <option value="">-- Elige un programa académico --</option>
                        <?php foreach (($programas ?? []) as $prog): ?>
                            <option value="<?= (int)$prog['id_programa'] ?>" 
                                    data-nombre="<?= htmlspecialchars($prog['nombre']) ?>" 
                                    data-codigo="<?= htmlspecialchars($prog['codigo']) ?>" 
                                    data-nivel-id="<?= (int)($prog['id_nivel_academico'] ?? 0) ?>" 
                                    data-nivel-nombre="<?= htmlspecialchars($prog['nivel_nombre'] ?? 'General') ?>">
                                <?= htmlspecialchars($prog['nombre']) ?> (<?= htmlspecialchars($prog['codigo']) ?>) - <?= htmlspecialchars($prog['nivel_nombre'] ?? 'General') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tarjeta Preview del Programa Seleccionado -->
                <div id="panel_info_programa_seleccionado" class="programa-preview-card" style="display:none;">
                    <div class="prog-info">
                        <h4 id="preview_prog_nombre">Nombre del Programa</h4>
                        <div class="prog-meta">
                            <span class="code-pill" id="preview_prog_codigo">IS-001</span>
                            <span class="badge" style="background:#e0e7ff; color:#3730a3;" id="preview_prog_nivel">Pregrado</span>
                        </div>
                    </div>
                    <div style="font-size:12px; color:var(--color-texto-secundario);">
                        <i class="fa-solid fa-check-circle" style="color:var(--color-secundario);"></i> Programa activo
                    </div>
                </div>

                <!-- Contenedor de Asignaturas del Programa Seleccionado -->
                <div id="contenedor_asignaturas_programa" style="display:none;">
                    <div class="asignaturas-selection-area">
                        <!-- Barra de herramientas superior -->
                        <div class="asignaturas-toolbar">
                            <div class="asignaturas-search-box">
                                <i class="fa-solid fa-search"></i>
                                <input type="text" id="buscar_asignatura_input" class="form-control" placeholder="Buscar asignatura...">
                            </div>
                            
                            <div class="asignaturas-quick-actions">
                                <button type="button" class="btn-toolbar-action" id="btn_seleccionar_todas_asigs">
                                    <i class="fa-solid fa-check-double" style="color:var(--color-secundario);"></i> Seleccionar todas
                                </button>
                                <button type="button" class="btn-toolbar-action" id="btn_limpiar_seleccion_asigs">
                                    <i class="fa-solid fa-xmark" style="color:#dc3545;"></i> Limpiar selección
                                </button>
                                <span class="contador-badge" id="badge_contador_asigs">
                                    <i class="fa-solid fa-list-check"></i> <span id="contador_num_asigs">0</span> asignaturas seleccionadas
                                </span>
                            </div>
                        </div>

                        <!-- Lista con checkboxes de asignaturas -->
                        <div class="asignaturas-checkbox-list" id="lista_checkboxes_asignaturas">
                            <!-- Filas inyectadas dinámicamente -->
                        </div>

                        <!-- Mensaje si no hay asignaturas registradas para el programa -->
                        <div id="mensaje_sin_asignaturas_programa" style="display:none; padding:24px; text-align:center; color:var(--color-texto-secundario); font-size:13.5px;">
                            <i class="fa-solid fa-folder-open" style="font-size:24px; opacity:0.5; margin-bottom:6px; display:block;"></i>
                            Este programa no tiene asignaturas asociadas en su plan de estudios.
                        </div>
                    </div>

                    <!-- Botón para añadir/actualizar el programa a la configuración -->
                    <div style="margin-top: 16px; display:flex; gap:10px; align-items:center;">
                        <button type="button" id="btn_confirmar_agregar_programa" class="btn-primario" style="padding:9px 18px; font-size:14px;">
                            <i class="fa-solid fa-plus"></i> <span id="texto_btn_agregar_prog">Agregar este programa</span>
                        </button>
                        <button type="button" id="btn_cancelar_edicion_prog" class="btn-outline" style="display:none; padding:8px 14px; font-size:13.5px;">
                            <i class="fa-solid fa-xmark"></i> Cancelar edición
                        </button>
                    </div>
                </div>

                <!-- Mensaje placeholder cuando no hay programa seleccionado -->
                <div id="placeholder_selecciona_programa" style="padding:22px; text-align:center; color:var(--color-texto-secundario); font-size:13.5px; border:1px dashed #CBD5E1; border-radius:8px; margin-top:8px;">
                    <i class="fa-solid fa-hand-pointer" style="color:var(--color-primario); font-size:20px; margin-bottom:6px; display:block;"></i>
                    Selecciona un programa académico arriba para visualizar y seleccionar sus asignaturas disponibles.
                </div>

            </div>

            <!-- ===================================================== -->
            <!-- 3. RESUMEN: PROGRAMAS CONFIGURADOS                    -->
            <!-- ===================================================== -->
            <div class="docente-section-title" style="margin-top: 32px;">
                <span class="section-step">3</span>
                <span>Programas configurados</span>
                <span class="badge" style="background:#e0e7ff; color:#3730a3; margin-left:auto;" id="badge_total_programas_configurados">0 programas</span>
            </div>
            <p style="font-size: 13px; color: var(--color-texto-secundario); margin: -8px 0 16px 0;">
                Revisa todos los programas y asignaturas que quedarán asignados a este docente. Puedes agregar más programas o editar los existentes antes de guardar.
            </p>

            <div id="contenedor_programas_configurados" class="programas-configurados-container">
                <!-- Se renderizan dinámicamente las tarjetas de programas configurados -->
            </div>

            <div id="empty_state_configurados" class="empty-configurados-state">
                <i class="fa-regular fa-folder-open"></i>
                <h4>Ningún programa configurado</h4>
                <p>Elige un programa en la sección superior, selecciona al menos una asignatura y presiona <strong>«Agregar este programa»</strong>.</p>
            </div>

            <!-- ===================================================== -->
            <!-- 4. BOTONES DE ACCIÓN FINAL                           -->
            <!-- ===================================================== -->
            <div class="form-footer-actions">
                <a class="btn-outline" href="?ruta=admin/academico/docentes">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" id="btn_guardar_docente" class="btn-primario" style="padding: 11px 24px; font-size: 15px; font-weight: 600;">
                    <i class="fa-solid fa-check"></i> Guardar docente
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================
     LÓGICA JAVASCRIPT DEL FORMULARIO DE DOCENTES UNIFET
     ========================================================= -->
<script>
// Catálogo completo de programas
const catalogoProgramas = <?= json_encode(array_map(function ($p) {
    return [
        'id_programa'        => (int)$p['id_programa'],
        'nombre'             => $p['nombre'],
        'codigo'             => $p['codigo'],
        'id_nivel_academico' => (int)($p['id_nivel_academico'] ?? 0),
        'nivel_nombre'       => $p['nivel_nombre'] ?? 'General',
    ];
}, $programas ?? [])) ?>;

// Perfil académico inicial (si es edición de docente existente)
const perfilInicial = <?= json_encode($perfilAcademico ?? []) ?>;
const esEdicion = <?= $esEdicion ? 'true' : 'false' ?>;
const idDocenteActual = <?= $idDocente ?>;

// Estado en memoria de los programas configurados para este docente
// Estructura: [ { id_programa, programa_nombre, programa_codigo, nivel_nombre, asignaturas: [ { id_asignatura, codigo, nombre, creditos, semestre } ] } ]
let programasConfigurados = [];

// Cache de asignaturas por programa para no repetir peticiones innecesarias
const cacheAsignaturasPorPrograma = {};

// Control de programa en edición actualmente
let programaEnEdicionId = null;

// Control de edición manual de código
let codigoManualHabilitado = false;

// Utilidad para limpiar acentos
function normalizarTexto(str) {
    return (str || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toUpperCase();
}

// Generación de previsualización de código en Frontend
function previsualizarCodigoFrontend() {
    if (codigoManualHabilitado) return;
    if (esEdicion && document.getElementById('docente_codigo').value !== '') return;

    const nombre = document.getElementById('docente_nombre').value.trim();
    const apellido = document.getElementById('docente_apellido').value.trim();

    if (!nombre && !apellido) {
        document.getElementById('docente_codigo').value = '';
        return;
    }

    const normNom = normalizarTexto(nombre).replace(/[^A-Z]/g, '');
    const normApe = normalizarTexto(apellido).replace(/[^A-Z]/g, '');

    const letraNom = normNom.charAt(0) || 'D';
    let letrasApe = normApe.substring(0, 2);
    if (letrasApe.length < 2) {
        letrasApe = letrasApe.padEnd(2, 'X');
    }

    const prefijo = letraNom + letrasApe;
    document.getElementById('docente_codigo').value = prefijo + '001';
}

// Solicitar al backend el código sugerido definitivo con consecutivo real
async function consultarCodigoSugeridoBackend() {
    if (codigoManualHabilitado) return;
    if (esEdicion && document.getElementById('docente_codigo').value !== '') return;

    const nombre = document.getElementById('docente_nombre').value.trim();
    const apellido = document.getElementById('docente_apellido').value.trim();

    if (nombre.length < 2 || apellido.length < 2) return;

    try {
        const url = `?ruta=admin/academico/docentes/api_generar_codigo&nombre=${encodeURIComponent(nombre)}&apellido=${encodeURIComponent(apellido)}${idDocenteActual ? '&id_docente=' + idDocenteActual : ''}`;
        const res = await fetch(url);
        const data = await res.json();
        if (data && data.codigo) {
            document.getElementById('docente_codigo').value = data.codigo;
        }
    } catch (e) {
        console.warn('No se pudo obtener el consecutivo de backend:', e);
    }
}

// Filtrar select de programas según Nivel Académico
function filtrarOpcionesPrograma() {
    const nivelId = document.getElementById('filtro_nivel_academico').value;
    const select = document.getElementById('select_programa_activo');
    const valorSeleccionadoActual = select.value;

    select.innerHTML = '<option value="">-- Elige un programa académico --</option>';

    catalogoProgramas.forEach(prog => {
        const cumpleNivel = !nivelId || prog.id_nivel_academico === parseInt(nivelId);

        if (cumpleNivel) {
            const opt = document.createElement('option');
            opt.value = prog.id_programa;
            opt.setAttribute('data-nombre', prog.nombre);
            opt.setAttribute('data-codigo', prog.codigo);
            opt.setAttribute('data-nivel-id', prog.id_nivel_academico);
            opt.setAttribute('data-nivel-nombre', prog.nivel_nombre);
            opt.textContent = `${prog.nombre} (${prog.codigo}) - ${prog.nivel_nombre}`;
            if (valorSeleccionadoActual && parseInt(valorSeleccionadoActual) === prog.id_programa) {
                opt.selected = true;
            }
            select.appendChild(opt);
        }
    });
}

// Cargar asignaturas de un programa desde el backend o caché
async function cargarAsignaturasPrograma(idPrograma) {
    if (!idPrograma) return [];
    if (cacheAsignaturasPorPrograma[idPrograma]) {
        return cacheAsignaturasPorPrograma[idPrograma];
    }
    try {
        const res = await fetch(`?ruta=admin/academico/docentes/api_asignaturas&id_programa=${idPrograma}`);
        const asigs = await res.json();
        cacheAsignaturasPorPrograma[idPrograma] = asigs || [];
        return cacheAsignaturasPorPrograma[idPrograma];
    } catch (err) {
        console.error('Error al cargar asignaturas:', err);
        return [];
    }
}

// Renderizar lista de checkboxes de asignaturas para el programa seleccionado
async function renderizarCheckboxesAsignaturas(idPrograma, asignaturasPreseleccionadasIds = []) {
    const contenedor = document.getElementById('lista_checkboxes_asignaturas');
    const panelAsigs = document.getElementById('contenedor_asignaturas_programa');
    const panelPlaceholder = document.getElementById('placeholder_selecciona_programa');
    const panelInfoProg = document.getElementById('panel_info_programa_seleccionado');
    const sinAsignaturasMsg = document.getElementById('mensaje_sin_asignaturas_programa');

    if (!idPrograma) {
        panelAsigs.style.display = 'none';
        panelInfoProg.style.display = 'none';
        panelPlaceholder.style.display = 'block';
        return;
    }

    panelPlaceholder.style.display = 'none';
    panelInfoProg.style.display = 'flex';
    panelAsigs.style.display = 'block';

    // Actualizar tarjeta preview del programa
    const progData = catalogoProgramas.find(p => p.id_programa === idPrograma);
    if (progData) {
        document.getElementById('preview_prog_nombre').textContent = progData.nombre;
        document.getElementById('preview_prog_codigo').textContent = progData.codigo;
        document.getElementById('preview_prog_nivel').textContent = progData.nivel_nombre;
    }

    contenedor.innerHTML = '<div style="padding:16px; text-align:center; color:var(--color-texto-secundario);"><i class="fa-solid fa-spinner fa-spin"></i> Cargando asignaturas del programa...</div>';

    const asignaturas = await cargarAsignaturasPrograma(idPrograma);

    if (asignaturas.length === 0) {
        contenedor.innerHTML = '';
        sinAsignaturasMsg.style.display = 'block';
        actualizarContadorAsignaturas();
        return;
    }

    sinAsignaturasMsg.style.display = 'none';
    contenedor.innerHTML = '';

    const idsPreseleccionados = new Set(asignaturasPreseleccionadasIds.map(a => typeof a === 'object' ? a.id_asignatura : parseInt(a)));

    asignaturas.forEach(a => {
        const isChecked = idsPreseleccionados.has(parseInt(a.id_asignatura));
        const fila = document.createElement('label');
        fila.className = `asignatura-item-row ${isChecked ? 'selected' : ''}`;
        fila.setAttribute('data-asig-id', a.id_asignatura);
        fila.setAttribute('data-asig-nombre', normalizarTexto(a.nombre + ' ' + a.codigo));

        fila.innerHTML = `
            <input type="checkbox" class="chk-asignatura" value="${a.id_asignatura}" 
                   data-codigo="${a.codigo}" 
                   data-nombre="${a.nombre}" 
                   data-creditos="${a.creditos || 0}" 
                   data-semestre="${a.semestre || ''}" 
                   ${isChecked ? 'checked' : ''}>
            <div class="asignatura-item-info">
                <div class="asignatura-item-nombre">
                    <span class="code-pill" style="margin-right:6px;">${a.codigo}</span>
                    <span>${a.nombre}</span>
                </div>
                <div class="asignatura-item-tags">
                    ${a.semestre ? `<span class="semestre-tag">Semestre ${a.semestre}</span>` : ''}
                    <span class="creditos-tag">${a.creditos || 0} créditos</span>
                </div>
            </div>
        `;

        fila.querySelector('input[type="checkbox"]').addEventListener('change', function () {
            fila.classList.toggle('selected', this.checked);
            actualizarContadorAsignaturas();
        });

        contenedor.appendChild(fila);
    });

    actualizarContadorAsignaturas();
}

// Actualizar contador reactivo de asignaturas seleccionadas
function actualizarContadorAsignaturas() {
    const checks = document.querySelectorAll('#lista_checkboxes_asignaturas .chk-asignatura:checked');
    const total = checks.length;
    document.getElementById('contador_num_asigs').textContent = total;
    const badge = document.getElementById('badge_contador_asigs');
    if (total > 0) {
        badge.style.background = 'rgba(15, 157, 88, 0.12)';
        badge.style.color = 'var(--color-secundario)';
    } else {
        badge.style.background = 'rgba(59, 13, 143, 0.1)';
        badge.style.color = 'var(--color-primario)';
    }
}

// Filtrar checkboxes de asignaturas según buscador de texto en tiempo real
function filtrarCheckboxesAsignaturas() {
    const texto = normalizarTexto(document.getElementById('buscar_asignatura_input').value);
    const filas = document.querySelectorAll('#lista_checkboxes_asignaturas .asignatura-item-row');
    filas.forEach(f => {
        const info = f.getAttribute('data-asig-nombre') || '';
        if (!texto || info.includes(texto)) {
            f.style.display = 'flex';
        } else {
            f.style.display = 'none';
        }
    });
}

// Renderizar lista completa de tarjetas de programas configurados
function renderizarProgramasConfigurados() {
    const contenedor = document.getElementById('contenedor_programas_configurados');
    const emptyState = document.getElementById('empty_state_configurados');
    const totalBadge = document.getElementById('badge_total_programas_configurados');

    totalBadge.textContent = `${programasConfigurados.length} programa${programasConfigurados.length === 1 ? '' : 's'}`;

    if (programasConfigurados.length === 0) {
        contenedor.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    contenedor.innerHTML = '';

    programasConfigurados.forEach((item, index) => {
        const card = document.createElement('div');
        card.className = 'programa-configurado-card';
        card.id = `card-prog-config-${item.id_programa}`;

        const chipsAsignaturas = item.asignaturas.map(a => `
            <div class="asig-chip">
                <strong>${a.codigo}</strong>
                <span>${a.nombre}</span>
                <span class="creditos-tag">${a.creditos || 0} cr</span>
                ${a.semestre ? `<span class="semestre-tag" style="font-size:10.5px;">Sem ${a.semestre}</span>` : ''}
            </div>
        `).join('');

        card.innerHTML = `
            <div class="prog-card-header">
                <div class="prog-card-title-group">
                    <span class="code-pill">${item.programa_codigo}</span>
                    <h4 class="prog-card-title">${item.programa_nombre}</h4>
                    <span class="badge" style="background:#e0e7ff; color:#3730a3;">${item.nivel_nombre}</span>
                    <span class="badge activo"><i class="fa-solid fa-check"></i> ${item.asignaturas.length} asignatura${item.asignaturas.length === 1 ? '' : 's'}</span>
                </div>
                <div class="prog-card-actions">
                    <button type="button" class="btn-card-accion btn-card-editar" onclick="editarProgramaConfigurado(${item.id_programa})">
                        <i class="fa-solid fa-pen"></i> Editar
                    </button>
                    <button type="button" class="btn-card-accion btn-card-eliminar" onclick="eliminarProgramaConfigurado(${item.id_programa})">
                        <i class="fa-solid fa-trash-can"></i> Eliminar
                    </button>
                </div>
            </div>
            <div class="prog-card-asigs-list">
                ${chipsAsignaturas}
            </div>
        `;

        contenedor.appendChild(card);
    });
}

// Agregar o actualizar el programa actualmente configurado
function agregarOActualizarPrograma() {
    const select = document.getElementById('select_programa_activo');
    const idPrograma = parseInt(select.value);

    if (!idPrograma) {
        Swal.fire({
            icon: 'warning',
            title: 'Programa no seleccionado',
            text: 'Por favor selecciona un programa académico antes de agregarlo.',
            confirmButtonColor: '#3B0D8F'
        });
        return;
    }

    const progData = catalogoProgramas.find(p => p.id_programa === idPrograma);
    if (!progData) return;

    // Obtener asignaturas seleccionadas
    const checkboxes = document.querySelectorAll('#lista_checkboxes_asignaturas .chk-asignatura:checked');
    if (checkboxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Asignaturas requeridas',
            text: 'Debes seleccionar al menos una asignatura para este programa.',
            confirmButtonColor: '#3B0D8F'
        });
        return;
    }

    const asignaturasSeleccionadas = [];
    checkboxes.forEach(chk => {
        asignaturasSeleccionadas.push({
            id_asignatura: parseInt(chk.value),
            codigo: chk.getAttribute('data-codigo'),
            nombre: chk.getAttribute('data-nombre'),
            creditos: parseInt(chk.getAttribute('data-creditos')) || 0,
            semestre: chk.getAttribute('data-semestre') ? parseInt(chk.getAttribute('data-semestre')) : null
        });
    });

    // Validar duplicidad de programa si no estamos editando ese mismo programa
    const indexExistente = programasConfigurados.findIndex(p => p.id_programa === idPrograma);

    if (programaEnEdicionId !== null) {
        // Estamos en modo edición
        if (programaEnEdicionId === idPrograma) {
            programasConfigurados[indexExistente] = {
                id_programa: idPrograma,
                programa_nombre: progData.nombre,
                programa_codigo: progData.codigo,
                nivel_nombre: progData.nivel_nombre,
                asignaturas: asignaturasSeleccionadas
            };
            cancelarModoEdicionPrograma();
        } else {
            if (indexExistente !== -1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Programa ya agregado',
                    text: 'Este programa ya fue agregado al docente. Puedes editarlo desde la lista inferior.',
                    confirmButtonColor: '#3B0D8F'
                });
                return;
            }
            // Reemplazar el viejo por el nuevo
            const indexViejo = programasConfigurados.findIndex(p => p.id_programa === programaEnEdicionId);
            if (indexViejo !== -1) {
                programasConfigurados.splice(indexViejo, 1);
            }
            programasConfigurados.push({
                id_programa: idPrograma,
                programa_nombre: progData.nombre,
                programa_codigo: progData.codigo,
                nivel_nombre: progData.nivel_nombre,
                asignaturas: asignaturasSeleccionadas
            });
            cancelarModoEdicionPrograma();
        }
    } else {
        // Modo nuevo agregado
        if (indexExistente !== -1) {
            Swal.fire({
                icon: 'warning',
                title: 'Programa ya agregado',
                text: 'Este programa ya fue agregado al docente. Puedes editar sus asignaturas en la lista de programas configurados.',
                confirmButtonColor: '#3B0D8F'
            });
            return;
        }

        programasConfigurados.push({
            id_programa: idPrograma,
            programa_nombre: progData.nombre,
            programa_codigo: progData.codigo,
            nivel_nombre: progData.nivel_nombre,
            asignaturas: asignaturasSeleccionadas
        });
    }

    renderizarProgramasConfigurados();

    // Resetear selector y configurador para permitir agregar otro programa cómodamente
    if (programaEnEdicionId === null) {
        select.value = '';
        renderizarCheckboxesAsignaturas(null);
    }
}

// Iniciar edición de un programa configurado
function editarProgramaConfigurado(idPrograma) {
    const prog = programasConfigurados.find(p => p.id_programa === idPrograma);
    if (!prog) return;

    programaEnEdicionId = idPrograma;

    // Actualizar UI del configurador
    const box = document.getElementById('bloque_configurador_programa');
    box.classList.add('en-edicion');
    document.getElementById('badge_estado_edicion_prog').style.display = 'inline-flex';
    document.getElementById('titulo_bloque_programa').innerHTML = `<i class="fa-solid fa-pen" style="color:var(--color-primario);"></i> Modificar programa: <strong>${prog.programa_nombre}</strong>`;
    document.getElementById('texto_btn_agregar_prog').textContent = 'Actualizar asignaturas del programa';
    document.getElementById('btn_cancelar_edicion_prog').style.display = 'inline-flex';

    // Seleccionar programa en el select y cargar sus asignaturas con las preseleccionadas
    const select = document.getElementById('select_programa_activo');
    select.value = idPrograma;

    renderizarCheckboxesAsignaturas(idPrograma, prog.asignaturas);

    // Scroll suave hacia el configurador
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Cancelar modo de edición de un programa
function cancelarModoEdicionPrograma() {
    programaEnEdicionId = null;
    const box = document.getElementById('bloque_configurador_programa');
    box.classList.remove('en-edicion');
    document.getElementById('badge_estado_edicion_prog').style.display = 'none';
    document.getElementById('titulo_bloque_programa').innerHTML = `<i class="fa-solid fa-plus-circle" style="color:var(--color-primario);"></i> Agregar Programa al Docente`;
    document.getElementById('texto_btn_agregar_prog').textContent = 'Agregar este programa';
    document.getElementById('btn_cancelar_edicion_prog').style.display = 'none';

    document.getElementById('select_programa_activo').value = '';
    renderizarCheckboxesAsignaturas(null);
}

// Eliminar un programa configurado
function eliminarProgramaConfigurado(idPrograma) {
    const prog = programasConfigurados.find(p => p.id_programa === idPrograma);
    if (!prog) return;

    Swal.fire({
        title: `¿Retirar ${prog.programa_nombre}?`,
        text: 'Se eliminará este programa y sus asignaturas de la configuración de este docente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, retirar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#E2E5E9',
        customClass: { cancelButton: 'swal-btn-cancelar' }
    }).then((res) => {
        if (res.isConfirmed) {
            programasConfigurados = programasConfigurados.filter(p => p.id_programa !== idPrograma);
            if (programaEnEdicionId === idPrograma) {
                cancelarModoEdicionPrograma();
            }
            renderizarProgramasConfigurados();
        }
    });
}

// =========================================================
// INICIALIZACIÓN DE EVENTOS Y CARGA
// =========================================================
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Cargar perfil inicial si es edición
    if (perfilInicial && perfilInicial.length > 0) {
        programasConfigurados = JSON.parse(JSON.stringify(perfilInicial));
        renderizarProgramasConfigurados();
    } else {
        renderizarProgramasConfigurados();
    }

    // 2. Generación automática y previsualización de código
    const inputNombre = document.getElementById('docente_nombre');
    const inputApellido = document.getElementById('docente_apellido');

    inputNombre.addEventListener('input', previsualizarCodigoFrontend);
    inputApellido.addEventListener('input', previsualizarCodigoFrontend);
    inputNombre.addEventListener('blur', consultarCodigoSugeridoBackend);
    inputApellido.addEventListener('blur', consultarCodigoSugeridoBackend);

    // 3. Toggle para desbloquear edición manual de código
    document.getElementById('toggle_codigo_manual').addEventListener('click', function () {
        const inputCodigo = document.getElementById('docente_codigo');
        const badgeModo = document.getElementById('badge_codigo_modo');
        const hintText = document.getElementById('code_hint_text');

        if (inputCodigo.hasAttribute('readonly')) {
            inputCodigo.removeAttribute('readonly');
            inputCodigo.style.backgroundColor = '#ffffff';
            inputCodigo.focus();
            codigoManualHabilitado = true;
            this.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Restablecer auto';
            if (badgeModo) {
                badgeModo.className = 'badge-auto-pill modo-manual';
                badgeModo.innerHTML = '<i class="fa-solid fa-pen"></i> Manual';
            }
            if (hintText) {
                hintText.innerHTML = '<i class="fa-solid fa-keyboard"></i> Ingreso de código manual habilitado';
            }
        } else {
            inputCodigo.setAttribute('readonly', 'readonly');
            inputCodigo.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
            codigoManualHabilitado = false;
            this.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Modificar';
            if (badgeModo) {
                badgeModo.className = 'badge-auto-pill';
                badgeModo.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Automático';
            }
            if (hintText) {
                hintText.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Consecutivo único institucional';
            }
            previsualizarCodigoFrontend();
            consultarCodigoSugeridoBackend();
        }
    });

    // 4. Filtro del selector de programas por nivel académico
    document.getElementById('filtro_nivel_academico').addEventListener('change', filtrarOpcionesPrograma);

    // 5. Cambio en el selector de programa activo
    document.getElementById('select_programa_activo').addEventListener('change', function () {
        const idProg = parseInt(this.value);
        renderizarCheckboxesAsignaturas(idProg);
    });

    // 6. Botones de acción en asignaturas
    document.getElementById('btn_seleccionar_todas_asigs').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('#lista_checkboxes_asignaturas .chk-asignatura');
        checkboxes.forEach(chk => {
            chk.checked = true;
            chk.closest('.asignatura-item-row').classList.add('selected');
        });
        actualizarContadorAsignaturas();
    });

    document.getElementById('btn_limpiar_seleccion_asigs').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('#lista_checkboxes_asignaturas .chk-asignatura');
        checkboxes.forEach(chk => {
            chk.checked = false;
            chk.closest('.asignatura-item-row').classList.remove('selected');
        });
        actualizarContadorAsignaturas();
    });

    document.getElementById('buscar_asignatura_input').addEventListener('input', filtrarCheckboxesAsignaturas);

    // 7. Botón agregar/actualizar programa
    document.getElementById('btn_confirmar_agregar_programa').addEventListener('click', agregarOActualizarPrograma);
    document.getElementById('btn_cancelar_edicion_prog').addEventListener('click', cancelarModoEdicionPrograma);

    // 8. Validación y envío unificado del formulario
    document.getElementById('form-docente').addEventListener('submit', function (e) {
        const nombre = inputNombre.value.trim();
        const apellido = inputApellido.value.trim();
        const correo = document.getElementById('docente_correo').value.trim();
        const codigo = document.getElementById('docente_codigo').value.trim();

        if (!nombre) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Nombre requerido', text: 'Por favor ingresa el nombre del docente.', confirmButtonColor: '#3B0D8F' });
            inputNombre.focus();
            return false;
        }

        if (!apellido) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Apellido requerido', text: 'Por favor ingresa el apellido del docente.', confirmButtonColor: '#3B0D8F' });
            inputApellido.focus();
            return false;
        }

        if (!correo) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Correo requerido', text: 'Por favor ingresa el correo institucional del docente.', confirmButtonColor: '#3B0D8F' });
            document.getElementById('docente_correo').focus();
            return false;
        }

        if (programasConfigurados.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Perfil académico incompleto',
                text: 'Debes asociar al menos un programa académico con sus asignaturas antes de guardar el docente.',
                confirmButtonColor: '#3B0D8F'
            });
            document.getElementById('select_programa_activo').focus();
            return false;
        }

        // Preparar estructura para enviar al backend
        const payloadProgramas = programasConfigurados.map(p => ({
            id_programa: p.id_programa,
            asignaturas: p.asignaturas.map(a => a.id_asignatura)
        }));

        document.getElementById('input_perfil_academico').value = JSON.stringify(payloadProgramas);
    });

    // Si es creación y ya hay nombre o apellido cargados, generar código
    if (!esEdicion) {
        previsualizarCodigoFrontend();
    }
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
