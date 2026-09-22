<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Asignaturas']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'asignaturas']);
?>
<div class="header-acciones"><div><h1>Asignaturas</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Catalogo curricular y asociacion con programas.</p></div><a class="btn-primario" href="?ruta=admin/academico/asignaturas/crear"><i class="fa-solid fa-plus"></i> Nueva Asignatura</a></div>
<div class="panel table-panel">
<form method="GET" class="filter-grid" style="margin-bottom:16px;">
    <input type="hidden" name="ruta" value="admin/academico/asignaturas">
    <div class="form-group">
        <label>Area academica</label>
        <select class="form-control" name="area_id">
            <option value="">Todas</option>
            <?php foreach (($areas ?? []) as $area): ?>
                <option value="<?= (int)$area['id_area_academica'] ?>" <?= ((int)($areaFiltro ?? 0) === (int)$area['id_area_academica']) ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><button class="btn-primario">Filtrar</button> <a class="btn-demo" href="?ruta=admin/academico/asignaturas">Limpiar</a></div>
</form>
<table class="table"><thead><tr><th>Codigo</th><th>Nombre</th><th>Creditos</th><th>Area Academica</th><th>Tipo</th><th>Programas</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>
<?php if (empty($asignaturas)): ?><tr><td colspan="8" style="text-align:center;">No hay asignaturas registradas.</td></tr><?php endif; ?>
<?php foreach ($asignaturas as $a): ?><tr><td><span class="code-pill"><?= htmlspecialchars($a['codigo']) ?></span></td><td><strong><?= htmlspecialchars($a['nombre']) ?></strong></td><td><?= (int)$a['creditos'] ?></td><td><?= htmlspecialchars($a['area_nombre'] ?? 'Sin area') ?></td><td><?= htmlspecialchars($a['tipo'] ?? '') ?></td><td><?= htmlspecialchars($a['programas'] ?? 'Sin programas') ?></td><td><span class="badge <?= strtolower($a['estado']) ?>"><?= htmlspecialchars($a['estado']) ?></span></td><td style="text-align:right;"><?php $this->render('shared/botones_accion', ['ruta_editar' => '?ruta=admin/academico/asignaturas/editar&id=' . (int)$a['id_asignatura'], 'ruta_desactivar' => '?ruta=admin/academico/asignaturas/desactivar&id=' . (int)$a['id_asignatura'], 'activo' => $a['estado'] === 'ACTIVO']); ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php $this->render('shared/footer'); ?>
