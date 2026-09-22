<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Solicitar acceso') ?> - UniFET</title>
    <link rel="stylesheet" href="assets/css/design-tokens.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="icon" type="image/png" href="assets/img/isotipo-color.png">
</head>
<body>
    <div class="login-container">
        <div class="login-brand">
            <img src="assets/img/logotipo-horizontal-oscuro.png" alt="UniFET Logo">
            <h2>Solicitud de acceso para lideres de programa.</h2>
        </div>
        <div class="login-form-wrapper">
            <h1>Solicitar acceso</h1>
            <?php if (empty($habilitado)): ?>
                <p class="subtitle">El autorregistro no esta habilitado. Contacta al Administrador para crear tu cuenta de lider de programa.</p>
                <a href="?ruta=login" class="btn-primario">Volver al login</a>
            <?php else: ?>
                <p class="subtitle">Completa tus datos. El Administrador revisara la solicitud antes de activar la cuenta.</p>
                <form action="?ruta=registro/lider/guardar" method="POST">
                    <div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" required></div>
                    <div class="form-group"><label>Apellido</label><input class="form-control" name="apellido" required></div>
                    <div class="form-group"><label>Correo institucional</label><input type="email" class="form-control" name="correo" required></div>
                    <div class="form-group"><label>Programa</label><select class="form-control" name="id_programa" required><option value="">Seleccionar programa</option><?php foreach ($programas as $programa): ?><option value="<?= (int)$programa['id_programa'] ?>"><?= htmlspecialchars($programa['nombre']) ?></option><?php endforeach; ?></select></div>
                    <button type="submit" class="btn-primario">Enviar solicitud</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
