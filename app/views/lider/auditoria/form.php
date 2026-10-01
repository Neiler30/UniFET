<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Auditar Clase']);
$this->render('elementos_compartidos/sidebar_lider');

$esReauditar = !empty($auditoriaExistente);
$fechaFormateada = date('d/m/Y', strtotime($fecha));
$diasEsp = [
    'LUNES' => 'Lunes', 'MARTES' => 'Martes', 'MIERCOLES' => 'Miércoles',
    'JUEVES' => 'Jueves', 'VIERNES' => 'Viernes', 'SABADO' => 'Sábado', 'DOMINGO' => 'Domingo'
];
$diaLabel = $diasEsp[$clase['dia_semana']] ?? $clase['dia_semana'];
?>

<style>
/* ============================== AUDITAR FORM ============================== */
.audit-form-container {
    max-width: 820px;
    margin: 0 auto;
    position: relative;
}
.audit-form-container::before {
    content: '';
    position: fixed;
    right: 40px; bottom: 40px;
    width: 280px; height: 280px;
    background: url('assets/img/isotipo-color.png') no-repeat center/contain;
    opacity: 0.03;
    pointer-events: none;
    z-index: 0;
}

/* Back link */
.audit-back-link {
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
.audit-back-link:hover { gap: 10px; text-decoration: none; }

/* Hero mini */
.audit-form-hero {
    background: linear-gradient(135deg, var(--color-primario), #24114f);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.audit-form-hero::before {
    content: '';
    position: absolute;
    right: -20px; bottom: -20px;
    width: 160px; height: 160px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.audit-form-hero h1 { font-size: 20px; font-weight: 700; margin-bottom: 3px; }
.audit-form-hero p { color: rgba(255,255,255,.75); font-size: 13.5px; margin: 0; }

/* Info card */
.clase-info-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 22px 24px;
    margin-bottom: 20px;
    box-shadow: var(--sombra-tarjeta);
}
.clase-info-card h2 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-oscuro);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.clase-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.clase-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
}
.clase-info-item i {
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
.clase-info-item .info-label { font-size: 11px; color: var(--color-texto-secundario); }
.clase-info-item .info-value { font-weight: 600; color: var(--color-oscuro); }

/* Checklist section */
.checklist-section {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 22px 24px;
    margin-bottom: 20px;
    box-shadow: var(--sombra-tarjeta);
}
.checklist-section h2 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-oscuro);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.checklist-grid { display: flex; flex-direction: column; gap: 10px; }
.checklist-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 16px;
    background: var(--color-superficie);
    border: 1px solid transparent;
    border-radius: 10px;
    transition: all .2s;
}
.checklist-item:hover { border-color: var(--color-borde); }
.checklist-item.checked { background: rgba(16,185,129,.06); border-color: rgba(16,185,129,.2); }

.chk-toggle {
    width: 22px; height: 22px;
    flex-shrink: 0;
    margin-top: 2px;
    accent-color: var(--color-secundario);
    cursor: pointer;
}
.chk-content { flex: 1; min-width: 0; }
.chk-label { font-size: 14px; font-weight: 600; color: var(--color-oscuro); margin-bottom: 6px; cursor: pointer; }
.chk-obs {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--color-borde);
    border-radius: 6px;
    font-family: var(--fuente-principal);
    font-size: 13px;
    color: var(--color-texto-principal);
    background: #fff;
    transition: border-color .2s;
}
.chk-obs:focus { outline: none; border-color: var(--color-primario); box-shadow: 0 0 0 3px rgba(59,13,143,.1); }

/* Result section */
.resultado-section {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 22px 24px;
    margin-bottom: 20px;
    box-shadow: var(--sombra-tarjeta);
}
.resultado-section h2 {
    font-size: 16px; font-weight: 700; color: var(--color-oscuro); margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.resultado-options {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.resultado-option {
    flex: 1;
    min-width: 200px;
    position: relative;
}
.resultado-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}
.resultado-option-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border: 2px solid var(--color-borde);
    border-radius: 10px;
    background: var(--color-superficie);
    transition: all .2s;
    cursor: pointer;
}
.resultado-option input[type="radio"]:checked + .resultado-option-label.label-sin {
    border-color: #10b981;
    background: rgba(16,185,129,.06);
}
.resultado-option input[type="radio"]:checked + .resultado-option-label.label-con {
    border-color: #ef4444;
    background: rgba(239,68,68,.05);
}
.resultado-option-label i {
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%;
    font-size: 16px;
    flex-shrink: 0;
}
.label-sin i { background: rgba(16,185,129,.12); color: #059669; }
.label-con i { background: rgba(239,68,68,.1); color: #dc2626; }
.resultado-option-label .opt-text h4 { font-size: 14px; font-weight: 700; margin: 0 0 2px; }
.resultado-option-label .opt-text p { font-size: 12px; color: var(--color-texto-secundario); margin: 0; }

/* Observations */
.observaciones-textarea {
    width: 100%;
    min-height: 100px;
    padding: 14px;
    border: 1px solid var(--color-borde);
    border-radius: 10px;
    font-family: var(--fuente-principal);
    font-size: 14px;
    color: var(--color-texto-principal);
    resize: vertical;
    transition: border-color .2s, box-shadow .2s;
}
.observaciones-textarea:focus { outline: none; border-color: var(--color-primario); box-shadow: 0 0 0 3px rgba(59,13,143,.12); }

.obs-required-hint {
    display: none;
    font-size: 12px;
    color: #ef4444;
    margin-top: 6px;
    align-items: center;
    gap: 5px;
}

/* Footer actions */
.form-audit-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 10px;
}

/* Re-audit warning */
.reaudit-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 10px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 18px;
}
.reaudit-banner i { font-size: 18px; flex-shrink: 0; }

