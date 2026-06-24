<?php
if (!isset($pagina)) {
    $pagina = '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin | <?= htmlspecialchars(ucfirst($pagina)) ?></title>

    <!-- Estilos globales del panel -->
    <link rel="stylesheet" href="<?= asset('/admin/css/panel.css') ?>">
</head>

<body>
    <aside class="sidebar">
        <div class="logo">
            <span>Cromos Mundial 2026</span>
        </div>

        <nav>
            <a href="/admin/dashboard.php" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/admin/usuarios/listar.php" class="<?= $pagina === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
            <a href="/admin/logs/sesiones.php" class="<?= $pagina === 'logs' ? 'active' : '' ?>">Logs</a>
            <a href="/admin/configuracion.php" class="<?= $pagina === 'config' ? 'active' : '' ?>">Configuración</a>
        </nav>

        <div class="logout">
            <!-- <a href="/admin/logout.php">Cerrar sesión</a> -->
        </div>
    </aside>

    <header class="topbar">
        <div class="topbar-user">
            <span>👤 <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></span>
        </div>
    </header>

    <main class="content">