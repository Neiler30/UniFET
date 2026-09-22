<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Periodos']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs_academico', ['activo' => 'periodos']);
?>
<div class="header-acciones">
    <div><h1>Periodos academicos</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Gestiona ciclos de planeacion y reutilizacion historica.</p></div>
    <a class="btn-primario" href="?ruta=admin/academico/periodos/crear"><i class="fa-solid fa-plus"></i> Nuevo Periodo</a>
</div>
<div class="panel">
    <table class="table">
        <thead><tr><th>Codigo</th><th>Nombre</th><th>Fechas</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
        <tbody>
        <?php if (empty($periodos)): ?><tr><td colspan="5" style="text-align:center;">No hay periodos registrados.</td></tr><?php endif; ?>
        <?php foreach ($periodos as $p): ?>
            <tr>
                <td><span class="code-pill"><?= htmlspecialchars($p['codigo']) ?></span></td>
                <td><?= htmlspecialchars($p['nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars(($p['fecha_inicio'] ?? '') . ' - ' . ($p['fecha_fin'] ?? '')) ?></td>
                <td><span class="badge <?= strtolower($p['estado']) ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
                <td style="text-align:right;"><a class="btn-accion btn-accion-editar" title="Editar" href="?ruta=admin/academico/periodos/editar&id=<?= (int)$p['id_periodo'] ?>"><i class="fa-solid fa-pen"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $this->render('shared/footer'); ?>
