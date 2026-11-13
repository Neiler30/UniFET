<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Lideres de Programa']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs', ['tabs' => [
    ['etiqueta' => 'Personalizacion', 'ruta' => 'admin/sistema/personalizacion', 'activa' => false],
    ['etiqueta' => 'Usuarios', 'ruta' => 'admin/sistema/usuarios', 'activa' => false],
    ['etiqueta' => 'Lideres de Programa', 'ruta' => 'admin/sistema/lideres', 'activa' => true],
]]);
$lideres = array_filter($usuarios ?? [], fn($u) => ($u['rol'] ?? '') === 'LIDER_PROGRAMA');
$dominioInstitucional = !empty($institucion['dominio_correo']) ? $institucion['dominio_correo'] : '@unicaribe.edu.co';
if (!str_starts_with($dominioInstitucional, '@')) {
    $dominioInstitucional = '@' . $dominioInstitucional;
}
?>
<style>
.lideres-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #200654 100%);
    color: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);
}
.lideres-hero::after {
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
.lideres-hero h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; position: relative; z-index: 1; }
.lideres-hero p { color: rgba(255,255,255,.82); font-size: 13.5px; margin: 0; position: relative; z-index: 1; }
.lideres-hero .hero-stats { display: flex; gap: 12px; z-index: 2; }
.lideres-hero .hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px;
    padding: 10px 18px;
    text-align: center;
    backdrop-filter: blur(4px);
}
.lideres-hero .hero-stat strong { display: block; font-size: 20px; font-weight: 700; }
.lideres-hero .hero-stat span { font-size: 11px; color: rgba(255,255,255,.75); text-transform: uppercase; }

