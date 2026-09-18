<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Facultad']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($facultad['id_facultad']);

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => true],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1><?= $esEdicion ? 'Editar Facultad' : 'Nueva Facultad' ?></h1>
    <a href="?ruta=admin/institucion/facultades" class="btn-demo" style="text-decoration:none;">← Volver</a>
</div>

<div class="panel" style="max-width: 600px;">
    <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="?ruta=admin/institucion/facultades/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_facultad" value="<?= $facultad['id_facultad'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($facultad['codigo'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($facultad['nombre'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($facultad['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($facultad['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <button type="submit" class="btn-primario">Guardar</button>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
