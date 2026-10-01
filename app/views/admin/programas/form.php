<?php
$esEdicion = !empty($programa['id_programa']);
$this->render('elementos_compartidos/header', ['titulo' => ($esEdicion ? 'Editar Programa' : 'Nuevo Programa') . ' - UniFET']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_institucion', ['activo' => 'programas']);
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; color: var(--color-oscuro);"><?= $esEdicion ? 'Editar Programa' : 'Nuevo Programa Académico' ?></h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            <?= $esEdicion ? 'Modifica los parámetros del programa académico.' : 'Registra un programa asociándolo a su facultad y nivel académico.' ?>
        </p>
    </div>
    <a href="?ruta=admin/institucion/programas" class="btn-demo">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>
</div>

<div class="panel" style="max-width: 680px; margin-top: 10px;">
    <?php if (!empty($error)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'No se pudo guardar',
                    text: <?= json_encode($error) ?>,
                    icon: 'error',
                    confirmButtonColor: '#3B0D8F'
                });
            });
        </script>
    <?php endif; ?>

    <form action="?ruta=admin/institucion/programas/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" method="POST">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id_programa" value="<?= $programa['id_programa'] ?>">
        <?php endif; ?>

        <!-- Facultad -->
        <div class="form-group">
            <label for="id_facultad" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                Facultad Responsable <span style="color:red;">*</span>
            </label>
            <select id="id_facultad" name="id_facultad" class="form-control" required>
                <option value="">Seleccione una facultad...</option>
                <?php foreach ($facultades as $facultad): ?>
                    <option value="<?= $facultad['id_facultad'] ?>" <?= ((int)($programa['id_facultad'] ?? 0) === (int)$facultad['id_facultad']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($facultad['nombre']) ?> (<?= htmlspecialchars($facultad['codigo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nivel académico -->
        <div class="form-group">
            <label for="id_nivel_academico" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                Nivel Académico <span style="color:red;">*</span>
            </label>
            <select id="id_nivel_academico" name="id_nivel_academico" class="form-control" required onchange="sugerirSemestres(this.value)">
                <option value="">Seleccione el nivel académico...</option>
                <?php foreach (($niveles ?? []) as $nivel): ?>
                    <option value="<?= (int)$nivel['id_nivel_academico'] ?>"
                            data-descripcion="<?= htmlspecialchars($nivel['descripcion'] ?? '') ?>"
                            <?= ((int)($programa['id_nivel_academico'] ?? 0) === (int)$nivel['id_nivel_academico']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nivel['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small id="nivel-descripcion" style="color: var(--color-texto-secundario); font-size: 12px; margin-top: 4px; display: block;"></small>
        </div>

        <!-- Código -->
        <div class="form-group">
            <label for="codigo" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                Código del Programa <span style="color:red;">*</span>
            </label>
            <input type="text" id="codigo" name="codigo" class="form-control"
                   value="<?= htmlspecialchars($programa['codigo'] ?? '') ?>"
                   placeholder="Ej: SIS, ADM, MED..." required>
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                Nombre del Programa <span style="color:red;">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($programa['nombre'] ?? '') ?>"
                   placeholder="Ej: Ingeniería de Sistemas" required>
        </div>

        <!-- Número de semestres -->
        <div class="form-group">
            <label for="numero_semestres" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">
                Número de Semestres <span style="color:red;">*</span>
            </label>
            <input type="number" id="numero_semestres" name="numero_semestres" class="form-control"
                   min="1" max="20"
                   value="<?= (int)($programa['numero_semestres'] ?? 0) ?: '' ?>"
                   placeholder="Ej: 10" required>
            <small style="color: var(--color-texto-secundario); font-size: 12px; margin-top: 4px; display: block;">
                Define cuántos semestres tiene el programa. Esto determina las opciones de semestre disponibles en la oferta académica.
            </small>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="estado" style="font-weight: 600; font-size: 13.5px; color: var(--color-oscuro);">Estado Académico</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="ACTIVO"   <?= ($programa['estado'] ?? 'ACTIVO') === 'ACTIVO'   ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= ($programa['estado'] ?? '')        === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>

        <div style="margin-top: 25px; display: flex; gap: 12px;">
            <button type="submit" class="btn-primario">
                <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Crear Programa' ?>
            </button>
            <a href="?ruta=admin/institucion/programas" class="btn-demo">Cancelar</a>
        </div>
    </form>
</div>

<script>
// Mapa de sugerencias de semestres por nombre de nivel
const sugerenciasSemestres = {
    'Técnico Profesional': 4,
    'Tecnólogo':           6,
    'Profesional':         10,
    // Técnico Laboral no tiene sugerencia fija
};

function sugerirSemestres(idNivel) {
    const select = document.getElementById('id_nivel_academico');
    const opt    = select.options[select.selectedIndex];
    if (!opt) return;

    // Mostrar descripción
    const desc = document.getElementById('nivel-descripcion');
    if (desc) desc.textContent = opt.getAttribute('data-descripcion') || '';

    // Sugerir número de semestres solo si el campo está vacío
    const inputSem = document.getElementById('numero_semestres');
    if (inputSem && !inputSem.value) {
        const nombre = opt.text.trim();
        if (sugerenciasSemestres[nombre] !== undefined) {
            inputSem.value = sugerenciasSemestres[nombre];
        }
    }
}

// Mostrar descripción al cargar si ya hay nivel seleccionado
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('id_nivel_academico');
    if (sel && sel.value) sugerirSemestres(sel.value);
});
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
