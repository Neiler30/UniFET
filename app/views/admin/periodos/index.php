<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Periodos Académicos']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'periodos']);
?>

<div class="header-acciones">
    <div>
        <h1>Periodos Académicos</h1>
        <p>Configuración y gestión de ciclos de planeación semestral y vigencias institucionales.</p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/periodos/crear">
        <i class="fa-solid fa-plus"></i> Nuevo Periodo
    </a>
</div>

<div class="panel table-panel">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 140px;">Código</th>
                    <th>Denominación del Periodo</th>
                    <th style="width: 220px;">Vigencia (Fechas)</th>
                    <th style="width: 130px; text-align: center;">Estado</th>
                    <th style="width: 100px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($periodos)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px 20px;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        <span style="color: var(--color-texto-secundario); font-size: 13.5px;">No hay periodos académicos registrados.</span>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($periodos as $p): ?>
                <tr>
                    <td><span class="code-pill"><?= htmlspecialchars($p['codigo']) ?></span></td>
                    <td><strong><?= htmlspecialchars($p['nombre'] ?? 'Sin nombre') ?></strong></td>
                    <td style="color: var(--color-texto-secundario); font-size: 13px;">
                        <i class="fa-regular fa-calendar" style="font-size: 11px; margin-right: 4px;"></i>
                        <?= htmlspecialchars(($p['fecha_inicio'] ?? '') . ' &mdash; ' . ($p['fecha_fin'] ?? '')) ?>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge <?= strtolower($p['estado']) ?>">
                            <?= htmlspecialchars($p['estado']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a class="btn-accion btn-accion-editar" title="Editar periodo" href="?ruta=admin/academico/periodos/editar&id=<?= (int)$p['id_periodo'] ?>">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
