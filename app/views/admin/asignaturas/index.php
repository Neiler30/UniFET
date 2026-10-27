<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Asignaturas']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'asignaturas']);
?>
<div class="header-acciones">
    <div>
        <h1>Asignaturas</h1>
        <p style="color:var(--color-texto-secundario);font-size:13px;">Catalogo curricular y asociacion con programas.</p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/asignaturas/crear"><i class="fa-solid fa-plus"></i> Nueva Asignatura</a>
</div>
<div class="panel table-panel">

    <!-- Filtros -->
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">

            <!-- Filtro por Área Académica (server-side) -->
            <form method="GET" style="display:flex;align-items:flex-end;gap:8px;">
                <input type="hidden" name="ruta" value="admin/academico/asignaturas">
                <div class="form-group" style="margin:0;">
                    <label style="font-size:13px;font-weight:600;color:var(--color-oscuro);display:block;margin-bottom:4px;">
                        <i class="fa-solid fa-shapes"></i> Área académica
                    </label>
                    <select class="form-control" name="area_id" style="width:220px;">
                        <option value="">Todas las áreas</option>
                        <?php foreach (($areas ?? []) as $area): ?>
                            <option value="<?= (int)$area['id_area_academica'] ?>" <?= ((int)($areaFiltro ?? 0) === (int)$area['id_area_academica']) ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-primario" style="height:38px;">Filtrar</button>
                <?php if (!empty($areaFiltro)): ?>
                    <a class="btn-demo" href="?ruta=admin/academico/asignaturas" style="height:38px;display:inline-flex;align-items:center;">Limpiar</a>
                <?php endif; ?>
            </form>

            <!-- Filtro por Programa (client-side JS) -->
            <div class="form-group" style="margin:0;">
                <label for="filtro-programa" style="font-size:13px;font-weight:600;color:var(--color-oscuro);display:block;margin-bottom:4px;">
                    <i class="fa-solid fa-filter"></i> Filtrar por Programa
                </label>
                <select id="filtro-programa" class="form-control" style="width:240px;">
                    <option value="">Todos los programas</option>
                    <?php
                    $programas_unicos = [];
                    foreach ($programas ?? [] as $p) {
                        if (!empty($p['nombre']) && !in_array($p['nombre'], $programas_unicos)) {
                            $programas_unicos[] = $p['nombre'];
                        }
                    }
                    sort($programas_unicos);
                    foreach ($programas_unicos as $pNombre): ?>
                        <option value="<?= htmlspecialchars($pNombre) ?>"><?= htmlspecialchars($pNombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Contador -->
        <div style="font-size:13px;color:var(--color-texto-secundario);white-space:nowrap;">
            Total: <strong id="contador-asignaturas"><?= count($asignaturas) ?></strong>
            <?php if (count($asignaturas) !== count($asignaturas)): ?><!-- placeholder --><?php endif; ?>
            <?php if (!empty($areaFiltro)): ?>
                <span style="color:var(--color-primario);margin-left:4px;">(filtrado)</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla -->
    <table class="table" id="tabla-asignaturas">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Créditos</th>
                <th>Área Académica</th>
                <th>Programas</th>
                <th>Estado</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($asignaturas)): ?>
            <tr><td colspan="7" style="text-align:center;">No hay asignaturas registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($asignaturas as $a): ?>
            <tr class="fila-asignatura" data-programas="<?= htmlspecialchars($a['programas'] ?? '') ?>">
                <td><span class="code-pill"><?= htmlspecialchars($a['codigo']) ?></span></td>
                <td><strong><?= htmlspecialchars($a['nombre']) ?></strong></td>
                <td>
                    <?php if ((int)$a['creditos'] === 0): ?>
                        <span style="display:inline-block; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #fef9c3; color: #854d0e;" title="Proyecto de Grado">
                            00
                        </span>
                    <?php else: ?>
                        <?= (int)$a['creditos'] ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($a['area_nombre'])): ?>
                        <span style="display:inline-block; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0369a1; white-space: nowrap;">
                            <?= htmlspecialchars($a['area_nombre']) ?>
                        </span>
                    <?php else: ?>
                        <span style="color:var(--color-texto-secundario); font-size:12px;">Sin área</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($a['programas_info'])): ?>
                        <div style="display:flex; flex-direction:column; gap:4px; max-width:280px;">
                            <?php 
                            $progs = explode(' || ', $a['programas_info']);
                            foreach ($progs as $pItem): 
                            ?>
                                <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 8px; border-radius:6px; color:#1e293b;">
                                    <i class="fa-solid fa-graduation-cap" style="color:var(--color-primario); font-size:10px;"></i>
                                    <?= htmlspecialchars($pItem) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span style="color:var(--color-texto-secundario); font-size:12px; font-style:italic;">Sin programas</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge <?= strtolower($a['estado']) ?>"><?= htmlspecialchars($a['estado']) ?></span></td>
                <td style="text-align:right;"><?php $this->render('elementos_compartidos/botones_accion', ['ruta_editar' => '?ruta=admin/academico/asignaturas/editar&id=' . (int)$a['id_asignatura'], 'ruta_desactivar' => '?ruta=admin/academico/asignaturas/desactivar&id=' . (int)$a['id_asignatura'], 'activo' => $a['estado'] === 'ACTIVO']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('filtro-programa')?.addEventListener('change', function () {
    const selected = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.fila-asignatura');
    let visible = 0;
    rows.forEach(row => {
        const programas = (row.getAttribute('data-programas') || '').toLowerCase();
        const mostrar = selected === '' || programas.includes(selected);
        row.style.display = mostrar ? '' : 'none';
        if (mostrar) visible++;
    });
    const contador = document.getElementById('contador-asignaturas');
    if (contador) contador.textContent = visible;
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
