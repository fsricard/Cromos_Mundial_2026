<aside class="sidebar">
    <div class="logo">Cromos 2026</div>

    <nav>
        <!-- Dashboard -->
        <a href="<?= asset('/admin/dashboard.php') ?>" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-notdog-duo fa-solid fa-chart-bar icon-dashboard"></i>
            Dashboard
        </a>

        <!-- Contacto -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/contacto/contacto_listado.php') ?>" class="<?= $pagina === 'contacto_listado' ? 'active' : '' ?>">
                <i class="fa-notdog fa-solid fa-envelope icon-contact"></i>
                Contacto
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/contacto/contacto_intro.php') ?>"
                    class="<?= $pagina === 'contacto_intro' ? 'active' : '' ?>">
                    <i class="fa-solid fa-hand-point-right icon-submenu"></i> Contacto intro
                </a>
            </div>
        </div>

        <!--  Logs -->
        <a href="<?= asset('/admin/modulos/logs/sesiones.php') ?>" class="<?= $pagina === 'logs' ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-user-clock icon-sessions"></i>
            Logs
        </a>

        <!-- Usuarios del panel de administración -->
        <a href="<?= asset('/admin/modulos/usuarios-panel/users_panel.php') ?>" class="<?= $pagina === 'users_panel' ? 'active' : '' ?>">
            <i class="fa-solid fa-users icon-users"></i>
            Usuarios del sistema
        </a>
    </nav>

    <div class="logout">
        <a href="<?= asset('/admin/logout.php') ?>">
            Cerrar sesión
        </a>
    </div>
</aside>