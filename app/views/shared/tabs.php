<?php
// $tabs debe ser un array de arrays: [['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => true], ...]
?>
<style>
.tabs-container {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 20px;
}
.tab-link {
    padding: 12px 20px;
    color: var(--color-texto-secundario);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.3s;
}
.tab-link:hover {
    color: var(--color-primario);
    background-color: rgba(0,0,0,0.02);
}
.tab-link.activa {
    color: var(--color-primario);
    border-bottom-color: var(--color-primario);
}
</style>

<div class="tabs-container">
    <?php foreach ($tabs as $tab): ?>
        <a href="?ruta=<?= htmlspecialchars($tab['ruta']) ?>" class="tab-link <?= $tab['activa'] ? 'activa' : '' ?>">
            <?= htmlspecialchars($tab['etiqueta']) ?>
        </a>
    <?php endforeach; ?>
</div>
