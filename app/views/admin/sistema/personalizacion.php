<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Sistema — Personalización']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs', ['tabs' => [
    ['etiqueta' => 'Personalización', 'ruta' => 'admin/sistema/personalizacion', 'activa' => true],
    ['etiqueta' => 'Usuarios', 'ruta' => 'admin/sistema/usuarios', 'activa' => false],
    ['etiqueta' => 'Líderes de Programa', 'ruta' => 'admin/sistema/lideres', 'activa' => false],
]]);

$colorPrimario = !empty($institucion['color_principal']) ? $institucion['color_principal'] : '#3B0D8F';
$colorSecundario = !empty($institucion['color_secundario']) ? $institucion['color_secundario'] : '#0F9D58';
$colorAcento = !empty($institucion['color_acento']) ? $institucion['color_acento'] : '#F5B301';
$nombreInstitucion = !empty($institucion['nombre']) ? $institucion['nombre'] : 'Fundación de Educación Superior del Caribe';
$nombreSistema = 'UniFET'; // El nombre del software es UniFET de forma inmutable
$lema = !empty($institucion['lema']) ? $institucion['lema'] : 'Gestión Académica Inteligente';
$logoUrl = !empty($institucion['logo_url']) ? $institucion['logo_url'] : 'assets/img/isotipo-color.png';
$faviconUrl = !empty($institucion['favicon_url']) ? $institucion['favicon_url'] : 'assets/img/isotipo-color.png';

// Función para oscurecer un color hex para los degradados
function darkenHex($hex, $percent = 35) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $num = hexdec($hex);
    $amt = round(2.55 * $percent);
    $r = max(0, min(255, ($num >> 16) - $amt));
    $g = max(0, min(255, (($num >> 8) & 0x00FF) - $amt));
    $b = max(0, min(255, ($num & 0x0000FF) - $amt));
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}
$colorPrimarioDark = darkenHex($colorPrimario, 35);
$colorPrimarioDeep = darkenHex($colorPrimario, 55);
?>

<style>
/* =========================================================
   ESTUDIO DE PERSONALIZACIÓN INSTITUCIONAL — UNIFET SAAS
   ========================================================= */

.personalizacion-container {
    margin-bottom: 30px;
    font-family: 'Poppins', sans-serif;
}

/* Banner de Previsualización en Tiempo Real */
.identity-preview-banner {
    position: relative;
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    color: #ffffff;
    box-shadow: 0 10px 30px rgba(59, 13, 143, 0.18);
    overflow: hidden;
    transition: background 0.35s ease, box-shadow 0.35s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.identity-preview-banner::before {
    content: '';
    position: absolute;
    right: -30px;
    bottom: -30px;
    width: 220px;
    height: 220px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.08;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.banner-left {
    display: flex;
    align-items: center;
    gap: 22px;
    position: relative;
    z-index: 2;
    flex: 1;
    min-width: 0;
}

.banner-logo-frame {
    width: 74px;
    height: 74px;
    background: #ffffff;
    border-radius: 14px;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.16);
    flex-shrink: 0;
    transition: transform 0.25s ease;
}

.banner-logo-frame:hover {
    transform: scale(1.04);
}

.banner-logo-frame img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.banner-info {
    flex: 1;
    min-width: 0;
}

.banner-sys-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.16);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 6px;
}

.banner-title {
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 4px 0;
    line-height: 1.25;
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
    word-break: break-word;
}

.banner-motto {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.88);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-style: italic;
    word-break: break-word;
}

.banner-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.banner-live-indicator {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.4px;
}

.live-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10B981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 1.8s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.banner-swatches-strip {
    display: flex;
    align-items: center;
    gap: 8px;
}

.banner-swatch-badge {
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    transition: all 0.2s ease;
}

/* Grilla del Formulario */
.studio-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
}

.studio-card {
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 18px rgba(11, 31, 51, 0.03);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.studio-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #F1F5F9;
}

