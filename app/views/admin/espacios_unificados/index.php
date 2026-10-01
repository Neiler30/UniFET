<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Gestión de Espacios Físicos - UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
?>

<style>
/* ============================== ESPACIOS UNIFICADOS ============================== */
.eu-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 60%, #1a0a3e 100%);
    color: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    position: relative;
    overflow: hidden;
}
.eu-hero::before {
    content: '';
    position: absolute;
    right: -30px; bottom: -30px;
    width: 220px; height: 220px;
    background: url('assets/img/isotipo-blanco.png') no-repeat center/contain;
    opacity: 0.06;
    pointer-events: none;
}
.eu-hero h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
.eu-hero p { color: rgba(255,255,255,.78); margin: 0; font-size: 14px; }

/* Progress Steps */
.eu-steps {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    justify-content: center;
}
.eu-step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 10px;
    background: #f4f6f8;
    border: 2px solid var(--color-borde);
    color: var(--color-texto-secundario);
    font-weight: 600;
    font-size: 13px;
    transition: all .2s;
}
.eu-step.active {
    background: var(--color-primario);
    border-color: var(--color-primario);
    color: #fff;
}
.eu-step.completed {
    background: rgba(15,157,88,.1);
    border-color: #0F9D58;
    color: #0b8046;
}
.eu-step .step-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    background: var(--color-borde); color: var(--color-texto-secundario);
}
.eu-step.active .step-num { background: rgba(255,255,255,.2); color: #fff; }
.eu-step.completed .step-num { background: #0F9D58; color: #fff; }
.eu-step i { font-size: 16px; }

/* Cards Container */
.eu-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}
.eu-card {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 22px;
    box-shadow: var(--sombra-tarjeta);
    transition: box-shadow .2s, transform .2s;
}
.eu-card:hover { box-shadow: var(--sombra-hover); transform: translateY(-2px); }
.eu-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.eu-card-header h3 { font-size: 15px; font-weight: 700; color: var(--color-oscuro); margin: 0; }
.eu-card-header .badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.badge-activo { background: #d1fae5; color: #065f46; }
.badge-inactivo { background: #f1f5f9; color: #64748b; }

/* Items */
.eu-item { margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid #f8fafc; }
.eu-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.eu-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.eu-item-code {
    font-family: monospace;
    font-size: 13px;
    background: #f1f3f5;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    color: var(--color-primario);
}
.eu-item-name { font-weight: 600; color: var(--color-oscuro); font-size: 14px; }
.eu-item-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    font-size: 12px;
    color: var(--color-texto-secundario);
}
.eu-item-meta span { display: flex; align-items: center; gap: 4px; }
.eu-item-meta i { color: var(--color-primario); opacity: .7; font-size: 11px; }

/* Empty State */
.eu-empty {
    text-align: center;
    padding: 48px 20px;
    color: var(--color-texto-secundario);
}
.eu-empty i { font-size: 48px; opacity: .3; margin-bottom: 14px; display: block; }
.eu-empty h3 { font-size: 18px; color: var(--color-oscuro); margin-bottom: 6px; }
.eu-empty p { font-size: 14px; max-width: 440px; margin: 0 auto 16px; }

/* Form Sections */
.eu-form-section {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--sombra-tarjeta);
}
.eu-form-section h3 {
    font-size: 16px; font-weight: 700; color: var(--color-oscuro);
    display: flex; align-items: center; gap: 8px; margin-bottom: 18px;
}
.eu-form-section h3 i { color: var(--color-primario); }

/* Form Grid */
.eu-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}
.eu-form-group { margin-bottom: 0; }
.eu-form-group label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--color-oscuro);
    margin-bottom: 6px;
}
.eu-form-group .required::after { content: ' *'; color: #ef4444; }
.eu-form-group input,
.eu-form-group select,
.eu-form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--color-borde);
    border-radius: 8px;
    font-family: var(--fuente-principal);
    font-size: 13.5px;
    color: var(--color-texto-principal);
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.eu-form-group input:focus,
.eu-form-group select:focus,
.eu-form-group textarea:focus {
    outline: none;
    border-color: var(--color-primario);
    box-shadow: 0 0 0 3px rgba(59,13,143,.1);
}
.eu-form-group input[readonly] {
    background: #f8fafc;
    color: var(--color-texto-secundario);
}

