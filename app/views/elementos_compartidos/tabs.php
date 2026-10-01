<?php
// $tabs debe ser un array de arrays:
// [['etiqueta' => 'Periodos', 'ruta' => 'admin/academico/periodos', 'activa' => true, 'icono' => 'fa-solid fa-...'], ...]
?>
<style>
.tabs-container {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 6px 8px;
    margin-bottom: 22px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    overflow-x: auto;
    scrollbar-width: thin;
    font-family: 'Poppins', sans-serif;
}
.tab-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    color: #64748B;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    border-radius: 8px;
    transition: all 0.2s ease;
    white-space: nowrap;
    border: 1px solid transparent;
}
.tab-link:hover {
    color: var(--color-primario, #3B0D8F);
    background-color: rgba(59, 13, 143, 0.05);
    transform: translateY(-1px);
}
.tab-link.activa {
    background: var(--color-primario, #3B0D8F);
    color: #fff !important;
    border-color: var(--color-primario, #3B0D8F);
    box-shadow: 0 4px 14px rgba(59, 13, 143, 0.22);
}
.tab-link.activa i {
    color: #fff !important;
}
.tab-link i {
    font-size: 14px;
    transition: transform 0.2s;
}
.tab-link:hover i {
    transform: scale(1.1);
}
.tab-link.activa i {
    transform: none;
}
</style>

<div class="tabs-container">
    <?php foreach ($tabs as $tab): ?>
        <a href="?ruta=<?= htmlspecialchars($tab['ruta']) ?>" class="tab-link <?= !empty($tab['activa']) ? 'activa' : '' ?>">
            <?php if (!empty($tab['icono'])): ?>
                <i class="<?= htmlspecialchars($tab['icono']) ?>"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($tab['etiqueta']) ?></span>
            <?php if (!empty($tab['badge'])): ?>
                <span class="badge" style="font-size:10px; margin-left:4px;"><?= htmlspecialchars($tab['badge']) ?></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
