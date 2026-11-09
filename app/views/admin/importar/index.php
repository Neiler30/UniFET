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
                        <option value="actualizar" selected>🔄 Sobrescribir / Actualizar registros existentes</option>
                        <option value="omitir">🛡️ Omitir duplicados y continuar (No arrojar error)</option>
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

    <!-- Tarjeta 2: EXPORTAR (BLOQUEADO / EN DESARROLLO) -->
    <div class="panel" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #f59e0b; position: relative; background: #fafbfd;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-file-export"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--color-oscuro); margin: 0;">Exportar Datos</h2>
                        <span style="font-size: 12px; color: var(--color-texto-secundario);">Descarga en formato compatible</span>
                    </div>
                </div>
                <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 99px;">
                    <i class="fa-solid fa-code"></i> EN DESARROLLO
                </span>
            </div>

            <!-- Banner Explicativo de En Desarrollo -->
            <div style="background: #fffbeb; border: 1.5px dashed #f59e0b; border-radius: 10px; padding: 14px; margin: 14px 0 16px 0; display: flex; align-items: flex-start; gap: 12px;">
                <i class="fa-solid fa-person-digging" style="color: #d97706; font-size: 20px; margin-top: 2px;"></i>
                <div>
                    <strong style="color: #92400e; font-size: 13.5px; display: block;">Módulo de Exportación en Construcción</strong>
                    <p style="color: #b45309; font-size: 12.5px; margin: 3px 0 0 0; line-height: 1.45;">
                        La herramienta de exportación de asignaturas y mallas curriculares a formato Excel (.xlsx) se encuentra actualmente en desarrollo y optimización de rendimiento.
                    </p>
                </div>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
                El motor de <strong>Importación de Datos</strong> se encuentra 100% operativo y listo para recibir y validar tus plantillas curriculares.
            </p>

            <button type="button" class="btn-demo" disabled style="width: 100%; justify-content: center; height: 44px; font-size: 14px; font-weight: 600; opacity: 0.65; cursor: not-allowed; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Función de Exportar en Desarrollo
            </button>
        </div>

        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: #94a3b8;">
                <i class="fa-solid fa-circle-exclamation" style="color:#f59e0b;"></i> Solo importación activa por el momento
            </span>
            <span style="font-size: 12px; font-weight: 700; color: #b45309;">Próxima actualización</span>
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
