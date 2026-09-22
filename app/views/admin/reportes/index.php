<?php $this->render('shared/header', ['titulo' => $titulo ?? 'Reportes']); $this->render('shared/sidebar_admin'); ?>
<div class="header-acciones"><div><h1>Reportes academicos</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Vista previa HTML con datos reales del sistema.</p></div></div>
<div class="panel">
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
        <?php foreach (['programa' => 'Horario por programa', 'docente' => 'Horario por docente', 'espacio' => 'Horario por espacio', 'auditorias' => 'Resultados de auditoria'] as $k => $label): ?>
            <a class="module-tab <?= $tipo === $k ? 'active' : '' ?>" style="border:1px solid var(--color-borde);border-radius:8px;padding:10px 12px;<?= $tipo === $k ? 'background:var(--color-primario);color:#fff;' : 'background:#fff;' ?>" href="?ruta=admin/reportes&tipo=<?= $k ?>"><?= htmlspecialchars($label) ?></a>
        <?php endforeach; ?>
    </div>
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:16px;">
        <input type="hidden" name="ruta" value="admin/reportes"><input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
        <?php if ($tipo === 'auditorias'): ?>
            <div class="form-group"><label>Resultado</label><select class="form-control" name="resultado"><option value="">Todos</option><?php foreach (['PENDIENTE','VERIFICADA','NO_REALIZADA'] as $r): ?><option value="<?= $r ?>" <?= $resultado === $r ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select></div>
        <?php else: ?>
            <div class="form-group"><label>Filtro</label><input class="form-control" name="filtro" value="<?= htmlspecialchars($filtro) ?>" placeholder="Buscar por <?= htmlspecialchars($tipo) ?>"></div>
        <?php endif; ?>
        <button class="btn-primario">Generar vista previa</button>
        <button class="btn-demo" type="button" title="TODO exportar PDF/Excel"><i class="fa-solid fa-file-export"></i> Exportar</button>
    </form>
    <div style="overflow-x:auto;"><table class="table">
        <?php if ($tipo === 'auditorias'): ?>
            <thead><tr><th>Fecha</th><th>Programa</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Resultado</th></tr></thead><tbody>
            <?php if (empty($datos)): ?><tr><td colspan="6" style="text-align:center;">Sin resultados.</td></tr><?php endif; ?>
            <?php foreach ($datos as $d): ?><tr><td><?= htmlspecialchars($d['fecha_auditoria'] ?? '') ?></td><td><?= htmlspecialchars($d['programa'] ?? '') ?></td><td><?= htmlspecialchars($d['asignatura'] ?? '') ?></td><td><?= htmlspecialchars($d['docente'] ?? '') ?></td><td><?= htmlspecialchars(($d['sede'] ?? '') . ' / ' . ($d['espacio'] ?? '')) ?></td><td><span class="badge <?= strtolower($d['resultado'] ?? '') ?>"><?= htmlspecialchars($d['resultado'] ?? '') ?></span></td></tr><?php endforeach; ?>
            </tbody>
        <?php else: ?>
            <thead><tr><th>Periodo</th><th>Programa</th><th>Asignatura</th><th>Docente</th><th>Espacio</th><th>Dia</th><th>Hora</th><th>Estado</th></tr></thead><tbody>
            <?php if (empty($datos)): ?><tr><td colspan="8" style="text-align:center;">Sin horarios para este reporte.</td></tr><?php endif; ?>
            <?php foreach ($datos as $d): ?><tr><td><?= htmlspecialchars($d['periodo'] ?? '') ?></td><td><?= htmlspecialchars($d['programa'] ?? '') ?></td><td><?= htmlspecialchars($d['asignatura'] ?? '') ?></td><td><?= htmlspecialchars($d['docente'] ?? '') ?></td><td><?= htmlspecialchars(($d['sede'] ?? '') . ' / ' . ($d['bloque'] ?? '') . ' / ' . ($d['espacio'] ?? '')) ?></td><td><?= htmlspecialchars($d['dia_semana'] ?? '') ?></td><td><?= htmlspecialchars(($d['hora_inicio'] ?? '') . ' - ' . ($d['hora_fin'] ?? '')) ?></td><td><span class="badge <?= strtolower($d['estado'] ?? '') ?>"><?= htmlspecialchars($d['estado'] ?? '') ?></span></td></tr><?php endforeach; ?>
            </tbody>
        <?php endif; ?>
    </table></div>
</div>
<?php $this->render('shared/footer'); ?>
