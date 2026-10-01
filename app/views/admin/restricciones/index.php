<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Restricciones']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'restricciones']);
?>

<div class="header-acciones">
    <div>
        <h1>Restricciones de Horarios</h1>
        <p>Reglas de disponibilidad horaria para docentes, espacios físicos y franjas institucionales.</p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/restricciones/crear">
        <i class="fa-solid fa-plus"></i> Nueva Restricción
    </a>
</div>

<div class="panel table-panel">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 130px;">Tipo</th>
                    <th>Periodo</th>
                    <th>Docente</th>
                    <th>Espacio</th>
                    <th>Día</th>
                    <th>Franja Horaria</th>
                    <th>Descripción</th>
                    <th style="width: 110px; text-align: center;">Estado</th>
                    <th style="width: 100px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($restricciones)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px 20px;">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                            <span style="color: var(--color-texto-secundario); font-size: 13.5px;">No hay restricciones registradas para este ciclo.</span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($restricciones as $r): ?>
                    <tr>
                        <td><span class="code-pill"><?= htmlspecialchars($r['tipo']) ?></span></td>
                        <td><strong><?= htmlspecialchars($r['periodo_codigo'] ?? 'General') ?></strong></td>
                        <td><?= htmlspecialchars($r['docente_nombre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['espacio_nombre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['dia_semana'] ?? '—') ?></td>
                        <td style="color: var(--color-texto-secundario); font-size: 13px;">
                            <i class="fa-regular fa-clock" style="font-size: 11px; margin-right: 3px;"></i>
                            <?= htmlspecialchars(($r['hora_inicio'] ?? '') . (($r['hora_fin'] ?? '') ? ' - ' . $r['hora_fin'] : '')) ?>
                        </td>
                        <td style="font-size: 12.5px;"><?= htmlspecialchars($r['descripcion'] ?? '') ?></td>
                        <td style="text-align: center;">
                            <span class="badge <?= strtolower($r['estado']) ?>">
                                <?= htmlspecialchars($r['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php $this->render('elementos_compartidos/botones_accion', [
                                'ruta_editar' => '?ruta=admin/academico/restricciones/editar&id=' . (int)$r['id_restriccion'],
                                'ruta_desactivar' => '?ruta=admin/academico/restricciones/desactivar&id=' . (int)$r['id_restriccion'],
                                'activo' => $r['estado'] === 'ACTIVA'
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
