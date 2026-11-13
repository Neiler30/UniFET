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
.module-tab{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border:1px solid var(--color-borde);border-radius:8px;background:#fff;color:var(--color-texto-secundario);font-weight:600;font-size:13px;transition:all .2s;text-decoration:none;}
.module-tab:hover{background:rgba(59,13,143,.04);border-color:var(--color-primario);color:var(--color-primario);text-decoration:none;}
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
    opacity: 0.08;
    pointer-events: none;
}
.usuarios-hero h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; position: relative; z-index: 1; }
.usuarios-hero p { color: rgba(255,255,255,.82); font-size: 13.5px; margin: 0; position: relative; z-index: 1; }
.usuarios-hero .hero-actions-wrap { display: flex; align-items: center; gap: 14px; z-index: 2; flex-wrap: wrap; }
.usuarios-hero .hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px;
    padding: 8px 16px;
    text-align: center;
    backdrop-filter: blur(4px);
}
.usuarios-hero .hero-stat strong { display: block; font-size: 18px; font-weight: 700; }
.usuarios-hero .hero-stat span { font-size: 11px; color: rgba(255,255,255,.75); text-transform: uppercase; }
.btn-hero-nuevo {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    color: var(--color-primario);
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transition: all .2s;
}
.btn-hero-nuevo:hover {
    background: #f8fafc;
    color: var(--color-primario);
    transform: translateY(-1px);
    text-decoration: none;
}

/* ── Tabla moderna de usuarios ── */
.usuarios-panel {
    background: #fff;
    border: 1px solid var(--color-borde);
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: var(--sombra-tarjeta);
}
.usuarios-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.usuarios-table thead th {
    background: #F8FAFC;
    color: var(--color-oscuro);
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 14px 16px;
    border-bottom: 2px solid var(--color-borde);
}
.usuarios-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #F1F5F9;
    font-size: 13.5px;
    color: var(--color-oscuro);
    vertical-align: middle;
}
.usuarios-table tbody tr:hover td {
    background: rgba(59,13,143,.02);
}
.user-avatar-mini {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(59,13,143,0.1);
    color: var(--color-primario);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    margin-right: 10px;
}
.rol-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #F1F5F9;
    color: #334155;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
}
.badge-temp-si {
    background: #FEF3C7;
    color: #92400E;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}
.badge-temp-no {
    background: #E2E8F0;
    color: #475569;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .usuarios-hero { flex-direction: column; align-items: stretch; padding: 22px; }
    .usuarios-hero .hero-actions-wrap { flex-wrap: wrap; }
}
</style>

<!-- Hero Banner -->
<div class="usuarios-hero">
    <div>
        <h1><i class="fa-solid fa-users-gear" style="margin-right:10px;opacity:.85;"></i> Gestión de Usuarios</h1>
        <p>Cuentas administrativas, líderes de programa y solicitudes preparadas para autorregistro.</p>
    </div>
    <div class="hero-actions-wrap">
        <div class="hero-stat">
            <strong><?= count($usuarios ?? []) ?></strong>
            <span>Usuarios</span>
        </div>
        <div class="hero-stat">
            <strong><?= count($pendientes ?? []) ?></strong>
            <span>Pendientes</span>
        </div>
        <a class="btn-hero-nuevo" href="?ruta=admin/sistema/usuarios/crear">
            <i class="fa-solid fa-plus"></i> Nuevo Usuario
        </a>
    </div>
</div>

<nav class="module-tabs">
    <a class="module-tab <?= $tab !== 'pendientes' ? 'active' : '' ?>" href="?ruta=admin/sistema/usuarios"><i class="fa-solid fa-users"></i> Usuarios Activos</a>
    <a class="module-tab <?= $tab === 'pendientes' ? 'active' : '' ?>" href="?ruta=admin/sistema/usuarios&tab=pendientes"><i class="fa-solid fa-user-clock"></i> Solicitudes pendientes (<?= count($pendientes ?? []) ?>)</a>
</nav>

<?php if ($tab === 'pendientes'): ?>
    <div class="usuarios-panel table-wrap">
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>Correo Electrónico</th>
                    <th>Programas Asignados</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pendientes)): ?>
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--color-texto-secundario);">No hay solicitudes pendientes de aprobación.</td></tr>
            <?php endif; ?>
            <?php foreach ($pendientes as $u): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;">
                            <span class="user-avatar-mini"><?= strtoupper(substr($u['nombre'] ?? 'U', 0, 1) . substr($u['apellido'] ?? 'S', 0, 1)) ?></span>
                            <strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong>
                        </div>
                    </td>
                    <td><i class="fa-regular fa-envelope" style="color:var(--color-primario);margin-right:6px;font-size:12px;"></i><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= htmlspecialchars($u['programas'] ?? '-') ?></td>
                    <td><span class="badge pendiente"><?= htmlspecialchars($u['estado']) ?></span></td>
                    <td style="text-align:right;">
                        <span class="actions-inline">
                            <form action="?ruta=admin/sistema/usuarios/aprobar&id=<?= (int)$u['id_usuario'] ?>" method="POST" onsubmit="return confirmarSolicitudUsuario(event, this, 'Aprobar solicitud', 'Se activará el líder y se generará una contraseña temporal.', 'Sí, aprobar', 'warning');">
                                <button class="btn-accion btn-accion-aceptar" title="Aprobar" type="submit"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="?ruta=admin/sistema/usuarios/rechazar&id=<?= (int)$u['id_usuario'] ?>" method="POST" onsubmit="return confirmarSolicitudUsuario(event, this, 'Rechazar solicitud', 'El usuario quedará inactivo y no podrá ingresar.', 'Sí, rechazar', 'error');">
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
    <div class="usuarios-panel table-wrap">
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Rol en el Sistema</th>
                    <th>Programas Liderados</th>
                    <th>Temporal</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--color-texto-secundario);">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;">
                            <span class="user-avatar-mini"><?= strtoupper(substr($u['nombre'] ?? 'U', 0, 1) . substr($u['apellido'] ?? 'S', 0, 1)) ?></span>
                            <div>
                                <strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong>
                            </div>
                        </div>
                    </td>
                    <td><i class="fa-regular fa-envelope" style="color:var(--color-primario);margin-right:6px;font-size:12px;"></i><?= htmlspecialchars($u['correo']) ?></td>
                    <td>
                        <span class="rol-badge">
                            <i class="fa-solid <?= ($u['rol'] === 'ADMINISTRADOR') ? 'fa-shield-halved' : 'fa-graduation-cap' ?>"></i>
                            <?= htmlspecialchars($u['rol']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($u['programas'] ?? '-') ?></td>
                    <td><?= !empty($u['password_temporal']) ? '<span class="badge-temp-si">TEMPORAL</span>' : '<span class="badge-temp-no">DEFINITIVA</span>' ?></td>
                    <td><span class="badge <?= strtolower($u['estado']) ?>"><?= htmlspecialchars($u['estado']) ?></span></td>
                    <td style="text-align:right;">
                        <?php $this->render('elementos_compartidos/botones_accion', ['ruta_editar' => '?ruta=admin/sistema/usuarios/editar&id=' . (int)$u['id_usuario'], 'ruta_desactivar' => '?ruta=admin/sistema/usuarios/desactivar&id=' . (int)$u['id_usuario'], 'activo' => $u['estado'] === 'ACTIVO']); ?>
                    </td>
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
