<?php 
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Oferta Académica']); 
$this->render('elementos_compartidos/sidebar_admin'); 
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'oferta']); 
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);">Cargar Oferta Académica</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Selecciona el periodo y programa para habilitar sus asignaturas curriculares en la oferta académica.
        </p>
    </div>
    <a class="btn-demo" href="?ruta=admin/academico/oferta&periodo_id=<?= (int)$periodo_id ?>&programa_id=<?= (int)$programa_id ?>">
        <i class="fa-solid fa-arrow-left"></i> Volver a la oferta
    </a>
</div>

<!-- Filtros de selección inicial de Periodo, Nivel Académico y Programa -->
<div class="panel" style="margin-bottom: 20px;">
    <form id="form-filtros-oferta" method="GET" action="" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="ruta" value="admin/academico/oferta/crear">

        <!-- Periodo -->
        <div class="form-group" style="margin: 0; min-width: 200px;">
            <label style="font-weight: 600; font-size: 13px; color: var(--color-oscuro); display: block; margin-bottom: 4px;">
                <i class="fa-solid fa-calendar-days" style="color: var(--color-primario);"></i> Periodo Académico
            </label>
            <select class="form-control" name="periodo_id" onchange="this.form.submit()">
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= (int)$p['id_periodo'] ?>" <?= ((int)$periodo_id === (int)$p['id_periodo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['codigo']) ?> - <?= htmlspecialchars($p['nombre'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nivel Académico -->
        <div class="form-group" style="margin: 0; min-width: 220px;">
            <label style="font-weight: 600; font-size: 13px; color: var(--color-oscuro); display: block; margin-bottom: 4px;">
                <i class="fa-solid fa-layer-group" style="color: var(--color-primario);"></i> Nivel Académico
            </label>
            <select class="form-control" name="nivel_id" id="filtro-nivel" onchange="cambiarNivel(this.value)">
                <option value="">Todos los niveles</option>
                <?php foreach (($niveles ?? []) as $nv): ?>
                    <option value="<?= (int)$nv['id_nivel_academico'] ?>" <?= ((int)($nivel_id ?? 0) === (int)$nv['id_nivel_academico']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nv['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Programa -->
        <div class="form-group" style="margin: 0; min-width: 280px; flex: 1;">
            <label style="font-weight: 600; font-size: 13px; color: var(--color-oscuro); display: block; margin-bottom: 4px;">
                <i class="fa-solid fa-graduation-cap" style="color: var(--color-primario);"></i> Programa Académico
            </label>
            <select class="form-control" name="programa_id" id="filtro-programa" onchange="cambiarPrograma(this)">
                <?php foreach ($programas as $p): 
                    $pIdNivel = (int)($p['id_nivel_academico'] ?? 0);
                    $mostrar = empty($nivel_id) || ($pIdNivel === (int)$nivel_id);
                ?>
                    <option value="<?= (int)$p['id_programa'] ?>" 
                            data-nivel="<?= $pIdNivel ?>"
                            <?= ((int)$programa_id === (int)$p['id_programa']) ? 'selected' : '' ?>
                            style="<?= $mostrar ? '' : 'display:none;' ?>">
                        <?= htmlspecialchars($p['nombre']) ?> (<?= htmlspecialchars($p['codigo']) ?>)<?= !empty($p['nivel_nombre']) ? ' · ' . htmlspecialchars($p['nivel_nombre']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<!-- Formulario de asignaturas disponibles y selección múltiple -->
<div class="panel">
    <form method="POST" action="?ruta=admin/academico/oferta/guardar" id="form-guardar-oferta">
        <input type="hidden" name="id_periodo" value="<?= (int)$periodo_id ?>">
        <input type="hidden" name="id_programa" value="<?= (int)$programa_id ?>">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: var(--color-oscuro); margin: 0;">
                    Asignaturas curriculares del programa
                </h2>
                <p style="font-size: 13px; color: var(--color-texto-secundario); margin: 3px 0 0 0;">
                    El semestre se toma automáticamente de la asociación curricular (asignatura_programa).
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 14px;">
                <span style="font-size: 13.5px; color: var(--color-texto-principal); background: #f1f5f9; padding: 6px 12px; border-radius: 20px; font-weight: 600;">
                    Seleccionadas: <strong id="contador-seleccionadas" style="color: var(--color-primario);">0</strong>
                </span>
                <button type="submit" class="btn-primario" id="btn-enviar-oferta">
                    <i class="fa-solid fa-plus"></i> Agregar a la oferta
                </button>
            </div>
        </div>

        <?php if (empty($asignaturas)): ?>
            <div style="padding: 36px; text-align: center; color: var(--color-texto-secundario); background: #fafafa; border-radius: 8px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-book-open" style="font-size: 32px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                <p style="font-weight: 600; margin: 0 0 4px 0;">Este programa no tiene asignaturas asociadas todavía.</p>
                <p style="font-size: 13px; margin: 0;">
                    Puedes asociar asignaturas a este programa en el módulo de <a href="?ruta=admin/academico/asignaturas" style="color: var(--color-primario); font-weight: 600;">Asignaturas</a>.
                </p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" id="tabla-oferta-asignaturas">
                    <thead>
                        <tr>
                            <th style="width: 44px; text-align: center;">
                                <input type="checkbox" id="check-todos" title="Seleccionar todas las disponibles" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th style="width: 140px;">Código</th>
                            <th>Asignatura</th>
                            <th style="width: 150px; text-align: center;">Semestre Curricular</th>
                            <th style="width: 90px; text-align: center;">Créditos</th>
                            <th style="width: 160px; text-align: center;">Estado en Periodo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaturas as $a): 
                            $yaOfertada = in_array((int)$a['id_asignatura'], $ofertadasIds ?? [], true);
                        ?>
                            <tr style="<?= $yaOfertada ? 'opacity: 0.7; background: #fdfdfd;' : '' ?>">
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php if ($yaOfertada): ?>
                                        <i class="fa-solid fa-check" style="color: #10b981;" title="Ya está en la oferta"></i>
                                    <?php else: ?>
                                        <input type="checkbox" 
                                               name="id_asignaturas[]" 
                                               value="<?= (int)$a['id_asignatura'] ?>" 
                                               class="check-asignatura"
                                               style="cursor: pointer; width: 16px; height: 16px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="code-pill"><?= htmlspecialchars($a['codigo']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($a['nombre']) ?></strong>
                                    <?php if (!empty($a['area_nombre'])): ?>
                                        <div style="font-size: 11.5px; color: var(--color-texto-secundario); margin-top: 2px;">
                                            <?= htmlspecialchars($a['area_nombre']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php if (!empty($a['semestre'])): ?>
                                        <span style="display: inline-block; padding: 3px 10px; background: #ede9fe; color: #4c1d95; font-size: 12px; font-weight: 700; border-radius: 12px;">
                                            Semestre <?= (int)$a['semestre'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--color-texto-secundario); font-size: 12px; font-style: italic;">
                                            Sin semestre
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= (int)$a['creditos'] ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php if ($yaOfertada): ?>
                                        <span class="badge activa" style="font-size: 11px;">
                                            <i class="fa-solid fa-circle-check" style="font-size: 9px;"></i> Ofertada
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--color-texto-secundario); font-size: 12px;">
                                            Disponible
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
function cambiarNivel(idNivel) {
    const progSelect = document.getElementById('filtro-programa');
    if (!progSelect) return;
    const options = progSelect.querySelectorAll('option');
    let primerVisible = null;
    let actualEsVisible = false;

    options.forEach(opt => {
        const optNivel = opt.getAttribute('data-nivel');
        const mostrar = !idNivel || optNivel === idNivel;
        opt.style.display = mostrar ? '' : 'none';
        if (mostrar && !primerVisible) {
            primerVisible = opt;
        }
        if (opt.selected && mostrar) {
            actualEsVisible = true;
        }
    });

    if (!actualEsVisible && primerVisible) {
        progSelect.value = primerVisible.value;
    }

    document.getElementById('form-filtros-oferta')?.submit();
}

function cambiarPrograma(selectProg) {
    const optSelected = selectProg.options[selectProg.selectedIndex];
    if (optSelected) {
        const idNivel = optSelected.getAttribute('data-nivel');
        const selectNivel = document.getElementById('filtro-nivel');
        if (selectNivel && idNivel) {
            selectNivel.value = idNivel;
        }
    }
    document.getElementById('form-filtros-oferta')?.submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const checkTodos = document.getElementById('check-todos');
    const checks = document.querySelectorAll('.check-asignatura');
    const contador = document.getElementById('contador-seleccionadas');
    const formOferta = document.getElementById('form-guardar-oferta');

    function actualizarContador() {
        const seleccionadas = document.querySelectorAll('.check-asignatura:checked').length;
        if (contador) {
            contador.textContent = seleccionadas;
        }
    }

    if (checkTodos) {
        checkTodos.addEventListener('change', function () {
            checks.forEach(chk => {
                chk.checked = checkTodos.checked;
            });
            actualizarContador();
        });
    }

    checks.forEach(chk => {
        chk.addEventListener('change', function () {
            if (!this.checked && checkTodos) {
                checkTodos.checked = false;
            } else if (checkTodos) {
                const todosMarcados = Array.from(checks).every(c => c.checked);
                checkTodos.checked = todosMarcados;
            }
            actualizarContador();
        });
    });

    if (formOferta) {
        formOferta.addEventListener('submit', function (e) {
            const seleccionadas = document.querySelectorAll('.check-asignatura:checked').length;
            if (seleccionadas === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Ninguna asignatura seleccionada',
                    text: 'Por favor marca al menos una asignatura para agregar a la oferta académica.',
                    confirmButtonColor: '#3B0D8F'
                });
                return false;
            }
        });
    }

    actualizarContador();
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
