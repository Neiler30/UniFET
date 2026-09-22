<?php $this->render('shared/header', ['titulo' => $titulo ?? 'Importar/Exportar']); $this->render('shared/sidebar_admin'); ?>
<div class="header-acciones"><div><h1>Importar / Exportar</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Valida archivos antes de confirmar cargas masivas.</p></div></div>
<div class="panel">
    <?php if (!$phpspreadsheet): ?><div class="badge pendiente" style="margin-bottom:16px;">PhpSpreadsheet no instalado. TODO: composer require phpoffice/phpspreadsheet</div><?php endif; ?>
    <form action="?ruta=admin/importar/validar" method="POST" enctype="multipart/form-data" class="responsive-form-grid">
        <div class="form-group"><label>Archivo Excel</label><input type="file" class="form-control" name="archivo" accept=".xlsx,.xls,.csv"></div>
        <div class="form-group"><label>Entidad</label><select class="form-control" name="tipo_entidad"><?php foreach (['DOCENTE','ASIGNATURA','ESPACIO','PROGRAMA'] as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><button class="btn-primario"><i class="fa-solid fa-check-double"></i> Validar</button></div>
    </form>
</div>
<?php $this->render('shared/footer'); ?>
