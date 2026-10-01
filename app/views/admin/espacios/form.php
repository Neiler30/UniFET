<?php
$esEdicion = !empty($espacio['id_espacio']);
$this->render('elementos_compartidos/header', ['titulo' => ($esEdicion ? 'Editar Espacio' : 'Nuevo Espacio') . ' — UniFET']);
$this->render('elementos_compartidos/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => true],
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
.espacio-form-container {
    position: relative;
    z-index: 1;
    max-width: 680px;
    margin: 0 auto;
}
.espacio-card-form {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 14px;
    box-shadow: var(--sombra-tarjeta);
    overflow: hidden;
    position: relative;
}
.espacio-card-form::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('assets/img/isotipo-color.png') no-repeat right -20px bottom -20px / 180px;
    opacity: 0.025;
    pointer-events: none;
    border-radius: inherit;
}
.espacio-card-header {
    background: linear-gradient(135deg, var(--color-primario) 0%, #24114f 100%);
    color: #fff;
    padding: 22px 26px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.espacio-card-header i { font-size: 28px; opacity: 0.9; }
.espacio-card-header h1 { font-size: 19px; font-weight: 700; margin: 0 0 2px; }
.espacio-card-header p { font-size: 13px; color: rgba(255,255,255,0.75); margin: 0; }
.espacio-card-body { padding: 26px; }

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

<div class="espacio-form-container">
    <div style="margin-bottom: 16px;">
        <a href="?ruta=admin/espacios/espacios" style="display:inline-flex; align-items:center; gap:6px; font-size:13.5px; font-weight:600; color:var(--color-primario); text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Volver a Espacios
        </a>
    </div>

    <div class="espacio-card-form">
        <div class="espacio-card-header">
            <i class="fa-solid fa-door-open"></i>
            <div>
                <h1><?= $esEdicion ? 'Editar Espacio Físico' : 'Nuevo Espacio Físico' ?></h1>
                <p><?= $esEdicion ? 'Modifica los datos del aula, laboratorio o sala.' : 'Registra un nuevo espacio asignándolo a su bloque correspondiente.' ?></p>
            </div>
        </div>

        <div class="espacio-card-body">
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

            <form action="?ruta=admin/espacios/espacios/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST" id="form-espacio">
                <?php if ($esEdicion): ?>
                    <input type="hidden" name="id_espacio" id="id_espacio" value="<?= (int)$espacio['id_espacio'] ?>">
                <?php endif; ?>

                <!-- Bloque -->
                <div class="form-group">
                    <label for="id_bloque" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                        Bloque de Ubicación <span style="color:red;">*</span>
                    </label>
                    <select id="id_bloque" name="id_bloque" class="form-control" required onchange="sugerirCodigoEspacio()">
                        <option value="">— Seleccione un bloque —</option>
                        <?php foreach ($bloques as $bloque): ?>
                            <option value="<?= $bloque['id_bloque'] ?>" <?= ((int)($espacio['id_bloque'] ?? 0) === (int)$bloque['id_bloque']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($bloque['sede_nombre'] ?? '') . ' / ' . $bloque['nombre'] . ' (' . $bloque['codigo'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nombre + Código en grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                            Nombre del Espacio <span style="color:red;">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required
                               placeholder="Ej: Aula 101, Laboratorio Redes..."
                               value="<?= htmlspecialchars($espacio['nombre'] ?? '') ?>"
                               oninput="sugerirCodigoEspacio()">
                    </div>

                    <div class="form-group">
                        <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                            Código <span style="color:red;">*</span>
                        </label>
                        <div class="auto-code-input-wrap">
                            <input type="text" id="codigo" name="codigo" required
                                   placeholder="A101" maxlength="20"
                                   value="<?= htmlspecialchars($espacio['codigo'] ?? '') ?>"
                                   oninput="marcarManualEspacio()">
                            <span class="auto-code-badge <?= !empty($espacio['codigo']) ? 'manual' : '' ?>" id="badge-codigo">
                                <i class="fa-solid fa-<?= !empty($espacio['codigo']) ? 'pen' : 'wand-magic-sparkles' ?>"></i>
                                <?= !empty($espacio['codigo']) ? 'Manual' : 'Auto' ?>
                            </span>
                        </div>
                        <div class="auto-code-hint">
                            <i class="fa-solid fa-circle-info"></i> Se genera automáticamente al escribir el nombre.
                        </div>
                    </div>
                </div>

                <!-- Tipo + Capacidad -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="tipo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Tipo de Espacio</label>
                        <select id="tipo" name="tipo" class="form-control" required>
                            <option value="AULA"          <?= ($espacio['tipo'] ?? 'AULA') === 'AULA'          ? 'selected' : '' ?>>Aula</option>
                            <option value="LABORATORIO"   <?= ($espacio['tipo'] ?? '') === 'LABORATORIO'       ? 'selected' : '' ?>>Laboratorio</option>
                            <option value="AUDITORIO"     <?= ($espacio['tipo'] ?? '') === 'AUDITORIO'         ? 'selected' : '' ?>>Auditorio</option>
                            <option value="OFICINA"       <?= ($espacio['tipo'] ?? '') === 'OFICINA'           ? 'selected' : '' ?>>Oficina</option>
                            <option value="SALA_REUNIONES"<?= ($espacio['tipo'] ?? '') === 'SALA_REUNIONES'    ? 'selected' : '' ?>>Sala de Reuniones</option>
                            <option value="GIMNASIO"      <?= ($espacio['tipo'] ?? '') === 'GIMNASIO'          ? 'selected' : '' ?>>Gimnasio</option>
                            <option value="BIBLIOTECA"    <?= ($espacio['tipo'] ?? '') === 'BIBLIOTECA'        ? 'selected' : '' ?>>Biblioteca</option>
                            <option value="OTRO"          <?= ($espacio['tipo'] ?? '') === 'OTRO'              ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="capacidad" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Capacidad (estudiantes)</label>
                        <input type="number" id="capacidad" name="capacidad" class="form-control"
                               value="<?= htmlspecialchars($espacio['capacidad'] ?? '0') ?>"
                               min="0" max="9999" placeholder="0">
                    </div>
                </div>

                <!-- Características -->
                <div class="form-group">
                    <label for="caracteristicas" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Características adicionales</label>
                    <input type="text" id="caracteristicas" name="caracteristicas" class="form-control"
                           placeholder='Ej: ["PC", "Videobeam", "Aire Acondicionado"]'
                           value="<?= htmlspecialchars($espacio['caracteristicas'] ?? '[]') ?>">
                    <small style="color: var(--color-texto-secundario); font-size: 11.5px; margin-top: 4px; display:block;">
                        Opcional. Formato JSON o etiquetas descriptivas.
                    </small>
                </div>

                <div style="margin-top: 26px; display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--color-borde); padding-top: 18px;">
                    <a href="?ruta=admin/espacios/espacios" class="btn-demo">Cancelar</a>
                    <button type="submit" class="btn-primario" style="padding: 10px 24px;">
                        <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Espacio' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let debounceTimerEsp = null;
let esManualEsp = <?= !empty($espacio['codigo']) ? 'true' : 'false' ?>;

function marcarManualEspacio() {
    esManualEsp = true;
    const badge = document.getElementById('badge-codigo');
    if (badge) {
        badge.classList.add('manual');
        badge.innerHTML = '<i class="fa-solid fa-pen"></i> Manual';
    }
}

function sugerirCodigoEspacio() {
    if (esManualEsp) return;
    const nombre = document.getElementById('nombre').value.trim();
    const idBloque = document.getElementById('id_bloque').value;
    const idExcluir = document.getElementById('id_espacio') ? document.getElementById('id_espacio').value : 0;

    if (!nombre || nombre.length < 2) return;

    clearTimeout(debounceTimerEsp);
    debounceTimerEsp = setTimeout(async () => {
        try {
            const url = `?ruta=admin/espacios/sedes/api_codigo&tipo=espacio&nombre=${encodeURIComponent(nombre)}&id_ref=${idBloque || 0}&id_excluir=${idExcluir}`;
            const res = await fetch(url);
            const data = await res.json();
            if (data && data.codigo && !esManualEsp) {
                document.getElementById('codigo').value = data.codigo;
            }
        } catch (e) {
            console.warn('Error sugiriendo código:', e);
        }
    }, 400);
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
