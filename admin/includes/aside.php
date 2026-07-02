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
                    <i class="fa-solid fa-message-text icon-submenu"></i> Contacto intro
                </a>
            </div>
        </div>

        <!-- Política de pribacidad -->
        <a href="<?= asset('/admin/modulos/politica-privacidad/politica_de_privacidad.php') ?>" class="<?= $pagina === 'politica_de_privacidad' ? 'active' : '' ?>">
            <i class="fa-jelly-fill fa-regular fa-lock icon-policy"></i>
            Política privacidad
        </a>

        <!--  Logs -->
        <a href="<?= asset('/admin/modulos/logs/sesiones.php') ?>" class="<?= $pagina === 'logs' ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-user-clock icon-sessions"></i>
            Logs
        </a>

        <!-- Sistema de pago -->
        <a href="<?= asset('/admin/modulos/sistemas-de-pagos/sistemas_de_pagos.php') ?>" class="<?= $pagina === 'sistemas-de-pagos' ? 'active' : '' ?>">
            <i class="fa-solid fa-coins icon-pay"></i>
            Sistemas de pago
        </a>

        <!-- Cromos -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/cromos/cromos_listado.php') ?>" class="<?= $pagina === 'cromos_listado' ? 'active' : '' ?>">
                <i class="fa-solid fa-futbol icon-cromos-listado"></i>
                Listado de cromos
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/cromos/cromos_subir.php') ?>"
                    class="<?= $pagina === 'cromos_subir' ? 'active' : '' ?>">
                    <i class="fa-solid fa-upload icon-cromos-up"></i>
                     Añadir cromos
                </a>
            </div>
        </div>

        <!-- Usuario del FrontEnd -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_listado.php') ?>" class="<?= $pagina === 'usuarios_frontend_listado' ? 'active' : '' ?>">
                <i class="fa-solid fa-person-soccer icon-soccer"></i>
                Users de la página
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_favoritos.php') ?>"
                    class="<?= $pagina === 'usuarios_frontend_favoritos' ? 'active' : '' ?>">
                    <i class="fa-solid fa-bookmark icon-favorite"></i> Ver favoritos
                </a>
                <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_intercambios.php') ?>"
                    class="<?= $pagina === 'usuarios_frontend_intercambios' ? 'active' : '' ?>">
                    <i class="fa-solid fa-arrows-turn-to-dots icon-interchange"></i> Ver intercambios
                </a>
            </div>
        </div>

        <!-- Usuarios del sistema -->
        <a href="<?= asset('/admin/modulos/usuarios-panel/users_panel.php') ?>" class="<?= $pagina === 'users_panel' ? 'active' : '' ?>">
            <i class="fa-solid fa-users icon-users"></i>
            Users del sistema
        </a>
    </nav>

    <div class="logout">
        <a href="<?= asset('/admin/logout.php') ?>">
            Cerrar sesión
        </a>
    </div>
</aside>