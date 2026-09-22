<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Bloques - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => true],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">GestiÃ³n de Bloques</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Edificios, torres y mÃ³dulos constructivos pertenecientes a cada sede.
        </p>
    </div>
    <a href="?ruta=admin/espacios/bloques/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nuevo Bloque
    </a>
</div>

<div class="panel table-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <label for="filtro-sede" style="font-size: 13.5px; font-weight: 600; color: var(--color-oscuro);">
                <i class="fa-solid fa-filter"></i> Filtrar por Sede:
            </label>
            <select id="filtro-sede" class="form-control" style="width: 280px;">
                <option value="">Todas las sedes</option>
                <?php
                $sedes_unicas = [];
                foreach ($bloques as $b) {
                    if (!empty($b['sede_nombre']) && !in_array($b['sede_nombre'], $sedes_unicas)) {
                        $sedes_unicas[] = $b['sede_nombre'];
                    }
                }
                sort($sedes_unicas);
                foreach ($sedes_unicas as $sedeName): ?>
                    <option value="<?= htmlspecialchars($sedeName) ?>"><?= htmlspecialchars($sedeName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="font-size: 13px; color: var(--color-texto-secundario);">
            Total registrados: <strong><?= count($bloques) ?></strong>
        </div>
    </div>

    <?php if (empty($bloques)): ?>
        <div class="empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay bloques registrados</h3>
            <p>Comienza registrando un bloque o edificio para alojar aulas, salas y laboratorios.</p>
            <a href="?ruta=admin/espacios/bloques/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primer Bloque
            </a>
        </div>
    <?php else: ?>
        <table class="table" id="tabla-bloques">
            <thead>
                <tr>
                    <th style="width: 140px;">CÃ³digo</th>
                    <th>Nombre del Bloque</th>
                    <th>Sede UbicaciÃ³n</th>
                    <th style="width: 130px;">Estado</th>
                    <th style="width: 180px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bloques as $bloque): ?>
                    <tr class="fila-bloque" data-sede="<?= htmlspecialchars($bloque['sede_nombre'] ?? '') ?>">
                        <td>
                            <strong style="font-family: monospace; font-size: 13.5px; background: #f1f3f5; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($bloque['codigo']) ?>
                            </strong>
                        </td>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($bloque['nombre']) ?></strong>
                        </td>
                        <td>
                            <span style="color: var(--color-texto-principal); font-size: 13.5px;">
                                <i class="fa-solid fa-building" style="color: var(--color-primario); font-size: 12px; opacity: 0.8; margin-right: 4px;"></i>
                                <?= htmlspecialchars($bloque['sede_nombre'] ?? '') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= strtolower($bloque['estado']) ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?= htmlspecialchars($bloque['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('shared/botones_accion', [
                                'ruta_editar' => '?ruta=admin/espacios/bloques/editar&id=' . (int)$bloque['id_bloque'],
                                'ruta_desactivar' => '?ruta=admin/espacios/bloques/desactivar&id=' . (int)$bloque['id_bloque'],
                                'activo' => $bloque['estado'] === 'ACTIVO',
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
document.getElementById('filtro-sede')?.addEventListener('change', function() {
    const selected = this.value;
    const rows = document.querySelectorAll('.fila-bloque');
    rows.forEach(row => {
        if (selected === '' || row.getAttribute('data-sede') === selected) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php $this->render('shared/footer'); ?>