.leader-layout{display:grid;grid-template-columns:minmax(280px,420px) 1fr;gap:20px;align-items:start}.wizard-card{position:sticky;top:86px}.wizard-steps{display:grid;gap:10px;margin-bottom:18px}.wizard-step{display:flex;gap:10px;align-items:center;padding:10px;border:1px solid var(--color-borde);border-radius:8px;color:var(--color-texto-secundario);font-size:13px}.wizard-step.active{border-color:var(--color-primario);background:rgba(59,13,143,.06);color:var(--color-primario);font-weight:700}.step-dot{width:26px;height:26px;border-radius:50%;display:grid;place-items:center;background:#f1f3f5}.wizard-step.active .step-dot{background:var(--color-primario);color:#fff}.wizard-panel{display:none}.wizard-panel.active{display:block}.program-preview{border:1px solid var(--color-borde);border-radius:8px;padding:14px;background:#fafbfc;margin-top:10px}.table-wrap{overflow-x:auto}@media(max-width:980px){.leader-layout{grid-template-columns:1fr}.wizard-card{position:static}}
</style>
<div class="lideres-hero">
    <div>
        <h1><i class="fa-solid fa-user-tie" style="margin-right:10px;opacity:.85;"></i> Líderes de Programa</h1>
        <p>Creación guiada con asociación obligatoria a un programa académico activo.</p>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong><?= count($lideres) ?></strong>
            <span>Líderes</span>
        </div>
    </div>
</div>
<div class="leader-layout">
    <div class="panel wizard-card">
        <div class="wizard-steps">
            <div class="wizard-step active" data-step-label="1"><span class="step-dot">1</span> Programa</div>
            <div class="wizard-step" data-step-label="2"><span class="step-dot">2</span> Datos del lider</div>
            <div class="wizard-step" data-step-label="3"><span class="step-dot">3</span> Confirmacion</div>
        </div>
        <form id="leader-wizard" action="?ruta=admin/sistema/lideres/guardar" method="POST">
            <section class="wizard-panel active" data-step="1">
                <div class="form-group">
                    <label>Programa a coordinar</label>
                    <select class="form-control" name="id_programa" id="leader-program" required>
                        <option value="">Seleccionar programa</option>
                        <?php foreach ($programas as $programa): ?>
                            <option value="<?= (int)$programa['id_programa'] ?>"
                                data-codigo="<?= htmlspecialchars($programa['codigo']) ?>"
                                data-facultad="<?= htmlspecialchars($programa['facultad_nombre'] ?? '') ?>"
                                data-estado="<?= htmlspecialchars($programa['estado']) ?>">
                                <?= htmlspecialchars(($programa['codigo'] ?? '') . ' - ' . $programa['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="program-preview" id="program-preview">Selecciona un programa para ver su facultad y estado.</div>
                <button type="button" class="btn-primario" data-next style="width:100%;margin-top:14px;">Continuar</button>
            </section>
            <section class="wizard-panel" data-step="2">
                <div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" id="leader-name" required></div>
                <div class="form-group"><label>Apellido</label><input class="form-control" name="apellido" id="leader-lastname" required></div>
                <div class="form-group">
                    <label>Correo institucional</label>
                    <input type="email" class="form-control" name="correo" id="leader-email" placeholder="usuario<?= htmlspecialchars($dominioInstitucional) ?>" required>
                    <small style="display:block;margin-top:6px;color:var(--color-texto-secundario);">Se genera automáticamente con dominio <?= htmlspecialchars($dominioInstitucional) ?>, pero puedes editarlo antes de guardar.</small>
                </div>
                <p style="color:var(--color-texto-secundario);font-size:13px;margin-bottom:12px;">El sistema generara una contrasena temporal y la mostrara una sola vez al guardar.</p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;"><button type="button" class="btn-demo" data-prev>Volver</button><button type="button" class="btn-primario" data-next>Continuar</button></div>
            </section>
            <section class="wizard-panel" data-step="3">
                <div class="program-preview" id="leader-summary"></div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;"><button type="button" class="btn-demo" data-prev>Volver</button><button type="submit" class="btn-primario">Crear lider</button></div>
            </section>
        </form>
    </div>
    <div class="panel table-wrap">
        <h2 style="font-size:18px;margin-bottom:12px;">Lideres registrados</h2>
        <table class="table">
            <thead><tr><th>Lider</th><th>Correo</th><th>Programas coordinados</th><th>Temporal</th><th>Estado</th></tr></thead>
            <tbody>
                <?php if (empty($lideres)): ?><tr><td colspan="5" style="text-align:center;padding:24px;">No hay lideres registrados.</td></tr><?php endif; ?>
                <?php foreach ($lideres as $lider): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) ?></strong></td>
                        <td><?= htmlspecialchars($lider['correo']) ?></td>
                        <td><?= htmlspecialchars($lider['programas'] ?? '-') ?></td>
                        <td><?= !empty($lider['password_temporal']) ? '<span class="badge pendiente">SI</span>' : '<span class="badge activo">NO</span>' ?></td>
                        <td><span class="badge <?= strtolower($lider['estado']) ?>"><?= htmlspecialchars($lider['estado']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('leader-wizard');
    const panels = [...document.querySelectorAll('.wizard-panel')];
    const labels = [...document.querySelectorAll('.wizard-step')];
    const program = document.getElementById('leader-program');
    const nameInput = document.getElementById('leader-name');
    const lastNameInput = document.getElementById('leader-lastname');
    const emailInput = document.getElementById('leader-email');
    const preview = document.getElementById('program-preview');
    const summary = document.getElementById('leader-summary');
    let step = 1;
    let emailTouched = false;
    const slug = value => String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9]+/g, '.')
        .replace(/^\.+|\.+$/g, '');
    const DOMINIO_INSTITUCIONAL = '<?= addslashes($dominioInstitucional) ?>';
    const updateEmail = () => {
        if (emailTouched) return;
        const nombre = slug(nameInput.value).split('.')[0] || '';
        const apellido = slug(lastNameInput.value).split('.')[0] || '';
        if (!nombre || !apellido) {
            emailInput.value = '';
            return;
        }
        emailInput.value = `${nombre}.${apellido}${DOMINIO_INSTITUCIONAL}`;
    };
    const show = (next) => {
        step = next;
        panels.forEach(panel => panel.classList.toggle('active', Number(panel.dataset.step) === step));
        labels.forEach(label => label.classList.toggle('active', Number(label.dataset.stepLabel) === step));
        if (step === 3) {
            const selected = program.options[program.selectedIndex];
            const nombre = form.nombre.value || '-';
            const apellido = form.apellido.value || '-';
            summary.innerHTML = `<strong>${nombre} ${apellido}</strong><br><span>${form.correo.value || '-'}</span><hr style="border:0;border-top:1px solid var(--color-borde);margin:10px 0;">Programa: <strong>${selected ? selected.textContent.trim() : '-'}</strong>`;
        }
    };
    const updatePreview = () => {
        const selected = program.options[program.selectedIndex];
        if (!program.value) {
            preview.textContent = 'Selecciona un programa para ver su facultad y estado.';
            return;
        }
        preview.innerHTML = `<strong>${selected.textContent.trim()}</strong><br>Facultad: ${selected.dataset.facultad || '-'}<br>Estado: <span class="badge ${String(selected.dataset.estado || '').toLowerCase()}">${selected.dataset.estado || '-'}</span>`;
    };
    document.querySelectorAll('[data-next]').forEach(btn => btn.addEventListener('click', () => {
        const active = panels.find(panel => panel.classList.contains('active'));
        const invalid = [...active.querySelectorAll('input,select')].find(input => !input.checkValidity());
        if (invalid) { invalid.reportValidity(); return; }
        show(Math.min(step + 1, 3));
    }));
    document.querySelectorAll('[data-prev]').forEach(btn => btn.addEventListener('click', () => show(Math.max(step - 1, 1))));
    program.addEventListener('change', updatePreview);
    [nameInput, lastNameInput].forEach(input => input.addEventListener('input', updateEmail));
    emailInput.addEventListener('input', () => { emailTouched = true; });
    updatePreview();
});
</script>
<?php $this->render('elementos_compartidos/footer'); ?>
