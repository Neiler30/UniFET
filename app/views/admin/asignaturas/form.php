<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Asignatura']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'asignaturas']);
$esEdicion = !empty($asignatura['id_asignatura']);

?>

<div class="form-shell">
    <div class="brand-form-card brand-form-card-wide" style="max-width: 860px;">
        <div class="form-entity-icon"><i class="fa-solid fa-book"></i></div>
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <p>Define los datos curriculares de la asignatura y asóciala a los programas con su respectivo semestre.</p>

        <form id="form-asignatura" method="POST" action="?ruta=admin/academico/asignaturas/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" class="brand-form-grid">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_asignatura" value="<?= (int)$asignatura['id_asignatura'] ?>">
            <?php endif; ?>

            <!-- Código -->
            <div class="form-group input-icon">
                <label>Código <span style="color:red;">*</span></label>
                <i class="fa-solid fa-barcode"></i>
                <input class="form-control" name="codigo" required placeholder="Ej: CONTAD953" value="<?= htmlspecialchars($asignatura['codigo'] ?? '') ?>">
            </div>

            <!-- Nombre -->
            <div class="form-group input-icon">
                <label>Nombre de la Asignatura <span style="color:red;">*</span></label>
                <i class="fa-solid fa-book-open"></i>
                <input class="form-control" name="nombre" required placeholder="Ej: Auditoría Tributaria" value="<?= htmlspecialchars($asignatura['nombre'] ?? '') ?>">
            </div>

            <!-- Créditos -->
            <div class="form-group input-icon">
                <label>Créditos Académicos <span style="color:red;">*</span></label>
                <i class="fa-solid fa-star"></i>
                <input type="number" min="0" max="20" class="form-control" name="creditos" required value="<?= htmlspecialchars((string)($asignatura['creditos'] ?? '3')) ?>">
                <small style="font-size: 11.5px; color: var(--color-texto-secundario); margin-top: 3px; display: block;">
                    <i class="fa-solid fa-circle-info" style="color: var(--color-primario);"></i>
                    Ingresa <strong>0</strong> (cero) para Proyectos de Grado — se mostrará como <strong>00</strong>.
                </small>
            </div>

            <!-- Área académica -->
            <div class="form-group input-icon">
                <label>Área académica</label>
                <i class="fa-solid fa-shapes"></i>
                <select class="form-control" name="id_area_academica">
                    <option value="">Sin área específica</option>
                    <?php foreach (($areas ?? []) as $area): ?>
                        <option value="<?= (int)$area['id_area_academica'] ?>" <?= ((int)($asignatura['id_area_academica'] ?? 0) === (int)$area['id_area_academica']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($area['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado -->
            <div class="form-group input-icon">
                <label>Estado</label>
                <i class="fa-solid fa-toggle-on"></i>
                <select class="form-control" name="estado">
                    <?php foreach (['ACTIVO','INACTIVO'] as $e): ?>
                        <option value="<?= $e ?>" <?= (($asignatura['estado'] ?? 'ACTIVO') === $e) ? 'selected' : '' ?>><?= $e ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- SECCIÓN: PROGRAMAS ASOCIADOS + SEMESTRE -->
            <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <label style="font-weight: 700; font-size: 15px; color: var(--color-oscuro); display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-graduation-cap" style="color: var(--color-primario);"></i> Programas asociados
                        </label>
                        <p style="font-size: 12.5px; color: var(--color-texto-secundario); margin: 2px 0 0 0;">
                            Define a qué programas pertenece esta asignatura y en qué semestre curricular se ubica en cada uno.
                        </p>
                    </div>
                    <button type="button" id="btn-agregar-asociacion" class="btn-demo" style="height: 36px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                        <i class="fa-solid fa-plus" style="color: var(--color-primario);"></i> Agregar programa
                    </button>
                </div>

                <div style="background: #ffffff; border: 1px solid var(--borde-suave, #e2e8f0); border-radius: 8px; overflow: hidden;">
                    <table class="table" style="margin: 0;" id="tabla-asociaciones">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="width: 55%;">Programa Académico</th>
                                <th style="width: 35%;">Semestre Curricular <span style="color:red;">*</span></th>
                                <th style="width: 10%; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="contenedor-filas-programas">
                            <!-- Las filas se renderizan dinámicamente o por PHP -->
                        </tbody>
                    </table>

                    <div id="mensaje-sin-programas" style="display: none; padding: 24px; text-align: center; color: var(--color-texto-secundario); font-size: 13.5px;">
                        <i class="fa-regular fa-folder-open" style="font-size: 24px; margin-bottom: 6px; display: block; opacity: 0.5;"></i>
                        No hay programas asociados a esta asignatura. Haz clic en <strong>«Agregar programa»</strong> para asignarle ubicación curricular.
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="form-actions" style="grid-column: 1 / -1; margin-top: 15px;">
                <button type="submit" class="btn-primario">
                    <i class="fa-solid fa-check"></i> Guardar Asignatura
                </button>
                <a class="btn-outline" href="?ruta=admin/academico/asignaturas">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Catálogo de programas disponibles
const catalogoProgramas = <?= json_encode(array_map(function ($p) {
    return [
        'id'        => (int)$p['id_programa'],
        'nombre'    => $p['nombre'],
        'codigo'    => $p['codigo'],
        'semestres' => (int)($p['numero_semestres'] ?? 10) ?: 10,
    ];
}, $programas ?? [])) ?>;

// Asociaciones iniciales (para edición)
const asociacionesIniciales = <?= json_encode(array_map(function ($ap) {
    return [
        'id_programa' => (int)$ap['id_programa'],
        'semestre'    => isset($ap['semestre']) && $ap['semestre'] !== null ? (int)$ap['semestre'] : null,
    ];
}, $programasAsignados ?? [])) ?>;

let contadorFilas = 0;

function generarOpcionesSemestre(maxSemestres, semestreSeleccionado) {
    let max = maxSemestres > 0 ? maxSemestres : 10;
    let html = '<option value="">Selecciona el semestre...</option>';
    for (let s = 1; s <= max; s++) {
        const selected = (semestreSeleccionado === s) ? 'selected' : '';
        html += `<option value="${s}" ${selected}>Semestre ${s}</option>`;
    }
    return html;
}

function agregarFilaPrograma(datos = null) {
    const tbody = document.getElementById('contenedor-filas-programas');
    const index = contadorFilas++;
    const idProgramaSel = datos ? parseInt(datos.id_programa) : null;
    const semestreSel = datos ? (datos.semestre !== null ? parseInt(datos.semestre) : null) : null;

    // Determinar cantidad de semestres del programa seleccionado si aplica
    let semestresMax = 10;
    if (idProgramaSel) {
        const progEncontrado = catalogoProgramas.find(p => p.id === idProgramaSel);
        if (progEncontrado && progEncontrado.semestres > 0) {
            semestresMax = progEncontrado.semestres;
        }
    }

    const tr = document.createElement('tr');
    tr.className = 'fila-asociacion';
    tr.id = `fila-asoc-${index}`;

    // Construir options para programas
    let optionsProg = '<option value="">Seleccione un programa...</option>';
    catalogoProgramas.forEach(p => {
        const selected = (idProgramaSel === p.id) ? 'selected' : '';
        optionsProg += `<option value="${p.id}" data-semestres="${p.semestres}" ${selected}>${p.nombre} (${p.codigo})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="asociaciones[${index}][id_programa]" class="form-control select-programa" required onchange="validarProgramaSeleccionado(this, ${index})">
                ${optionsProg}
            </select>
        </td>
        <td>
            <select name="asociaciones[${index}][semestre]" class="form-control select-semestre" id="select-semestre-${index}" required>
                ${generarOpcionesSemestre(semestresMax, semestreSel)}
            </select>
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <button type="button" class="btn-accion btn-accion-eliminar" title="Retirar programa" onclick="eliminarFilaPrograma(${index})">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    actualizarEstadoVacio();
}

function eliminarFilaPrograma(index) {
    const fila = document.getElementById(`fila-asoc-${index}`);
    if (fila) {
        fila.remove();
        actualizarEstadoVacio();
    }
}

function validarProgramaSeleccionado(selectElement, index) {
    const idSeleccionado = parseInt(selectElement.value);
    if (!idSeleccionado) {
        return;
    }

    // Verificar si ya está en otra fila
    const selects = document.querySelectorAll('.select-programa');
    let conteo = 0;
    selects.forEach(s => {
        if (parseInt(s.value) === idSeleccionado) {
            conteo++;
        }
    });

    if (conteo > 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Programa duplicado',
            text: 'Este programa ya está asociado a la asignatura.',
            confirmButtonColor: '#3B0D8F'
        });
        selectElement.value = '';
        return;
    }

    // Actualizar selector de semestre con la duración del programa
    const optSelected = selectElement.options[selectElement.selectedIndex];
    const semestresProg = parseInt(optSelected.getAttribute('data-semestres') || '10');
    const selectSem = document.getElementById(`select-semestre-${index}`);
    if (selectSem) {
        const valActual = parseInt(selectSem.value) || null;
        selectSem.innerHTML = generarOpcionesSemestre(semestresProg, valActual);
    }
}

function actualizarEstadoVacio() {
    const filas = document.querySelectorAll('.fila-asociacion');
    const sinProgramas = document.getElementById('mensaje-sin-programas');
    const tabla = document.getElementById('tabla-asociaciones');
    if (filas.length === 0) {
        sinProgramas.style.display = 'block';
    } else {
        sinProgramas.style.display = 'none';
    }
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    const btnAgregar = document.getElementById('btn-agregar-asociacion');
    if (btnAgregar) {
        btnAgregar.addEventListener('click', function () {
            agregarFilaPrograma();
        });
    }

    if (asociacionesIniciales && asociacionesIniciales.length > 0) {
        asociacionesIniciales.forEach(asoc => agregarFilaPrograma(asoc));
    } else {
        actualizarEstadoVacio();
    }

    // Validación antes de enviar el formulario
    const form = document.getElementById('form-asignatura');
    if (form) {
        form.addEventListener('submit', function (e) {
            const filas = document.querySelectorAll('.fila-asociacion');
            for (let fila of filas) {
                const progSelect = fila.querySelector('.select-programa');
                const semSelect = fila.querySelector('.select-semestre');

                if (progSelect && progSelect.value) {
                    const semVal = parseInt(semSelect.value);
                    if (!semVal || semVal <= 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Semestre obligatorio',
                            text: 'Selecciona el semestre correspondiente al programa.',
                            confirmButtonColor: '#3B0D8F'
                        });
                        semSelect.focus();
                        return false;
                    }
                }
            }
        });
    }
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
