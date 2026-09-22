<?php
$esEdicion = !empty($bloque['id_bloque']);
$this->render('shared/header', ['titulo' => ($esEdicion ? 'Editar Bloque' : 'Nuevo Bloque') . ' - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => true],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);"><?= $esEdicion ? 'Editar Bloque' : 'Nuevo Bloque' ?></h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            <?= $esEdicion ? 'Modifica los datos del bloque o edificio.' : 'Registra un nuevo bloque asignándolo a su sede correspondiente.' ?>
        </p>
    </div>
    <a href="?ruta=admin/espacios/bloques" class="btn-demo">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>
</div>

<div class="panel" style="max-width: 640px; margin-top: 10px;">
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

    <form action="?ruta=admin/espacios/bloques/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_bloque" value="<?= $bloque['id_bloque'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="id_sede" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Sede de Ubicación <span style="color:red;">*</span></label>
            <select id="id_sede" name="id_sede" class="form-control" required>
                <option value="">Seleccione una sede...</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= $sede['id_sede'] ?>" <?= ((int)($bloque['id_sede'] ?? 0) === (int)$sede['id_sede']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sede['nombre']) ?> (<?= htmlspecialchars($sede['codigo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Código del Bloque <span style="color:red;">*</span></label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($bloque['codigo'] ?? '') ?>" placeholder="Ej: BLQ-A, EDIF-1..." required>
        </div>
        
        <div class="form-group">
            <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Nombre del Bloque <span style="color:red;">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($bloque['nombre'] ?? '') ?>" placeholder="Ej: Bloque A - Ingenierías" required>
        </div>
        
        <div class="form-group">
            <label for="estado" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($bloque['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($bloque['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <div style="margin-top: 25px; display: flex; gap: 12px;">
            <button type="submit" class="btn-primario">
                <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Bloque' ?>
            </button>
            <a href="?ruta=admin/espacios/bloques" class="btn-demo">Cancelar</a>
        </div>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
