<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Oferta Académica']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'oferta']);
?>
<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">Oferta Académica</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Asignaturas ofertadas por programa y periodo académico con su ubicación curricular.
        </p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/oferta/crear&periodo_id=<?= (int)$periodo_seleccionado ?>&programa_id=<?= (int)($programa_seleccionado ?? 0) ?>">
        <i class="fa-solid fa-plus"></i> Agregar Oferta
    </a>
</div>

<div class="panel">
    <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; margin-bottom:16px;">
        <input type="hidden" name="ruta" value="admin/academico/oferta">

        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; font-weight: 600; color: var(--color-oscuro); display: block; margin-bottom: 4px;">
                <i class="fa-solid fa-calendar-days" style="color: var(--color-primario);"></i> Periodo
            </label>
            <select class="form-control" name="periodo_id">
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$periodo_seleccionado === (int)$p['id_periodo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['codigo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; font-weight: 600; color: var(--color-oscuro); display: block; margin-bottom: 4px;">
                <i class="fa-solid fa-graduation-cap" style="color: var(--color-primario);"></i> Programa
            </label>
            <select class="form-control" name="programa_id">
                <option value="">Todos los programas</option>
                <?php foreach ($programas as $p): ?>
                    <option value="<?= (int)$p['id_programa'] ?>" <?= ((int)($programa_seleccionado ?? 0) === (int)$p['id_programa']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn-primario" style="height: 38px;">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>
    </form>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Programa Académico</th>
                    <th>Asignatura</th>
                    <th style="width: 140px; text-align: center;">Semestre</th>
                    <th style="width: 90px; text-align: center;">Créditos</th>
                    <th style="width: 110px; text-align: center;">Estado</th>
                    <th style="width: 100px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ofertas)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 24px; color: var(--color-texto-secundario);">
                            No hay asignaturas ofertadas para los filtros seleccionados.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($ofertas as $o): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--color-oscuro);"><?= htmlspecialchars($o['programa_nombre']) ?></strong>
                        </td>
                        <td>
                            <span class="code-pill"><?= htmlspecialchars($o['asignatura_codigo']) ?></span>
                            <span style="font-weight: 600; margin-left: 6px;"><?= htmlspecialchars($o['asignatura_nombre']) ?></span>
                        </td>
                        <td style="text-align: center;">
                            <?php if (!empty($o['semestre'])): ?>
                                <span style="display: inline-block; padding: 2px 8px; background: #ede9fe; color: #4c1d95; font-size: 12px; font-weight: 700; border-radius: 12px;">
                                    Semestre <?= (int)$o['semestre'] ?>
                                </span>
                            <?php else: ?>
                                <span style="color: var(--color-texto-secundario); font-size: 12px; font-style: italic;">Sin definir</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><?= (int)$o['creditos'] ?></td>
                        <td style="text-align: center;">
                            <span class="badge <?= strtolower($o['estado']) ?>"><?= htmlspecialchars($o['estado']) ?></span>
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" action="?ruta=admin/academico/oferta/desactivar&id=<?= (int)$o['id_oferta'] ?>&periodo_id=<?= (int)$periodo_seleccionado ?>&programa_id=<?= (int)($programa_seleccionado ?? 0) ?>" onsubmit="return confirmarDesactivacion(event,this);">
                                <button class="btn-accion btn-accion-desactivar" title="Desactivar de la oferta">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->render('elementos_compartidos/footer'); ?>
