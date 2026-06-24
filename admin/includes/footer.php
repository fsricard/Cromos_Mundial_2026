        </main>

        <footer class="footer">
            <p>
                <?= CopyrightRicardFS(); ?>
            </p>
        </footer>
        </div>

        <script>
            // Script para abrir/cerrar el menú responsive
            document.getElementById('menuToggle').addEventListener('click', () => {
                document.querySelector('.sidebar').classList.toggle('open');
            });
        </script>

 </body>

</html>