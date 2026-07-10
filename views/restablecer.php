<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/funciones.php';
require_once __DIR__ . '/../config/database.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Introduce tu correo electrónico.';
    } else {

        // Buscar usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios_frontend WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $error = 'No existe ninguna cuenta con ese correo.';
        } else {

            // Generar token
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            // Guardar token
            $stmt = $pdo->prepare("
                INSERT INTO recuperacion_clave (usuario_id, token, expira_en)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$usuario['id'], $token, $expira]);

            // Enviar email
            $enlace = asset("/restablecer-confirmar?token=$token");
            $contenido = generarContenidoRestablecerClave($enlace);

            enviarCorreo(
                $email,
                "Restablecer contraseña",
                $contenido
            );

            $mensaje = 'Te hemos enviado un correo con instrucciones para restablecer tu contraseña.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Restablecer contraseña</title>

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

    <link rel="stylesheet" href="<?= asset('/css/frontend-login.css') ?>">
</head>

<body>
    <main class="login-container">

        <form method="post" class="login-form">

            <div class="login-links">
                <a href=<?= asset('/') ?>><i class="fa-solid fa-person-walking-arrow-loop-left"></i> Volver a la página de inicio</a>
            </div>

            <h1 class="login-title">Restablecer contraseña</h1>

            <?php if ($mensaje): ?>
                <p class="alert alert-success"><?= $mensaje ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="alert alert-error"><?= $error ?></p>
            <?php endif; ?>

            <label for="email">Introduce tu correo</label>
            <input type="email" name="email" required>

            <button class="btn-login">
                <i class="fa-light fa-envelope-circle-check"></i> Enviar enlace
            </button>

            <div class="login-links">
                <a href="<?= asset('/login') ?>">
                    <i class="fa-sharp fa-light fa-person-to-portal"></i> Volver al login
                </a>
            </div>

        </form>

    </main>
</body>

</html>