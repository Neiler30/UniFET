<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Usuarios']);
$this->render('elementos_compartidos/sidebar_admin');
$tab = $_GET['tab'] ?? 'usuarios';
$this->render('elementos_compartidos/tabs', ['tabs' => [
    ['etiqueta' => 'Personalizacion', 'ruta' => 'admin/sistema/personalizacion', 'activa' => false],
    ['etiqueta' => 'Usuarios', 'ruta' => 'admin/sistema/usuarios', 'activa' => true],
    ['etiqueta' => 'Lideres de Programa', 'ruta' => 'admin/sistema/lideres', 'activa' => false],
]]);
?>

<style>
.module-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}
.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px}
.module-tab.active{background:var(--color-primario);border-color:var(--color-primario);color:#fff}
.table-wrap{overflow-x:auto}
.actions-inline{display:inline-flex;justify-content:flex-end;gap:6px;align-items:center}

/* ── Hero banner para Usuarios ── */
.usuarios-hero {
    background: linear-gradient(135deg, var(--color-primario) 0%, #200654 100%);
    color: #fff;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 22px rgba(59, 13, 143, 0.16);
}
.usuarios-hero::after {
    content: '';
    position: absolute;
    right: -20px;
    bottom: -30px;
    width: 220px;
    height: 220px;
    background-image: url('assets/img/isotipo-blanco.png');
    background-repeat: no-repeat;
    background-size: contain;
    opacity: 0.07;
    pointer-events: none;
}
.usuarios-hero h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; }
.usuarios-hero p { color: rgba(255,255,255,.82); font-size: 14px; margin: 0; }
.usuarios-hero .hero-stats { display: flex; gap: 14px; z-index: 2; }
.usuarios-hero .hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px;
    padding: 10px 18px;
    text-align: center;
}
.usuarios-hero .hero-stat strong { display: block; font-size: 22px; }
.usuarios-hero .hero-stat span { font-size: 11px; color: rgba(255,255,255,.7); }

@media (max-width: 768px) {
    .usuarios-hero { flex-direction: column; align-items: stretch; padding: 20px; }
    .usuarios-hero .hero-stats { flex-wrap: wrap; }
}
</style>

<!-- Hero Banner -->
<div class="usuarios-hero">
    <div>
        <h1><i class="fa-solid fa-users-gear" style="margin-right:10px;opacity:.85;"></i> Gestión de Usuarios</h1>
        <p>Cuentas administrativas, líderes de programa y solicitudes preparadas para autorregistro.</p>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong><?= count($usuarios ?? []) ?></strong>
            <span>Usuarios</span>
        </div>
        <div class="hero-stat">
            <strong><?= count($pendientes ?? []) ?></strong>
            <span>Pendientes</span>
        </div>
    </div>
</div>

<nav class="module-tabs">
    <a class="module-tab <?= $tab !== 'pendientes' ? 'active' : '' ?>" href="?ruta=admin/sistema/usuarios"><i class="fa-solid fa-users"></i> Usuarios</a>
    <a class="module-tab <?= $tab === 'pendientes' ? 'active' : '' ?>" href="?ruta=admin/sistema/usuarios&tab=pendientes"><i class="fa-solid fa-user-clock"></i> Solicitudes pendientes (<?= count($pendientes ?? []) ?>)</a>
</nav>

<?php if ($tab === 'pendientes'): ?>
    <div class="panel table-wrap">
        <table class="table">
            <thead><tr><th>Solicitante</th><th>Correo</th><th>Programas</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            <?php if (empty($pendientes)): ?><tr><td colspan="5" style="text-align:center;padding:24px;">No hay solicitudes pendientes.</td></tr><?php endif; ?>
            <?php foreach ($pendientes as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong></td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= htmlspecialchars($u['programas'] ?? '-') ?></td>
                    <td><span class="badge pendiente"><?= htmlspecialchars($u['estado']) ?></span></td>
                    <td style="text-align:right;">
                        <span class="actions-inline">
                            <form action="?ruta=admin/sistema/usuarios/aprobar&id=<?= (int)$u['id_usuario'] ?>" method="POST" onsubmit="return confirmarSolicitudUsuario(event, this, 'Aprobar solicitud', 'Se activara el lider y se generara una contrasena temporal.', 'Si, aprobar', 'warning');">
                                <button class="btn-accion btn-accion-aceptar" title="Aprobar" type="submit"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="?ruta=admin/sistema/usuarios/rechazar&id=<?= (int)$u['id_usuario'] ?>" method="POST" onsubmit="return confirmarSolicitudUsuario(event, this, 'Rechazar solicitud', 'El usuario quedara inactivo y no podra ingresar.', 'Si, rechazar', 'error');">
                                <button class="btn-accion btn-accion-eliminar" title="Rechazar" type="submit"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="panel table-wrap">
        <table class="table">
            <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Programas liderados</th><th>Temporal</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            <?php if (empty($usuarios)): ?><tr><td colspan="7" style="text-align:center;">No hay usuarios registrados.</td></tr><?php endif; ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong></td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><span class="code-pill"><?= htmlspecialchars($u['rol']) ?></span></td>
                    <td><?= htmlspecialchars($u['programas'] ?? '-') ?></td>
                    <td><?= !empty($u['password_temporal']) ? '<span class="badge pendiente">SI</span>' : '<span class="badge activo">NO</span>' ?></td>
                    <td><span class="badge <?= strtolower($u['estado']) ?>"><?= htmlspecialchars($u['estado']) ?></span></td>
                    <td style="text-align:right;"><?php $this->render('elementos_compartidos/botones_accion', ['ruta_editar' => '?ruta=admin/sistema/usuarios/editar&id=' . (int)$u['id_usuario'], 'ruta_desactivar' => '?ruta=admin/sistema/usuarios/desactivar&id=' . (int)$u['id_usuario'], 'activo' => $u['estado'] === 'ACTIVO']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<script>
function confirmarSolicitudUsuario(event, form, title, text, confirmText, icon) {
    event.preventDefault();
    Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3B0D8F',
        cancelButtonColor: '#E2E5E9',
        customClass: { cancelButton: 'swal-btn-cancelar' }
    }).then((resultado) => {
        if (resultado.isConfirmed) form.submit();
    });
    return false;
}
</script>
<?php $this->render('elementos_compartidos/footer'); ?>
