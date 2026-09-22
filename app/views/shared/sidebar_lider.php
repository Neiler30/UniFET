<div class="sidebar">
    <div class="sidebar-header" style="padding: 24px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.12);">
        <img src="assets/img/logotipo-horizontal-oscuro.png" alt="UniFET" style="width: 180px; height: auto; margin-bottom: 6px; display: block; margin-left: auto; margin-right: auto;">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.7); font-weight: 600;">Lider de Programa</div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="?ruta=lider/dashboard" class="nav-link-simple" data-prefix="lider/dashboard"><i class="fa-solid fa-chart-pie icon-left"></i> Dashboard</a></li>
        <li><a href="?ruta=lider/programa" class="nav-link-simple" data-prefix="lider/programa"><i class="fa-solid fa-graduation-cap icon-left"></i> Mi Programa</a></li>
        <li><a href="?ruta=lider/espacios" class="nav-link-simple" data-prefix="lider/espacios"><i class="fa-solid fa-door-open icon-left"></i> Espacios</a></li>
        <li><a href="?ruta=lider/horarios" class="nav-link-simple" data-prefix="lider/horarios"><i class="fa-solid fa-calendar-week icon-left"></i> Horarios</a></li>
        <li><a href="?ruta=lider/reportes" class="nav-link-simple" data-prefix="lider/reportes"><i class="fa-solid fa-chart-line icon-left"></i> Reportes</a></li>
    </ul>
</div>
<div class="content">
    <?php
    $usuario_actual = \App\Core\Auth::getUsuarioActual();
    $iniciales = strtoupper(substr($usuario_actual['nombre'], 0, 1) . substr($usuario_actual['apellido'] ?? '', 0, 1));
    $periodo_activo = \App\Models\PeriodoAcademico::getActivo();
    $programas_lider = \App\Models\LiderPanel::programasDelLider($usuario_actual['id_usuario']);
    $programa_lider_activo = \App\Models\LiderPanel::resolverProgramaActivo($usuario_actual['id_usuario'], $_GET['programa_id'] ?? null);
    $ruta_actual_lider = htmlspecialchars($_GET['ruta'] ?? 'lider/dashboard');
    ?>
    <div class="topbar">
        <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-label="Abrir menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div style="display:flex; align-items:center; gap:10px; min-width:0; flex-wrap:wrap;">
            <div class="topbar-periodo">
                Periodo Activo: <?= $periodo_activo ? htmlspecialchars($periodo_activo['codigo']) : 'Ninguno' ?>
            </div>
            <?php if (count($programas_lider) > 1): ?>
                <form method="GET" style="margin:0;">
                    <input type="hidden" name="ruta" value="<?= $ruta_actual_lider ?>">
                    <select name="programa_id" class="form-control" onchange="this.form.submit()" style="height:34px; min-width:210px; font-size:13px; padding:4px 10px;">
                        <?php foreach ($programas_lider as $programa): ?>
                            <option value="<?= (int)$programa['id_programa'] ?>" <?= (($programa_lider_activo['id_programa'] ?? null) == $programa['id_programa']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($programa['codigo'] . ' - ' . $programa['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php elseif ($programa_lider_activo): ?>
                <div class="topbar-periodo" style="background:rgba(59,13,143,.08); color:var(--color-primario);">
                    <?= htmlspecialchars($programa_lider_activo['codigo'] . ' - ' . $programa_lider_activo['nombre']) ?>
                </div>
            <?php endif; ?>
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
