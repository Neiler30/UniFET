<?php $this->render('shared/header', ['titulo' => $titulo ?? 'Usuario']); $this->render('shared/sidebar_admin'); $esEdicion = !empty($usuario['id_usuario']); ?>
<div class="header-acciones"><h1><?= htmlspecialchars($titulo) ?></h1><a class="btn-demo" href="?ruta=admin/sistema/usuarios">Volver</a></div>
<div class="panel"><form method="POST" action="?ruta=admin/sistema/usuarios/<?= $esEdicion ? 'actualizar' : 'guardar' ?>" style="display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:16px;">
<?php if ($esEdicion): ?><input type="hidden" name="id_usuario" value="<?= (int)$usuario['id_usuario'] ?>"><?php endif; ?>
<div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" id="usuario-nombre" required value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"></div><div class="form-group"><label>Apellido</label><input class="form-control" name="apellido" id="usuario-apellido" required value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>"></div>
<div class="form-group"><label>Correo</label><input type="email" class="form-control" name="correo" id="usuario-correo" required value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>"><small id="correo-help" style="display:block;margin-top:6px;color:var(--color-texto-secundario);"></small></div><div class="form-group" id="password-field"><label>Contrasena <?= $esEdicion ? '(opcional)' : '' ?></label><input type="password" class="form-control" name="password" <?= $esEdicion ? '' : 'required' ?>><small id="password-help" style="display:block;margin-top:6px;color:var(--color-texto-secundario);"></small></div>
<div class="form-group"><label>Rol</label><select class="form-control" name="rol" id="rol-usuario"><?php foreach (['ADMINISTRADOR','LIDER_PROGRAMA'] as $r): ?><option value="<?= $r ?>" <?= (($usuario['rol'] ?? 'LIDER_PROGRAMA') === $r) ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Estado</label><select class="form-control" name="estado"><?php foreach (['ACTIVO','INACTIVO','PENDIENTE'] as $e): ?><option value="<?= $e ?>" <?= (($usuario['estado'] ?? 'ACTIVO') === $e) ? 'selected' : '' ?>><?= $e ?></option><?php endforeach; ?></select></div>
<div class="form-group" style="grid-column:1/-1;"><label>Programas si es lider</label><select class="form-control" name="programas[]" multiple size="7"><?php foreach ($programas as $p): ?><option value="<?= (int)$p['id_programa'] ?>" <?= in_array((int)$p['id_programa'], $programasAsignados ?? [], true) ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?></select></div>
<div class="form-group" style="grid-column:1/-1;"><button class="btn-primario">Guardar usuario</button></div>
</form></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const esEdicion = <?= $esEdicion ? 'true' : 'false' ?>;
    const rol = document.getElementById('rol-usuario');
    const passwordInput = document.querySelector('#password-field input[name="password"]');
    const help = document.getElementById('password-help');
    const nombre = document.getElementById('usuario-nombre');
    const apellido = document.getElementById('usuario-apellido');
    const correo = document.getElementById('usuario-correo');
    const correoHelp = document.getElementById('correo-help');
    let correoTouched = esEdicion;
    const slug = value => String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9]+/g, '.')
        .replace(/^\.+|\.+$/g, '');
    const syncCorreo = () => {
        if (correoTouched || rol.value !== 'LIDER_PROGRAMA') return;
        const nom = slug(nombre.value).split('.')[0] || '';
        const ape = slug(apellido.value).split('.')[0] || '';
        correo.value = nom && ape ? `${nom}.${ape}@unicaribe.edu.co` : '';
    };
    const syncPassword = () => {
        if (!esEdicion && rol.value === 'LIDER_PROGRAMA') {
            passwordInput.required = false;
            passwordInput.value = '';
            passwordInput.disabled = true;
            help.textContent = 'El sistema generara una contrasena temporal y la mostrara una sola vez al guardar.';
            correoHelp.textContent = 'El correo se genera automaticamente con dominio @unicaribe.edu.co, pero puedes editarlo.';
            syncCorreo();
        } else {
            passwordInput.disabled = false;
            passwordInput.required = !esEdicion;
            help.textContent = esEdicion ? 'Dejalo vacio si no deseas cambiarla.' : '';
            correoHelp.textContent = '';
        }
    };
    rol.addEventListener('change', syncPassword);
    [nombre, apellido].forEach(input => input.addEventListener('input', () => { syncCorreo(); }));
    correo.addEventListener('input', () => { correoTouched = true; });
    syncPassword();
});
</script>
<?php $this->render('shared/footer'); ?>
