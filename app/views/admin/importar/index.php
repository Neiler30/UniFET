<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Importar / Exportar']);
$this->render('elementos_compartidos/sidebar_admin');
?>

<div class="header-acciones">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--color-oscuro);">Importar / Exportar Datos</h1>
        <p style="color: var(--color-texto-secundario); font-size: 13.5px; margin-top: 4px;">
            Gestión masiva y sincronización curricular mediante archivos Microsoft Excel (.xlsx / .xls).
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a class="btn-demo" href="?ruta=admin/importar/plantilla" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Descargar Plantilla Oficial
        </a>
    </div>
</div>

<?php if (!empty($resultado)): 
    $entityNombre = htmlspecialchars($resultado['entity_nombre'] ?? 'Registros');
    $creados      = (int)($resultado['creados']              ?? $resultado['asignaturas_creadas'] ?? 0);
    $actualizados = (int)($resultado['actualizados']         ?? $resultado['asociaciones_creadas'] ?? 0);
    $existentes   = (int)($resultado['existentes_omitidas']  ?? 0);
    $omitidas     = (int)($resultado['filas_omitidas']       ?? 0);
?>
    <!-- Resumen de última importación exitosa -->
    <div class="panel" style="margin-bottom: 24px; border-left: 5px solid #10b981; background: #f0fdf4;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="color: #065f46; font-size: 17px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check"></i> Carga Masiva Completada — <?= $entityNombre ?>
                </h3>
                <p style="color: #047857; font-size: 13px; margin: 0;">
                    Archivo procesado: <strong><?= htmlspecialchars($resultado['nombre_archivo'] ?? '') ?></strong> (<?= (int)($resultado['total_procesadas'] ?? 0) ?> filas evaluadas)
                </p>
            </div>
            <?php if (!empty($resultado['errores']) && count($resultado['errores']) > 0): ?>
                <a href="?ruta=admin/importar/reporte_errores" class="btn-demo" style="border-color: #f59e0b; color: #b45309; background: #fff;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> Descargar Reporte de Inconsistencias (<?= count($resultado['errores']) ?>)
                </a>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-top: 16px;">
            <div style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #a7f3d0;">
                <span style="font-size: 12px; color: #047857; text-transform: uppercase; font-weight: 600;"><?= $entityNombre ?> Nuevos</span>
                <strong style="display: block; font-size: 22px; color: #065f46; margin-top: 2px;">+<?= $creados ?></strong>
            </div>
            <?php if ($actualizados > 0): ?>
            <div style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #bae6fd;">
                <span style="font-size: 12px; color: #0369a1; text-transform: uppercase; font-weight: 600;">Actualizados</span>
                <strong style="display: block; font-size: 22px; color: #0284c7; margin-top: 2px;">~<?= $actualizados ?></strong>
            </div>
            <?php endif; ?>
            <div style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <span style="font-size: 12px; color: var(--color-texto-secundario); text-transform: uppercase; font-weight: 600;">Existentes Conservados</span>
                <strong style="display: block; font-size: 22px; color: var(--color-oscuro); margin-top: 2px;"><?= $existentes ?></strong>
            </div>
            <?php if (!empty($resultado['duplicados_omitidos']) && (int)$resultado['duplicados_omitidos'] > 0): ?>
            <div style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #c7d2fe;">
                <span style="font-size: 12px; color: #4338ca; text-transform: uppercase; font-weight: 600;">Duplicados Omitidos</span>
                <strong style="display: block; font-size: 22px; color: #3730a3; margin-top: 2px;"><?= (int)$resultado['duplicados_omitidos'] ?></strong>
            </div>
            <?php endif; ?>
            <div style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #fed7aa;">
                <span style="font-size: 12px; color: #c2410c; text-transform: uppercase; font-weight: 600;">Inconsistencias Omitidas</span>
                <strong style="display: block; font-size: 22px; color: #ea580c; margin-top: 2px;"><?= $omitidas ?></strong>
            </div>
        </div>
    </div>
<?php endif; ?>


<!-- Selector de Entidad Principal -->
<div class="panel" style="margin-bottom: 24px;">
    <div style="max-width: 480px;">
        <label for="tipo_entidad_selector" style="font-weight: 700; font-size: 14px; color: var(--color-oscuro); display: block; margin-bottom: 6px;">
            <i class="fa-solid fa-database" style="color: var(--color-primario);"></i> Tipo de Información Académica
        </label>
        <select id="tipo_entidad_selector" class="form-control" style="font-weight: 600; font-size: 14px; padding: 8px 12px;" onchange="cambiarTipoEntidad(this.value)">
            <option value="AUTO" selected>Auto-detectar (Recomendado)</option>
            <option value="ASIGNATURA">Asignaturas y Malla Curricular</option>
            <option value="PROGRAMA">Programas Académicos</option>
            <option value="DOCENTE">Docentes</option>
            <option value="ESPACIO">Espacios Físicos</option>
            <option value="OFERTA">Oferta Académica</option>
        </select>
        <p style="font-size: 12px; color: var(--color-texto-secundario); margin: 6px 0 0 0;">
            El Motor de Importación Universal detectará automáticamente la estructura de tu archivo Excel sin importar el orden de las columnas.
        </p>
    </div>
</div>

