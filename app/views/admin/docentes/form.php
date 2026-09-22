<?php $this->render('shared/header', ['titulo' => $titulo ?? 'Docente']); $this->render('shared/sidebar_admin'); $esEdicion = !empty($docente['id_docente']); ?>
<div class="header-acciones"><h1><?= htmlspecialchars($titulo) ?></h1><a class="btn-demo" href="?ruta=admin/academico/docentes">Volver</a></div>
<div class="panel">
<form method="POST" action="?ruta=admin/academico/docentes/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" style="display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:16px;">
<?php if ($esEdicion): ?><input type="hidden" name="id_docente" value="<?= (int)$docente['id_docente'] ?>"><?php endif; ?>
<div class="form-group"><label>Codigo</label><input class="form-control" name="codigo" required value="<?= htmlspecialchars($docente['codigo'] ?? '') ?>"></div>
<div class="form-group"><label>Correo</label><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($docente['correo'] ?? '') ?>"></div>
<div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" required value="<?= htmlspecialchars($docente['nombre'] ?? '') ?>"></div>
<div class="form-group"><label>Apellido</label><input class="form-control" name="apellido" required value="<?= htmlspecialchars($docente['apellido'] ?? '') ?>"></div>
<div class="form-group"><label>Estado</label><select class="form-control" name="estado"><?php foreach (['ACTIVO','INACTIVO'] as $e): ?><option value="<?= $e ?>" <?= (($docente['estado'] ?? 'ACTIVO') === $e) ? 'selected' : '' ?>><?= $e ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Programas</label><select class="form-control" name="programas[]" multiple size="6"><?php foreach ($programas as $p): ?><option value="<?= (int)$p['id_programa'] ?>" <?= in_array((int)$p['id_programa'], $relaciones['programas'] ?? [], true) ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Asignaturas compatibles</label><select class="form-control" name="asignaturas[]" multiple size="8"><?php foreach ($asignaturas as $a): ?><option value="<?= (int)$a['id_asignatura'] ?>" <?= in_array((int)$a['id_asignatura'], $relaciones['asignaturas'] ?? [], true) ? 'selected' : '' ?>><?= htmlspecialchars($a['codigo'] . ' - ' . $a['nombre']) ?></option><?php endforeach; ?></select></div>
<div class="form-group" style="grid-column:1/-1;"><button class="btn-primario">Guardar docente</button></div>
</form></div><?php $this->render('shared/footer'); ?>
