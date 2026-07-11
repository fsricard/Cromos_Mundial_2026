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

        </div>

    </article>
</section>