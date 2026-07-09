<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado, no tiene sentido mostrar esta página
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmar salida</title>
    <link rel="stylesheet" href="<?= asset('/css/panel.css') ?>">
</head>

<body>

    <main class="logout-confirm-container">

        <section class="logout-card">
            <h2>¿Seguro que quieres cerrar sesión?</h2>

            <p>Tu sesión actual se cerrará y deberás iniciar sesión nuevamente para acceder al panel.</p>

            <div class="logout-buttons">
                <a href="<?= asset('/panel') ?>" class="btn-cancelar">
                    <i class="fa-solid fa-circle-xmark"></i> Cancelar
                </a>

                <a href="<?= asset('/logout-confirm') ?>" class="btn-confirmar">
                    <i class="fa-solid fa-right-from-bracket"></i> Sí, cerrar sesión
                </a>
            </div>
        </section>

    </main>

</body>

</html>