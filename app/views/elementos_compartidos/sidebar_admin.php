<div class="sidebar">
    <div class="sidebar-header" style="padding: 24px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.12);">
        <img src="assets/img/logotipo-horizontal-oscuro.png" alt="UniFET" style="width: 180px; height: auto; margin-bottom: 6px; display: block; margin-left: auto; margin-right: auto;">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.7); font-weight: 600;">Portal Académico</div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="?ruta=admin/dashboard" class="nav-link-simple" data-prefix="admin/dashboard">
                <i class="fa-solid fa-chart-pie icon-left"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="?ruta=admin/institucion/facultades" class="nav-link-simple" data-prefix="admin/institucion">
                <i class="fa-solid fa-building-columns icon-left"></i> Institución
            </a>
        </li>
        <li>
            <a href="?ruta=admin/espacios/sedes" class="nav-link-simple" data-prefix="admin/espacios">
                <i class="fa-solid fa-map-location-dot icon-left"></i> Espacios
            </a>
        </li>
        <li>
            <a href="?ruta=admin/academico/periodos" class="nav-link-simple" data-prefix="admin/academico">
                <i class="fa-solid fa-book-open icon-left"></i> Académico
            </a>
        </li>
        <li>
            <a href="?ruta=admin/horarios" class="nav-link-simple" data-prefix="admin/horarios">
                <i class="fa-solid fa-calendar-week icon-left"></i> Horarios
            </a>
        </li>
        <li>
            <a href="?ruta=admin/reportes" class="nav-link-simple" data-prefix="admin/reportes">
                <i class="fa-solid fa-chart-line icon-left"></i> Reportes
            </a>
        </li>
        <li>
            <a href="?ruta=admin/importar" class="nav-link-simple" data-prefix="admin/importar">
                <i class="fa-solid fa-file-export icon-left"></i> Importar/Exportar
            </a>
        </li>
        <li>
            <a href="?ruta=admin/sistema/personalizacion" class="nav-link-simple" data-prefix="admin/sistema/personalizacion">
                <i class="fa-solid fa-server icon-left"></i> Sistema
            </a>
        </li>
    </ul>
</div>
<div class="content">
    <?php
    $usuario_actual = \App\Core\Auth::getUsuarioActual();
    $iniciales = strtoupper(substr($usuario_actual['nombre'], 0, 1) . substr($usuario_actual['apellido'] ?? '', 0, 1));
    $periodo_activo = \App\Models\PeriodoAcademico::getActivo();
    ?>
    <div class="topbar">
        <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-label="Abrir menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="topbar-periodo">
            Periodo Activo: <?= $periodo_activo ? htmlspecialchars($periodo_activo['codigo']) : 'Ninguno' ?>
        </div>
        <div class="topbar-user">
            <div class="user-info">
                <span class="name"><?= htmlspecialchars($usuario_actual['nombre'] . ' ' . ($usuario_actual['apellido'] ?? '')) ?></span>
                <span class="role"><?= str_replace('_', ' ', $usuario_actual['rol']) ?></span>
            </div>
            <div class="avatar"><?= $iniciales ?></div>
            <a href="?ruta=logout" class="btn-logout">Salir</a>
        </div>
    </div>
    <div class="main-container">
