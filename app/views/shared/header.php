<?php
$institucion_ui = null;
try {
    if (class_exists('\App\Models\Institucion')) {
        $institucion_ui = \App\Models\Institucion::obtener(1);
    }
} catch (\Throwable $e) {
    $institucion_ui = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'UniFET') ?></title>
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/design-tokens.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="icon" type="image/png" href="assets/img/isotipo-color.png">
    <!-- SweetAlert2 vía CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        <?php if (!empty($institucion_ui)): ?>
        :root {
            <?php if (!empty($institucion_ui['color_principal'])): ?>--color-primario: <?= htmlspecialchars($institucion_ui['color_principal']) ?>;<?php endif; ?>
            <?php if (!empty($institucion_ui['color_secundario'])): ?>--color-secundario: <?= htmlspecialchars($institucion_ui['color_secundario']) ?>;<?php endif; ?>
            <?php if (!empty($institucion_ui['color_acento'])): ?>--color-acento: <?= htmlspecialchars($institucion_ui['color_acento']) ?>;<?php endif; ?>
        }
        <?php endif; ?>
        .layout-app {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: var(--color-primario);
            color: var(--color-blanco);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            align-self: flex-start;
        }
        .sidebar-backdrop {
            display: none;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header img {
            width: 50px;
            margin-bottom: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 10px 0;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        /* Estilos de scrollbar para sidebar */
        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 5px; }

        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-group-header:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.05);
        }
        
        .nav-group-header i.icon-left {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .nav-group-header i.icon-right {
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        .nav-item.active .nav-group-header i.icon-right {
            transform: rotate(90deg);
        }

        .submenu {
            display: none;
            background-color: rgba(0,0,0,0.15);
            padding-left: 0;
            list-style: none;
        }
        
        .nav-item.active .submenu {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .submenu li a {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 55px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 13.5px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .submenu li a i {
            margin-right: 10px;
            font-size: 12px;
            opacity: 0.7;
        }

        .submenu li a:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.1);
        }

        .submenu li a.active {
            color: var(--color-primario);
            background-color: var(--color-blanco);
            border-left-color: var(--color-primario);
            font-weight: 600;
        }
        
        /* Enlace simple (como el Dashboard) */
        .nav-link-simple {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .nav-link-simple i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
        }
        .nav-link-simple:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.05);
        }

        .nav-link-simple.active {
            color: var(--color-primario);
            background-color: var(--color-blanco);
            border-left-color: var(--color-primario);
            font-weight: 600;
        }
        .content {
            flex: 1 1 0;
            min-width: 0;
            width: auto;
            background-color: var(--color-superficie);
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background-color: var(--color-blanco);
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            width: 100%;
            min-width: 0;
        }
        .sidebar-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: rgba(59,13,143,.08);
            color: var(--color-primario);
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        .topbar-periodo {
            background-color: rgba(15, 157, 88, 0.1);
            color: var(--color-secundario);
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--color-primario);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }
        .user-info .name { font-weight: 600; font-size: 14px; color: var(--color-oscuro); }
        .user-info .role { font-size: 12px; color: var(--color-texto-secundario); }
        .btn-logout { color: #dc3545; font-size: 13px; text-decoration: none; margin-left: 10px; }
        
        .main-container {
            padding: 30px;
            flex-grow: 1;
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }
        /* Utilidades generales */
        .panel {
            background: var(--color-blanco);
            border-radius: var(--radio-borde);
            padding: 20px;
            box-shadow: var(--sombra-tarjeta);
            margin-bottom: 20px;
        }
        .header-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.activo { background-color: var(--color-secundario); color: white; }
        .badge.inactivo { background-color: #dc3545; color: white; }
        @media (max-width: 992px) {
            .layout-app {
                display: block;
            }
            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform .25s ease;
                box-shadow: 12px 0 30px rgba(0,0,0,.18);
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                z-index: 999;
                background: rgba(11,31,51,.42);
                display: none;
            }
            body.sidebar-open .sidebar-backdrop {
                display: block;
            }
            .content {
                min-height: 100vh;
            }
            .sidebar-toggle {
                display: inline-flex;
            }
            .topbar {
                padding: 0 16px;
                gap: 12px;
            }
            .main-container {
                padding: 18px;
            }
            .header-acciones {
                align-items: flex-start;
                flex-direction: column;
            }
        }
        @media (max-width: 600px) {
            .topbar-periodo {
                display: none;
            }
            .user-info {
                display: none;
            }
            .main-container {
                padding: 14px;
            }
            .panel {
                padding: 14px;
            }
            .brand-form-card {
                padding: 22px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar-backdrop" data-sidebar-close></div>
    <div class="layout-app">
        <script>
            // Función global para confirmación de desactivación
            function confirmarDesactivacion(event, form, texto = 'Podrás reactivarlo más adelante desde el mismo listado.') {
                if (event) event.preventDefault();
                Swal.fire({
                    title: '¿Desactivar este registro?',
                    text: texto,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3B0D8F',
                    cancelButtonColor: '#E2E5E9',
                    customClass: { cancelButton: 'swal-btn-cancelar' }
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }

            // Función global para cerrar horario (acción sensible e irreversible)
            function confirmarCerrarHorario(event, url) {
                if (event) event.preventDefault();
                Swal.fire({
                    title: '¿Cerrar este horario?',
                    text: 'Esta acción es definitiva e irreversible. Ya no podrás modificarlo después.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar horario',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3B0D8F',
                    cancelButtonColor: '#E2E5E9',
                    customClass: { cancelButton: 'swal-btn-cancelar' }
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        window.location.href = url;
                    }
                });
                return false;
            }

            // Función global para eliminación física (severa, solo casos excepcionales)
            function confirmarEliminacion(event, form, entidad) {
                if (event) event.preventDefault();
                Swal.fire({
                    title: '¿Eliminar ' + (entidad || 'este registro') + '?',
                    text: 'Esta acción no se puede deshacer. El registro será eliminado permanentemente del sistema.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar permanentemente',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#D93636',
                    cancelButtonColor: '#E2E5E9',
                    customClass: { cancelButton: 'swal-btn-cancelar' }
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }


            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('[data-actions-trigger]').forEach((trigger) => {
                    trigger.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        const menu = trigger.closest('[data-actions-menu]');
                        document.querySelectorAll('[data-actions-menu].open').forEach((openMenu) => {
                            if (openMenu !== menu) openMenu.classList.remove('open');
                        });
                        menu.classList.toggle('open');
                        trigger.setAttribute('aria-expanded', menu.classList.contains('open') ? 'true' : 'false');
                    });
                });
                document.addEventListener('click', () => {
                    document.querySelectorAll('[data-actions-menu].open').forEach((menu) => menu.classList.remove('open'));
                });
                document.querySelectorAll('[data-actions-dropdown]').forEach((dropdown) => {
                    dropdown.addEventListener('click', (event) => event.stopPropagation());
                });
                document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
                    document.body.classList.toggle('sidebar-open');
                });
                document.querySelectorAll('[data-sidebar-close], .sidebar a').forEach((item) => {
                    item.addEventListener('click', () => document.body.classList.remove('sidebar-open'));
                });

                const currentParams = new URLSearchParams(window.location.search);
                const currentRuta = currentParams.get('ruta') || 'admin/dashboard';
                
                const links = document.querySelectorAll('.sidebar-menu .nav-link-simple');
                links.forEach(link => {
                    const prefix = link.getAttribute('data-prefix');
                    if (prefix && currentRuta.startsWith(prefix)) {
                        link.classList.add('active');
                    }
                });

                <?php if (!empty($_SESSION['swal_cadena'])): 
                    $cadena = $_SESSION['swal_cadena'];
                    unset($_SESSION['swal_cadena']);
                ?>
                    const cadena = <?= json_encode($cadena) ?>;
                    if (cadena.tipo === 'facultad') {
                        Swal.fire({
                            title: 'Facultad creada con éxito',
                            text: '¿Querés crear un Programa para esta facultad ahora?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear Programa',
                            cancelButtonText: 'No, volver al listado',
                            confirmButtonColor: '#3B0D8F',
                            cancelButtonColor: '#E2E5E9',
                            customClass: { cancelButton: 'swal-btn-cancelar' }
                        }).then((resultado) => {
                            window.location.href = resultado.isConfirmed
                                ? '?ruta=admin/institucion/programas/crear&facultad_id=' + cadena.id
                                : '?ruta=admin/institucion/facultades';
                        });
                    } else if (cadena.tipo === 'sede') {
                        Swal.fire({
                            title: 'Sede creada con éxito',
                            text: '¿Querés crear un Bloque para esta sede ahora?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear Bloque',
                            cancelButtonText: 'No, volver al listado',
                            confirmButtonColor: '#3B0D8F',
                            cancelButtonColor: '#E2E5E9',
                            customClass: { cancelButton: 'swal-btn-cancelar' }
                        }).then((resultado) => {
                            window.location.href = resultado.isConfirmed
                                ? '?ruta=admin/espacios/bloques/crear&sede_id=' + cadena.id
                                : '?ruta=admin/espacios/sedes';
                        });
                    } else if (cadena.tipo === 'bloque') {
                        Swal.fire({
                            title: 'Bloque creado con éxito',
                            text: '¿Querés crear un Espacio para este bloque ahora?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear Espacio',
                            cancelButtonText: 'No, volver al listado',
                            confirmButtonColor: '#3B0D8F',
                            cancelButtonColor: '#E2E5E9',
                            customClass: { cancelButton: 'swal-btn-cancelar' }
                        }).then((resultado) => {
                            window.location.href = resultado.isConfirmed
                                ? '?ruta=admin/espacios/espacios/crear&bloque_id=' + cadena.id
                                : '?ruta=admin/espacios/bloques';
                        });
                    } else if (cadena.tipo === 'periodo') {
                        Swal.fire({
                            title: 'Periodo académico creado con éxito',
                            text: '¿Querés cargar la Oferta académica de algún programa para este periodo ahora?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, cargar Oferta académica',
                            cancelButtonText: 'No, volver al listado',
                            confirmButtonColor: '#3B0D8F',
                            cancelButtonColor: '#E2E5E9',
                            customClass: { cancelButton: 'swal-btn-cancelar' }
                        }).then((resultado) => {
                            window.location.href = resultado.isConfirmed
                                ? '?ruta=admin/academico/oferta/crear&periodo_id=' + cadena.id
                                : '?ruta=admin/academico/periodos';
                        });
                    }
                <?php endif; ?>

                <?php if (!empty($_SESSION['swal_alerta'])): 
                    $alerta = $_SESSION['swal_alerta'];
                    unset($_SESSION['swal_alerta']);
                ?>
                    Swal.fire({
                        title: <?= json_encode($alerta['title'] ?? '') ?>,
                        text: <?= json_encode($alerta['text'] ?? '') ?>,
                        icon: <?= json_encode($alerta['icon'] ?? 'info') ?>,
                        confirmButtonColor: '#3B0D8F'
                    });
                <?php endif; ?>

                <?php if (!empty($_SESSION['password_temporal_generada'])):
                    $temporal = $_SESSION['password_temporal_generada'];
                    unset($_SESSION['password_temporal_generada']);
                ?>
                    Swal.fire({
                        title: 'Usuario creado',
                        html: `
                            <div style="text-align:left">
                                <p style="margin-bottom:10px">Contrasena temporal para <strong><?= htmlspecialchars($temporal['correo'] ?? '') ?></strong>:</p>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <code id="temp-password-value" style="flex:1; padding:10px; background:#f1f3f5; border-radius:8px; color:#0B1F33; font-size:16px;"><?= htmlspecialchars($temporal['password'] ?? '') ?></code>
                                    <button type="button" id="copy-temp-password" class="btn-primario" style="width:auto; padding:9px 12px;">Copiar</button>
                                </div>
                                <p style="margin-top:12px; font-size:13px;">Copiala y compartila con el lider. No se volvera a mostrar.</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3B0D8F',
                        didOpen: () => {
                            document.getElementById('copy-temp-password')?.addEventListener('click', async () => {
                                await navigator.clipboard.writeText(document.getElementById('temp-password-value')?.textContent || '');
                                Swal.showValidationMessage('Copiada al portapapeles');
                                setTimeout(() => Swal.resetValidationMessage(), 1200);
                            });
                        }
                    });
                <?php endif; ?>

                <?php if (!empty($_SESSION['debe_cambiar_password'])): ?>
                    Swal.fire({
                        title: 'Por seguridad, cambia tu contrasena',
                        html: `
                            <form id="temp-password-form" method="POST" action="?ruta=auth/cambiar_password_temporal">
                                <div class="form-group" style="text-align:left;">
                                    <label>Nueva contrasena</label>
                                    <input type="password" name="password" id="new-temp-password" class="form-control" minlength="8" required>
                                </div>
                                <div class="form-group" style="text-align:left;">
                                    <label>Confirmar nueva contrasena</label>
                                    <input type="password" name="password_confirmacion" id="confirm-temp-password" class="form-control" minlength="8" required>
                                </div>
                            </form>
                        `,
                        icon: 'warning',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCancelButton: false,
                        confirmButtonText: 'Actualizar contrasena',
                        confirmButtonColor: '#3B0D8F',
                        preConfirm: () => {
                            const pass = document.getElementById('new-temp-password').value;
                            const confirm = document.getElementById('confirm-temp-password').value;
                            if (pass.length < 8) {
                                Swal.showValidationMessage('La contrasena debe tener minimo 8 caracteres.');
                                return false;
                            }
                            if (pass !== confirm) {
                                Swal.showValidationMessage('Las contrasenas no coinciden.');
                                return false;
                            }
                            document.getElementById('temp-password-form').submit();
                            return false;
                        }
                    });
                <?php endif; ?>
            });
        </script>
