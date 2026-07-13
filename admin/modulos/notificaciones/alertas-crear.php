<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$pagina = 'alertas-crear';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Crear alerta para un usuario</h2>

        <form method="POST" action="alertas-crear-accion.php" class="filtros-admin">
            <div class="form-grupo">
                <label>Tipo de alerta</label>
                <select name="tipo">
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                    <option value="success">Success</option>
                </select>
            </div>

            <div class="form-grupo">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="10" required></textarea>
            </div>

            <?php if (esAdmin()): ?>
                <button class="btn btn-generar">
                    <i class="fa-regular fa-wind-turbine"></i> Crear alerta
                </button>
            <?php endif; ?>

            <a href="alertas.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>
        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>