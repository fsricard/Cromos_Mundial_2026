            <div class="hamburguesa-movil" id="hamburguesa" onclick="toggleMobileMenu()">
                <i class="fa-notdog-duo fa-solid fa-bars"></i>
            </div>

            <div class="menu-movil" id="menuMovil">
                <nav>
                    <ul>
                        <li><a href="<?= asset('/') ?>"><i class="fa-chisel fa-regular fa-house"></i> Inicio</a></li>
                        <li><a href="<?= asset('/contacto') ?>"><i class="fa-chisel fa-regular fa-at"></i> Contacto</a></li>

                        <li>
                            <?php
                            $claseLogin = isset($_SESSION['usuario_id']) ? 'user-login-on' : 'user-login-off';
                            $iconoLogin = isset($_SESSION['usuario_id']) ? 'fa-solid fa-user-check' : 'fa-regular fa-user-gear';
                            ?>

                            <a href="<?= asset('/login') ?>" class="<?= $claseLogin ?>">
                                <i class="<?= $iconoLogin ?>"></i>
                                <?= isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Tu espacio' ?>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>