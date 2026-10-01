<?php
$esEdicion = !empty($sedeCompleta['id_sede']);
$this->render('elementos_compartidos/header', ['titulo' => ($esEdicion ? 'Editar Sede' : 'Nueva Sede') . ' — UniFET']);
$this->render('elementos_compartidos/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => true],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('elementos_compartidos/tabs', ['tabs' => $tabs]);

// Data for JS
$bloquesPrevios = $sedeCompleta['bloques'] ?? [];
?>

<style>
/* =========================================================
   FORMULARIO UNIFICADO SEDE-BLOQUE-ESPACIO — UniFET
   ========================================================= */

/* Page watermark */
.form-page-bg {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.form-page-bg img {
    width: min(60vw, 500px);
    opacity: 0.025;
    user-select: none;
}

/* Main container */
.sede-form-wrap {
    position: relative;
    z-index: 1;
    max-width: 920px;
    margin: 0 auto;
}

/* Hero */
.sede-form-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 60%, #1a0a3e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
}
.sede-form-hero::before {
    content: '';
    position: absolute;
    right: -20px; top: -20px;
    width: 160px; height: 160px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.07;
    pointer-events: none;
}
.sede-form-hero i { font-size: 36px; opacity: .85; }
.sede-form-hero h1 { font-size: 20px; font-weight: 700; margin: 0 0 3px; }
.sede-form-hero p { color: rgba(255,255,255,.75); font-size: 13.5px; margin: 0; }

/* Section card */
.form-section-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 14px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: var(--sombra-tarjeta);
    position: relative;
}
.form-section-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('assets/img/isotipo-color.png') no-repeat right -30px bottom -30px / 200px;
    opacity: 0.025;
    pointer-events: none;
    border-radius: inherit;
}
.form-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--color-borde);
    background: var(--color-superficie);
}
.section-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--color-primario);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.form-section-header h2 {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-oscuro);
    margin: 0;
}
.form-section-header p {
    font-size: 12.5px;
    color: var(--color-texto-secundario);
    margin: 0;
}
.form-section-body {
    padding: 22px;
    position: relative;
    z-index: 1;
}

/* Form grid */
.fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(240px, 100%), 1fr));
    gap: 16px;
}
.field-full { grid-column: 1 / -1; }

/* Auto-code widget */
.auto-code-field {
    position: relative;
}
.auto-code-input-wrap {
    display: flex;
    gap: 0;
    border: 1px solid var(--color-borde);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.auto-code-input-wrap:focus-within {
    border-color: var(--color-primario);
    box-shadow: 0 0 0 3px rgba(59,13,143,.12);
}
.auto-code-input-wrap input {
    flex: 1;
    border: none;
    outline: none;
    padding: 10px 14px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-primario);
    letter-spacing: .5px;
    background: rgba(59,13,143,.03);
    text-transform: uppercase;
}
.auto-code-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 0 12px;
    background: rgba(59,13,143,.06);
    color: var(--color-primario);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-left: 1px solid var(--color-borde);
    white-space: nowrap;
    cursor: default;
}
.auto-code-badge.manual {
    background: rgba(245,179,1,.08);
    color: #92400e;
    border-left-color: #fbbf24;
}
.auto-code-hint {
    font-size: 11.5px;
    color: var(--color-texto-secundario);
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Bloque cards */
.bloques-list { display: flex; flex-direction: column; gap: 18px; }

.bloque-card {
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow .25s;
    background: #fafbfc;
}
.bloque-card:hover { box-shadow: 0 4px 16px rgba(59,13,143,.08); }
.bloque-card.collapsed .bloque-body { display: none; }

.bloque-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #f1f4f9;
    border-bottom: 1px solid var(--color-borde);
    cursor: pointer;
    user-select: none;
}
.bloque-number {
    width: 30px; height: 30px;
    border-radius: 6px;
    background: var(--color-primario);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
    flex-shrink: 0;
}
.bloque-header-info { flex: 1; }
.bloque-header-info h3 { font-size: 14px; font-weight: 700; color: var(--color-oscuro); margin: 0 0 2px; }
.bloque-header-info p { font-size: 12px; color: var(--color-texto-secundario); margin: 0; }
.bloque-collapse-btn {
    background: none; border: none; cursor: pointer; padding: 5px;
    color: var(--color-texto-secundario); font-size: 16px;
    transition: transform .25s;
}
.bloque-card:not(.collapsed) .bloque-collapse-btn { transform: rotate(180deg); }
.btn-eliminar-bloque {
    background: none;
    border: 1px solid #fca5a5;
    color: #dc2626;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all .2s;
}
.btn-eliminar-bloque:hover { background: #fee2e2; }

.bloque-body { padding: 18px; display: flex; flex-direction: column; gap: 18px; }
.bloque-fields { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px,1fr)); gap: 14px; }

