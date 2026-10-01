<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Programas Académicos - UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_institucion', ['activo' => 'programas']);

$totalProgramas = count($programas ?? []);
$activosCount = 0;
$facultadesUnicas = [];
$nivelesUnicos = [];

foreach ($programas ?? [] as $p) {
    if (($p['estado'] ?? 'ACTIVO') === 'ACTIVO') $activosCount++;
    if (!empty($p['facultad_nombre']) && !in_array($p['facultad_nombre'], $facultadesUnicas)) {
        $facultadesUnicas[] = $p['facultad_nombre'];
    }
    if (!empty($p['nivel_nombre']) && !in_array($p['nivel_nombre'], $nivelesUnicos)) {
        $nivelesUnicos[] = $p['nivel_nombre'];
    }
}
sort($facultadesUnicas);

// Orden canónico de niveles UniFET
$ordenNiveles = ['Técnico Laboral', 'Técnico Profesional', 'Tecnólogo', 'Profesional'];
usort($nivelesUnicos, function($a, $b) use ($ordenNiveles) {
    $posA = array_search($a, $ordenNiveles);
    $posB = array_search($b, $ordenNiveles);
    return ($posA === false ? 99 : $posA) <=> ($posB === false ? 99 : $posB);
});
?>

<!-- 1. HEADER DE ACCIONES -->
<div class="header-acciones">
    <div>
        <h1>Programas Académicos</h1>
        <p>Oferta de carreras universitarias, pregrados y programas tecnológicos adscritos a las facultades.</p>
    </div>
    <a href="?ruta=admin/institucion/programas/crear" class="btn-primario">
        <i class="fa-solid fa-plus"></i> Nuevo Programa
    </a>
</div>

<!-- 2. RESUMEN KPI DE PROGRAMAS -->
<div class="inst-kpi-row">
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon programas"><i class="fa-solid fa-graduation-cap"></i></div>
        <div class="inst-kpi-info">
            <span>Total Programas</span>
            <strong><?= $totalProgramas ?></strong>
        </div>
    </div>
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon activos"><i class="fa-solid fa-circle-check"></i></div>
        <div class="inst-kpi-info">
            <span>Programas Activos</span>
            <strong style="color: #0F9D58;"><?= $activosCount ?></strong>
        </div>
    </div>
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon facultades"><i class="fa-solid fa-building-columns"></i></div>
        <div class="inst-kpi-info">
            <span>Facultades con Oferta</span>
            <strong><?= count($facultadesUnicas) ?></strong>
        </div>
    </div>
    <div class="inst-kpi-card">
        <div class="inst-kpi-icon" style="background: rgba(124,58,237,0.08); color: #7c3aed;"><i class="fa-solid fa-layer-group"></i></div>
        <div class="inst-kpi-info">
            <span>Niveles Formativos</span>
            <strong><?= count($nivelesUnicos) ?></strong>
        </div>
    </div>
</div>

<!-- 3. PANEL DE FILTROS Y BÚSQUEDA DINÁMICA -->
<div class="panel" style="padding: 14px 18px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        
        <!-- Buscador en tiempo real -->
        <div style="position: relative; flex: 1; min-width: 240px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 13px;"></i>
            <input type="text" id="filtro-busqueda-input" placeholder="Buscar por nombre, código o facultad..." 
                   style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; font-family: inherit;"
                   oninput="aplicarFiltrosProgramas()">
        </div>

        <!-- Filtro Facultad -->
        <select id="filtro-facultad-select" 
                style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12.5px; outline: none; background: #fff; font-family: inherit; max-width: 220px;"
                onchange="aplicarFiltrosProgramas()">
            <option value="">Todas las facultades</option>
            <?php foreach ($facultadesUnicas as $facName): ?>
                <option value="<?= htmlspecialchars($facName) ?>"><?= htmlspecialchars($facName) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Filtro Nivel Académico -->
        <select id="filtro-nivel-select" 
                style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12.5px; outline: none; background: #fff; font-family: inherit; max-width: 180px;"
                onchange="aplicarFiltrosProgramas()">
            <option value="">Todos los niveles</option>
            <?php foreach ($nivelesUnicos as $nivNombre): ?>
                <option value="<?= htmlspecialchars($nivNombre) ?>"><?= htmlspecialchars($nivNombre) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Filtro Estado -->
        <select id="filtro-estado-select" 
                style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12.5px; outline: none; background: #fff; font-family: inherit; max-width: 140px;"
                onchange="aplicarFiltrosProgramas()">
            <option value="">Todos los estados</option>
            <option value="ACTIVO">Activos</option>
            <option value="INACTIVO">Inactivos</option>
        </select>

        <!-- Botón Limpiar -->
        <button type="button" class="btn-demo" style="height: 36px; font-size: 12.5px; padding: 6px 12px;" onclick="limpiarFiltrosProgramas()">
            <i class="fa-solid fa-rotate-left"></i> Limpiar
        </button>

        <span id="contador-programas-label" style="font-size: 12.5px; color: #64748b; font-weight: 500; white-space: nowrap;">
            Total: <strong><?= $totalProgramas ?></strong>
        </span>
    </div>
</div>

