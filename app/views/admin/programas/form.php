<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Programa']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($programa['id_programa']);

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => false],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => true],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1><?= $esEdicion ? 'Editar Programa' : 'Nuevo Programa' ?></h1>
    <a href="?ruta=admin/institucion/programas" class="btn-demo" style="text-decoration:none;">← Volver</a>
</div>

<div class="panel" style="max-width: 600px;">
    <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="?ruta=admin/institucion/programas/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_programa" value="<?= $programa['id_programa'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="id_facultad">Facultad</label>
            <select id="id_facultad" name="id_facultad" class="form-control" required>
                <option value="">Seleccione una facultad...</option>
                <?php foreach ($facultades as $facultad): ?>
                    <option value="<?= $facultad['id_facultad'] ?>" <?= ($programa['id_facultad'] ?? '') == $facultad['id_facultad'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($facultad['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($programa['codigo'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($programa['nombre'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($programa['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($programa['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <button type="submit" class="btn-primario">Guardar</button>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
