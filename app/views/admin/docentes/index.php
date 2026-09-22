<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Docentes']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'docentes']);
?>
<div class="header-acciones"><div><h1>Docentes</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Perfiles academicos, programas y asignaturas compatibles.</p></div><a class="btn-primario" href="?ruta=admin/academico/docentes/crear"><i class="fa-solid fa-plus"></i> Nuevo Docente</a></div>
<div class="panel"><table class="table"><thead><tr><th>Codigo</th><th>Docente</th><th>Correo</th><th>Programas</th><th>Asignaturas</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>
<?php if (empty($docentes)): ?><tr><td colspan="7" style="text-align:center;">No hay docentes registrados.</td></tr><?php endif; ?>
<?php foreach ($docentes as $d): ?><tr>
<td><span class="code-pill"><?= htmlspecialchars($d['codigo']) ?></span></td><td><strong><?= htmlspecialchars($d['apellido'] . ' ' . $d['nombre']) ?></strong></td><td><?= htmlspecialchars($d['correo'] ?? '') ?></td><td><?= htmlspecialchars($d['programas'] ?? 'Sin programas') ?></td><td><?= htmlspecialchars($d['asignaturas'] ?? 'Sin asignaturas') ?></td><td><span class="badge <?= strtolower($d['estado']) ?>"><?= htmlspecialchars($d['estado']) ?></span></td>
<td style="text-align:right;"><?php $this->render('shared/botones_accion', ['ruta_editar' => '?ruta=admin/academico/docentes/editar&id=' . (int)$d['id_docente'], 'ruta_desactivar' => '?ruta=admin/academico/docentes/desactivar&id=' . (int)$d['id_docente'], 'activo' => $d['estado'] === 'ACTIVO']); ?></td>
</tr><?php endforeach; ?>
</tbody></table></div>
<?php $this->render('shared/footer'); ?>
