<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Periodo']);
$this->render('shared/sidebar_admin');
$esEdicion = !empty($periodo['id_periodo']);
?>
<div class="header-acciones"><h1><?= htmlspecialchars($titulo) ?></h1><a class="btn-demo" href="?ruta=admin/academico/periodos">Volver</a></div>
<div class="panel">
    <?php if (!empty($error)): ?><div class="badge no_realizada" style="margin-bottom:14px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" action="?ruta=admin/academico/periodos/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" style="display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:16px;">
        <?php if ($esEdicion): ?><input type="hidden" name="id_periodo" value="<?= (int)$periodo['id_periodo'] ?>"><?php endif; ?>
        <div class="form-group"><label>Codigo</label><input class="form-control" name="codigo" required value="<?= htmlspecialchars($periodo['codigo'] ?? '') ?>"></div>
        <div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" required value="<?= htmlspecialchars($periodo['nombre'] ?? '') ?>"></div>
        <div class="form-group"><label>Fecha inicio</label><input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($periodo['fecha_inicio'] ?? '') ?>"></div>
        <div class="form-group"><label>Fecha fin</label><input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($periodo['fecha_fin'] ?? '') ?>"></div>
        <?php if (!$esEdicion): ?>
        <div class="form-group"><label>Reutilizar datos de otro periodo</label><select class="form-control" name="id_periodo_base"><option value="">Crear vacio</option><?php foreach (($periodos ?? []) as $p): ?><option value="<?= (int)$p['id_periodo'] ?>"><?= htmlspecialchars($p['codigo'] . ' - ' . ($p['nombre'] ?? '')) ?></option><?php endforeach; ?></select></div>
        <?php endif; ?>
        <div class="form-group"><label>Estado</label><select class="form-control" name="estado"><?php foreach (['PLANIFICACION','ACTIVO','CERRADO','HISTORICO'] as $e): ?><option value="<?= $e ?>" <?= (($periodo['estado'] ?? 'PLANIFICACION') === $e) ? 'selected' : '' ?>><?= $e ?></option><?php endforeach; ?></select></div>
        <div class="form-group" style="grid-column:1/-1;"><button class="btn-primario">Guardar periodo</button></div>
    </form>
</div>
<?php $this->render('shared/footer'); ?>
