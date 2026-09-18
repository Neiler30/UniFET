<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Espacios']);
$this->render('shared/sidebar_admin');
?>

<div class="header-acciones">
    <h1>Gestión de Espacios</h1>
    <a href="?ruta=admin/espacios/crear" class="btn-primario">+ Nuevo Espacio</a>
</div>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Tipo</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($espacios)): ?>
                <tr><td colspan="7">No hay espacios registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($espacios as $espacio): ?>
                    <tr>
                        <td><?= htmlspecialchars($espacio['codigo']) ?></td>
                        <td><?= htmlspecialchars($espacio['nombre']) ?></td>
                        <td><?= htmlspecialchars(($espacio['sede_nombre'] ?? '') . ' - ' . ($espacio['bloque_nombre'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($espacio['tipo']) ?></td>
                        <td><?= htmlspecialchars($espacio['capacidad']) ?></td>
                        <td>
                            <span class="badge <?= strtolower($espacio['estado']) ?>">
                                <?= htmlspecialchars($espacio['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?ruta=admin/espacios/editar&id=<?= $espacio['id_espacio'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <?php if ($espacio['estado'] === 'DISPONIBLE'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/espacios/cambiar_estado&id=<?= $espacio['id_espacio'] ?>&estado=MANTENIMIENTO" method="POST" style="display:inline;">
                                    <button type="submit" style="background:none; border:none; color:orange; cursor:pointer; text-decoration:underline;">Mantenimiento</button>
                                </form>
                            <?php elseif ($espacio['estado'] === 'MANTENIMIENTO'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/espacios/cambiar_estado&id=<?= $espacio['id_espacio'] ?>&estado=DISPONIBLE" method="POST" style="display:inline;">
                                    <button type="submit" style="background:none; border:none; color:green; cursor:pointer; text-decoration:underline;">Disponible</button>
                                </form>
                            <?php endif; ?>
                            &nbsp;|&nbsp;
                            <form action="?ruta=admin/espacios/cambiar_estado&id=<?= $espacio['id_espacio'] ?>&estado=INACTIVO" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas desactivar este espacio?');">
                                <button type="submit" style="background:none; border:none; color:red; cursor:pointer; text-decoration:underline;"><i class="fa-solid fa-trash"></i> Desactivar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->render('shared/footer'); ?>
