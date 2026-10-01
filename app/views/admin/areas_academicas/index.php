<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Áreas Académicas']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'areas']);
?>

<div class="header-acciones">
    <div>
        <h1>Áreas Académicas</h1>
        <p>Catálogo institucional de áreas del conocimiento para clasificación y organización de asignaturas.</p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/areas/crear">
        <i class="fa-solid fa-plus"></i> Nueva Área
    </a>
</div>

<div class="panel table-panel">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Área del Conocimiento</th>
                    <th style="width: 140px; text-align: center;">Estado</th>
                    <th style="width: 120px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($areas)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px 20px;">
                            <i class="fa-solid fa-shapes" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                            <span style="color: var(--color-texto-secundario); font-size: 13.5px;">No hay áreas académicas cargadas en el sistema.</span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($areas as $area): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--color-oscuro); font-size: 14px;">
                                <i class="fa-solid fa-layer-group" style="color: var(--color-primario); font-size: 12px; margin-right: 6px;"></i>
                                <?= htmlspecialchars($area['nombre']) ?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge <?= strtolower($area['estado']) ?>">
                                <?= htmlspecialchars($area['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('elementos_compartidos/botones_accion', [
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
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