/* Code Suggestion */
.eu-code-suggestion {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding: 8px 12px;
    background: rgba(59,13,143,.06);
    border: 1px dashed var(--color-primario);
    border-radius: 8px;
    font-size: 12.5px;
    color: var(--color-primario);
}
.eu-code-suggestion i { font-size: 14px; }
.eu-code-suggestion code {
    font-family: monospace;
    font-size: 13px;
    font-weight: 600;
    background: rgba(59,13,143,.1);
    padding: 2px 6px;
    border-radius: 4px;
}

/* Alert */
.eu-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
}
.eu-alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.eu-alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

/* Actions */
.eu-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}

/* Completed Steps Summary */
.eu-summary {
    background: linear-gradient(135deg, rgba(15,157,88,.08), rgba(15,157,88,.04));
    border: 1px solid rgba(15,157,88,.2);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
}
.eu-summary h4 { font-size: 14px; font-weight: 700; color: #0b8046; margin-bottom: 12px; }
.eu-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}
.eu-summary-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #d1fae5;
}
.eu-summary-item i { color: #0F9D58; font-size: 18px; }
.eu-summary-item .sum-label { font-size: 11px; color: var(--color-texto-secundario); text-transform: uppercase; }
.eu-summary-item .sum-value { font-weight: 600; color: var(--color-oscuro); font-size: 13px; }

@media (max-width: 900px) {
    .eu-hero { flex-direction: column; align-items: stretch; padding: 22px; }
    .eu-steps { flex-wrap: wrap; }
    .eu-step { flex: 1; min-width: 140px; justify-content: center; }
    .eu-form-grid { grid-template-columns: 1fr; }
}
</style>

<!-- =================== HERO =================== -->
<div class="eu-hero">
    <div>
        <h1><i class="fa-solid fa-building-columns" style="margin-right: 8px;"></i> Gestión de Espacios Físicos</h1>
        <p>Administra sedes, bloques y espacios (aulas, laboratorios, auditorios) en un flujo unificado</p>
    </div>
    <a href="?ruta=admin/espacios/unificados/crear" class="btn-primario" style="padding: 12px 24px; font-size: 14px; white-space: nowrap;">
        <i class="fa-solid fa-plus"></i> Nueva Sede / Bloque / Espacio
    </a>
</div>

<!-- =================== PROGRESS STEPS =================== -->
<nav class="eu-steps" aria-label="Pasos de creación">
    <div class="eu-step completed">
        <span class="step-num"><i class="fa-solid fa-check"></i></span>
        <span>1. Sede</span>
    </div>
    <div class="eu-step completed">
        <span class="step-num"><i class="fa-solid fa-check"></i></span>
        <span>2. Bloque</span>
    </div>
    <div class="eu-step completed">
        <span class="step-num"><i class="fa-solid fa-check"></i></span>
        <span>3. Espacios</span>
    </div>
</nav>

<!-- =================== CONTENT =================== -->
<?php if (empty($sedes)): ?>
    <div class="panel">
        <div class="eu-empty">
            <i class="fa-solid fa-building-columns"></i>
            <h3>No hay sedes registradas</h3>
            <p>Comienza creando la primera sede institucional para poder organizar bloques y espacios.</p>
            <a href="?ruta=admin/espacios/unificados/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primera Sede
            </a>
        </div>
    </div>
<?php else: ?>
    <!-- Resumen rápido -->
    <div class="eu-summary">
        <h4><i class="fa-solid fa-check-circle"></i> Resumen de la institución</h4>
        <div class="eu-summary-grid">
            <div class="eu-summary-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <div class="sum-label">Total Sedes</div>
                    <div class="sum-value"><?= count($sedes) ?></div>
                </div>
            </div>
            <div class="eu-summary-item">
                <i class="fa-solid fa-building"></i>
                <div>
                    <div class="sum-label">Total Bloques</div>
                    <div class="sum-value"><?= count($bloques) ?></div>
                </div>
            </div>
            <div class="eu-summary-item">
                <i class="fa-solid fa-door-open"></i>
                <div>
                    <div class="sum-label">Total Espacios</div>
                    <div class="sum-value"><?= count($espacios) ?></div>
                </div>
            </div>
            <div class="eu-summary-item">
                <i class="fa-solid fa-users"></i>
                <div>
                    <div class="sum-label">Capacidad Total</div>
                    <div class="sum-value"><?= array_sum(array_column($espacios, 'capacidad')) ?> est.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards por Sede -->
    <?php
    $sedesConDatos = [];
    foreach ($sedes as $sede) {
        $bloquesSede = array_filter($bloques, fn($b) => (int)$b['id_sede'] === (int)$sede['id_sede']);
        $espaciosSede = [];
        foreach ($bloquesSede as $b) {
            $espaciosBloque = array_filter($espacios, fn($e) => (int)$e['id_bloque'] === (int)$b['id_bloque']);
            $espaciosSede = array_merge($espaciosSede, $espaciosBloque);
        }
        $sedesConDatos[] = ['sede' => $sede, 'bloques' => $bloquesSede, 'espacios' => $espaciosSede];
    }
    ?>

    <?php foreach ($sedesConDatos as $sd): ?>
    <div class="eu-card">
        <div class="eu-card-header">
            <h3>
                <i class="fa-solid fa-location-dot" style="color: var(--color-primario); margin-right: 6px;"></i>
                <?= htmlspecialchars($sd['sede']['nombre']) ?>
            </h3>
            <span class="badge badge-<?= strtolower($sd['sede']['estado']) ?>">
                <i class="fa-solid fa-circle" style="font-size: 7px;"></i> <?= htmlspecialchars($sd['sede']['estado']) ?>
            </span>
        </div>

        <div class="eu-item">
            <div class="eu-item-header">
                <div>
                    <div class="eu-item-name"><?= htmlspecialchars($sd['sede']['nombre']) ?></div>
                    <div class="eu-item-code"><?= htmlspecialchars($sd['sede']['codigo']) ?></div>
                </div>
            </div>
            <div class="eu-item-meta">
                <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($sd['sede']['direccion'] ?: 'Sin dirección') ?></span>
                <span><i class="fa-solid fa-building"></i> <?= count($sd['bloques']) ?> bloques</span>
                <span><i class="fa-solid fa-door-open"></i> <?= count($sd['espacios']) ?> espacios</span>
            </div>
        </div>

        <?php if (!empty($sd['bloques'])): ?>
            <?php foreach ($sd['bloques'] as $bloque):
                $espaciosBloque = array_filter($sd['espacios'], fn($e) => (int)$e['id_bloque'] === (int)$bloque['id_bloque']);
            ?>
            <div class="eu-item">
                <div class="eu-item-header">
                    <div>
                        <div class="eu-item-name"><?= htmlspecialchars($bloque['nombre']) ?></div>
                        <div class="eu-item-code"><?= htmlspecialchars($bloque['codigo']) ?></div>
                    </div>
                    <span class="badge badge-<?= strtolower($bloque['estado']) ?>">
                        <i class="fa-solid fa-circle" style="font-size: 7px;"></i> <?= htmlspecialchars($bloque['estado']) ?>
                    </span>
                </div>
                <div class="eu-item-meta">
                    <span><i class="fa-solid fa-door-open"></i> <?= count($espaciosBloque) ?> espacios</span>
                    <?php
                    $tipos = array_count_values(array_column($espaciosBloque, 'tipo'));
                    foreach ($tipos as $tipo => $count):
                    ?>
                        <span><i class="fa-solid fa-tag"></i> <?= $count ?> <?= strtolower($tipo) ?><?= $count > 1 ? 's' : '' ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="eu-item" style="text-align: center; color: var(--color-texto-secundario); padding: 20px;">
                <i class="fa-solid fa-building" style="font-size: 24px; opacity: .3; margin-bottom: 8px; display: block;"></i>
                <span>No hay bloques en esta sede</span>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php $this->render('elementos_compartidos/footer'); ?>
