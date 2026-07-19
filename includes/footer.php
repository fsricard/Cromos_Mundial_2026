        <footer class="container-footer">
            <div class="footer">
                <div class="sections-footer">
                    <div class="sections-footer-left">
                        <h3>Esas cosillas legales</h3>
                        <ul>
                            <li><a href="<?= asset('/contacto') ?>">Contacta con nosotros</a></li>
                            <li><a href="<?= asset('/politica-de-privacidad') ?>">Política de privacidad</a></li>
                        </ul>
                    </div>

                    <div class="sections-footer-center">
                        <h3>Vuestro espacio</h3>
                        <ul>
                            <li><a href="<?= asset('/panel?mod=perfil') ?>">Tu perfil</a></li>
                            <li><a href="<?= asset('/panel?mod=favoritos') ?>">Tus favoritos</a></li>
                            <li><a href="<?= asset('/panel?mod=intercambios') ?>">Tus intercambios</a></li>
                        </ul>
                    </div>

                    <div class="sections-footer-right">
                        <img src="<?= asset('/img/cromos-mundial-2026-0002.png') ?>" alt="Logotipo Cromos Mundial 2026" class="footer-logo" />
                    </div>
                </div>

                <hr />

                <h4>
                    <?php
                    echo CopyrightRicardFS();
                    ?>
                </h4>
            </div>
        </footer>

        <script>
            // Script para el menú responsive
            function toggleMobileMenu() {
                const menu = document.getElementById('menuMovil');
                menu.style.left = (menu.style.left === '0px') ? '-100%' : '0px';
            }
        </script>

        </body>

        </html>