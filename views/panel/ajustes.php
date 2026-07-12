<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/funciones.php';

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Cargar ajustes visuales
$stmt = $pdo->prepare("SELECT tema, fuente, animaciones FROM usuarios_ajustes WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$ajustes = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ajustes) {
    $ajustes = [
        'tema'        => 'oscuro',
        'fuente'      => 'normal',
        'animaciones' => 1
    ];
}

// Cargar ajustes de notificaciones
$stmt = $pdo->prepare("
    SELECT email_importante, email_info, panel_alertas
    FROM usuarios_notificaciones
    WHERE usuario_id = ?
");
$stmt->execute([$usuario_id]);
$notificaciones = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notificaciones) {
    $notificaciones = [
        'email_importante' => 1,
        'email_info'       => 0,
        'panel_alertas'    => 1
    ];
}

// Cargar ajustes de idioma y región
$stmt = $pdo->prepare("
    SELECT idioma, formato_fecha, zona_horaria
    FROM usuarios_region
    WHERE usuario_id = ?
");
$stmt->execute([$usuario_id]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$region) {
    $region = [
        'idioma'        => 'es',
        'formato_fecha' => 'd/m/Y',
        'zona_horaria'  => 'Europe/Madrid'
    ];
}

$mensaje = $_SESSION['ajustes_mensaje'] ?? '';
$error   = $_SESSION['ajustes_error'] ?? '';

unset($_SESSION['ajustes_mensaje'], $_SESSION['ajustes_error']);
?>

<section class="content ajustes-content">
    <article>

        <h2 class="content-title ajustes-title">
            <i class="fa-light fa-sliders"></i> Ajustes
        </h2>

        <div class="content-block ajustes-block">

            <?php if ($mensaje): ?>
                <p class="alert alert-success ajustes-alert"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="alert alert-error ajustes-alert"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Ajustes visuales -->
            <section class="ajustes-section ajustes-visual">
                <h3 class="ajustes-subtitle">
                    <i class="fa-light fa-eye"></i> Apariencia del panel
                </h3>

                <form action="<?= asset('/ajustes-guardar-visual') ?>" method="post" class="ajustes-form">

                    <label>Tema</label>
                    <select name="tema">
                        <option value="oscuro" <?= $ajustes['tema'] === 'oscuro' ? 'selected' : '' ?>>Oscuro</option>
                        <option value="claro" <?= $ajustes['tema'] === 'claro' ? 'selected' : '' ?>>Claro</option>
                    </select>

                    <label>Tamaño de fuente</label>
                    <select name="fuente">
                        <option value="normal" <?= $ajustes['fuente'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="grande" <?= $ajustes['fuente'] === 'grande' ? 'selected' : '' ?>>Grande</option>
                    </select>

                    <label class="ajustes-checkbox-label">
                        <input type="checkbox" name="animaciones" value="1" <?= $ajustes['animaciones'] ? 'checked' : '' ?>>
                        Activar animaciones del panel
                    </label>

                    <button type="submit" class="btn btn-guardar">
                        <i class="fa-light fa-floppy-disk"></i> Guardar ajustes visuales
                    </button>
                </form>
            </section>

            <!-- Ajustes de notificaciones -->
            <section class="ajustes-section ajustes-notificaciones">
                <h3 class="ajustes-subtitle">
                    <i class="fa-light fa-bell"></i> Notificaciones
                </h3>

                <form action="<?= asset('/ajustes-guardar-notificaciones') ?>" method="post" class="ajustes-form">

                    <label class="ajustes-checkbox-label">
                        <input type="checkbox" name="email_importante" value="1"
                            <?= $notificaciones['email_importante'] ? 'checked' : '' ?>>
                        Recibir emails importantes (seguridad, actividad sospechosa)
                    </label>

                    <label class="ajustes-checkbox-label">
                        <input type="checkbox" name="email_info" value="1"
                            <?= $notificaciones['email_info'] ? 'checked' : '' ?>>
                        Recibir emails informativos (novedades, avisos generales)
                    </label>

                    <label class="ajustes-checkbox-label">
                        <input type="checkbox" name="panel_alertas" value="1"
                            <?= $notificaciones['panel_alertas'] ? 'checked' : '' ?>>
                        Mostrar alertas dentro del panel
                    </label>

                    <button type="submit" class="btn btn-guardar">
                        <i class="fa-light fa-floppy-disk"></i> Guardar ajustes de notificaciones
                    </button>
                </form>
            </section>

            <!-- Ajustes de idioma y región -->
            <section class="ajustes-section ajustes-region">
                <h3 class="ajustes-subtitle">
                    <i class="fa-light fa-globe"></i> Idioma y región
                </h3>

                <form action="<?= asset('/ajustes-guardar-region') ?>" method="post" class="ajustes-form">

                    <label>Idioma del panel</label>
                    <select name="idioma">
                        <option value="es" <?= $region['idioma'] === 'es' ? 'selected' : '' ?>>Español</option>
                    </select>

                    <label>Formato de fecha</label>
                    <select name="formato_fecha">
                        <option value="d/m/Y" <?= $region['formato_fecha'] === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                        <option value="Y-m-d" <?= $region['formato_fecha'] === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                        <option value="m/d/Y" <?= $region['formato_fecha'] === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                    </select>

                    <label>Zona horaria</label>
                    <select name="zona_horaria">
                        <option value="Europe/Madrid" <?= $region['zona_horaria'] === 'Europe/Madrid' ? 'selected' : '' ?>>Europa / Madrid</option>
                        <option value="Europe/London" <?= $region['zona_horaria'] === 'Europe/London' ? 'selected' : '' ?>>Europa / Londres</option>
                        <option value="America/New_York" <?= $region['zona_horaria'] === 'America/New_York' ? 'selected' : '' ?>>América / Nueva York</option>
                        <option value="America/Mexico_City" <?= $region['zona_horaria'] === 'America/Mexico_City' ? 'selected' : '' ?>>América / Ciudad de México</option>
                    </select>

                    <button type="submit" class="btn btn-guardar">
                        <i class="fa-light fa-floppy-disk"></i> Guardar ajustes de idioma y región
                    </button>
                </form>
            </section>

        </div>

    </article>
</section>