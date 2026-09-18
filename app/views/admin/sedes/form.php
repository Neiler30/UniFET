<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Sede']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($sede['id_sede']);

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => true],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1><?= $esEdicion ? 'Editar Sede' : 'Nueva Sede' ?></h1>
    <a href="?ruta=admin/espacios/sedes" class="btn-demo" style="text-decoration:none;">← Volver</a>
</div>

<div class="panel" style="max-width: 600px;">
    <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="?ruta=admin/espacios/sedes/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_sede" value="<?= $sede['id_sede'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($sede['codigo'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($sede['nombre'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" class="form-control" value="<?= htmlspecialchars($sede['direccion'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($sede['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($sede['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <button type="submit" class="btn-primario">Guardar</button>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
