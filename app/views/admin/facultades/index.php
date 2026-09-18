<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Facultades']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => true],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1>Gestión de Facultades</h1>
    <a href="?ruta=admin/institucion/facultades/crear" class="btn-primario">+ Nueva Facultad</a>
</div>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($facultades)): ?>
                <tr><td colspan="4">No hay facultades registradas.</td></tr>
            <?php else: ?>
                <?php foreach ($facultades as $fac): ?>
                    <tr>
                        <td><?= htmlspecialchars($fac['codigo']) ?></td>
                        <td><?= htmlspecialchars($fac['nombre']) ?></td>
                        <td>
                            <span class="badge <?= strtolower($fac['estado']) ?>">
                                <?= htmlspecialchars($fac['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?ruta=admin/institucion/facultades/editar&id=<?= $fac['id_facultad'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <?php if ($fac['estado'] === 'ACTIVO'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/institucion/facultades/desactivar&id=<?= $fac['id_facultad'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas desactivar esta facultad?');">
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