@media (max-width: 700px) {
    .clase-info-grid { grid-template-columns: 1fr; }
    .resultado-options { flex-direction: column; }
}
</style>

<div class="audit-form-container">
    <a href="?ruta=lider/auditoria&fecha=<?= urlencode($fecha) ?>&programa_id=<?= (int)$clase['id_programa'] ?>" class="audit-back-link">
        <i class="fa-solid fa-arrow-left"></i> Volver a la lista de clases
    </a>

    <!-- Hero -->
    <div class="audit-form-hero">
        <h1><i class="fa-solid fa-clipboard-check"></i> <?= $esReauditar ? 'Re-auditar clase' : 'Auditar clase' ?></h1>
        <p><?= htmlspecialchars($clase['programa_nombre']) ?> — <?= $diaLabel ?>, <?= $fechaFormateada ?></p>
    </div>

    <?php if ($esReauditar): ?>
    <div class="reaudit-banner">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Esta clase ya fue auditada.</strong> Si continúas, la auditoría anterior será reemplazada por esta nueva.
            Resultado previo: <strong><?= htmlspecialchars($auditoriaExistente['resultado']) ?></strong>
        </div>
    </div>
    <?php endif; ?>

    <!-- Class info card -->
    <div class="clase-info-card">
        <h2><i class="fa-solid fa-info-circle" style="color: var(--color-primario);"></i> Información de la Clase</h2>
        <div class="clase-info-grid">
            <div class="clase-info-item">
                <i class="fa-solid fa-book"></i>
                <div>
                    <div class="info-label">Asignatura</div>
                    <div class="info-value"><?= htmlspecialchars($clase['asignatura_nombre']) ?> (<?= htmlspecialchars($clase['asignatura_codigo']) ?>)</div>
                </div>
            </div>
            <div class="clase-info-item">
                <i class="fa-solid fa-chalkboard-user"></i>
                <div>
                    <div class="info-label">Docente</div>
                    <div class="info-value"><?= htmlspecialchars($clase['docente_nombre']) ?></div>
                </div>
            </div>
            <div class="clase-info-item">
                <i class="fa-solid fa-clock"></i>
                <div>
                    <div class="info-label">Horario</div>
                    <div class="info-value"><?= date('H:i', strtotime($clase['hora_inicio'])) ?> – <?= date('H:i', strtotime($clase['hora_fin'])) ?></div>
                </div>
            </div>
            <div class="clase-info-item">
                <i class="fa-solid fa-door-open"></i>
                <div>
                    <div class="info-label">Espacio</div>
                    <div class="info-value"><?= htmlspecialchars($clase['espacio_nombre']) ?> (<?= htmlspecialchars($clase['espacio_codigo']) ?>)</div>
                </div>
            </div>
            <div class="clase-info-item">
                <i class="fa-solid fa-building"></i>
                <div>
                    <div class="info-label">Ubicación</div>
                    <div class="info-value"><?= htmlspecialchars($clase['sede_nombre']) ?> — <?= htmlspecialchars($clase['bloque_nombre']) ?></div>
                </div>
            </div>
            <div class="clase-info-item">
                <i class="fa-solid fa-calendar"></i>
                <div>
                    <div class="info-label">Fecha auditoria</div>
                    <div class="info-value"><?= $diaLabel ?>, <?= $fechaFormateada ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM -->
    <form action="?ruta=lider/auditoria/guardar" method="POST" id="form-auditoria">
        <input type="hidden" name="id_horario" value="<?= (int)$clase['id_horario'] ?>">
        <input type="hidden" name="fecha_auditoria" value="<?= htmlspecialchars($fecha) ?>">

        <!-- Checklist -->
        <?php if (!empty($criterios)): ?>
        <div class="checklist-section">
            <h2><i class="fa-solid fa-list-check" style="color: var(--color-secundario);"></i> Lista de Verificación</h2>
            <p style="font-size: 13px; color: var(--color-texto-secundario); margin-bottom: 16px;">
                Marca los criterios que se cumplen y agrega observaciones individuales si aplica.
            </p>
            <div class="checklist-grid">
                <?php foreach ($criterios as $i => $criterio):
                    $idC = (int)$criterio['id_criterio'];
                    $prevCumple = false;
                    $prevObs = '';
                    if ($esReauditar && !empty($auditoriaExistente['detalles'])) {
                        foreach ($auditoriaExistente['detalles'] as $det) {
                            if ((int)$det['id_criterio'] === $idC) {
                                $prevCumple = !empty($det['cumple']);
                                $prevObs = $det['observacion'] ?? '';
                                break;
                            }
                        }
                    }
                ?>
                <div class="checklist-item <?= $prevCumple ? 'checked' : '' ?>" id="chk-item-<?= $idC ?>">
                    <input type="hidden" name="criterios[<?= $idC ?>][cumple]" value="0">
                    <input type="checkbox"
                           class="chk-toggle"
                           name="criterios[<?= $idC ?>][cumple]"
                           value="1"
                           id="chk-<?= $idC ?>"
                           <?= $prevCumple ? 'checked' : '' ?>
                           onchange="this.closest('.checklist-item').classList.toggle('checked', this.checked)">
                    <div class="chk-content">
                        <label class="chk-label" for="chk-<?= $idC ?>"><?= htmlspecialchars($criterio['descripcion']) ?></label>
                        <input type="text"
                               class="chk-obs"
                               name="criterios[<?= $idC ?>][observacion]"
                               placeholder="Observación de este criterio (opcional)"
                               value="<?= htmlspecialchars($prevObs) ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resultado -->
        <div class="resultado-section">
            <h2><i class="fa-solid fa-flag" style="color: var(--color-acento);"></i> Resultado de la Auditoría</h2>
            <p style="font-size: 13px; color: var(--color-texto-secundario); margin-bottom: 16px;">
                ¿Cómo se desarrolló la clase? Selecciona el resultado que corresponda.
            </p>

            <div class="resultado-options">
                <div class="resultado-option">
                    <input type="radio" name="resultado" value="SIN_NOVEDAD" id="res-sin" required
                           <?= ($esReauditar && ($auditoriaExistente['resultado'] ?? '') === 'SIN_NOVEDAD') ? 'checked' : '' ?>>
                    <label for="res-sin" class="resultado-option-label label-sin">
                        <i class="fa-solid fa-check-circle"></i>
                        <div class="opt-text">
                            <h4>Sin novedades</h4>
                            <p>La clase se desarrolló con normalidad, sin ningún inconveniente.</p>
                        </div>
                    </label>
                </div>
                <div class="resultado-option">
                    <input type="radio" name="resultado" value="CON_NOVEDAD" id="res-con"
                           <?= ($esReauditar && ($auditoriaExistente['resultado'] ?? '') === 'CON_NOVEDAD') ? 'checked' : '' ?>>
                    <label for="res-con" class="resultado-option-label label-con">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <div class="opt-text">
                            <h4>Con novedades</h4>
                            <p>Se encontraron irregularidades o incidencias que se deben registrar.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-top: 8px;">
                <label style="font-weight: 600; font-size: 14px; color: var(--color-oscuro); margin-bottom: 8px;">
                    <i class="fa-solid fa-comment-dots" style="margin-right: 4px; color: var(--color-primario);"></i>
                    Observaciones generales
                </label>
                <textarea class="observaciones-textarea" name="observaciones" id="observaciones_generales"
                    placeholder="Describe las observaciones generales de la auditoría..."><?= htmlspecialchars($esReauditar ? ($auditoriaExistente['observacion'] ?? '') : '') ?></textarea>
                <div class="obs-required-hint" id="obs-required-hint">
                    <i class="fa-solid fa-circle-exclamation"></i> Las observaciones son obligatorias cuando hay novedades.
                </div>
            </div>
        </div>

        <!-- Footer actions -->
        <div class="form-audit-footer">
            <a href="?ruta=lider/auditoria&fecha=<?= urlencode($fecha) ?>&programa_id=<?= (int)$clase['id_programa'] ?>" class="btn-demo" style="padding: 10px 18px; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn-primario" style="padding: 12px 28px; font-size: 15px; font-weight: 600;" id="btn-guardar-auditoria">
                <i class="fa-solid fa-check"></i> <?= $esReauditar ? 'Actualizar Auditoría' : 'Guardar Auditoría' ?>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioSin = document.getElementById('res-sin');
    const radioCon = document.getElementById('res-con');
    const obsTextarea = document.getElementById('observaciones_generales');
    const obsHint = document.getElementById('obs-required-hint');

    function toggleObsRequired() {
        if (radioCon && radioCon.checked) {
            obsHint.style.display = 'flex';
        } else {
            obsHint.style.display = 'none';
        }
    }

    if (radioSin) radioSin.addEventListener('change', toggleObsRequired);
    if (radioCon) radioCon.addEventListener('change', toggleObsRequired);
    toggleObsRequired();

    // Validation
    document.getElementById('form-auditoria').addEventListener('submit', function(e) {
        const resultado = document.querySelector('input[name="resultado"]:checked');

        if (!resultado) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Resultado requerido',
                text: 'Debes seleccionar si la clase se desarrolló sin o con novedades.',
                confirmButtonColor: '#3B0D8F'
            });
            return;
        }

        if (resultado.value === 'CON_NOVEDAD' && !obsTextarea.value.trim()) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Observación requerida',
                text: 'Cuando hay novedades debes describir la observación encontrada.',
                confirmButtonColor: '#3B0D8F'
            });
            obsTextarea.focus();
            return;
        }
    });
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
