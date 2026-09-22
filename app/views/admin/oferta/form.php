<?php $this->render('shared/header', ['titulo' => $titulo ?? 'Oferta']); $this->render('shared/sidebar_admin'); ?>
<div class="header-acciones"><h1>Cargar oferta academica</h1><a class="btn-demo" href="?ruta=admin/academico/oferta">Volver</a></div>
<div class="panel">
    <form method="POST" action="?ruta=admin/academico/oferta/guardar" class="responsive-form-grid">
        <div class="form-group"><label>Periodo</label><select class="form-control" name="id_periodo" required><?php foreach ($periodos as $p): ?><option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$periodo_id === (int)$p['id_periodo']) ? 'selected' : '' ?>><?= htmlspecialchars($p['codigo']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Programa</label><select class="form-control" name="id_programa" required><?php foreach ($programas as $p): ?><option value="<?= (int)$p['id_programa'] ?>" <?= ((int)$programa_id === (int)$p['id_programa']) ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Asignatura</label><select class="form-control" name="id_asignatura" required><?php foreach ($asignaturas as $a): ?><option value="<?= (int)$a['id_asignatura'] ?>"><?= htmlspecialchars($a['codigo'] . ' - ' . $a['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group" style="grid-column:1/-1;"><button class="btn-primario">Agregar a oferta</button></div>
    </form>
</div>
<?php $this->render('shared/footer'); ?>