<!-- Grid de Módulos: IMPORTAR vs EXPORTAR -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px;">

    <!-- Tarjeta 1: IMPORTAR -->
    <div class="panel" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--color-primario);">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: #ede9fe; color: var(--color-primario); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-file-import"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: var(--color-oscuro); margin: 0;">Importar Datos</h2>
                    <span style="font-size: 12px; color: var(--color-texto-secundario);">Carga masiva desde archivo Excel</span>
                </div>
            </div>

            <p style="font-size: 13.5px; color: var(--color-texto-secundario); line-height: 1.5; margin: 12px 0 16px 0;">
                Sube tu archivo con las columnas oficiales (<code>PROGRAMA</code>, <code>ASIGNATURA</code>, <code>CODIGO</code>, <code>SEMESTRE</code>, etc.). 
                El sistema analizará cada fila, detectará registros existentes y te presentará una <strong>vista previa de validación</strong> antes de aplicar cualquier cambio.
            </p>

            <form id="form-importar" action="?ruta=admin/importar/validar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_entidad" id="input_tipo_entidad_form" value="AUTO">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 600; font-size: 13px; color: var(--color-oscuro); display: block; margin-bottom: 6px;">
                        Seleccionar Archivo Excel (.xlsx / .xls) <span style="color:red;">*</span>
                    </label>
                    <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; background: #f8fafc; cursor: pointer;" onclick="document.getElementById('input-archivo').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--color-primario); margin-bottom: 8px; display: block;"></i>
                        <span id="texto-archivo" style="font-size: 13.5px; color: var(--color-oscuro); font-weight: 600; display: block;">
                            Haz clic para examinar o arrastra el archivo aquí
                        </span>
                        <span style="font-size: 12px; color: var(--color-texto-secundario); display: block; margin-top: 4px;">
                            Soporta formatos .xlsx y .xls hasta 20 MB
                        </span>
                        <input type="file" id="input-archivo" name="archivo" accept=".xlsx,.xls" style="display: none;" required onchange="archivoSeleccionado(this)">
                    </div>
                </div>

                <!-- Parámetro de Manejo de Duplicados / Conflictos -->
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="select_modo_duplicados" style="font-weight: 600; font-size: 13px; color: var(--color-oscuro); display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span><i class="fa-solid fa-sliders" style="color: var(--color-primario);"></i> Manejo de Códigos Duplicados</span>
                        <span style="font-size: 11px; color: #10b981; font-weight: 700;">Recomendado</span>
                    </label>
                    <select name="modo_duplicados" id="select_modo_duplicados" class="form-control" style="font-size: 13px; padding: 8px 12px; font-weight: 600;">
                        <option value="omitir" selected>🛡️ Omitir duplicados y continuar (No arrojar error)</option>
                        <option value="actualizar">🔄 Sobrescribir / Actualizar registros existentes</option>
                        <option value="estricto">⚠️ Modo estricto (Revertir todo si hay error)</option>
                    </select>
                    <small style="color: var(--color-texto-secundario); font-size: 11.5px; display: block; margin-top: 4px;">
                        Evita errores de clave duplicada (como <code>uq_asignatura_codigo</code>) omitiendo o actualizando los registros existentes de forma segura.
                    </small>
                </div>

                <button type="submit" class="btn-primario" style="width: 100%; justify-content: center; height: 42px; font-size: 14px;">
                    <i class="fa-solid fa-magnifying-glass-chart"></i> Previsualizar y Validar Archivo
                </button>
            </form>
        </div>

        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: var(--color-texto-secundario);">
                <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> Idempotente: seguro contra duplicados
            </span>
            <a href="?ruta=admin/importar/plantilla" style="font-size: 12.5px; font-weight: 600; color: var(--color-primario);">
                Descargar plantilla <i class="fa-solid fa-download"></i>
            </a>
        </div>
    </div>

    <!-- Tarjeta 2: EXPORTAR -->
    <div class="panel" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #10b981;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-file-export"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: var(--color-oscuro); margin: 0;">Exportar Datos</h2>
                    <span style="font-size: 12px; color: var(--color-texto-secundario);">Descarga en formato compatible</span>
                </div>
            </div>

            <p style="font-size: 13.5px; color: var(--color-texto-secundario); line-height: 1.5; margin: 12px 0 16px 0;">
                Genera una hoja de cálculo con la totalidad de asignaturas registradas y sus respectivas asociaciones curriculares (programa, semestre romano, créditos, nivel y área académica).
            </p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: var(--color-texto-secundario); line-height: 1.6;">
                    <li>Mantiene una fila por cada asociación <code>asignatura_programa</code>.</li>
                    <li>Semestres formateados en números romanos estándar (I - X).</li>
                    <li>Créditos con formato de dos dígitos (02, 03, 04).</li>
                    <li>Listo para editarse y reimportarse en cualquier momento.</li>
                </ul>
            </div>

            <a href="?ruta=admin/importar/exportar" class="btn-demo" style="width: 100%; justify-content: center; height: 42px; font-size: 14px; font-weight: 600; border-color: #10b981; color: #047857; background: #fff;">
                <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Exportar Asignaturas a Excel (.xlsx)
            </a>
        </div>

        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: var(--color-texto-secundario);">
                <i class="fa-solid fa-clock-rotate-left"></i> Incluye datos actualizados en tiempo real
            </span>
            <span style="font-size: 12px; font-weight: 600; color: #059669;">Formato oficial UniFET</span>
        </div>
    </div>
</div>

<script>
function cambiarTipoEntidad(valor) {
    document.getElementById('input_tipo_entidad_form').value = valor;
}

function archivoSeleccionado(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const span = document.getElementById('texto-archivo');
        if (span) {
            span.innerHTML = `<span style="color:#10b981;"><i class="fa-solid fa-file-circle-check"></i> ${file.name}</span> (${(file.size / 1024).toFixed(1)} KB)`;
        }
    }
}
</script>

<?php $this->render('elementos_compartidos/footer'); ?>
