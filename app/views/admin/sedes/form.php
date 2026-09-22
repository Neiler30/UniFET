<?php
$esEdicion = !empty($sede['id_sede']);
$this->render('shared/header', ['titulo' => ($esEdicion ? 'Editar Sede' : 'Nueva Sede') . ' - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => true],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);"><?= $esEdicion ? 'Editar Sede' : 'Nueva Sede Institucional' ?></h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            <?= $esEdicion ? 'Modifica la información física de la sede.' : 'Registra una nueva sede o campus para la universidad.' ?>
        </p>
    </div>
    <a href="?ruta=admin/espacios/sedes" class="btn-demo">
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

    <form action="?ruta=admin/espacios/sedes/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_sede" value="<?= $sede['id_sede'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Código de la Sede <span style="color:red;">*</span></label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($sede['codigo'] ?? '') ?>" placeholder="Ej: SEDE-P, SEDE-SUR..." required autofocus>
        </div>
        
        <div class="form-group">
            <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Nombre de la Sede <span style="color:red;">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($sede['nombre'] ?? '') ?>" placeholder="Ej: Sede Principal Norte" required>
        </div>

        <div class="form-group">
            <label for="direccion" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Dirección</label>
            <input type="text" id="direccion" name="direccion" class="form-control" value="<?= htmlspecialchars($sede['direccion'] ?? '') ?>" placeholder="Ej: Calle 100 # 15-20">
        </div>
        
        <div class="form-group">
            <label for="estado" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($sede['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($sede['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <div style="margin-top: 25px; display: flex; gap: 12px;">
            <button type="submit" class="btn-primario">
                <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Sede' ?>
            </button>
            <a href="?ruta=admin/espacios/sedes" class="btn-demo">Cancelar</a>
        </div>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
