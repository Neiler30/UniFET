<?php
$this->render('shared/header', ['titulo' => $titulo ?? 'Sistema']);
$this->render('shared/sidebar_admin');
$this->render('shared/tabs', ['tabs' => [
    ['etiqueta' => 'Personalizacion', 'ruta' => 'admin/sistema/personalizacion', 'activa' => true],
    ['etiqueta' => 'Usuarios', 'ruta' => 'admin/sistema/usuarios', 'activa' => false],
    ['etiqueta' => 'Lideres de Programa', 'ruta' => 'admin/sistema/lideres', 'activa' => false],
]]);
?>
<div class="header-acciones"><div><h1>Personalizacion institucional</h1><p style="color:var(--color-texto-secundario);font-size:13px;">Identidad visual y datos visibles del sistema.</p></div></div>
<div class="panel">
<form method="POST" action="?ruta=admin/sistema/personalizacion/guardar" style="display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:16px;">
<div class="form-group"><label>Nombre institucion</label><input class="form-control" name="nombre" required value="<?= htmlspecialchars($institucion['nombre'] ?? '') ?>"></div>
<div class="form-group"><label>Nombre del sistema</label><input class="form-control" name="nombre_sistema" value="<?= htmlspecialchars($institucion['nombre_sistema'] ?? 'UniFET') ?>"></div>
<div class="form-group" style="grid-column:1/-1;"><label>Lema</label><input class="form-control" name="lema" value="<?= htmlspecialchars($institucion['lema'] ?? '') ?>"></div>
<div class="form-group"><label>Logo URL</label><input class="form-control" name="logo_url" value="<?= htmlspecialchars($institucion['logo_url'] ?? '') ?>"></div>
<div class="form-group"><label>Favicon URL</label><input class="form-control" name="favicon_url" value="<?= htmlspecialchars($institucion['favicon_url'] ?? '') ?>"></div>
<div class="form-group"><label>Color principal</label><input type="color" class="form-control" name="color_principal" value="<?= htmlspecialchars($institucion['color_principal'] ?? '#3B0D8F') ?>"></div>
<div class="form-group"><label>Color secundario</label><input type="color" class="form-control" name="color_secundario" value="<?= htmlspecialchars($institucion['color_secundario'] ?? '#0F9D58') ?>"></div>
<div class="form-group"><label>Color acento</label><input type="color" class="form-control" name="color_acento" value="<?= htmlspecialchars($institucion['color_acento'] ?? '#F5B301') ?>"></div>
<div class="form-group" style="grid-column:1/-1;"><button class="btn-primario">Guardar personalizacion</button></div>
</form></div>
<?php $this->render('shared/footer'); ?>
