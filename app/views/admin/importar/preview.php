<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Vista Previa de Importación']);
$this->render('elementos_compartidos/sidebar_admin');

$catalogo = \App\Services\Importar\Catalog::getEntities()[$tipo];
$camposVisibles = $catalogo['campos'];

$totalValidas = (int)(($stats['nuevos'] ?? 0) + ($stats['completar'] ?? 0) + ($stats['actualizar'] ?? 0));
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--color-oscuro);">Vista Previa de Validación - <?= htmlspecialchars($catalogo['nombre_amigable']) ?></h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Archivo: <strong><?= htmlspecialchars($nombre_archivo ?? 'archivo.xlsx') ?></strong> &mdash; 
            <?= (int)($stats['total'] ?? 0) ?> registros analizados. Revisa la consistencia antes de confirmar.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a class="btn-demo" href="?ruta=admin/importar">
            <i class="fa-solid fa-arrow-left"></i> Cancelar y Volver
        </a>
    </div>
</div>

<!-- Tarjetas KPI de Resumen -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px;">
    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #64748b;">
        <span style="font-size: 11px; text-transform: uppercase; color: var(--color-texto-secundario); font-weight: 700;">Total Filas</span>
        <strong style="display: block; font-size: 22px; color: var(--color-oscuro); margin-top: 4px;"><?= (int)$stats['total'] ?></strong>
    </div>

    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #10b981;">
        <span style="font-size: 11px; text-transform: uppercase; color: #047857; font-weight: 700;">Nuevos</span>
        <strong style="display: block; font-size: 22px; color: #059669; margin-top: 4px;">+<?= (int)$stats['nuevos'] ?></strong>
    </div>

    <?php if ((int)($stats['actualizar'] ?? 0) > 0): ?>
    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #0284c7;">
        <span style="font-size: 11px; text-transform: uppercase; color: #0369a1; font-weight: 700;">A Actualizar</span>
        <strong style="display: block; font-size: 22px; color: #0284c7; margin-top: 4px;">~<?= (int)$stats['actualizar'] ?></strong>
    </div>
    <?php endif; ?>

    <?php if ((int)($stats['completar'] ?? 0) > 0): ?>
    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #3b82f6;">
        <span style="font-size: 11px; text-transform: uppercase; color: #1d4ed8; font-weight: 700;">Por Completar</span>
        <strong style="display: block; font-size: 22px; color: #2563eb; margin-top: 4px;"><?= (int)$stats['completar'] ?></strong>
    </div>
    <?php endif; ?>

    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #f59e0b;">
        <span style="font-size: 11px; text-transform: uppercase; color: #b45309; font-weight: 700;">En BD / Sin Cambios</span>
        <strong style="display: block; font-size: 22px; color: #d97706; margin-top: 4px;"><?= (int)$stats['duplicados'] ?></strong>
    </div>

    <div class="panel" style="padding: 14px; text-align: center; border-bottom: 3px solid #ef4444;">
        <span style="font-size: 11px; text-transform: uppercase; color: #b91c1c; font-weight: 700;">Errores / Conflictos</span>
        <strong style="display: block; font-size: 22px; color: #dc2626; margin-top: 4px;"><?= (int)$stats['errores'] ?></strong>
    </div>
</div>