.studio-icon-badge {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(59, 13, 143, 0.08);
    color: var(--color-primario, #3B0D8F);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.studio-card-title h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0B1F33;
    margin: 0;
}

.studio-card-title p {
    font-size: 12.5px;
    color: #64748B;
    margin: 2px 0 0 0;
}

.form-fields-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.field-block {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field-block label {
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0;
}

.field-block label i {
    color: var(--color-primario, #3B0D8F);
    font-size: 13px;
}

.field-block input.form-control {
    height: 42px;
    border-radius: 8px;
    border: 1.5px solid #CBD5E1;
    font-size: 13.5px;
    padding: 8px 14px;
    transition: all 0.2s ease;
    background: #ffffff;
    color: #1E293B;
}

.field-block input.form-control:focus {
    border-color: var(--color-primario, #3B0D8F);
    box-shadow: 0 0 0 3px rgba(59, 13, 143, 0.12);
    outline: none;
}

/* Tarjeta de Color Moderna */
.color-picker-card {
    background: #F8FAFC;
    border: 1.5px solid #E2E8F0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    transition: all 0.2s ease;
}

.color-picker-card:hover {
    background: #ffffff;
    border-color: #CBD5E1;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
}

.color-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.color-meta strong {
    font-size: 13px;
    color: #1E293B;
}

.color-meta span {
    font-size: 11.5px;
    color: #64748B;
}

.color-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.color-swatch-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 36px;
    height: 36px;
    background-color: transparent;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    overflow: hidden;
}

.color-swatch-input::-webkit-color-swatch-wrapper {
    padding: 0;
    border-radius: 8px;
}

.color-swatch-input::-webkit-color-swatch {
    border: 2px solid rgba(0, 0, 0, 0.12);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.color-hex-text {
    width: 82px;
    height: 36px;
    text-align: center;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 12.5px;
    border: 1px solid #CBD5E1;
    border-radius: 6px;
    padding: 4px;
    text-transform: uppercase;
    background: #ffffff;
}

/* Paletas Prediseñadas Rápidas */
.presets-section {
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid #F1F5F9;
}

.presets-title {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.preset-pills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.preset-btn {
    border: 1px solid #E2E8F0;
    background: #F8FAFC;
    border-radius: 8px;
    padding: 6px 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}

.preset-btn:hover {
    background: #ffffff;
    border-color: #CBD5E1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

.preset-dots {
    display: inline-flex;
    gap: 3px;
}

.preset-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
}

/* Previsualización en Vivo de Componentes */
.preview-components-card {
    background: #F8FAFC;
    border: 1px dashed #CBD5E1;
    border-radius: 10px;
    padding: 14px;
    margin-top: 16px;
}

.preview-components-card h4 {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748B;
    margin: 0 0 10px 0;
}

.preview-samples-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Pie y Botón Guardar */
.studio-submit-bar {
    margin-top: 24px;
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 18px rgba(11, 31, 51, 0.03);
}

.studio-submit-bar p {
    margin: 0;
    font-size: 13px;
    color: #64748B;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-save-identity {
    background: linear-gradient(135deg, var(--color-primario, #3B0D8F), #240b5c);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 12px 28px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    box-shadow: 0 4px 14px rgba(59, 13, 143, 0.25);
    transition: all 0.2s ease;
}

.btn-save-identity:hover {
    box-shadow: 0 6px 20px rgba(59, 13, 143, 0.38);
    transform: translateY(-1px);
}

@media (max-width: 960px) {
    .studio-grid {
        grid-template-columns: 1fr;
    }
    .identity-preview-banner {
        flex-direction: column;
        align-items: flex-start;
    }
    .banner-right {
        align-items: flex-start;
    }
    .studio-submit-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .btn-save-identity {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="personalizacion-container">

    <!-- BANNER DINÁMICO DE IDENTIDAD EN TIEMPO REAL -->
    <div class="identity-preview-banner" id="live-identity-banner"
         style="background: linear-gradient(135deg, <?= htmlspecialchars($colorPrimario) ?> 0%, <?= htmlspecialchars($colorPrimarioDark) ?> 65%, <?= htmlspecialchars($colorPrimarioDeep) ?> 100%);">
        
        <div class="banner-left">
            <div class="banner-logo-frame">
                <img src="<?= htmlspecialchars($logoUrl) ?>" id="preview-logo-img" alt="Logo Institucional"
                     onerror="this.src='assets/img/isotipo-color.png';">
            </div>
            <div class="banner-info">
                <span class="banner-sys-tag">
                    <i class="fa-solid fa-sparkles"></i>
                    <span id="preview-nombre-sistema"><?= htmlspecialchars($nombreSistema) ?></span>
                </span>
                <h1 class="banner-title" id="preview-nombre"><?= htmlspecialchars($nombreInstitucion) ?></h1>
                <p class="banner-motto" id="preview-lema">
                    <i class="fa-solid fa-quote-left" style="font-size:11px; opacity:0.7;"></i>
                    <span id="preview-lema-text"><?= htmlspecialchars($lema) ?></span>
                </p>
            </div>
        </div>

        <div class="banner-right">
            <div class="banner-live-indicator">
                <span class="live-pulse-dot"></span>
                <span>Previsualización en Vivo</span>
            </div>
            <div class="banner-swatches-strip">
                <span class="banner-swatch-badge" id="preview-chip-secundario"
                      style="background: <?= htmlspecialchars($colorSecundario) ?>; color: #ffffff;">
                    <i class="fa-solid fa-circle-check"></i> Secundario
                </span>
                <span class="banner-swatch-badge" id="preview-chip-acento"
                      style="background: <?= htmlspecialchars($colorAcento) ?>; color: #0B1F33;">
                    <i class="fa-solid fa-bolt"></i> Acento
                </span>
            </div>
        </div>
    </div>

    <!-- FORMULARIO DE EDICIÓN -->
    <form method="POST" action="?ruta=admin/sistema/personalizacion/guardar" id="form-personalizacion">
        <div class="studio-grid">

            <!-- TARJETA 1: DATOS Y MARCA -->
            <div class="studio-card">
                <div>
                    <div class="studio-card-header">
                        <div class="studio-icon-badge">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div class="studio-card-title">
                            <h3>Identidad y Marca</h3>
                            <p>Configura los textos corporativos e isotipos visibles</p>
                        </div>
                    </div>

                    <div class="form-fields-group">
                        <div class="field-block">
                            <label for="input_nombre">
                                <i class="fa-solid fa-university"></i> Nombre de la Institución
                            </label>
                            <input class="form-control" name="nombre" id="input_nombre" required
                                   value="<?= htmlspecialchars($nombreInstitucion) ?>"
                                   placeholder="Ej: Fundación de Educación Superior...">
                        </div>

                        <div class="field-block">
                            <label for="input_nombre_sistema">
                                <i class="fa-solid fa-laptop-code"></i> Nombre del Software / Sistema
                            </label>
                            <div style="position:relative;">
                                <input class="form-control" name="nombre_sistema" id="input_nombre_sistema"
                                       value="UniFET" readonly
                                       style="background:#f8fafc; font-weight:700; color:var(--color-primario); padding-right:110px;">
                                <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:11px; background:rgba(59,13,143,0.08); color:var(--color-primario); padding:3px 8px; border-radius:6px; font-weight:700;">
                                    <i class="fa-solid fa-lock"></i> Predeterminado
                                </span>
                            </div>
                            <small style="color:var(--color-texto-secundario); font-size:11px; margin-top:2px; display:block;">El nombre oficial de la plataforma es inmutable: <strong>UniFET</strong>.</small>
                        </div>

                        <div class="field-block">
                            <label for="input_lema">
                                <i class="fa-solid fa-quote-left"></i> Lema o Eslogan Institucional
                            </label>
                            <input class="form-control" name="lema" id="input_lema"
                                   value="<?= htmlspecialchars($lema) ?>"
                                   placeholder="Ej: Gestión Académica Inteligente">
                        </div>

                        <div class="field-block">
                            <label for="input_logo_url">
                                <i class="fa-solid fa-image"></i> Logo Institucional (Ruta o URL)
                            </label>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <input class="form-control" name="logo_url" id="input_logo_url"
                                       value="<?= htmlspecialchars($logoUrl) ?>"
                                       placeholder="assets/img/isotipo-color.png o https://...">
                                <button type="button" class="btn-demo" id="btn-reset-logo" title="Usar logo UniFET predeterminado"
                                        style="font-size:12px; padding:9px 12px; white-space:nowrap;">
                                    <i class="fa-solid fa-rotate-left"></i> Default
                                </button>
                            </div>
                        </div>

                        <div class="field-block">
                            <label for="input_favicon_url">
                                <i class="fa-solid fa-globe"></i> Favicon (Ruta o URL)
                            </label>
                            <input class="form-control" name="favicon_url" id="input_favicon_url"
                                   value="<?= htmlspecialchars($faviconUrl) ?>"
                                   placeholder="assets/img/isotipo-color.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TARJETA 2: PALETA DE COLORES -->
            <div class="studio-card">
                <div>
                    <div class="studio-card-header">
                        <div class="studio-icon-badge">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        <div class="studio-card-title">
                            <h3>Paleta Cromática</h3>
                            <p>Personaliza los tokens de color del entorno SaaS</p>
                        </div>
                    </div>

                    <div class="form-fields-group">
                        <!-- Color Primario -->
                        <div class="color-picker-card">
                            <div class="color-meta">
                                <strong>Color Primario Institucional</strong>
                                <span>Botones principales, héroes y navegación</span>
                            </div>
                            <div class="color-controls">
                                <input type="color" class="color-swatch-input" id="swatch_color_principal"
                                       value="<?= htmlspecialchars($colorPrimario) ?>">
                                <input type="text" class="color-hex-text" name="color_principal" id="input_color_principal"
                                       value="<?= htmlspecialchars($colorPrimario) ?>" maxlength="7">
                            </div>
                        </div>

                        <!-- Color Secundario -->
                        <div class="color-picker-card">
                            <div class="color-meta">
                                <strong>Color Secundario</strong>
                                <span>Badges de estado, éxitos y acentos de soporte</span>
                            </div>
                            <div class="color-controls">
                                <input type="color" class="color-swatch-input" id="swatch_color_secundario"
                                       value="<?= htmlspecialchars($colorSecundario) ?>">
                                <input type="text" class="color-hex-text" name="color_secundario" id="input_color_secundario"
                                       value="<?= htmlspecialchars($colorSecundario) ?>" maxlength="7">
                            </div>
                        </div>

                        <!-- Color Acento -->
                        <div class="color-picker-card">
                            <div class="color-meta">
                                <strong>Color de Acento</strong>
                                <span>Indicadores de advertencia y destaques visuales</span>
                            </div>
                            <div class="color-controls">
                                <input type="color" class="color-swatch-input" id="swatch_color_acento"
                                       value="<?= htmlspecialchars($colorAcento) ?>">
                                <input type="text" class="color-hex-text" name="color_acento" id="input_color_acento"
                                       value="<?= htmlspecialchars($colorAcento) ?>" maxlength="7">
                            </div>
                        </div>
                    </div>

                    <!-- Paletas Prediseñadas Rápidas -->
                    <div class="presets-section">
                        <div class="presets-title">
                            <i class="fa-solid fa-wand-magic-sparkles" style="color:var(--color-primario);"></i> Combinaciones Recomendadas
                        </div>
                        <div class="preset-pills-row">
                            <button type="button" class="preset-btn" data-primary="#3B0D8F" data-secondary="#0F9D58" data-accent="#F5B301">
                                <span class="preset-dots">
                                    <span class="preset-dot" style="background:#3B0D8F;"></span>
                                    <span class="preset-dot" style="background:#0F9D58;"></span>
                                    <span class="preset-dot" style="background:#F5B301;"></span>
                                </span>
                                UniFET Morado Real
                            </button>

                            <button type="button" class="preset-btn" data-primary="#0284C7" data-secondary="#10B981" data-accent="#F59E0B">
                                <span class="preset-dots">
                                    <span class="preset-dot" style="background:#0284C7;"></span>
                                    <span class="preset-dot" style="background:#10B981;"></span>
                                    <span class="preset-dot" style="background:#F59E0B;"></span>
                                </span>
                                Azul Caribe
                            </button>

                            <button type="button" class="preset-btn" data-primary="#4338CA" data-secondary="#059669" data-accent="#FCD34D">
                                <span class="preset-dots">
                                    <span class="preset-dot" style="background:#4338CA;"></span>
                                    <span class="preset-dot" style="background:#059669;"></span>
                                    <span class="preset-dot" style="background:#FCD34D;"></span>
                                </span>
                                Índigo Campus
                            </button>

                            <button type="button" class="preset-btn" data-primary="#831843" data-secondary="#0D9488" data-accent="#FB923C">
                                <span class="preset-dots">
                                    <span class="preset-dot" style="background:#831843;"></span>
                                    <span class="preset-dot" style="background:#0D9488;"></span>
                                    <span class="preset-dot" style="background:#FB923C;"></span>
                                </span>
                                Borgoña Decanal
                            </button>
                        </div>
                    </div>

                    <!-- Muestra en vivo de Componentes -->
                    <div class="preview-components-card">
                        <h4>Muestra de Componentes con tus Colores</h4>
                        <div class="preview-samples-row">
                            <button type="button" class="btn-primario" id="sample-btn-primary"
                                    style="background: <?= htmlspecialchars($colorPrimario) ?>; border-color: <?= htmlspecialchars($colorPrimario) ?>; font-size:12px; padding:8px 14px;">
                                <i class="fa-solid fa-calendar-check"></i> Botón Principal
                            </button>
                            <span class="badge" id="sample-badge-secondary"
                                  style="background: <?= htmlspecialchars($colorSecundario) ?>; color:#ffffff; font-size:11px; padding:5px 10px; border-radius:99px; font-weight:700;">
                                <i class="fa-solid fa-check"></i> Estado Activo
                            </span>
                            <span class="badge" id="sample-badge-accent"
                                  style="background: <?= htmlspecialchars($colorAcento) ?>; color:#0B1F33; font-size:11px; padding:5px 10px; border-radius:99px; font-weight:700;">
                                <i class="fa-solid fa-star"></i> Destacado
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- BARRA INFERIOR DE ACCIÓN -->
        <div class="studio-submit-bar">
            <p>
                <i class="fa-solid fa-circle-info" style="color:var(--color-primario);"></i>
                Los cambios se reflejarán en las barras de navegación, encabezados y elementos interactivos del sistema.
            </p>
            <button type="submit" class="btn-save-identity" id="btn-save-identity">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Personalización
            </button>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inputs de texto
    const inputNombre = document.getElementById('input_nombre');
    const inputSistema = document.getElementById('input_nombre_sistema');
    const inputLema = document.getElementById('input_lema');
    const inputLogo = document.getElementById('input_logo_url');
    const btnResetLogo = document.getElementById('btn-reset-logo');

    // Elementos de Previsualización en Banner
    const banner = document.getElementById('live-identity-banner');
    const prevNombre = document.getElementById('preview-nombre');
    const prevSistema = document.getElementById('preview-nombre-sistema');
    const prevLemaText = document.getElementById('preview-lema-text');
    const prevLogoImg = document.getElementById('preview-logo-img');
    const prevChipSecundario = document.getElementById('preview-chip-secundario');
    const prevChipAcento = document.getElementById('preview-chip-acento');

    // Muestra de componentes
    const sampleBtn = document.getElementById('sample-btn-primary');
    const sampleBadgeSec = document.getElementById('sample-badge-secondary');
    const sampleBadgeAcc = document.getElementById('sample-badge-accent');

    // Colores
    const swatchPrincipal = document.getElementById('swatch_color_principal');
    const inputPrincipal = document.getElementById('input_color_principal');
    const swatchSecundario = document.getElementById('swatch_color_secundario');
    const inputSecundario = document.getElementById('input_color_secundario');
    const swatchAcento = document.getElementById('swatch_color_acento');
    const inputAcento = document.getElementById('input_color_acento');

    // Sincronizador de texto reactivo
    const updateTexts = () => {
        if (prevNombre && inputNombre) {
            prevNombre.textContent = inputNombre.value.trim() || 'Fundación de Educación Superior';
        }
        if (prevSistema && inputSistema) {
            prevSistema.textContent = inputSistema.value.trim() || 'UniFET';
        }
        if (prevLemaText && inputLema) {
            prevLemaText.textContent = inputLema.value.trim() || 'Gestión Académica Inteligente';
        }
    };
    [inputNombre, inputSistema, inputLema].forEach(el => el && el.addEventListener('input', updateTexts));

    // Sincronizador de Logo
    const updateLogo = () => {
        if (!prevLogoImg || !inputLogo) return;
        const val = inputLogo.value.trim();
        prevLogoImg.src = val || 'assets/img/isotipo-color.png';
    };
    inputLogo && inputLogo.addEventListener('input', updateLogo);
    inputLogo && inputLogo.addEventListener('change', updateLogo);

    btnResetLogo && btnResetLogo.addEventListener('click', () => {
        if (inputLogo) {
            inputLogo.value = 'assets/img/isotipo-color.png';
            updateLogo();
        }
    });

    // Función para oscurecer colores hex automáticamente
    const darkenColorHex = (hex, percent) => {
        let cleanHex = hex.replace('#', '');
        if (cleanHex.length === 3) {
            cleanHex = cleanHex[0] + cleanHex[0] + cleanHex[1] + cleanHex[1] + cleanHex[2] + cleanHex[2];
        }
        let num = parseInt(cleanHex, 16);
        let amt = Math.round(2.55 * percent);
        let R = Math.max(0, Math.min(255, (num >> 16) - amt));
        let G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) - amt));
        let B = Math.max(0, Math.min(255, (num & 0x0000FF) - amt));
        return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
    };

    // Actualizador de Banner y Colores
    const updateThemeColors = () => {
        const cPri = swatchPrincipal ? swatchPrincipal.value : '#3B0D8F';
        const cSec = swatchSecundario ? swatchSecundario.value : '#0F9D58';
        const cAcc = swatchAcento ? swatchAcento.value : '#F5B301';

        // Degradado automático derivado exclusivamente del color principal escogido y sus variantes más oscuras
        const cPriDark = darkenColorHex(cPri, 35);
        const cPriDeep = darkenColorHex(cPri, 55);

        if (banner) {
            banner.style.background = `linear-gradient(135deg, ${cPri} 0%, ${cPriDark} 65%, ${cPriDeep} 100%)`;
        }

        // Swatches en el banner
        if (prevChipSecundario) prevChipSecundario.style.background = cSec;
        if (prevChipAcento) prevChipAcento.style.background = cAcc;

        // Componentes de prueba
        if (sampleBtn) {
            sampleBtn.style.background = cPri;
            sampleBtn.style.borderColor = cPri;
        }
        if (sampleBadgeSec) sampleBadgeSec.style.background = cSec;
        if (sampleBadgeAcc) sampleBadgeAcc.style.background = cAcc;
    };

    // Par Swatch <-> Hex Text
    const bindColorPair = (swatch, textInput) => {
        if (!swatch || !textInput) return;
        swatch.addEventListener('input', () => {
            textInput.value = swatch.value.toUpperCase();
            updateThemeColors();
        });
        textInput.addEventListener('input', () => {
            let val = textInput.value.trim();
            if (!val.startsWith('#')) val = '#' + val;
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                swatch.value = val;
                updateThemeColors();
            }
        });
    };

    bindColorPair(swatchPrincipal, inputPrincipal);
    bindColorPair(swatchSecundario, inputSecundario);
    bindColorPair(swatchAcento, inputAcento);

    // Botones de presets
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const p = btn.dataset.primary;
            const s = btn.dataset.secondary;
            const a = btn.dataset.accent;

            if (swatchPrincipal && inputPrincipal) {
                swatchPrincipal.value = p;
                inputPrincipal.value = p.toUpperCase();
            }
            if (swatchSecundario && inputSecundario) {
                swatchSecundario.value = s;
                inputSecundario.value = s.toUpperCase();
            }
            if (swatchAcento && inputAcento) {
                swatchAcento.value = a;
                inputAcento.value = a.toUpperCase();
            }
            updateThemeColors();
        });
    });

    // Prevención de envíos duplicados al guardar
    const form = document.getElementById('form-personalizacion');
    const btnSave = document.getElementById('btn-save-identity');
    if (form && btnSave) {
        form.addEventListener('submit', () => {
            btnSave.disabled = true;
            btnSave.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Guardando cambios...';
        });
    }
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
