<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Mapeo de Columnas']);
$this->render('elementos_compartidos/sidebar_admin');
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--color-oscuro);">Mapear Columnas</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Revisa cómo se asociarán las columnas de tu archivo Excel con los campos internos de <strong><?= htmlspecialchars($catalogo['nombre_amigable']) ?></strong>.
        </p>
    </div>
</div>

<div class="panel">
    <form action="?ruta=admin/importar/previsualizar" method="POST">
        <input type="hidden" name="tipo_entidad" value="<?= htmlspecialchars($tipo) ?>">
        <input type="hidden" name="modo_duplicados" value="<?= htmlspecialchars($modo_duplicados ?? 'omitir') ?>">

        <table class="tabla-datos" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: var(--color-oscuro);">Columna en el Excel</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: var(--color-oscuro);">Campo en UniFET</th>
                    <th style="padding: 12px; text-align: center; font-size: 13px; color: var(--color-oscuro);">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapeo['mapping'] as $col => $info): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; font-weight: 600; font-size: 13px;">
                        <?= htmlspecialchars($info['original']) ?>
                        <br>
                        <small style="color: #94a3b8; font-weight: normal;">Columna <?= htmlspecialchars($col) ?></small>
                    </td>
                    <td style="padding: 12px;">
                        <select name="mapeo[<?= htmlspecialchars($col) ?>]" class="form-control" style="width: 100%; padding: 6px; font-size: 13px;">
                            <option value="">-- Ignorar columna --</option>
                            <?php foreach ($catalogo['campos'] as $key => $campoConfig): ?>
                                <option value="<?= htmlspecialchars($key) ?>" <?= ($info['mapped_to'] === $key) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($campoConfig['label']) ?> <?= $campoConfig['requerido'] ? '(*)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <?php if ($info['status'] === 'DETECTED'): ?>
                            <span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                <i class="fa-solid fa-check"></i> Detectado
                            </span>
                        <?php else: ?>
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                <i class="fa-solid fa-triangle-exclamation"></i> Revisar
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <p style="font-size: 13px; color: var(--color-texto-secundario); margin: 0;">
                <i class="fa-solid fa-circle-info" style="color: var(--color-primario);"></i> Las columnas marcadas como "Ignorar" no serán importadas. Asegúrate de mapear todos los campos obligatorios (*).
            </p>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="?ruta=admin/importar" class="btn-demo" style="background: #fff;">Cancelar</a>
            <button type="submit" class="btn-primario">
                Continuar a Vista Previa <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
