<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Horarios']);
$this->render('shared/sidebar_admin');
?>

<div class="header-acciones">
    <h1>Consulta General de Horarios</h1>
</div>

<div class="panel">
    <form action="" method="GET" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="ruta" value="admin/horarios">
        
        <div style="flex: 1; min-width: 200px;">
            <label for="programa" style="display:block; margin-bottom:5px; font-size:13px; font-weight:600;">Programa</label>
            <input type="text" name="programa" id="programa" class="form-control" value="<?= htmlspecialchars($filtros['programa']) ?>" placeholder="Buscar programa...">
        </div>
        
        <div style="flex: 1; min-width: 200px;">
            <label for="docente" style="display:block; margin-bottom:5px; font-size:13px; font-weight:600;">Docente</label>
            <input type="text" name="docente" id="docente" class="form-control" value="<?= htmlspecialchars($filtros['docente']) ?>" placeholder="Buscar docente...">
        </div>
        
        <div style="flex: 1; min-width: 150px;">
            <label for="espacio" style="display:block; margin-bottom:5px; font-size:13px; font-weight:600;">Espacio</label>
            <input type="text" name="espacio" id="espacio" class="form-control" value="<?= htmlspecialchars($filtros['espacio']) ?>" placeholder="Buscar espacio...">
        </div>
        
        <div style="flex: 1; min-width: 150px;">
            <label for="estado" style="display:block; margin-bottom:5px; font-size:13px; font-weight:600;">Estado</label>
            <select name="estado" id="estado" class="form-control">
                <option value="">Todos</option>
                <option value="BORRADOR" <?= $filtros['estado'] == 'BORRADOR' ? 'selected' : '' ?>>Borrador</option>
                <option value="PROPUESTA" <?= $filtros['estado'] == 'PROPUESTA' ? 'selected' : '' ?>>Propuesta</option>
                <option value="CONFIRMADO" <?= $filtros['estado'] == 'CONFIRMADO' ? 'selected' : '' ?>>Confirmado</option>
                <option value="CERRADO" <?= $filtros['estado'] == 'CERRADO' ? 'selected' : '' ?>>Cerrado</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="btn-primario" style="height: 40px; padding: 0 20px;">Filtrar</button>
            <a href="?ruta=admin/horarios" class="btn-demo" style="height: 40px; line-height: 40px; padding: 0 20px; display:inline-block; text-align:center;">Limpiar</a>
        </div>
    </form>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Programa</th>
                    <th>Asignatura</th>
                    <th>Docente</th>
                    <th>Espacio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($horarios)): ?>
                    <tr><td colspan="7" style="text-align:center; padding:20px;">No se encontraron horarios con los filtros seleccionados.</td></tr>
                <?php else: ?>
                    <?php foreach ($horarios as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['dia_semana']) ?></td>
                            <td><?= date('H:i', strtotime($h['hora_inicio'])) ?> - <?= date('H:i', strtotime($h['hora_fin'])) ?></td>
                            <td><?= htmlspecialchars($h['programa']) ?></td>
                            <td><?= htmlspecialchars($h['asignatura']) ?></td>
                            <td><?= htmlspecialchars($h['docente']) ?></td>
                            <td><?= htmlspecialchars($h['sede'] . ' - ' . $h['bloque'] . ' ' . $h['espacio']) ?></td>
                            <td><span class="badge <?= strtolower($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->render('shared/footer'); ?>
