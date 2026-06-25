<aside class="sidebar">
    <div class="logo">Cromos 2026</div>

    <nav>
        <a href="<?= asset('/admin/dashboard.php') ?>" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= asset('/admin/usuarios/listar.php') ?>" class="<?= $pagina === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
        <a href="<?= asset('/admin/logs/sesiones.php') ?>" class="<?= $pagina === 'logs' ? 'active' : '' ?>">Logs</a>
        <a href="<?= asset('/admin/configuracion.php') ?>" class="<?= $pagina === 'config' ? 'active' : '' ?>">Configuración</a>
    </nav>

    <div class="logout">
        <a href="<?= asset('/admin/logout.php') ?>">Cerrar sesión</a>
    </div>
</aside>