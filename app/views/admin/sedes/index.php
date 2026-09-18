<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Sedes']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => true],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => false],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1>Gestión de Sedes</h1>
    <a href="?ruta=admin/espacios/sedes/crear" class="btn-primario">+ Nueva Sede</a>
</div>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sedes)): ?>
                <tr><td colspan="5">No hay sedes registradas.</td></tr>
            <?php else: ?>
                <?php foreach ($sedes as $sede): ?>
                    <tr>
                        <td><?= htmlspecialchars($sede['codigo']) ?></td>
                        <td><?= htmlspecialchars($sede['nombre']) ?></td>
                        <td><?= htmlspecialchars($sede['direccion']) ?></td>
                        <td>
                            <span class="badge <?= strtolower($sede['estado']) ?>">
                                <?= htmlspecialchars($sede['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?ruta=admin/espacios/sedes/editar&id=<?= $sede['id_sede'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <?php if ($sede['estado'] === 'ACTIVO'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/espacios/sedes/desactivar&id=<?= $sede['id_sede'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas desactivar esta sede?');">
                                    <button type="submit" style="background:none; border:none; color:red; cursor:pointer; text-decoration:underline;"><i class="fa-solid fa-trash"></i> Desactivar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->render('shared/footer'); ?>
