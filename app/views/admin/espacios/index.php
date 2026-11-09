<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Espacios Físicos — UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
?>

<!-- Banner Institucional UniFET -->
<div class="institucion-hero-banner" style="background: linear-gradient(135deg, var(--color-primario, #3B0D8F) 0%, #24114f 65%, #180936 100%); color: #fff; border-radius: 14px; padding: 22px 28px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px; position: relative; overflow: hidden; box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);">
    <div style="position: relative; z-index: 2;">
        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255, 255, 255, 0.12); color: #fff; padding: 3px 10px; border-radius: 99px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
            <i class="fa-solid fa-door-open"></i> Infraestructura Universitaria UniFET
        </span>
        <h1 style="font-size: 21px; font-weight: 700; margin: 0 0 5px; color: #fff; display: flex; align-items: center; gap: 10px;">Gestión de Espacios Físicos</h1>
        <p style="color: rgba(255, 255, 255, 0.82); font-size: 13.5px; margin: 0; line-height: 1.4;">Aulas, laboratorios, auditorios y salas disponibles para docencia, investigación y eventos académicos.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 10px; position: relative; z-index: 2; flex-shrink: 0;">
        <div style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #fff; backdrop-filter: blur(4px);">
            <i class="fa-solid fa-layer-group" style="color: #6ee7b7;"></i>
            <span>Total: <strong><?= count($espacios) ?> Espacios</strong></span>
        </div>
    </div>
</div>

<?php
$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => true],
];
$this->render('elementos_compartidos/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">Gestión de Espacios Físicos</h1>
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
            <h3>No hay espacios físicos registrados</h3>
            <p>Comienza registrando aulas, salas o laboratorios para habilitar la asignación de horarios.</p>
            <a href="?ruta=admin/espacios/espacios/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primer Espacio
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 120px;">Código</th>
                    <th>Nombre del Espacio</th>
                    <th>Ubicación</th>
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
                            <?php $this->render('elementos_compartidos/botones_accion', [
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

<?php $this->render('elementos_compartidos/footer'); ?>

