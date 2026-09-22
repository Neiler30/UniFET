<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Mi Programa']);
$this->render('shared/sidebar_lider');
$tabs = [
    'resumen' => ['Resumen', 'fa-circle-info'],
    'docentes' => ['Docentes', 'fa-chalkboard-user'],
    'asignaturas' => ['Asignaturas', 'fa-book-open'],
    'asignaciones' => ['Asignaciones', 'fa-user-check']
];
?>
<style>
.module-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px}.module-tab.active{background:var(--color-primario);border-color:var(--color-primario);color:#fff}.table-wrap{overflow-x:auto}.program-card{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}.info-tile{border:1px solid var(--color-borde);border-radius:8px;padding:16px;background:#fff}.info-tile span{font-size:12px;color:var(--color-texto-secundario);text-transform:uppercase}.info-tile strong{display:block;margin-top:6px;color:var(--color-oscuro)}.assignment-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(240px,100%),1fr));gap:14px;align-items:end;margin-bottom:18px}
</style>
<div class="panel">
    <h1 style="font-size:22px;margin-bottom:6px;">Mi Programa</h1>
    <p style="color:var(--color-texto-secundario);margin:0;">Informacion academica de solo lectura, filtrada por tu programa activo.</p>
</div>
<nav class="module-tabs">
    <?php foreach ($tabs as $id => $item): ?>
        <a class="module-tab <?= $tab === $id ? 'active' : '' ?>" href="?ruta=lider/programa&tab=<?= $id ?>"><i class="fa-solid <?= $item[1] ?>"></i><?= $item[0] ?></a>
    <?php endforeach; ?>
</nav>
<?php if (!$programaActivo): ?>
    <div class="panel">No tienes programas asignados.</div>
<?php elseif ($tab === 'resumen'): ?>
    <div class="panel program-card">
        <div class="info-tile"><span>Codigo</span><strong><?= htmlspecialchars($programaActivo['codigo']) ?></strong></div>
        <div class="info-tile"><span>Programa</span><strong><?= htmlspecialchars($programaActivo['nombre']) ?></strong></div>
        <div class="info-tile"><span>Facultad</span><strong><?= htmlspecialchars($programaActivo['facultad_nombre']) ?></strong></div>
        <div class="info-tile"><span>Estado</span><strong><?= htmlspecialchars($programaActivo['estado']) ?></strong></div>
    </div>
<?php elseif ($tab === 'docentes'): ?>
    <div class="panel table-wrap"><table class="table"><thead><tr><th>Codigo</th><th>Docente</th><th>Correo</th><th>Estado</th></tr></thead><tbody>
    <?php if (empty($docentes)): ?><tr><td colspan="4" style="text-align:center;padding:22px;">No hay docentes vinculados.</td></tr><?php endif; ?>
    <?php foreach ($docentes as $d): ?><tr><td><?= htmlspecialchars($d['codigo']) ?></td><td><?= htmlspecialchars($d['nombre'].' '.$d['apellido']) ?></td><td><?= htmlspecialchars($d['correo']) ?></td><td><span class="badge <?= strtolower($d['estado']) ?>"><?= htmlspecialchars($d['estado']) ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div>
<?php elseif ($tab === 'asignaturas'): ?>
    <div class="panel table-wrap"><table class="table"><thead><tr><th>Codigo</th><th>Asignatura</th><th>Area academica</th><th>Creditos</th><th>Estado</th></tr></thead><tbody>
    <?php if (empty($asignaturas)): ?><tr><td colspan="5" style="text-align:center;padding:22px;">No hay asignaturas vinculadas.</td></tr><?php endif; ?>
    <?php foreach ($asignaturas as $a): ?><tr><td><?= htmlspecialchars($a['codigo']) ?></td><td><?= htmlspecialchars($a['nombre']) ?></td><td><?= htmlspecialchars($a['area_nombre'] ?? 'Sin area') ?></td><td><?= (int)$a['creditos'] ?></td><td><span class="badge <?= strtolower($a['estado']) ?>"><?= htmlspecialchars($a['estado']) ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div>
<?php else: ?>
    <div class="panel">
        <h2 style="font-size:18px;margin-bottom:6px;">Asignar docente a asignatura</h2>
        <p style="color:var(--color-texto-secundario);font-size:13px;margin-bottom:14px;">Accion limitada al programa activo y al periodo <?= htmlspecialchars($periodoActivo['codigo'] ?? 'activo') ?>. No crea docentes ni asignaturas nuevas.</p>
        <form action="?ruta=lider/programa/asignar_docente" method="POST" class="assignment-form">
            <input type="hidden" name="programa_id" value="<?= (int)$programaActivo['id_programa'] ?>">
            <div class="form-group">
                <label>Asignatura del programa</label>
                <select name="id_asignatura" class="form-control" required>
                    <option value="">Seleccionar asignatura</option>
                    <?php foreach ($asignaturas as $a): ?><option value="<?= (int)$a['id_asignatura'] ?>"><?= htmlspecialchars($a['codigo'] . ' - ' . $a['nombre']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Docente vinculado</label>
                <select name="id_docente" class="form-control" required>
                    <option value="">Seleccionar docente</option>
                    <?php foreach ($docentes as $d): ?><option value="<?= (int)$d['id_docente'] ?>"><?= htmlspecialchars($d['codigo'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><button class="btn-primario" type="submit"><i class="fa-solid fa-user-check"></i> Registrar asignacion</button></div>
        </form>
    </div>
    <div class="panel table-wrap">
        <h2 style="font-size:18px;margin-bottom:12px;">Asignaciones del periodo</h2>
        <table class="table"><thead><tr><th>Periodo</th><th>Asignatura</th><th>Docente</th><th>Estado</th></tr></thead><tbody>
        <?php if (empty($asignaciones)): ?><tr><td colspan="4" style="text-align:center;padding:22px;">No hay asignaciones registradas para este periodo.</td></tr><?php endif; ?>
        <?php foreach ($asignaciones as $asig): ?><tr><td><?= htmlspecialchars($asig['periodo_codigo']) ?></td><td><?= htmlspecialchars($asig['asignatura_codigo'] . ' - ' . $asig['asignatura_nombre']) ?></td><td><?= htmlspecialchars($asig['docente_codigo'] . ' - ' . $asig['docente_nombre']) ?></td><td><span class="badge <?= strtolower($asig['estado']) ?>"><?= htmlspecialchars($asig['estado']) ?></span></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
<?php endif; ?>
<?php $this->render('shared/footer'); ?>
