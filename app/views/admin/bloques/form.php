<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Bloque']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($bloque['id_bloque']);

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => true],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1><?= $esEdicion ? 'Editar Bloque' : 'Nuevo Bloque' ?></h1>
    <a href="?ruta=admin/espacios/bloques" class="btn-demo" style="text-decoration:none;">← Volver</a>
</div>

<div class="panel" style="max-width: 600px;">
    <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="?ruta=admin/espacios/bloques/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_bloque" value="<?= $bloque['id_bloque'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="id_sede">Sede</label>
            <select id="id_sede" name="id_sede" class="form-control" required>
                <option value="">Seleccione una sede...</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= $sede['id_sede'] ?>" <?= ($bloque['id_sede'] ?? '') == $sede['id_sede'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sede['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($bloque['codigo'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($bloque['nombre'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($bloque['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($bloque['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <button type="submit" class="btn-primario">Guardar</button>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
