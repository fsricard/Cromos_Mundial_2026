<?php
require_once __DIR__ . '/../includes/session.php';
require_once(__DIR__ . '/../config/funciones.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>

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

        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['ok'])): ?>
            <p class="success">Registro completado correctamente.</p>
        <?php endif; ?>

        <form action="registro_procesar.php" method="POST" enctype="multipart/form-data" class="login-form">

            <h1 class="login-title">Crear cuenta</h1>

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <label>Nombre completo</label>
            <input type="text" name="nombre" required minlength="3">

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Teléfono</label>
            <input type="text" name="telefono" required pattern="[0-9]{6,15}">

            <label>Ciudad</label>
            <input type="text" name="ciudad">

            <label>Provincia</label>
            <input type="text" name="provincia">

            <label>Contraseña</label>
            <input type="password" name="clave" required minlength="6">

            <label>Foto de perfil (opcional)</label>
            <input type="file" name="foto" accept="image/*">

            <!-- Estado NO lo elige el usuario, siempre "activo" -->
            <input type="hidden" name="estado" value="activo">

            <button type="submit" class="btn-login" >
                <i class="fa-thin fa-person-circle-plus"></i> Registrarme
            </button>
        </form>
     </main>

</body>

</html>