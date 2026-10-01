<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Facultades - UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_institucion', ['activo' => 'facultades']);

$totalFacultades = count($facultades ?? []);
$activasCount = 0;
foreach ($facultades ?? [] as $f) {
    if (($f['estado'] ?? 'ACTIVO') === 'ACTIVO') $activasCount++;
}
$inactivasCount = $totalFacultades - $activasCount;
?>

<!-- 1. HEADER DE ACCIONES -->
<div class="header-acciones">
    <div>
        <h1>Facultades Académicas</h1>
        <p>Unidades académicas mayores que coordinan programas curriculares, claustros y departamentos.</p>
    </div>
    <a href="?ruta=admin/institucion/facultades/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nueva Facultad
    </a>
</div>

<!-- 2. RESUMEN KPI DE FACULTADES -->
<div class="inst-kpi-row">
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon facultades"><i class="fa-solid fa-building-columns"></i></div>
        <div class="inst-kpi-info">
            <span>Total Facultades</span>
            <strong><?= $totalFacultades ?></strong>
        </div>
    </div>
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon activos"><i class="fa-solid fa-circle-check"></i></div>
        <div class="inst-kpi-info">
            <span>Facultades Activas</span>
            <strong style="color: #0F9D58;"><?= $activasCount ?></strong>
        </div>
    </div>
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon" style="background: rgba(148,163,184,0.12); color: #64748b;"><i class="fa-solid fa-circle-pause"></i></div>
        <div class="inst-kpi-info">
            <span>Inactivas</span>
            <strong><?= $inactivasCount ?></strong>
        </div>
    </div>
</div>

<!-- 3. BARRA DE BÚSQUEDA Y FILTRADO -->
<div class="panel" style="padding: 12px 18px; margin-bottom: 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="position: relative; flex: 1; min-width: 250px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 13px;"></i>
            <input type="text" id="filtro-facultades-input" placeholder="Buscar por código o nombre de facultad..." 
                   style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; font-family: inherit;"
                   oninput="filtrarFacultades()">
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
            <select id="filtro-facultades-estado" 
                    style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12.5px; outline: none; background: #fff; font-family: inherit;"
                    onchange="filtrarFacultades()">
                <option value="">Todos los Estados</option>
                <option value="ACTIVO">Activas</option>
                <option value="INACTIVO">Inactivas</option>
            </select>

            <span id="contador-facultades-label" style="font-size: 12.5px; color: #64748b; font-weight: 500; margin-left: 6px;">
                Mostrando <?= $totalFacultades ?> facultades
            </span>
        </div>
    </div>
</div>

<!-- 4. TABLA DE FACULTADES -->
<div class="panel table-panel">
    <?php if (empty($facultades)): ?>
        <div class="empty-state" style="padding: 40px 20px;">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay facultades registradas</h3>
            <p>Comienza creando la primera facultad académica para estructurar programas y asignaturas de la universidad.</p>
            <a href="?ruta=admin/institucion/facultades/crear" class="btn-primario" style="margin-top: 10px;">
                <i class="fa-solid fa-plus"></i> Crear Primera Facultad
            </a>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" id="tabla-facultades">
                <thead>
                    <tr>
                        <th style="width: 140px;">Código</th>
                        <th>Nombre de la Facultad</th>
                        <th style="width: 140px; text-align: center;">Estado</th>
                        <th style="width: 140px; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facultades as $fac): 
                        $estadoClass = strtolower($fac['estado'] ?? 'activo');
                    ?>
                        <tr class="fila-facultad" 
                            data-codigo="<?= htmlspecialchars(strtolower($fac['codigo'] ?? '')) ?>" 
                            data-nombre="<?= htmlspecialchars(strtolower($fac['nombre'] ?? '')) ?>" 
                            data-estado="<?= htmlspecialchars($fac['estado'] ?? 'ACTIVO') ?>">
                            <td>
                                <span class="code-pill"><?= htmlspecialchars($fac['codigo']) ?></span>
                            </td>
                            <td>
                                <strong style="color: var(--color-oscuro); font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-building-columns" style="color: var(--color-primario); font-size: 13px; opacity: 0.85;"></i>
                                    <?= htmlspecialchars($fac['nombre']) ?>
                                </strong>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge <?= $estadoClass ?>">
                                    <?= htmlspecialchars($fac['estado']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <?php $this->render('elementos_compartidos/botones_accion', [
                                    'ruta_editar' => '?ruta=admin/institucion/facultades/editar&id=' . (int)$fac['id_facultad'],
                                    'ruta_desactivar' => '?ruta=admin/institucion/facultades/desactivar&id=' . (int)$fac['id_facultad'],
                                    'activo' => $fac['estado'] === 'ACTIVO',
                                ]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function filtrarFacultades() {
    const texto = (document.getElementById('filtro-facultades-input')?.value || '').trim().toLowerCase();
    const estado = (document.getElementById('filtro-facultades-estado')?.value || '').toUpperCase();

    let visibles = 0;
    const filas = document.querySelectorAll('.fila-facultad');

    filas.forEach(f => {
        const cod = f.getAttribute('data-codigo') || '';
        const nom = f.getAttribute('data-nombre') || '';
        const est = f.getAttribute('data-estado') || '';

        const matchTexto = !texto || cod.includes(texto) || nom.includes(texto);
        const matchEstado = !estado || est === estado;

        if (matchTexto && matchEstado) {
            f.style.display = '';
            visibles++;
        } else {
            f.style.display = 'none';
        }
    });

    const contador = document.getElementById('contador-facultades-label');
    if (contador) {
        contador.textContent = `Mostrando ${visibles} de <?= $totalFacultades ?> facultades`;
    }
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
