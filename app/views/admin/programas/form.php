<?php
$esEdicion = !empty($programa['id_programa']);
$this->render('shared/header', ['titulo' => ($esEdicion ? 'Editar Programa' : 'Nuevo Programa') . ' - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => false],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => true],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);"><?= $esEdicion ? 'Editar Programa' : 'Nuevo Programa Académico' ?></h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            <?= $esEdicion ? 'Modifica los parámetros del programa académico.' : 'Registra un programa asociándolo a su facultad correspondiente.' ?>
        </p>
    </div>
    <a href="?ruta=admin/institucion/programas" class="btn-demo">
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

    <form action="?ruta=admin/institucion/programas/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_programa" value="<?= $programa['id_programa'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="id_facultad" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Facultad Responsable <span style="color:red;">*</span></label>
            <select id="id_facultad" name="id_facultad" class="form-control" required>
                <option value="">Seleccione una facultad...</option>
                <?php foreach ($facultades as $facultad): ?>
                    <option value="<?= $facultad['id_facultad'] ?>" <?= ((int)($programa['id_facultad'] ?? 0) === (int)$facultad['id_facultad']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($facultad['nombre']) ?> (<?= htmlspecialchars($facultad['codigo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Código del Programa <span style="color:red;">*</span></label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="<?= htmlspecialchars($programa['codigo'] ?? '') ?>" placeholder="Ej: SIS, ADM, MED..." required>
        </div>
        
        <div class="form-group">
            <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Nombre del Programa <span style="color:red;">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($programa['nombre'] ?? '') ?>" placeholder="Ej: Ingeniería de Sistemas" required>
        </div>
        
        <div class="form-group">
            <label for="estado" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Estado Académico</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO" <?= ($programa['estado'] ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($programa['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>
        
        <div style="margin-top: 25px; display: flex; gap: 12px;">
            <button type="submit" class="btn-primario">
                <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Programa' ?>
            </button>
            <a href="?ruta=admin/institucion/programas" class="btn-demo">Cancelar</a>
        </div>
    </form>
</div>

<?php $this->render('shared/footer'); ?>
