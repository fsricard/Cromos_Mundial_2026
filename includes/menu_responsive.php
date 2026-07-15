            <div class="hamburguesa-movil" id="hamburguesa" onclick="toggleMobileMenu()">
                <i class="fa-notdog-duo fa-solid fa-bars"></i>
            </div>

            <div class="menu-movil" id="menuMovil">
                <nav>
                    <ul>
                        <li><a href="<?= asset('/') ?>"><i class="fa-chisel fa-regular fa-house"></i> Inicio</a></li>
                        <li><a href="<?= asset('/contacto') ?>"><i class="fa-chisel fa-regular fa-at"></i> Contacto</a></li>

                        <li>
                            <a href="<?= asset('/login') ?>">
                                <i class="fa-regular fa-user-gear"></i>
                                <?= isset($_SESSION['usuario_id']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Tu espacio' ?>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>