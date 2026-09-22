<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Facultades - UniFET']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => true],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">Gestión de Facultades</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Unidades académicas principales que coordinan programas y departamentos.
        </p>
    </div>
    <a href="?ruta=admin/institucion/facultades/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nueva Facultad
    </a>
</div>

<div class="panel table-panel">
    <?php if (empty($facultades)): ?>
        <div class="empty-state">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay facultades registradas</h3>
            <p>Comienza creando la primera facultad académica para estructurar programas y asignaturas de la universidad.</p>
            <a href="?ruta=admin/institucion/facultades/crear" class="btn-primario">
                <i class="fa-solid fa-plus"></i> Crear Primera Facultad
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 140px;">Código</th>
                    <th>Nombre de la Facultad</th>
                    <th style="width: 130px;">Estado</th>
                    <th style="width: 180px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facultades as $fac): ?>
                    <tr>
                        <td>
                            <strong style="font-family: monospace; font-size: 13.5px; background: #f1f3f5; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($fac['codigo']) ?>
                            </strong>
                        </td>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($fac['nombre']) ?></strong>
                        </td>
                        <td>
                            <span class="badge <?= strtolower($fac['estado']) ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?= htmlspecialchars($fac['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('shared/botones_accion', [
                                'ruta_editar' => '?ruta=admin/institucion/facultades/editar&id=' . (int)$fac['id_facultad'],
                                'ruta_desactivar' => '?ruta=admin/institucion/facultades/desactivar&id=' . (int)$fac['id_facultad'],
                                'activo' => $fac['estado'] === 'ACTIVO',
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $this->render('shared/footer'); ?>
