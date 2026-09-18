<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Bloques']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Sedes', 'ruta' => 'admin/espacios/sedes', 'activa' => false],
    ['etiqueta' => 'Bloques', 'ruta' => 'admin/espacios/bloques', 'activa' => true],
    ['etiqueta' => 'Espacios', 'ruta' => 'admin/espacios/espacios', 'activa' => false],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1>Gestión de Bloques</h1>
    <a href="?ruta=admin/espacios/bloques/crear" class="btn-primario">+ Nuevo Bloque</a>
</div>

<div class="panel">
    <div style="margin-bottom: 15px;">
        <label for="filtro-sede">Filtrar por Sede: </label>
        <select id="filtro-sede" class="form-control" style="width: 250px; display: inline-block;">
            <option value="">Todas</option>
            <?php
            $sedes_unicas = [];
            foreach ($bloques as $b) {
                if (!empty($b['sede_nombre']) && !in_array($b['sede_nombre'], $sedes_unicas)) {
                    $sedes_unicas[] = $b['sede_nombre'];
                }
            }
            sort($sedes_unicas);
            foreach ($sedes_unicas as $sedeName): ?>
                <option value="<?= htmlspecialchars($sedeName) ?>"><?= htmlspecialchars($sedeName) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <table class="table" id="tabla-bloques">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Sede</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bloques)): ?>
                <tr><td colspan="5">No hay bloques registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($bloques as $bloque): ?>
                    <tr class="fila-bloque" data-sede="<?= htmlspecialchars($bloque['sede_nombre'] ?? '') ?>">
                        <td><?= htmlspecialchars($bloque['codigo']) ?></td>
                        <td><?= htmlspecialchars($bloque['nombre']) ?></td>
                        <td><?= htmlspecialchars($bloque['sede_nombre'] ?? '') ?></td>
                        <td>
                            <span class="badge <?= strtolower($bloque['estado']) ?>">
                                <?= htmlspecialchars($bloque['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?ruta=admin/espacios/bloques/editar&id=<?= $bloque['id_bloque'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <?php if ($bloque['estado'] === 'ACTIVO'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/espacios/bloques/desactivar&id=<?= $bloque['id_bloque'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas desactivar este bloque?');">
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

<script>
document.getElementById('filtro-sede').addEventListener('change', function() {
    const selected = this.value;
    const rows = document.querySelectorAll('.fila-bloque');
    rows.forEach(row => {
        if (selected === '' || row.getAttribute('data-sede') === selected) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php $this->render('shared/footer'); ?>
