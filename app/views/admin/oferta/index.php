<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Oferta']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'oferta']);
?>
<div class="header-acciones">
    <div><h1>Oferta academica</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Asignaturas ofertadas por programa y periodo.</p></div>
    <a class="btn-primario" href="?ruta=admin/academico/oferta/crear&periodo_id=<?= (int)$periodo_seleccionado ?>&programa_id=<?= (int)($programa_seleccionado ?? 0) ?>"><i class="fa-solid fa-plus"></i> Agregar Oferta</a>
</div>
<div class="panel">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:16px;">
        <input type="hidden" name="ruta" value="admin/academico/oferta">
        <div class="form-group"><label>Periodo</label><select class="form-control" name="periodo_id"><?php foreach ($periodos as $p): ?><option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$periodo_seleccionado === (int)$p['id_periodo']) ? 'selected' : '' ?>><?= htmlspecialchars($p['codigo']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Programa</label><select class="form-control" name="programa_id"><option value="">Todos</option><?php foreach ($programas as $p): ?><option value="<?= (int)$p['id_programa'] ?>" <?= ((int)($programa_seleccionado ?? 0) === (int)$p['id_programa']) ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?></select></div>
        <button class="btn-primario">Filtrar</button>
    </form>
    <table class="table"><thead><tr><th>Programa</th><th>Asignatura</th><th>Creditos</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead><tbody>
    <?php if (empty($ofertas)): ?><tr><td colspan="5" style="text-align:center;">No hay oferta para los filtros seleccionados.</td></tr><?php endif; ?>
    <?php foreach ($ofertas as $o): ?><tr>
        <td><?= htmlspecialchars($o['programa_nombre']) ?></td><td><?= htmlspecialchars($o['asignatura_codigo'] . ' - ' . $o['asignatura_nombre']) ?></td><td><?= (int)$o['creditos'] ?></td><td><span class="badge <?= strtolower($o['estado']) ?>"><?= htmlspecialchars($o['estado']) ?></span></td>
        <td style="text-align:right;"><form method="POST" action="?ruta=admin/academico/oferta/desactivar&id=<?= (int)$o['id_oferta'] ?>&periodo_id=<?= (int)$periodo_seleccionado ?>" onsubmit="return confirmarDesactivacion(event,this);"><button class="btn-accion btn-accion-desactivar" title="Desactivar"><i class="fa-solid fa-ban"></i></button></form></td>
    </tr><?php endforeach; ?>
    </tbody></table>
</div>
<?php $this->render('shared/footer'); ?>
