        </main>

        <footer class="panel-footer">
            <p>
                <?php
                echo CopyrightRicardFS();
                ?>
            </p>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.querySelector('.panel-menu-toggle');
                const nav = document.querySelector('.panel-nav');

                if (toggle && nav) {
                    toggle.addEventListener('click', function() {
                        nav.classList.toggle('is-open');
                    });
                }
            });
        </script>

    </body>

</html>