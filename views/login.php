<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';

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
        if (login($email, $clave, 'frontend')) { // login adaptado para frontend
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

            <button type="submit" class="btn-login">Entrar</button>

            <div class="login-links">
                <a href="/restablecer">¿Olvidaste tu contraseña?</a>
                <a href="/registro">Crear una cuenta nueva</a>
            </div>

        </form>
    </main>

</body>

</html>