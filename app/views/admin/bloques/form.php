<?php
$esEdicion = !empty($bloque['id_bloque']);
$this->render('elementos_compartidos/header', ['titulo' => ($esEdicion ? 'Editar Bloque' : 'Nuevo Bloque') . ' — UniFET']);
$this->render('elementos_compartidos/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => true],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('elementos_compartidos/tabs', ['tabs' => $tabs]);
?>

<style>
.form-watermark-bg {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.form-watermark-bg img {
    width: min(50vw, 420px);
    opacity: 0.025;
    user-select: none;
}
.bloque-form-container {
    position: relative;
    z-index: 1;
    max-width: 680px;
    margin: 0 auto;
}
.bloque-card-form {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 14px;
    box-shadow: var(--sombra-tarjeta);
    overflow: hidden;
    position: relative;
}
.bloque-card-form::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('assets/img/isotipo-color.png') no-repeat right -20px bottom -20px / 180px;
    opacity: 0.025;
    pointer-events: none;
    border-radius: inherit;
}
.bloque-card-header {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 100%);
    color: #fff;
    padding: 22px 26px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.bloque-card-header i { font-size: 28px; opacity: 0.9; }
.bloque-card-header h1 { font-size: 19px; font-weight: 700; margin: 0 0 2px; }
.bloque-card-header p { font-size: 13px; color: rgba(255,255,255,0.75); margin: 0; }
.bloque-card-body { padding: 26px; }

.auto-code-input-wrap {
    display: flex;
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
</style>

<div class="form-watermark-bg">
    <img src="assets/img/isotipo-color.png" alt="">
</div>

<div class="bloque-form-container">
    <div style="margin-bottom: 16px;">
        <a href="?ruta=admin/espacios/bloques" style="display:inline-flex; align-items:center; gap:6px; font-size:13.5px; font-weight:600; color:var(--color-primario); text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Volver a Bloques
        </a>
    </div>

    <div class="bloque-card-form">
        <div class="bloque-card-header">
            <i class="fa-solid fa-cube"></i>
            <div>
                <h1><?= $esEdicion ? 'Editar Bloque / Edificio' : 'Nuevo Bloque / Edificio' ?></h1>
                <p><?= $esEdicion ? 'Actualiza los datos del bloque.' : 'Registra un nuevo bloque arquitectónico para una sede institucional.' ?></p>
            </div>
        </div>

        <div class="bloque-card-body">
            <?php if (!empty($error)): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: 'No se pudo guardar',
                            text: <?= json_encode($error) ?>,
                            icon: 'error',
                            confirmButtonColor: '#3B0D8F'
                        });
                    });
                </script>
            <?php endif; ?>

            <form action="?ruta=admin/espacios/bloques/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST" id="form-bloque">
                <?php if ($esEdicion): ?>
                    <input type="hidden" name="id_bloque" id="id_bloque" value="<?= (int)$bloque['id_bloque'] ?>">
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Sede -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="id_sede" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                            Sede de Ubicación <span style="color:red;">*</span>
                        </label>
                        <select id="id_sede" name="id_sede" class="form-control" required onchange="sugerirCodigoBloque()">
                            <option value="">— Seleccione una sede —</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede['id_sede'] ?>" <?= ((int)($bloque['id_sede'] ?? 0) === (int)$sede['id_sede']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sede['nombre']) ?> (<?= htmlspecialchars($sede['codigo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nombre del Bloque -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                            Nombre del Bloque <span style="color:red;">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($bloque['nombre'] ?? '') ?>"
                               placeholder="Ej: Bloque A - Ingenierías, Torre 1..." required
                               oninput="sugerirCodigoBloque()">
                    </div>

                    <!-- Código con Auto-Generación -->
                    <div class="form-group">
                        <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                            Código del Bloque <span style="color:red;">*</span>
                        </label>
                        <div class="auto-code-input-wrap">
                            <input type="text" id="codigo" name="codigo"
                                   value="<?= htmlspecialchars($bloque['codigo'] ?? '') ?>"
                                   placeholder="BLQ-001" maxlength="20" required
                                   oninput="marcarManualBloque()">
                            <span class="auto-code-badge <?= !empty($bloque['codigo']) ? 'manual' : '' ?>" id="badge-codigo">
                                <i class="fa-solid fa-<?= !empty($bloque['codigo']) ? 'pen' : 'wand-magic-sparkles' ?>"></i>
                                <?= !empty($bloque['codigo']) ? 'Manual' : 'Auto' ?>
                            </span>
                        </div>
                        <div class="auto-code-hint">
                            <i class="fa-solid fa-circle-info"></i> Se calcula con el nombre y la sede seleccionada.
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            <option value="ACTIVO" <?= ($bloque['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                            <option value="INACTIVO" <?= ($bloque['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 26px; display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--color-borde); padding-top: 18px;">
                    <a href="?ruta=admin/espacios/bloques" class="btn-demo">Cancelar</a>
                    <button type="submit" class="btn-primario" style="padding: 10px 24px;">
                        <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Bloque' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let debounceTimer = null;
let esManual = <?= !empty($bloque['codigo']) ? 'true' : 'false' ?>;

function marcarManualBloque() {
    esManual = true;
    const badge = document.getElementById('badge-codigo');
    if (badge) {
        badge.classList.add('manual');
        badge.innerHTML = '<i class="fa-solid fa-pen"></i> Manual';
    }
}

function sugerirCodigoBloque() {
    if (esManual) return;
    const nombre = document.getElementById('nombre').value.trim();
    const idSede = document.getElementById('id_sede').value;
    const idExcluir = document.getElementById('id_bloque') ? document.getElementById('id_bloque').value : 0;

    if (!nombre || nombre.length < 2 || !idSede) return;

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
        try {
            const url = `?ruta=admin/espacios/sedes/api_codigo&tipo=bloque&nombre=${encodeURIComponent(nombre)}&id_ref=${idSede}&id_excluir=${idExcluir}`;
            const res = await fetch(url);
            const data = await res.json();
            if (data && data.codigo && !esManual) {
                document.getElementById('codigo').value = data.codigo;
            }
        } catch (e) {
            console.warn('Error sugiriendo código:', e);
        }
    }, 400);
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
