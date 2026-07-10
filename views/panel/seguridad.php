<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/funciones.php';

// Proteger el panel
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

// Datos del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];
$ultimo_acceso = $_SESSION['usuario_ultimo_acceso'] ?? null;

// Mensajes de acciones
$mensaje = $_SESSION['seguridad_mensaje'] ?? '';
$error   = $_SESSION['seguridad_error'] ?? '';

unset($_SESSION['seguridad_mensaje'], $_SESSION['seguridad_error']);
?>

<section class="content seguridad-content">
    <article>

        <h2 class="content-title seguridad-title">
            <i class="fa-light fa-shield-keyhole"></i> Seguridad
        </h2>

        <div class="content-block seguridad-block">

            <?php if ($mensaje): ?>
                <p class="alert alert-success seguridad-alert"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="alert alert-error seguridad-alert"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Cambiar contraseña -->
            <section class="seguridad-section seguridad-cambiar-clave">
                <h3 class="seguridad-subtitle">
                    <i class="fa-light fa-key"></i> Cambiar contraseña
                </h3>

                <form action="<?= asset('/seguridad-actualizar-clave') ?>" method="post" class="seguridad-form">

                    <label>Contraseña actual</label>
                    <input type="password" name="clave_actual" required>

                    <label>Nueva contraseña</label>
                    <input type="password" name="clave_nueva" required>

                    <label>Repite la nueva contraseña</label>
                    <input type="password" name="clave_nueva2" required>

                    <button type="submit" class="btn btn-guardar seguridad-btn">
                        <i class="fa-light fa-floppy-disk"></i> Guardar nueva contraseña
                    </button>
                </form>
            </section>

            <!-- Cerrar sesiones -->
            <section class="seguridad-section seguridad-cerrar-sesiones">
                <h3 class="seguridad-subtitle">
                    <i class="fa-light fa-door-closed"></i> Cerrar sesión en todos los dispositivos
                </h3>

                <p class="seguridad-text">
                    Si sospechas que alguien ha accedido a tu cuenta, puedes cerrar sesión en todos los dispositivos.
                </p>

                <form action="<?= asset('/seguridad-cerrar-sesiones') ?>" method="post">
                    <button type="submit" class="btn seguridad-btn-danger">
                        <i class="fa-light fa-power-off"></i> Cerrar todas las sesiones
                    </button>
                </form>
            </section>

        </div>

    </article>
</section>