<!-- 4. TABLA DE PROGRAMAS ACADÉMICOS -->
<div class="panel table-panel">
    <?php if (empty($programas)): ?>
        <div class="empty-state" style="padding: 40px 20px;">
            <img src="assets/img/isotipo-color.png" alt="UniFET" class="empty-state-img">
            <h3>No hay programas académicos registrados</h3>
            <p>Registra las carreras y planes de estudio vinculados a las facultades.</p>
            <a href="?ruta=admin/institucion/programas/crear" class="btn-primario" style="margin-top: 10px;">
                <i class="fa-solid fa-plus"></i> Crear Primer Programa
            </a>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" id="tabla-programas">
                <thead>
                    <tr>
                        <th style="width: 130px;">Código</th>
                        <th>Programa Académico</th>
                        <th>Facultad Adscrita</th>
                        <th style="width: 170px;">Nivel Formativo</th>
                        <th style="width: 110px; text-align: center;">Duración</th>
                        <th style="width: 120px; text-align: center;">Estado</th>
                        <th style="width: 120px; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programas as $programa): 
                        $nivelLower = mb_strtolower($programa['nivel_nombre'] ?? '', 'UTF-8');
                        $nivelBadgeStyle = 'background: #f1f5f9; color: #475569;';
                        if (str_contains($nivelLower, 'profesional') && !str_contains($nivelLower, 'tecnico')) {
                            $nivelBadgeStyle = 'background: #ede9fe; color: #4c1d95; border: 1px solid #ddd6fe;';
                        } elseif (str_contains($nivelLower, 'tecnol')) {
                            $nivelBadgeStyle = 'background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;';
                        } elseif (str_contains($nivelLower, 'tecnico')) {
                            $nivelBadgeStyle = 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;';
                        }
                    ?>
                        <tr class="fila-programa" 
                            data-codigo="<?= htmlspecialchars(strtolower($programa['codigo'] ?? '')) ?>"
                            data-nombre="<?= htmlspecialchars(strtolower($programa['nombre'] ?? '')) ?>"
                            data-facultad="<?= htmlspecialchars($programa['facultad_nombre'] ?? '') ?>" 
                            data-nivel="<?= htmlspecialchars($programa['nivel_nombre'] ?? '') ?>"
                            data-estado="<?= htmlspecialchars($programa['estado'] ?? 'ACTIVO') ?>">
                            
                            <td>
                                <span class="code-pill"><?= htmlspecialchars($programa['codigo']) ?></span>
                            </td>
                            <td>
                                <strong style="color: var(--color-oscuro); font-size: 14px;">
                                    <?= htmlspecialchars($programa['nombre']) ?>
                                </strong>
                            </td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 6px; color: var(--color-texto-principal); font-size: 13px;">
                                    <i class="fa-solid fa-building-columns" style="color: var(--color-primario); font-size: 12px; opacity: 0.85;"></i>
                                    <?= htmlspecialchars($programa['facultad_nombre'] ?? 'Sin asignar') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($programa['nivel_nombre'])): ?>
                                    <span style="font-size: 12px; padding: 2px 8px; border-radius: 99px; font-weight: 600; display: inline-block; <?= $nivelBadgeStyle ?>">
                                        <?= htmlspecialchars($programa['nivel_nombre']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">Sin definir</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ((int)($programa['numero_semestres'] ?? 0) > 0): ?>
                                    <span style="font-weight: 700; color: #0B1F33; font-size: 13px; background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        <?= (int)$programa['numero_semestres'] ?> sem.
                                    </span>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge <?= strtolower($programa['estado'] ?? 'activo') ?>">
                                    <?= htmlspecialchars($programa['estado'] ?? 'ACTIVO') ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <?php $this->render('elementos_compartidos/botones_accion', [
                                    'ruta_editar' => '?ruta=admin/institucion/programas/editar&id=' . (int)$programa['id_programa'],
                                    'ruta_desactivar' => '?ruta=admin/institucion/programas/desactivar&id=' . (int)$programa['id_programa'],
                                    'activo' => $programa['estado'] === 'ACTIVO',
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
function aplicarFiltrosProgramas() {
    const texto = (document.getElementById('filtro-busqueda-input')?.value || '').trim().toLowerCase();
    const facultad = document.getElementById('filtro-facultad-select')?.value || '';
    const nivel = document.getElementById('filtro-nivel-select')?.value || '';
    const estado = (document.getElementById('filtro-estado-select')?.value || '').toUpperCase();

    let visibles = 0;
    const filas = document.querySelectorAll('.fila-programa');

    filas.forEach(f => {
        const cod = f.getAttribute('data-codigo') || '';
        const nom = f.getAttribute('data-nombre') || '';
        const fac = f.getAttribute('data-facultad') || '';
        const niv = f.getAttribute('data-nivel') || '';
        const est = f.getAttribute('data-estado') || '';

        const matchTexto = !texto || cod.includes(texto) || nom.includes(texto) || fac.toLowerCase().includes(texto);
        const matchFac = !facultad || fac === facultad;
        const matchNiv = !nivel || niv === nivel;
        const matchEst = !estado || est === estado;

        if (matchTexto && matchFac && matchNiv && matchEst) {
            f.style.display = '';
            visibles++;
        } else {
            f.style.display = 'none';
        }
    });

    const contador = document.getElementById('contador-programas-label');
    if (contador) {
        contador.innerHTML = `Mostrando: <strong>${visibles}</strong> de <?= $totalProgramas ?> programas`;
    }
}

function limpiarFiltrosProgramas() {
    if (document.getElementById('filtro-busqueda-input')) document.getElementById('filtro-busqueda-input').value = '';
    if (document.getElementById('filtro-facultad-select')) document.getElementById('filtro-facultad-select').value = '';
    if (document.getElementById('filtro-nivel-select')) document.getElementById('filtro-nivel-select').value = '';
    if (document.getElementById('filtro-estado-select')) document.getElementById('filtro-estado-select').value = '';
    aplicarFiltrosProgramas();
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
