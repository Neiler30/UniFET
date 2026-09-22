<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Sedes - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => true],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">GestiÃ³n de Sedes</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Campus e instalaciones fÃ­sicas principales donde opera la universidad.
        </p>
    </div>
    <a href="?ruta=admin/espacios/sedes/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nueva Sede
    </a>
</div>

<div class="panel table-panel">
    <?php if (empty($sedes)): ?>
        <div class="empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay sedes registradas</h3>
            <p>Registra la primera sede institucional para empezar a organizar bloques y aulas.</p>
            <a href="?ruta=admin/espacios/sedes/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primera Sede
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 140px;">CÃ³digo</th>
                    <th>Nombre de la Sede</th>
                    <th>DirecciÃ³n</th>
                    <th style="width: 130px;">Estado</th>
                    <th style="width: 180px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sedes as $sede): ?>
                    <tr>
                        <td>
                            <strong style="font-family: monospace; font-size: 13.5px; background: #f1f3f5; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($sede['codigo']) ?>
                            </strong>
                        </td>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($sede['nombre']) ?></strong>
                        </td>
                        <td>
                            <span style="color: var(--color-texto-secundario); font-size: 13.5px;">
                                <i class="fa-solid fa-location-dot" style="margin-right: 4px; opacity: 0.7;"></i>
                                <?= htmlspecialchars($sede['direccion'] ?: 'Sin direcciÃ³n especificada') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= strtolower($sede['estado']) ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?= htmlspecialchars($sede['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('shared/botones_accion', [
                                'ruta_editar' => '?ruta=admin/espacios/sedes/editar&id=' . (int)$sede['id_sede'],
                                'ruta_desactivar' => '?ruta=admin/espacios/sedes/desactivar&id=' . (int)$sede['id_sede'],
                                'activo' => $sede['estado'] === 'ACTIVO',
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $this->render('shared/footer'); ?>

