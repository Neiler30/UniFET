<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Espacios FÃ­sicos - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => true],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">GestiÃ³n de Espacios FÃ­sicos</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Aulas, laboratorios, auditorios y salas disponibles para docencia y eventos.
        </p>
    </div>
    <a href="?ruta=admin/espacios/espacios/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nuevo Espacio
    </a>
</div>

<div class="panel table-panel">
    <?php if (empty($espacios)): ?>
        <div class="empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay espacios fÃ­sicos registrados</h3>
            <p>Comienza registrando aulas, salas o laboratorios para habilitar la asignaciÃ³n de horarios.</p>
            <a href="?ruta=admin/espacios/espacios/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primer Espacio
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 120px;">CÃ³digo</th>
                    <th>Nombre del Espacio</th>
                    <th>UbicaciÃ³n</th>
                    <th>Tipo</th>
                    <th style="width: 110px;">Capacidad</th>
                    <th style="width: 140px;">Estado</th>
                    <th style="width: 250px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($espacios as $espacio): ?>
                    <tr>
                        <td>
                            <strong style="font-family: monospace; font-size: 13.5px; background: #f1f3f5; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($espacio['codigo']) ?>
                            </strong>
                        </td>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($espacio['nombre']) ?></strong>
                        </td>
                        <td>
                            <span style="color: var(--color-texto-principal); font-size: 13.5px;">
                                <?= htmlspecialchars($espacio['sede_nombre'] ?? '') ?> &bull; 
                                <span style="color: var(--color-texto-secundario);"><?= htmlspecialchars($espacio['bloque_nombre'] ?? '') ?></span>
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 12.5px; font-weight: 500; background: #f8f9fa; padding: 3px 8px; border-radius: 6px; border: 1px solid var(--color-borde);">
                                <?= htmlspecialchars($espacio['tipo']) ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($espacio['capacidad']) ?></strong> <span style="color: var(--color-texto-secundario); font-size: 12px;">est.</span>
                        </td>
                        <td>
                            <span class="badge <?= strtolower($espacio['estado']) ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?= htmlspecialchars($espacio['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('shared/botones_accion', [
                                'ruta_editar' => '?ruta=admin/espacios/espacios/editar&id=' . (int)$espacio['id_espacio'],
                                'ruta_desactivar' => '?ruta=admin/espacios/espacios/cambiar_estado&id=' . (int)$espacio['id_espacio'] . '&estado=' . ($espacio['estado'] === 'INACTIVO' ? 'DISPONIBLE' : 'INACTIVO'),
                                'activo' => $espacio['estado'] !== 'INACTIVO',
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $this->render('shared/footer'); ?>

