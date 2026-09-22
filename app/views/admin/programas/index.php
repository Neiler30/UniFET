<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Programas Académicos - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => false],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => true],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">Gestión de Programas Académicos</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Carreras y pregrados adscritos a las diferentes facultades de la institución.
        </p>
    </div>
    <a href="?ruta=admin/institucion/programas/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nuevo Programa
    </a>
</div>

<div class="panel table-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <label for="filtro-facultad" style="font-size: 13.5px; font-weight: 600; color: var(--color-oscuro);">
                <i class="fa-solid fa-filter"></i> Filtrar por Facultad:
            </label>
            <select id="filtro-facultad" class="form-control" style="width: 280px;">
                <option value="">Todas las facultades</option>
                <?php
                $facultades_unicas = [];
                foreach ($programas as $p) {
                    if (!empty($p['facultad_nombre']) && !in_array($p['facultad_nombre'], $facultades_unicas)) {
                        $facultades_unicas[] = $p['facultad_nombre'];
                    }
                }
                sort($facultades_unicas);
                foreach ($facultades_unicas as $facName): ?>
                    <option value="<?= htmlspecialchars($facName) ?>"><?= htmlspecialchars($facName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="font-size: 13px; color: var(--color-texto-secundario);">
            Total registrados: <strong><?= count($programas) ?></strong>
        </div>
    </div>

    <?php if (empty($programas)): ?>
        <div class="empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay programas académicos registrados</h3>
            <p>Registra las carreras y planes de estudio vinculados a las facultades.</p>
            <a href="?ruta=admin/institucion/programas/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primer Programa
            </a>
        </div>
    <?php else: ?>
        <table class="table" id="tabla-programas">
            <thead>
                <tr>
                    <th style="width: 130px;">Código</th>
                    <th>Nombre del Programa</th>
                    <th>Facultad Adscrita</th>
                    <th style="width: 130px;">Estado</th>
                    <th style="width: 180px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($programas as $programa): ?>
                    <tr class="fila-programa" data-facultad="<?= htmlspecialchars($programa['facultad_nombre'] ?? '') ?>">
                        <td>
                            <strong style="font-family: monospace; font-size: 13.5px; background: #f1f3f5; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($programa['codigo']) ?>
                            </strong>
                        </td>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($programa['nombre']) ?></strong>
                        </td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 6px; color: var(--color-texto-principal); font-size: 13.5px;">
                                <i class="fa-solid fa-building-columns" style="color: var(--color-primario); font-size: 12px; opacity: 0.8;"></i>
                                <?= htmlspecialchars($programa['facultad_nombre'] ?? 'Sin asignar') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= strtolower($programa['estado']) ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?= htmlspecialchars($programa['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('shared/botones_accion', [
                                'ruta_editar' => '?ruta=admin/institucion/programas/editar&id=' . (int)$programa['id_programa'],
                                'ruta_desactivar' => '?ruta=admin/institucion/programas/desactivar&id=' . (int)$programa['id_programa'],
                                'activo' => $programa['estado'] === 'ACTIVO',
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
document.getElementById('filtro-facultad')?.addEventListener('change', function() {
    const selected = this.value;
    const rows = document.querySelectorAll('.fila-programa');
    rows.forEach(row => {
        if (selected === '' || row.getAttribute('data-facultad') === selected) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php $this->render('shared/footer'); ?>
