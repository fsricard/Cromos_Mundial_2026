<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/funciones.php';
require_once __DIR__ . '/../config/database.php';

// Si no está logueado, no tiene sentido mostrar esta página
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Cargar ajustes visuales
$stmt = $pdo->prepare("SELECT tema, fuente, animaciones FROM usuarios_ajustes WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$ajustes = $stmt->fetch(PDO::FETCH_ASSOC);

// Valores por defecto si no existen
if (!$ajustes) {
    $ajustes = [
        'tema'        => 'oscuro',
        'fuente'      => 'normal',
        'animaciones' => 1
    ];
}

// Convertimos los ajustes en clases CSS
$clase_tema        = $ajustes['tema'] === 'claro' ? 'tema-claro' : 'tema-oscuro';
$clase_fuente      = $ajustes['fuente'] === 'grande' ? 'fuente-grande' : 'fuente-normal';
$clase_animaciones = $ajustes['animaciones'] ? 'animaciones-on' : 'animaciones-off';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Confirmar salida</title>
    
    <link rel="stylesheet" href="<?= asset('/css/panel.css') ?>">
</head>

<body class="<?= $clase_tema ?> <?= $clase_fuente ?> <?= $clase_animaciones ?>">

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