<!-- Filtros de Estado y Confirmación -->
<div class="panel" style="margin-bottom: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 13px; font-weight: 600; color: var(--color-oscuro); margin-right: 4px;">
                <i class="fa-solid fa-filter"></i> Filtrar vista:
            </span>
            <button type="button" class="btn-filtro active" data-estado="TODOS" onclick="filtrarEstado('TODOS', this)">
                Todos (<?= count($filas) ?>)
            </button>
            <button type="button" class="btn-filtro" data-estado="LISTOS" onclick="filtrarEstado('LISTOS', this)" style="color: #059669;">
                Accionables (<?= $totalValidas ?>)
            </button>
            <button type="button" class="btn-filtro" data-estado="NUEVO" onclick="filtrarEstado('NUEVO', this)">
                Nuevos (<?= (int)$stats['nuevos'] ?>)
            </button>
            <?php if ((int)($stats['actualizar'] ?? 0) > 0): ?>
                <button type="button" class="btn-filtro" data-estado="ACTUALIZAR" onclick="filtrarEstado('ACTUALIZAR', this)" style="color: #0284c7;">
                    Por Actualizar (<?= (int)$stats['actualizar'] ?>)
                </button>
            <?php endif; ?>
            <?php if ((int)($stats['duplicados'] ?? 0) > 0): ?>
                <button type="button" class="btn-filtro" data-estado="EXISTENTE" onclick="filtrarEstado('EXISTENTE', this)" style="color: #b45309;">
                    En BD (<?= (int)$stats['duplicados'] ?>)
                </button>
            <?php endif; ?>
            <?php if ((int)$stats['errores'] > 0): ?>
                <button type="button" class="btn-filtro" data-estado="ERROR" onclick="filtrarEstado('ERROR', this)" style="color: #dc2626;">
                    Errores (<?= (int)$stats['errores'] ?>)
                </button>
            <?php endif; ?>
        </div>

        <div>
            <form id="form-ejecutar-carga" method="POST" action="?ruta=admin/importar/ejecutar" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin: 0;">
                <input type="hidden" name="accion" id="accion" value="cargar_datos">

                <div style="display: flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 8px;">
                    <label for="modo_duplicados" style="font-size: 12px; font-weight: 700; color: var(--color-oscuro); margin: 0; white-space: nowrap;">
                        <i class="fa-solid fa-sliders" style="color: var(--color-primario);"></i> Duplicados:
                    </label>
                    <select name="modo_duplicados" id="modo_duplicados" style="border: none; background: transparent; font-size: 12.5px; font-weight: 600; color: var(--color-oscuro); padding: 6px 4px; cursor: pointer; outline: none;">
                        <option value="omitir" <?= ($modo_duplicados ?? 'omitir') === 'omitir' ? 'selected' : '' ?>>🛡️ Omitir (Sin error)</option>
                        <option value="actualizar" <?= ($modo_duplicados ?? '') === 'actualizar' ? 'selected' : '' ?>>🔄 Sobrescribir</option>
                        <option value="estricto" <?= ($modo_duplicados ?? '') === 'estricto' ? 'selected' : '' ?>>⚠️ Modo estricto</option>
                    </select>
                </div>

                <button type="button" class="btn-primario" style="height: 40px; font-weight: 700; font-size: 14px;" onclick="establecerAccion('cargar_datos'); confirmarEjecucion()">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Cargar datos (<?= $totalValidas ?> accionables)
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Tabla de Previsualización Dinámica -->
<div class="panel" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto; max-height: 600px;">
        <table class="table" style="margin: 0;" id="tabla-preview">
            <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 2;">
                <tr>
                    <th style="width: 50px; text-align: center;"># Fila</th>
                    <?php foreach ($camposVisibles as $key => $config): ?>
                        <th><?= htmlspecialchars($config['label']) ?></th>
                    <?php endforeach; ?>
                    <th style="width: 140px; text-align: center;">Estado</th>
                    <th>Detalle Técnico</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filas as $f): 
                    $est = $f['estado'];
                    $esValida = in_array($est, ['NUEVO', 'COMPLETAR', 'ACTUALIZAR'], true);
                ?>
                    <tr class="fila-preview" data-estado="<?= $est ?>" data-valida="<?= $esValida ? '1' : '0' ?>" style="<?= $est === 'ERROR' ? 'background: #fef2f2;' : ($est === 'ACTUALIZAR' ? 'background: #f0f9ff;' : ($est === 'CONFLICTO' ? 'background: #fffbeb;' : '')) ?>">
                        <td style="text-align: center; color: var(--color-texto-secundario); font-size: 12px; font-family: monospace;">
                            <?= (int)$f['fila_excel'] ?>
                        </td>
                        
                        <?php foreach ($camposVisibles as $key => $config): ?>
                            <td>
                                <?= htmlspecialchars($f[$key] ?? '—') ?>
                            </td>
                        <?php endforeach; ?>

                        <td style="text-align: center;">
                            <?php if ($est === 'NUEVO'): ?>
                                <span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                                    <i class="fa-solid fa-plus" style="font-size: 9px;"></i> NUEVO
                                </span>
                            <?php elseif ($est === 'ACTUALIZAR'): ?>
                                <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                    <i class="fa-solid fa-arrows-rotate" style="font-size: 9px;"></i> ACTUALIZAR
                                </span>
                            <?php elseif ($est === 'EXISTENTE'): ?>
                                <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                    <i class="fa-solid fa-check-double" style="font-size: 9px;"></i> EN BD
                                </span>
                            <?php elseif ($est === 'COMPLETAR'): ?>
                                <span class="badge" style="background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                                    <i class="fa-solid fa-wrench" style="font-size: 9px;"></i> COMPLETAR
                                </span>
                            <?php elseif ($est === 'DUPLICADO'): ?>
                                <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                    <i class="fa-solid fa-clone" style="font-size: 9px;"></i> DUPLICADO
                                </span>
                            <?php elseif ($est === 'ERROR'): ?>
                                <span class="badge" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
                                    <i class="fa-solid fa-circle-xmark" style="font-size: 9px;"></i> ERROR
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 9px;"></i> <?= $est ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 12.5px; color: var(--color-texto-principal); line-height: 1.4;">
                            <?= htmlspecialchars($f['detalle'] ?? '') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.btn-filtro {
    border: 1px solid #cbd5e1;
    background: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--color-oscuro);
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-filtro:hover {
    background: #f1f5f9;
}
.btn-filtro.active {
    background: var(--color-primario);
    color: #fff !important;
    border-color: var(--color-primario);
}
</style>

