            <main class="layout-main">

                <!-- Módulo Política de privacidad -->
                <section class="content">
                    <article>

                        <h2 class="content-title">
                            <i class="fa-regular fa-user-secret"></i> Política de privacidad
                        </h2>

                        <div class="content-block">

                            <?php
                            $stmt = $pdo->query("SELECT contenido FROM politica_privacidad ORDER BY id DESC LIMIT 1");
                            $politica = $stmt->fetchColumn();

                            echo $politica;
                            ?>

                        </div>

                    </article>
                </section>

            </main>