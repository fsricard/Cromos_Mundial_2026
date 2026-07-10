<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

$token = $_GET['token'] ?? '';
$error = '';
$mensaje = '';

if ($token === '') {
    $error = 'Token no válido.';
} else {

    // Buscar token
    $stmt = $pdo->prepare("
        SELECT rc.id, rc.usuario_id, rc.expira_en, rc.usado, u.email
        FROM recuperacion_clave rc
        JOIN usuarios_frontend u ON u.id = rc.usuario_id
        WHERE token = ?
    ");
    $stmt->execute([$token]);
    $data = $stmt->fetch();

    if (!$data) {
        $error = 'Token no válido.';
    } elseif ($data['usado']) {
        $error = 'Este enlace ya ha sido utilizado.';
    } elseif (strtotime($data['expira_en']) < time()) {
        $error = 'El enlace ha expirado.';
    }

    // Si el token es válido y no hay error
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {

        $clave1 = $_POST['clave1'] ?? '';
        $clave2 = $_POST['clave2'] ?? '';

        if ($clave1 === '' || $clave2 === '') {
            $error = 'Introduce ambas contraseñas.';
        } elseif ($clave1 !== $clave2) {
            $error = 'Las contraseñas no coinciden.';
        } else {

            // Actualizar contraseña
            $hash = password_hash($clave1, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE usuarios_frontend SET clave = ? WHERE id = ?");
            $stmt->execute([$hash, $data['usuario_id']]);

            // Marcar token como usado
            $stmt = $pdo->prepare("UPDATE recuperacion_clave SET usado = 1 WHERE id = ?");
            $stmt->execute([$data['id']]);

            $mensaje = 'Tu contraseña ha sido actualizada correctamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nueva contraseña</title>

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

            <h1 class="login-title">Nueva contraseña</h1>

            <?php if ($mensaje): ?>
                <p class="alert alert-success"><?= $mensaje ?></p>
                <div class="login-links">
                    <a href="<?= asset('/login') ?>">Iniciar sesión</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="alert alert-error"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!$mensaje && !$error): ?>

                <label>Nueva contraseña</label>
                <input type="password" name="clave1" required>

                <label>Repite la contraseña</label>
                <input type="password" name="clave2" required>

                <button class="btn-login">
                    <i class="fa-light fa-key"></i> Guardar nueva contraseña
                </button>

            <?php endif; ?>

        </form>

    </main>
</body>

</html>