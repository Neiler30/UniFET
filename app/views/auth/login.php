<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UniFET</title>
    <link rel="stylesheet" href="assets/css/design-tokens.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="icon" type="image/png" href="assets/img/isotipo-color.png">
</head>
<body>

    <div class="login-container">
        
        <!-- Panel izquierdo: Branding (como en el wireframe) -->
        <div class="login-brand">
            <img src="assets/img/logotipo-horizontal-oscuro.png" alt="UniFET Logo">
            <h2>Planificación académica, espacios y horarios <span class="acento">más eficientes.</span></h2>
        </div>

        <!-- Panel derecho: Formulario -->
        <div class="login-form-wrapper">
            <h1>Bienvenido a UniFET</h1>
            <p class="subtitle">Inicia sesión para continuar</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="?ruta=login_post" method="POST">
                <!-- CSRF Token (simulado por ahora, o usando el de sesión si se implementó) -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="email" id="usuario" name="usuario" class="form-control" placeholder="Ingresa tu correo" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="recordar" name="recordar">
                    <label for="recordar" style="margin-bottom:0; font-weight:normal; font-size:14px;">Recordar sesión</label>
                </div>

                <button type="submit" class="btn-primario">Iniciar sesión &rarr;</button>
            </form>

            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>

            <!-- Modo Demo: como se pedía en el prompt original -->
            <div class="demo-banner">
                <p>Modo Demo (Atajos para revisión):</p>
                <div class="demo-buttons">
                    <a href="?ruta=admin/dashboard" class="btn-demo">Panel Admin</a>
                    <a href="?ruta=lider/dashboard" class="btn-demo">Panel Líder</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
