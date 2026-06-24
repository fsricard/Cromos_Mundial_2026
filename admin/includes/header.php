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

    <aside class="sidebar">
        <div class="logo">Cromos 2026</div>

        <nav>
            <a href="/admin/dashboard.php" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/admin/usuarios/listar.php" class="<?= $pagina === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
            <a href="/admin/logs/sesiones.php" class="<?= $pagina === 'logs' ? 'active' : '' ?>">Logs</a>
            <a href="/admin/configuracion.php" class="<?= $pagina === 'config' ? 'active' : '' ?>">Configuración</a>
        </nav>

        <div class="logout">
            <a href="<?= asset('/admin/logout.php') ?>">Cerrar sesión</a>
        </div>
    </aside>

    <div class="page">
        <header class="topbar">

            <button class="menu-toggle" id="menuToggle">☰</button>

            <div class="topbar-user">
                👤 <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?>
            </div>
        </header>

        <main class="content">