<script>
function filtrarEstado(estado, btn) {
    document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const filas = document.querySelectorAll('.fila-preview');
    filas.forEach(f => {
        const estFila = f.getAttribute('data-estado');
        const esValida = f.getAttribute('data-valida') === '1';

        if (estado === 'TODOS') {
            f.style.display = '';
        } else if (estado === 'LISTOS') {
            f.style.display = esValida ? '' : 'none';
        } else {
            f.style.display = (estFila === estado) ? '' : 'none';
        }
    });
}

function confirmarEjecucion() {
    const totalValidas = <?= $totalValidas ?>;

    if (totalValidas === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No hay registros válidos',
            text: 'Todas las filas del archivo ya existen o presentan inconsistencias. No hay cambios pendientes por aplicar.',
            confirmButtonColor: '#3B0D8F'
        });
        return;
    }

    const nuevos = <?= (int)($stats['nuevos'] ?? 0) ?>;
    const actualizar = <?= (int)($stats['actualizar'] ?? 0) ?>;

    const selectModo = document.getElementById('modo_duplicados');
    const modoVal = selectModo ? selectModo.value : 'omitir';
    let avisoModo = '';
    if (modoVal === 'omitir') {
        avisoModo = '<div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 12px; margin-top: 10px; font-size: 12px; color: #166534;"><i class="fa-solid fa-shield-halved"></i> <strong>Parámetro de Duplicados:</strong> Omitir sin error. Si un código ya existe en BD (ej. <code>uq_asignatura_codigo</code>), se conservará el de BD y se importarán todos los demás registros sin revertir la transacción.</div>';
    } else if (modoVal === 'actualizar') {
        avisoModo = '<div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 12px; margin-top: 10px; font-size: 12px; color: #1e40af;"><i class="fa-solid fa-arrows-rotate"></i> <strong>Parámetro de Duplicados:</strong> Sobrescribir existentes. Los registros con el mismo código serán actualizados con los nuevos datos.</div>';
    } else {
        avisoModo = '<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 12px; margin-top: 10px; font-size: 12px; color: #991b1b;"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Parámetro de Duplicados:</strong> Modo estricto. Cualquier duplicado o conflicto provocará la reversión total de la transacción.</div>';
    }

    let resumenTexto = `<p style="margin-bottom: 12px;">Se procesarán <strong>${totalValidas}</strong> registros en la base de datos:</p>`;
    resumenTexto += `<ul style="margin: 0 0 12px 18px; padding: 0; color: #475569; font-size: 13px;">`;
    if (nuevos > 0) resumenTexto += `<li><strong>${nuevos}</strong> registro(s) nuevo(s) a insertar.</li>`;
    if (actualizar > 0) resumenTexto += `<li><strong>${actualizar}</strong> registro(s) con divergencia de datos/código habilitados para sobreescribir.</li>`;
    resumenTexto += `</ul>`;
    resumenTexto += `<p style="color: #64748b; font-size: 12px; margin: 0;">Los registros idénticos o con errores serán preservados u omitidos sin alteraciones.</p>`;
    resumenTexto += avisoModo;

    Swal.fire({
        title: '¿Confirmar carga y sincronización?',
        html: `<div style="text-align: left; font-size: 14px; line-height: 1.5; color: #334155;">${resumenTexto}</div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-cloud-arrow-up"></i> Sí, cargar datos',
        cancelButtonText: 'Revisar más',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando importación...',
                text: 'Por favor espera mientras se registran los datos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById('form-ejecutar-carga').submit();
                }
            });
        }
    });
}

function establecerAccion(modo) {
    document.getElementById('accion').value = modo;
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
