<?php
if (!isset($pagina)) {
    $pagina = '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>Admin | <?= htmlspecialchars(ucfirst($pagina)) ?></title>

    <!-- FontAwesome 7.0.1 CSS -->
    <link href="<?= asset('css/fontawesome/css/brands.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/chisel-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/duotone.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/etch-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/fontawesome.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/jelly-duo-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/jelly-fill-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/jelly-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/notdog-duo-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/notdog-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-duotone-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/sharp-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/slab-press-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/slab-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/svg.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/svg-with-js.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/thumbprint-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/v4-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/v4-shims.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/v5-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('css/fontawesome/css/whiteboard-semibold.css') ?>" rel="stylesheet" />

    <link rel="stylesheet" href="<?= asset('/admin/css/panel.css') ?>">

    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body class="layout">

    <?php include('aside.php'); ?>

    <div class="page">
        <header class="topbar">

            <button class="menu-toggle" id="menuToggle">☰</button>

            <div class="topbar-user">
                <i class="fa-jelly-fill fa-regular fa-user icon-topbar-user"></i> <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?>
            </div>
        </header>