/* Espacios list inside bloque */
.espacios-section { margin-top: 6px; }
.espacios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.espacios-section-header h4 {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--color-oscuro);
    display: flex;
    align-items: center;
    gap: 7px;
}
.espacios-list { display: flex; flex-direction: column; gap: 10px; }

.espacio-row {
    display: grid;
    grid-template-columns: 1fr 1fr 100px 80px 32px;
    gap: 10px;
    align-items: start;
    padding: 12px 14px;
    background: #fff;
    border: 1px solid #e8ecf0;
    border-radius: 8px;
    transition: border-color .2s, background .2s;
}
.espacio-row:hover { border-color: rgba(59,13,143,.2); background: rgba(59,13,143,.01); }
.espacio-row .form-group { margin-bottom: 0; }
.espacio-row label { font-size: 11px; font-weight: 600; color: var(--color-texto-secundario); margin-bottom: 4px; }
.btn-del-espacio {
    width: 32px; height: 32px;
    border: 1px solid #fca5a5;
    background: none;
    border-radius: 6px;
    color: #dc2626;
    cursor: pointer;
    font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
    margin-top: 22px;
}
.btn-del-espacio:hover { background: #fee2e2; }

/* Add buttons */
.btn-add-espacio {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px dashed rgba(59,13,143,.3);
    border-radius: 8px;
    background: rgba(59,13,143,.04);
    color: var(--color-primario);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.btn-add-espacio:hover { border-color: var(--color-primario); background: rgba(59,13,143,.08); }

.btn-add-bloque {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px;
    border: 2px dashed rgba(59,13,143,.22);
    border-radius: 12px;
    background: rgba(59,13,143,.025);
    color: var(--color-primario);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all .25s;
    margin-top: 8px;
}
.btn-add-bloque:hover { border-color: var(--color-primario); background: rgba(59,13,143,.06); }

/* Empty bloques placeholder */
.bloques-empty {
    text-align: center;
    padding: 32px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    color: var(--color-texto-secundario);
    font-size: 14px;
}
.bloques-empty i { font-size: 32px; opacity: .3; display: block; margin-bottom: 10px; }

/* Footer actions */
.form-footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 20px 0 8px;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border: 1px solid var(--color-borde);
    border-radius: 8px;
    background: #fff;
    color: var(--color-texto-secundario);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}
.btn-outline:hover { border-color: var(--color-primario); color: var(--color-primario); text-decoration: none; }

@media (max-width: 700px) {
    .fields-grid, .bloque-fields { grid-template-columns: 1fr; }
    .espacio-row { grid-template-columns: 1fr 1fr; }
    .espacio-row > :last-child { grid-column: 1 / -1; justify-self: start; margin-top: 0; }
    .sede-form-hero { flex-direction: column; align-items: flex-start; }
}
</style>

<!-- Page watermark -->
<div class="form-page-bg">
    <img src="assets/img/isotipo-color.png" alt="">
</div>

<div class="sede-form-wrap">

    <!-- Back -->
    <a href="?ruta=admin/espacios/sedes" style="display:inline-flex; align-items:center; gap:6px; font-size:13.5px; font-weight:600; color:var(--color-primario); margin-bottom:16px; text-decoration:none; transition:gap .2s;" onmouseover="this.style.gap='10px'" onmouseout="this.style.gap='6px'">
        <i class="fa-solid fa-arrow-left"></i> Volver a Sedes
    </a>

    <!-- Hero -->
    <div class="sede-form-hero">
        <i class="fa-solid fa-building-columns"></i>
        <div>
            <h1><?= $esEdicion ? 'Editar Sede Institucional' : 'Nueva Sede Institucional' ?></h1>
            <p>Configura la sede, sus bloques y espacios físicos en un solo formulario. Los códigos se generan automáticamente.</p>
        </div>
    </div>

    <!-- ===================== FORM ===================== -->
    <form action="?ruta=admin/espacios/sedes/guardar_completo" method="POST" id="form-sede-completo" novalidate>
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_sede" value="<?= (int)$sedeCompleta['id_sede'] ?>">
        <?php endif; ?>

        <!-- === SECCIÓN 1: DATOS DE LA SEDE === -->
        <div class="form-section-card">
            <div class="form-section-header">
                <span class="section-num">1</span>
                <div>
                    <h2>Datos de la Sede</h2>
                    <p>Información principal de la sede institucional.</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="fields-grid">
                    <div class="form-group field-full">
                        <label style="font-weight:600; font-size:13.5px; color:var(--color-oscuro);">
                            Nombre de la Sede <span style="color:red;">*</span>
                        </label>
                        <input type="text"
                               name="sede_nombre"
                               id="sede_nombre"
                               class="form-control"
                               required
                               placeholder="Ej: Sede Principal Norte"
                               value="<?= htmlspecialchars($sedeCompleta['nombre'] ?? '') ?>"
                               oninput="generarCodigoAuto('sede', this.value, 'sede_codigo', <?= $esEdicion ? (int)$sedeCompleta['id_sede'] : 0 ?>, 0)">
                    </div>

                    <div class="form-group auto-code-field">
                        <label style="font-weight:600; font-size:13.5px; color:var(--color-oscuro);">
                            Código <span style="color:red;">*</span>
                        </label>
                        <div class="auto-code-input-wrap" id="sede_codigo_wrap">
                            <input type="text"
                                   name="sede_codigo"
                                   id="sede_codigo"
                                   required
                                   placeholder="SP-001"
                                   maxlength="20"
                                   value="<?= htmlspecialchars($sedeCompleta['codigo'] ?? '') ?>"
                                   oninput="marcarManual('sede_codigo_badge')">
                            <span class="auto-code-badge" id="sede_codigo_badge">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Auto
                            </span>
                        </div>
                        <div class="auto-code-hint">
                            <i class="fa-solid fa-circle-info"></i> Se genera automáticamente al escribir el nombre. Puedes modificarlo.
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:600; font-size:13.5px; color:var(--color-oscuro);">Estado</label>
                        <select name="sede_estado" class="form-control">
                            <option value="ACTIVO" <?= (($sedeCompleta['estado'] ?? 'ACTIVO') === 'ACTIVO') ? 'selected' : '' ?>>Activo</option>
                            <option value="INACTIVO" <?= (($sedeCompleta['estado'] ?? '') === 'INACTIVO') ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="form-group field-full">
                        <label style="font-weight:600; font-size:13.5px; color:var(--color-oscuro);">Dirección</label>
                        <input type="text"
                               name="sede_direccion"
                               class="form-control"
                               placeholder="Ej: Calle 100 # 15-20"
                               value="<?= htmlspecialchars($sedeCompleta['direccion'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- === SECCIÓN 2: BLOQUES Y ESPACIOS === -->
        <div class="form-section-card">
            <div class="form-section-header">
                <span class="section-num">2</span>
                <div>
                    <h2>Bloques y Espacios</h2>
                    <p>Agrega los bloques de la sede y dentro de cada bloque sus espacios físicos (aulas, laboratorios, etc.).</p>
                </div>
            </div>
            <div class="form-section-body">
                <div id="bloques-list" class="bloques-list">
                    <!-- Bloques se renderizan aquí — por JS y por PHP para edición -->
                </div>

                <div id="bloques-empty" class="bloques-empty" style="<?= !empty($bloquesPrevios) ? 'display:none;' : '' ?>">
                    <i class="fa-solid fa-building"></i>
                    <strong>Sin bloques aún.</strong><br>
                    Pulsa el botón para agregar el primer bloque de esta sede.
                </div>

                <button type="button" class="btn-add-bloque" id="btn-add-bloque" onclick="agregarBloque()">
                    <i class="fa-solid fa-plus-circle"></i> Agregar Bloque
                </button>
            </div>
        </div>

        <!-- === FOOTER === -->
        <div class="form-footer-actions">
            <a href="?ruta=admin/espacios/sedes" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn-primario" style="padding: 12px 28px; font-size: 15px; font-weight: 700;">
                <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Sede' ?>
            </button>
        </div>
    </form>
</div>

<script>
// =========================================================
// DATOS INICIALES (EDICIÓN)
// =========================================================
const bloquesPrevios = <?= json_encode($bloquesPrevios) ?>;
const tiposEspacio = ['AULA', 'LABORATORIO', 'AUDITORIO', 'OFICINA', 'SALA_REUNIONES', 'GIMNASIO', 'BIBLIOTECA', 'OTRO'];

let bloqueCounter = 0;
let espacioCounter = 0;

// =========================================================
// CÓDIGO AUTOMÁTICO (API)
// =========================================================
const codigoTimers = {};
function generarCodigoAuto(tipo, nombre, targetId, idExcluir, idRef) {
    clearTimeout(codigoTimers[targetId]);
    if (!nombre || nombre.length < 2) return;
    codigoTimers[targetId] = setTimeout(async () => {
        const badge = document.getElementById(targetId + '_badge');
        if (badge && badge.classList.contains('manual')) return; // No sobreescribir modo manual
        try {
            const url = `?ruta=admin/espacios/sedes/api_codigo&tipo=${tipo}&nombre=${encodeURIComponent(nombre)}&id_ref=${idRef || 0}&id_excluir=${idExcluir || 0}`;
            const res = await fetch(url);
            const data = await res.json();
            if (data && data.codigo) {
                const input = document.getElementById(targetId);
                if (input && !input.getAttribute('data-manual')) {
                    input.value = data.codigo;
                }
            }
        } catch(e) { console.warn('No se pudo generar código:', e); }
    }, 500);
}

function marcarManual(badgeId) {
    const badge = document.getElementById(badgeId);
    if (badge) {
        badge.classList.add('manual');
        badge.innerHTML = '<i class="fa-solid fa-pen"></i> Manual';
    }
    const inputId = badgeId.replace('_badge', '');
    const input = document.getElementById(inputId);
    if (input) input.setAttribute('data-manual', '1');
}

// =========================================================
// AGREGAR BLOQUE
// =========================================================
function agregarBloque(data = null) {
    const idx = bloqueCounter++;
    const idBloque = data ? data.id_bloque : 0;
    const nombre = data ? data.nombre : '';
    const codigo = data ? data.codigo : '';
    const estado = data ? data.estado : 'ACTIVO';
    const espacios = data ? (data.espacios || []) : [];

    const card = document.createElement('div');
    card.className = 'bloque-card';
    card.id = `bloque-card-${idx}`;

    const title = nombre || `Bloque ${idx + 1}`;

    card.innerHTML = `
        <div class="bloque-card-header" onclick="toggleBloque(${idx})">
            <span class="bloque-number">${idx + 1}</span>
            <div class="bloque-header-info">
                <h3 id="bloque-title-${idx}">${title}</h3>
                <p id="bloque-subtitle-${idx}">${codigo || 'Código pendiente'} · ${espacios.length} espacio${espacios.length !== 1 ? 's' : ''}</p>
            </div>
            <button type="button" class="btn-eliminar-bloque" onclick="eliminarBloque(event, ${idx})">
                <i class="fa-solid fa-trash-can"></i> Eliminar
            </button>
            <button type="button" class="bloque-collapse-btn" id="bloque-chevron-${idx}">
                <i class="fa-solid fa-chevron-up"></i>
            </button>
        </div>
        <div class="bloque-body" id="bloque-body-${idx}">
            ${idBloque ? `<input type="hidden" name="bloques[${idx}][id_bloque]" value="${idBloque}">` : ''}

            <div class="bloque-fields">
                <div class="form-group" style="grid-column:1/-1">
                    <label style="font-weight:600; font-size:13px; color:var(--color-oscuro);">
                        Nombre del bloque <span style="color:red;">*</span>
                    </label>
                    <input type="text" class="form-control" name="bloques[${idx}][nombre]" id="bloque-nombre-${idx}"
                           placeholder="Ej: Bloque A, Edificio Norte..."
                           value="${escHtml(nombre)}"
                           required
                           oninput="onBloqueNombreChange(${idx}, this.value)">
                </div>

                <div class="form-group auto-code-field">
                    <label style="font-weight:600; font-size:13px; color:var(--color-oscuro);">Código del bloque</label>
                    <div class="auto-code-input-wrap">
                        <input type="text" class="form-control" style="border:none;"
                               name="bloques[${idx}][codigo]" id="bloque-codigo-${idx}"
                               placeholder="BLA-001" maxlength="20"
                               value="${escHtml(codigo)}"
                               oninput="marcarManual('bloque-codigo-${idx}_badge')">
                        <span class="auto-code-badge ${codigo ? 'manual' : ''}" id="bloque-codigo-${idx}_badge">
                            <i class="fa-solid fa-${codigo ? 'pen' : 'wand-magic-sparkles'}"></i> ${codigo ? 'Manual' : 'Auto'}
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label style="font-weight:600; font-size:13px; color:var(--color-oscuro);">Estado</label>
                    <select class="form-control" name="bloques[${idx}][estado]">
                        <option value="ACTIVO" ${estado === 'ACTIVO' ? 'selected' : ''}>Activo</option>
                        <option value="INACTIVO" ${estado === 'INACTIVO' ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- Espacios del bloque -->
            <div class="espacios-section">
                <div class="espacios-section-header">
                    <h4>
                        <i class="fa-solid fa-door-open" style="color:var(--color-secundario);"></i>
                        Espacios del bloque
                        <span style="background:rgba(59,13,143,.08); color:var(--color-primario); padding:2px 8px; border-radius:12px; font-size:11px;" id="esp-count-${idx}">
                            ${espacios.length}
                        </span>
                    </h4>
                    <button type="button" class="btn-add-espacio" onclick="agregarEspacio(${idx})">
                        <i class="fa-solid fa-plus"></i> Agregar espacio
                    </button>
                </div>
                <div class="espacios-list" id="espacios-list-${idx}">
                    <!-- Espacios injected here -->
                </div>
                <div id="espacios-empty-${idx}" class="auto-code-hint" style="padding:12px 0; ${espacios.length > 0 ? 'display:none;' : ''}">
                    <i class="fa-solid fa-circle-info"></i> Sin espacios en este bloque aún. Agrega aulas, laboratorios, etc.
                </div>
            </div>
        </div>
    `;

    document.getElementById('bloques-list').appendChild(card);
    document.getElementById('bloques-empty').style.display = 'none';

    // Pre-cargar espacios existentes
    espacios.forEach(esp => agregarEspacio(idx, esp));

    updateBloqueNumbers();
}

// =========================================================
// NOMBRE BLOQUE → actualizar header dinámicamente
// =========================================================
function onBloqueNombreChange(idx, val) {
    const titleEl = document.getElementById(`bloque-title-${idx}`);
    if (titleEl) titleEl.textContent = val || `Bloque ${idx + 1}`;
    // Generar código (necesita la sede_id... para API usamos 0 como placeholder)
    const codigoInput = document.getElementById(`bloque-codigo-${idx}`);
    if (codigoInput && !codigoInput.getAttribute('data-manual')) {
        generarCodigoAuto('bloque', val, `bloque-codigo-${idx}`, 0, 0);
    }
}

// =========================================================
// AGREGAR ESPACIO
// =========================================================
function agregarEspacio(bloqueIdx, data = null) {
    const espIdx = espacioCounter++;
    const idEsp = data ? data.id_espacio : 0;
    const nombre = data ? data.nombre : '';
    const codigo = data ? data.codigo : '';
    const tipo   = data ? data.tipo : 'AULA';
    const cap    = data ? (data.capacidad || '') : '';

    const list = document.getElementById(`espacios-list-${bloqueIdx}`);
    const empty = document.getElementById(`espacios-empty-${bloqueIdx}`);
    if (empty) empty.style.display = 'none';

    const row = document.createElement('div');
    row.className = 'espacio-row';
    row.id = `espacio-row-${espIdx}`;

    const tiposOpts = tiposEspacio.map(t =>
        `<option value="${t}" ${tipo === t ? 'selected' : ''}>${tipoLabel(t)}</option>`
    ).join('');

    row.innerHTML = `
        ${idEsp ? `<input type="hidden" name="bloques[${bloqueIdx}][espacios][${espIdx}][id_espacio]" value="${idEsp}">` : ''}
        <div class="form-group">
            <label>Nombre del espacio *</label>
            <input type="text" class="form-control" required
                   name="bloques[${bloqueIdx}][espacios][${espIdx}][nombre]"
                   id="esp-nombre-${espIdx}"
                   placeholder="Ej: Aula 101"
                   value="${escHtml(nombre)}"
                   oninput="onEspacioNombreChange(${bloqueIdx}, ${espIdx}, this.value)">
        </div>
        <div class="form-group auto-code-field">
            <label>Código</label>
            <div class="auto-code-input-wrap">
                <input type="text" style="border:none;" maxlength="20"
                       name="bloques[${bloqueIdx}][espacios][${espIdx}][codigo]"
                       id="esp-codigo-${espIdx}"
                       placeholder="A101"
                       value="${escHtml(codigo)}"
                       oninput="marcarManual('esp-codigo-${espIdx}_badge')">
                <span class="auto-code-badge ${codigo ? 'manual' : ''}" id="esp-codigo-${espIdx}_badge">
                    <i class="fa-solid fa-${codigo ? 'pen' : 'wand-magic-sparkles'}"></i>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select class="form-control" name="bloques[${bloqueIdx}][espacios][${espIdx}][tipo]">
                ${tiposOpts}
            </select>
        </div>
        <div class="form-group">
            <label>Cap.</label>
            <input type="number" class="form-control" min="0" max="9999"
                   name="bloques[${bloqueIdx}][espacios][${espIdx}][capacidad]"
                   placeholder="0"
                   value="${escHtml(String(cap))}">
        </div>
        <button type="button" class="btn-del-espacio" onclick="eliminarEspacio(${bloqueIdx}, ${espIdx})" title="Eliminar espacio">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    list.appendChild(row);
    actualizarContadorEspacios(bloqueIdx);
}

function onEspacioNombreChange(bloqueIdx, espIdx, val) {
    const codigoInput = document.getElementById(`esp-codigo-${espIdx}`);
    if (codigoInput && !codigoInput.getAttribute('data-manual')) {
        generarCodigoAuto('espacio', val, `esp-codigo-${espIdx}`, 0, 0);
    }
}

// =========================================================
// ELIMINAR
// =========================================================
function eliminarBloque(event, idx) {
    event.stopPropagation();
    const card = document.getElementById(`bloque-card-${idx}`);
    if (card) {
        card.style.transition = 'opacity .25s, max-height .3s';
        card.style.opacity = '0';
        card.style.overflow = 'hidden';
        card.style.maxHeight = card.offsetHeight + 'px';
        setTimeout(() => { card.style.maxHeight = '0'; card.style.marginBottom = '0'; }, 10);
        setTimeout(() => { card.remove(); checkBloquesEmpty(); updateBloqueNumbers(); }, 320);
    }
}

function eliminarEspacio(bloqueIdx, espIdx) {
    const row = document.getElementById(`espacio-row-${espIdx}`);
    if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(10px)';
        row.style.transition = 'all .2s';
        setTimeout(() => { row.remove(); actualizarContadorEspacios(bloqueIdx); }, 210);
    }
}

// =========================================================
// HELPERS
// =========================================================
function toggleBloque(idx) {
    const card = document.getElementById(`bloque-card-${idx}`);
    if (card) card.classList.toggle('collapsed');
}

function checkBloquesEmpty() {
    const list = document.getElementById('bloques-list');
    const empty = document.getElementById('bloques-empty');
    if (list && empty) {
        empty.style.display = list.children.length === 0 ? 'block' : 'none';
    }
}

function updateBloqueNumbers() {
    document.querySelectorAll('.bloque-number').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

function actualizarContadorEspacios(bloqueIdx) {
    const list = document.getElementById(`espacios-list-${bloqueIdx}`);
    const counter = document.getElementById(`esp-count-${bloqueIdx}`);
    const empty = document.getElementById(`espacios-empty-${bloqueIdx}`);
    const subtitle = document.getElementById(`bloque-subtitle-${bloqueIdx}`);
    if (!list) return;
    const count = list.querySelectorAll('.espacio-row').length;
    if (counter) counter.textContent = count;
    if (empty) empty.style.display = count === 0 ? 'flex' : 'none';
    if (subtitle) {
        const codigoEl = document.querySelector(`#bloque-card-${bloqueIdx} [name$="[codigo]"]`);
        const codigoVal = codigoEl ? codigoEl.value : '';
        subtitle.textContent = `${codigoVal || 'Código pendiente'} · ${count} espacio${count !== 1 ? 's' : ''}`;
    }
}

