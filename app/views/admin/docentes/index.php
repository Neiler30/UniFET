<?php
$this->render('elementos_compartidos/header', ['titulo' => $titulo ?? 'Docentes']);
$this->render('elementos_compartidos/sidebar_admin');
$this->render('elementos_compartidos/tabs_academico', ['activo' => 'docentes']);
?>

<div class="header-acciones">
    <div>
        <h1>Docentes</h1>
        <p style="color:var(--color-texto-secundario); font-size:13.5px; margin-top:2px;">
            Administración de perfiles docentes, programas asociados y asignaturas compatibles.
        </p>
    </div>
    <a class="btn-primario" href="?ruta=admin/academico/docentes/crear">
        <i class="fa-solid fa-plus"></i> Nuevo Docente
    </a>
</div>

<div class="panel table-panel">
    <table class="table">
        <thead>
            <tr>
                <th style="width: 100px;">Código</th>
                <th>Docente</th>
                <th>Correo institucional</th>
                <th>Programas vinculados</th>
                <th>Asignaturas</th>
                <th style="text-align: center; width: 100px;">Estado</th>
                <th style="text-align: right; width: 100px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($docentes)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px 20px;">
                        <div class="empty-state" style="border:none; margin:0; padding:0;">
                            <i class="fa-solid fa-chalkboard-user" style="font-size:36px; color:#A0AEC0; margin-bottom:10px; display:block;"></i>
                            <h3>No hay docentes registrados</h3>
                            <p>Comienza registrando un docente con sus programas académicos y asignaturas asignadas.</p>
                            <a class="btn-primario btn-sm" href="?ruta=admin/academico/docentes/crear">
                                <i class="fa-solid fa-plus"></i> Registrar primer docente
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($docentes as $d): ?>
                <tr>
                    <td>
                        <span class="code-pill" style="font-weight:600;"><?= htmlspecialchars($d['codigo']) ?></span>
                    </td>
                    <td>
                        <strong style="color:var(--color-oscuro); font-size:14.5px;">
                            <?= htmlspecialchars($d['apellido'] . ' ' . $d['nombre']) ?>
                        </strong>
                    </td>
                    <td>
                        <span style="color:var(--color-texto-secundario); font-size:13.5px;">
                            <?= htmlspecialchars($d['correo'] ?? 'Sin correo') ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($d['programas'])): ?>
                            <div style="font-size: 13px; color: var(--color-texto-principal); line-height: 1.4;">
                                <?= htmlspecialchars($d['programas']) ?>
                            </div>
                        <?php else: ?>
                            <span style="color:#A0AEC0; font-style:italic; font-size:12.5px;">Sin programas</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($d['total_asignaturas'])): ?>
                            <span class="badge" style="background: rgba(59, 13, 143, 0.08); color: var(--color-primario); font-weight: 600;" title="<?= htmlspecialchars($d['asignaturas'] ?? '') ?>">
                                <i class="fa-solid fa-book"></i> <?= (int)$d['total_asignaturas'] ?> asignatura<?= (int)$d['total_asignaturas'] === 1 ? '' : 's' ?>
                            </span>
                        <?php else: ?>
                            <span style="color:#A0AEC0; font-style:italic; font-size:12.5px;">0 asignaturas</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge <?= strtolower($d['estado']) ?>">
                            <?= htmlspecialchars($d['estado']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <?php 
                        $this->render('elementos_compartidos/botones_accion', [
                            'ruta_editar' => '?ruta=admin/academico/docentes/editar&id=' . (int)$d['id_docente'],
                            'ruta_desactivar' => '?ruta=admin/academico/docentes/desactivar&id=' . (int)$d['id_docente'],
                            'activo' => $d['estado'] === 'ACTIVO',
                            'tipo' => 'docente'
                        ]); 
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $this->render('elementos_compartidos/footer'); ?>
