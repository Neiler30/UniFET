<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Programas']);
$this->render('shared/sidebar_admin');

$tabs = [
    ['etiqueta' => 'Facultades', 'ruta' => 'admin/institucion/facultades', 'activa' => false],
    ['etiqueta' => 'Programas', 'ruta' => 'admin/institucion/programas', 'activa' => true],
];
$this->render('shared/tabs', ['tabs' => $tabs]);
?>

<div class="header-acciones">
    <h1>Gestión de Programas</h1>
    <a href="?ruta=admin/institucion/programas/crear" class="btn-primario">+ Nuevo Programa</a>
</div>

<div class="panel">
    <div style="margin-bottom: 15px;">
        <label for="filtro-facultad">Filtrar por Facultad: </label>
        <select id="filtro-facultad" class="form-control" style="width: 250px; display: inline-block;">
            <option value="">Todas</option>
            <?php
            // Extraer facultades únicas de la lista de programas
            $facultades_unicas = [];
            foreach ($programas as $p) {
                if (!empty($p['facultad_nombre']) && !in_array($p['facultad_nombre'], $facultades_unicas)) {
                    $facultades_unicas[] = $p['facultad_nombre'];
                }
            }
            sort($facultades_unicas);
            foreach ($facultades_unicas as $facName): ?>
                <option value="<?= htmlspecialchars($facName) ?>"><?= htmlspecialchars($facName) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <table class="table" id="tabla-programas">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Facultad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($programas)): ?>
                <tr><td colspan="5">No hay programas registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($programas as $programa): ?>
                    <tr class="fila-programa" data-facultad="<?= htmlspecialchars($programa['facultad_nombre'] ?? '') ?>">
                        <td><?= htmlspecialchars($programa['codigo']) ?></td>
                        <td><?= htmlspecialchars($programa['nombre']) ?></td>
                        <td><?= htmlspecialchars($programa['facultad_nombre'] ?? '') ?></td>
                        <td>
                            <span class="badge <?= strtolower($programa['estado']) ?>">
                                <?= htmlspecialchars($programa['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?ruta=admin/institucion/programas/editar&id=<?= $programa['id_programa'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <?php if ($programa['estado'] === 'ACTIVO'): ?>
                                &nbsp;|&nbsp;
                                <form action="?ruta=admin/institucion/programas/desactivar&id=<?= $programa['id_programa'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas desactivar este programa?');">
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
document.getElementById('filtro-facultad').addEventListener('change', function() {
    const selected = this.value;
    const rows = document.querySelectorAll('.fila-programa');
    rows.forEach(row => {
        if (selected === '' || row.getAttribute('data-facultad') === selected) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php $this->render('shared/footer'); ?>
