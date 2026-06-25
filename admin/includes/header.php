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

    <link rel="stylesheet" href="<?= asset('/admin/css/panel.css') ?>">
</head>

<body class="layout">

    <?php include('aside.php'); ?>

    <div class="page">
        <header class="topbar">

            <button class="menu-toggle" id="menuToggle">☰</button>

            <div class="topbar-user">
                👤 <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?>
            </div>
        </header>