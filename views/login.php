<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once(__DIR__ . '/../config/funciones.php');

// Si ya está logueado, redirigir al panel personal
if (isLoggedIn()) {
    header("Location: /panel");
    exit;
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$error = '';
$expiredMsg = isset($_GET['expired']) ? 'Tu sesión ha expirado por inactividad. Vuelve a iniciar sesión.' : '';

// Inicializar intentos
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lock_until'])) {
    $_SESSION['lock_until'] = 0;
}

// Bloqueo temporal
if (time() < $_SESSION['lock_until']) {
    $error = 'Demasiados intentos fallidos. Espera unos minutos antes de volver a intentarlo.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $csrf  = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = 'Solicitud no válida. Inténtalo de nuevo.';
    } elseif ($email === '' || $clave === '') {
        $error = 'Por favor, introduce tu email y contraseña.';
    } else {
        if (login($email, $clave)) {
            secureSessionRegenerate();
            $_SESSION['login_attempts'] = 0;
            logSessionEvent("Login correcto (frontend)", $email);
            header("Location: /panel");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            logSessionEvent("Login fallido (frontend)", $email);

            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['lock_until'] = time() + 300;
                $error = 'Has superado el número máximo de intentos. Espera 5 minutos.';
            } else {
                $error = 'Email o contraseña incorrectos. Intento ' . $_SESSION['login_attempts'] . ' de 5.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Cromos Mundial 2026</title>

    <!-- FontAwesome 7.0.1 CSS -->
    <link href="<?= asset('/css/fontawesome/css/brands.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/chisel-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/etch-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/fontawesome.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-duo-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-fill-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-duo-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-press-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg-with-js.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thumbprint-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-shims.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v5-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/whiteboard-semibold.css') ?>" rel="stylesheet" />
    
    <link rel="stylesheet" href="css/frontend-login.css">
</head>

<body>

    <main class="login-container">
        <form method="post" class="login-form" autocomplete="off" novalidate>

            <h1 class="login-title">Accede a tu cuenta</h1>

            <?php if ($expiredMsg): ?>
                <p class="alert alert-warning"><?= htmlspecialchars($expiredMsg) ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" required>

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <button type="submit" class="btn-login">
                <i class="fa-sharp fa-light fa-person-to-portal"></i> Entrar
            </button>

            <div class="login-links">
                <a href=<?= asset('/restablecer'); ?>>¿Olvidaste tu contraseña?</a>
                <a href=<?= asset('/registro'); ?>>Crear una cuenta nueva</a>
            </div>

        </form>
    </main>

</body>

</html>