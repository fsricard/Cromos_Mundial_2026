<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$pagina = 'users_panel';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Crear nuevo usuario</h2>

        <a href="users_panel.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <form action="procesar_crear.php" method="POST" class="form-admin">

            <div class="form-grupo">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-grupo">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
            </div>

            <div class="form-grupo">
                <label for="clave">Contraseña</label>
                <input type="password" id="clave" name="clave" required>
            </div>

            <div class="form-grupo">
                <label for="rol">Rol del usuario</label>
                <select id="rol" name="rol" required>
                    <option value="visitante">Visitante</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <button type="submit" class="btn btn-generar">
                <i class="fa-solid fa-floppy-disk"></i> Crear usuario
            </button>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>