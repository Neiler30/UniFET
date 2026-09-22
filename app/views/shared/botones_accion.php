<?php
$puede_eliminar = $puede_eliminar ?? false;
$ruta_eliminar = $ruta_eliminar ?? '';
$texto_entidad = $texto_entidad ?? 'este registro';
$activo = $activo ?? true;
$menuId = 'menu_acciones_' . substr(md5(($ruta_editar ?? '') . ($ruta_desactivar ?? '') . random_int(1, 999999)), 0, 10);
?>
<div class="acciones-menu" data-actions-menu>
    <a href="<?= htmlspecialchars($ruta_editar) ?>" class="btn-accion btn-accion-editar" title="Editar" aria-label="Editar">
        <i class="fa-solid fa-pen"></i>
    </a>
    <button type="button" class="btn-accion btn-accion-kebab" title="Mas acciones" aria-label="Mas acciones" aria-expanded="false" aria-controls="<?= htmlspecialchars($menuId) ?>" data-actions-trigger>
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </button>
    <div class="acciones-dropdown" id="<?= htmlspecialchars($menuId) ?>" data-actions-dropdown>
        <?php if ($activo): ?>
            <form action="<?= htmlspecialchars($ruta_desactivar) ?>" method="POST" onsubmit="return confirmarDesactivacion(event, this, 'Podras reactivarlo mas adelante desde este mismo listado.');">
                <button type="submit" class="accion-menu-item accion-menu-warning">
                    <i class="fa-solid fa-ban"></i> Desactivar
                </button>
            </form>
        <?php else: ?>
            <form action="<?= htmlspecialchars($ruta_desactivar) ?>" method="POST">
                <button type="submit" class="accion-menu-item accion-menu-success">
                    <i class="fa-solid fa-check"></i> Reactivar
                </button>
            </form>
        <?php endif; ?>
        <?php if ($puede_eliminar): ?>
            <form action="<?= htmlspecialchars($ruta_eliminar) ?>" method="POST" onsubmit="return confirmarEliminacion(event, this, '<?= htmlspecialchars($texto_entidad, ENT_QUOTES) ?>');">
                <button type="submit" class="accion-menu-item accion-menu-danger">
                    <i class="fa-solid fa-trash-can"></i> Eliminar
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
