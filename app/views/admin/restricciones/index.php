<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Restricciones']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'restricciones']);
?>
<div class="header-acciones"><div><h1>Restricciones</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Reglas de disponibilidad para docentes, espacios y franjas.</p></div><a class="btn-primario" href="?ruta=admin/academico/restricciones/crear"><i class="fa-solid fa-plus"></i> Nueva Restriccion</a></div>
<div class="panel"><table class="table"><thead><tr><th>Tipo</th><th>Periodo</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Horario</th><th>Descripcion</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>
<?php if (empty($restricciones)): ?><tr><td colspan="9" style="text-align:center;">No hay restricciones registradas.</td></tr><?php endif; ?>
<?php foreach ($restricciones as $r): ?><tr>
<td><span class="code-pill"><?= htmlspecialchars($r['tipo']) ?></span></td><td><?= htmlspecialchars($r['periodo_codigo'] ?? 'General') ?></td><td><?= htmlspecialchars($r['docente_nombre'] ?? '-') ?></td><td><?= htmlspecialchars($r['espacio_nombre'] ?? '-') ?></td><td><?= htmlspecialchars($r['dia_semana'] ?? '-') ?></td><td><?= htmlspecialchars(($r['hora_inicio'] ?? '') . (($r['hora_fin'] ?? '') ? ' - ' . $r['hora_fin'] : '')) ?></td><td><?= htmlspecialchars($r['descripcion'] ?? '') ?></td><td><span class="badge <?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
<td style="text-align:right;"><?php $this->render('shared/botones_accion', ['ruta_editar' => '?ruta=admin/academico/restricciones/editar&id=' . (int)$r['id_restriccion'], 'ruta_desactivar' => '?ruta=admin/academico/restricciones/desactivar&id=' . (int)$r['id_restriccion'], 'activo' => $r['estado'] === 'ACTIVA']); ?></td>
</tr><?php endforeach; ?>
</tbody></table></div><?php $this->render('shared/footer'); ?>
