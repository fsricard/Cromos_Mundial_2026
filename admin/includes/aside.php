<aside class="sidebar">
    <div class="logo">Cromos 2026</div>

    <nav>
        <!-- Dashboard -->
        <a href="<?= asset('/admin/dashboard.php') ?>" class="<?= $pagina === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-regular fa-chart-bar icon-dashboard"></i>
            Dashboard
        </a>

        <!-- Contacto -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/contacto/contacto_listado.php') ?>" class="<?= $pagina === 'contacto_listado' ? 'active' : '' ?>">
                <i class="fa-regular fa-envelope icon-contact"></i>
                Contacto
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/contacto/contacto_intro.php') ?>"
                    class="<?= $pagina === 'contacto_intro' ? 'active' : '' ?>">
                    <i class="fa-regular fa-message-text icon-submenu"></i> Contacto intro
                </a>
            </div>
        </div>

        <!-- Política de pribacidad -->
        <a href="<?= asset('/admin/modulos/politica-privacidad/politica_de_privacidad.php') ?>" class="<?= $pagina === 'politica_de_privacidad' ? 'active' : '' ?>">
            <i class="fa-regular fa-lock icon-policy"></i>
            Política privacidad
        </a>

        <!--  Logs -->
        <a href="<?= asset('/admin/modulos/logs/sesiones.php') ?>" class="<?= $pagina === 'logs' ? 'active' : '' ?>">
            <i class="fa-regular fa-circle-user-clock icon-sessions"></i>
            Logs
        </a>

        <!-- Sistema de pago -->
        <a href="<?= asset('/admin/modulos/sistemas-de-pagos/sistemas_de_pagos.php') ?>" class="<?= $pagina === 'sistemas-de-pagos' ? 'active' : '' ?>">
            <i class="fa-regular fa-coins icon-pay"></i>
            Sistemas de pago
        </a>

        <!-- Cromos -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/cromos/cromos_listado.php') ?>" class="<?= $pagina === 'cromos_listado' ? 'active' : '' ?>">
                <i class="fa-regular fa-futbol icon-cromos-listado"></i>
                Listado de cromos
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/cromos/cromo_nuevo.php') ?>"
                    class="<?= $pagina === 'cromo_nuevo' ? 'active' : '' ?>">
                    <i class="fa-regular fa-upload icon-cromos-up"></i>
                    Añadir cromos
                </a>

                <a href="<?= asset('/admin/modulos/cromos/rareza_listado.php') ?>"
                    class="<?= $pagina === 'rareza_listado' ? 'active' : '' ?>">
                    <i class="fa-regular fa-transgender icon-queer"></i>
                    Listado de rarezas
                </a>

                <a href="<?= asset('/admin/modulos/cromos/importar_csv.php') ?>"
                    class="<?= $pagina === 'cromos_importar_csv' ? 'active' : '' ?>">
                    <i class="fa-regular fa-file-csv icon-csv"></i>
                    Importar CSV
                </a>
            </div>
        </div>

        <!-- Usuario del FrontEnd -->
        <div class="menu-group">
            <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_listado.php') ?>" class="<?= $pagina === 'usuarios_frontend_listado' ? 'active' : '' ?>">
                <i class="fa-regular fa-person-soccer icon-soccer"></i>
                Users de la página
            </a>

            <div class="submenu">
                <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_favoritos.php') ?>"
                    class="<?= $pagina === 'usuarios_frontend_favoritos' ? 'active' : '' ?>">
                    <i class="fa-regular fa-bookmark icon-favorite"></i> Ver favoritos
                </a>
                <a href="<?= asset('/admin/modulos/usuarios-frontend/usuarios_frontend_intercambios.php') ?>"
                    class="<?= $pagina === 'usuarios_frontend_intercambios' ? 'active' : '' ?>">
                    <i class="fa-regular fa-arrows-turn-to-dots icon-interchange"></i> Ver intercambios
                </a>
                <a href="<?= asset('/admin/modulos/notificaciones/alertas.php') ?>" class="<?= $pagina === 'alertas' ? 'active' : '' ?>">
                    <i class="fa-regular fa-bell icon-alert"></i>
                    Alertas del panel
                </a>
                <a href="<?= asset('/admin/modulos/notificaciones/alertas-crear.php') ?>" class="<?= $pagina === 'alertas-crear' ? 'active' : '' ?>">
                    <i class="fa-regular fa-location-plus icon-create-alert"></i>
                    Crear nueva alerta
                </a>
            </div>
        </div>

        <?php if (esAdmin()): ?>
            <!-- Usuarios del sistema -->
            <a href="<?= asset('/admin/modulos/usuarios-panel/users_panel.php') ?>" class="<?= $pagina === 'users_panel' ? 'active' : '' ?>">
                <i class="fa-regular fa-users icon-users"></i>
                Users del sistema
            </a>
        <?php endif; ?>

    </nav>

    <div class="logout">
        <a href="<?= asset('/admin/logout.php') ?>">
            <i class="fa-regular fa-person-to-door icon-logout"></i> Cerrar sesión
        </a>
    </div>
</aside>