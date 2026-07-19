            <main class="layout-main">

                <!-- Módulo de contacto -->
                <section class="content">
                    <article>

                        <h2 class="content-title">
                            <i class="fa-chisel fa-regular fa-at"></i> Contacto
                        </h2>

                        <div class="contact-wrapper">

                            <div class="contact-intro">
                                <?php
                                $stmt = $pdo->query("SELECT contenido FROM intro_contacto ORDER BY id DESC LIMIT 1");
                                $intro = $stmt->fetchColumn();

                                echo $intro;
                                ?>
                            </div>

                            <div class="contact-form">

                                <form id="form-contacto" class="form-contact">

                                    <input type="text" name="hp_trampa" id="hp_trampa" style="display:none">

                                    <div class="form-group">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" id="nombre" name="nombre" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="asunto">Asunto</label>
                                        <input type="text" id="asunto" name="asunto" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="mensaje">Mensaje</label>
                                        <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-enviar">
                                        <i class="fa-light fa-envelope-circle-user"></i> Enviar mensaje
                                    </button>

                                    <div id="ajax-respuesta" class="ajax-respuesta"></div>

                                </form>

                            </div>

                        </div>

                    </article>
                </section>

            </main>

            <script>
                document.getElementById("form-contacto").addEventListener("submit", function(e) {
                    e.preventDefault();

                    const form = document.getElementById("form-contacto");
                    const respuesta = document.getElementById("ajax-respuesta");

                    const datos = new FormData(form);

                    fetch("ajax/contacto_ajax.php", {
                            method: "POST",
                            body: datos
                        })
                        .then(res => res.json())
                        .then(data => {

                            if (data.ok) {
                                respuesta.innerHTML = `<div class="msg-ok">${data.msg}</div>`;
                                form.reset();
                            } else {
                                respuesta.innerHTML = `<div class="msg-error">${data.msg}</div>`;
                            }

                        })
                        .catch(() => {
                            respuesta.innerHTML = `<div class="msg-error">Error inesperado al enviar el mensaje.</div>`;
                        });
                });
            </script>