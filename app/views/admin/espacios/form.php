<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Espacio']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($espacio['id_espacio']);
?>

<div class="header-acciones">
    <h1><?= $esEdicion ? 'Editar Espacio' : 'Nuevo Espacio' ?></h1>
    <a href="?ruta=admin/espacios" class="btn-demo" style="text-decoration:none;">← Volver</a>
</div>

<div class="panel" style="max-width: 600px;">
    <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="?ruta=admin/espacios/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_espacio" value="<?= $espacio['id_espacio'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="id_bloque">Bloque</label>
            <select id="id_bloque" name="id_bloque" class="form-control" required>
                <option value="">Seleccione un bloque...</option>
                <?php foreach ($bloques as $bloque): ?>
                    <option value="<?= $bloque['id_bloque'] ?>" <?= ($espacio['id_bloque'] ?? '') == $bloque['id_bloque'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars(($bloque['sede_nombre'] ?? '') . ' - ' . $bloque['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($espacio['codigo'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($espacio['nombre'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="AULA" <?= ($espacio['tipo'] ?? '') == 'AULA' ? 'selected' : '' ?>>AULA</option>
                <option value="LABORATORIO" <?= ($espacio['tipo'] ?? '') == 'LABORATORIO' ? 'selected' : '' ?>>LABORATORIO</option>
                <option value="SALA" <?= ($espacio['tipo'] ?? '') == 'SALA' ? 'selected' : '' ?>>SALA</option>
                <option value="AUDITORIO" <?= ($espacio['tipo'] ?? '') == 'AUDITORIO' ? 'selected' : '' ?>>AUDITORIO</option>
                <option value="OTRO" <?= ($espacio['tipo'] ?? '') == 'OTRO' ? 'selected' : '' ?>>OTRO</option>
            </select>
        </div>

        <div class="form-group">
            <label for="capacidad">Capacidad</label>
            <input type="number" id="capacidad" name="capacidad" class="form-control" value="<?= htmlspecialchars($espacio['capacidad'] ?? '0') ?>" min="0" required>
        </div>

        <div class="form-group">
            <label for="caracteristicas">Características (JSON)</label>
            <input type="text" id="caracteristicas" name="caracteristicas" class="form-control" value="<?= htmlspecialchars($espacio['caracteristicas'] ?? '[]') ?>" placeholder="Ej: [&quot;PC&quot;, &quot;Proyector&quot;]">
        </div>
        
        <button type="submit" class="btn-primario">Guardar</button>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
