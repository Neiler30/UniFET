<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Areas Academicas']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'areas']);
?>
<div class="header-acciones">
    <div>
        <h1>Areas academicas</h1>
        <p style="color:var(--color-texto-secundario);font-size:13px;">Catalogo plano por institucion para clasificar asignaturas.</p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/areas/crear"><i class="fa-solid fa-plus"></i> Nueva Area</a>
</div>
<div class="panel table-panel">
    <table class="table">
        <thead><tr><th>Nombre</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
        <tbody>
            <?php if (empty($areas)): ?><tr><td colspan="3" style="text-align:center;">No hay areas academicas cargadas. Si esperabas 23 areas oficiales, falta adjuntar/ejecutar la migracion semilla.</td></tr><?php endif; ?>
            <?php foreach ($areas as $area): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($area['nombre']) ?></strong></td>
                    <td><span class="badge <?= strtolower($area['estado']) ?>"><?= htmlspecialchars($area['estado']) ?></span></td>
                    <td style="text-align:right;">
                        <?php $this->render('shared/botones_accion', [
                            'ruta_editar' => '?ruta=admin/academico/areas/editar&id=' . (int)$area['id_area_academica'],
                            'ruta_desactivar' => '?ruta=admin/academico/areas/desactivar&id=' . (int)$area['id_area_academica'],
                            'activo' => $area['estado'] === 'ACTIVO',
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $this->render('shared/footer'); ?>