function tipoLabel(tipo) {
    const labels = {
        AULA: 'Aula', LABORATORIO: 'Laboratorio', AUDITORIO: 'Auditorio',
        OFICINA: 'Oficina', SALA_REUNIONES: 'Sala de Reuniones',
        GIMNASIO: 'Gimnasio', BIBLIOTECA: 'Biblioteca', OTRO: 'Otro'
    };
    return labels[tipo] || tipo;
}

function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// =========================================================
// VALIDACIÓN
// =========================================================
document.getElementById('form-sede-completo').addEventListener('submit', function(e) {
    const nombre = document.getElementById('sede_nombre').value.trim();
    const codigo = document.getElementById('sede_codigo').value.trim();

    if (!nombre || !codigo) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Datos incompletos', text: 'El nombre y código de la sede son obligatorios.', confirmButtonColor: '#3B0D8F' });
        return;
    }
});

// =========================================================
// INICIALIZAR con datos previos (modo edición)
// =========================================================
document.addEventListener('DOMContentLoaded', function() {
    if (bloquesPrevios && bloquesPrevios.length > 0) {
        bloquesPrevios.forEach(b => agregarBloque(b));
    }
    <?php if (!$esEdicion && empty($bloquesPrevios)): ?>
    // Primer bloque automático para el modo creación
    // agregarBloque(); // Comentado: el usuario puede agregarlo manualmente
    <?php endif; ?>
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
