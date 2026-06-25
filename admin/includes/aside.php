<aside class="sidebar">
    <div class="logo">Cromos 2026</div>

    <nav>
        <a href="<?= asset('/admin/dashboard.php') ?>" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>"><i class="fa-notdog-duo fa-solid fa-chart-bar icon-dashboard"></i> Dashboard</a>
        <a href="<?= asset('/admin/modulos/usuarios-panel/users_panel.php') ?>" class="<?= $pagina === 'users_panel' ? 'active' : '' ?>"><i class="fa-solid fa-users icon-users"></i> Usuarios del sistema</a>
        <a href="<?= asset('/admin/modulos/logs/sesiones.php') ?>" class="<?= $pagina === 'logs' ? 'active' : '' ?>"><i class="fa-solid fa-circle-user-clock icon-sessions"></i> Logs</a>
    </nav>

    <div class="logout">
        <a href="<?= asset('/admin/logout.php') ?>">Cerrar sesión</a>
    </div>
</aside>