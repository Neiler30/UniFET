<?php
$esEdicion = !empty($facultad['id_facultad']);
$this->render('shared/header', ['titulo' => ($esEdicion ? 'Editar Facultad' : 'Nueva Facultad') . ' - UniFET']);
$this->render('shared/sidebar_admin');
$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => true],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

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

<div class="form-shell">
    <div class="brand-form-card">
        <div class="form-entity-icon"><i class="fa-solid fa-building-columns"></i></div>
        <h1><?= $esEdicion ? 'Editar Facultad' : 'Nueva Facultad' ?></h1>
        <p><?= $esEdicion ? 'Actualiza la unidad academica mayor.' : 'Registra una unidad academica del catalogo institucional.' ?></p>

        <form action="?ruta=admin/institucion/facultades/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_facultad" value="<?= (int)$facultad['id_facultad'] ?>">
            <?php endif; ?>

            <div class="form-group input-icon">
                <label for="codigo">Codigo institucional</label>
                <i class="fa-solid fa-barcode"></i>
                <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($facultad['codigo'] ?? '') ?>" placeholder="Ej: ING" required autofocus>
            </div>

            <div class="form-group input-icon">
                <label for="nombre">Nombre de la facultad</label>
                <i class="fa-solid fa-graduation-cap"></i>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($facultad['nombre'] ?? '') ?>" placeholder="Ej: Ingenieria" required>
            </div>

            <div class="form-group input-icon">
                <label for="estado">Estado academico</label>
                <i class="fa-solid fa-toggle-on"></i>
                <select id="estado" name="estado" class="form-control" required>
                    <option value="ACTIVO" <?= ($facultad['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                    <option value="INACTIVO" <?= ($facultad['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primario"><i class="fa-solid fa-check"></i> Guardar</button>
                <a href="?ruta=admin/institucion/facultades" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php $this->render('shared/footer'); ?>
