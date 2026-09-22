<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Area Academica']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'areas']);
$esEdicion = !empty($area['id_area_academica']);
?>
<div class="form-shell">
    <div class="brand-form-card">
        <div class="form-entity-icon"><i class="fa-solid fa-shapes"></i></div>
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <p>Clasifica asignaturas sin relacionarla directamente con programas o facultades.</p>
        <form method="POST" action="?ruta=admin/academico/areas/<?= $esEdicion ? 'actualizar' : 'guardar' ?>">
            <?php if ($esEdicion): ?><input type="hidden" name="id_area_academica" value="<?= (int)$area['id_area_academica'] ?>"><?php endif; ?>
            <div class="form-group input-icon">
                <label>Nombre</label>
                <i class="fa-solid fa-tag"></i>
                <input class="form-control" name="nombre" required value="<?= htmlspecialchars($area['nombre'] ?? '') ?>">
            </div>
            <div class="form-group input-icon">
                <label>Estado</label>
                <i class="fa-solid fa-toggle-on"></i>
                <select class="form-control" name="estado">
                    <?php foreach (['ACTIVO','INACTIVO'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= (($area['estado'] ?? 'ACTIVO') === $estado) ? 'selected' : '' ?>><?= $estado ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <button class="btn-primario"><i class="fa-solid fa-check"></i> Guardar</button>
                <a class="btn-outline" href="?ruta=admin/academico/areas"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php $this->render('shared/footer'); ?>
