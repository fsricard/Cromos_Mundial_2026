<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$idioma        = $_POST['idioma'] ?? 'es';
$formato_fecha = $_POST['formato_fecha'] ?? 'd/m/Y';
$zona_horaria  = $_POST['zona_horaria'] ?? 'Europe/Madrid';

$valid_idiomas = ['es', 'en'];
$valid_formatos = ['d/m/Y', 'Y-m-d', 'm/d/Y'];
$valid_zonas = [
    'Europe/Madrid',
    'Europe/London',
    'America/New_York',
    'America/Mexico_City'
];

if (
    !in_array($idioma, $valid_idiomas) ||
    !in_array($formato_fecha, $valid_formatos) ||
    !in_array($zona_horaria, $valid_zonas)
) {

    $_SESSION['ajustes_error'] = 'Valores de región no válidos.';
    header("Location: " . asset('/panel?mod=ajustes'));
    exit;
}

// Comprobar si existen ajustes
$stmt = $pdo->prepare("SELECT id FROM usuarios_region WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $stmt = $pdo->prepare("
        UPDATE usuarios_region
        SET idioma = ?, formato_fecha = ?, zona_horaria = ?
        WHERE usuario_id = ?
    ");
    $stmt->execute([$idioma, $formato_fecha, $zona_horaria, $usuario_id]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO usuarios_region (usuario_id, idioma, formato_fecha, zona_horaria)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $idioma, $formato_fecha, $zona_horaria]);
}

$_SESSION['ajustes_mensaje'] = 'Ajustes de idioma y región guardados correctamente.';

header("Location: " . asset('/panel?mod=ajustes'));
exit;
