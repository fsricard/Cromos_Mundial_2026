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

$tema        = $_POST['tema'] ?? 'oscuro';
$fuente      = $_POST['fuente'] ?? 'normal';
$animaciones = isset($_POST['animaciones']) ? 1 : 0;

$valid_tema   = ['oscuro', 'claro'];
$valid_fuente = ['normal', 'grande'];

if (!in_array($tema, $valid_tema) || !in_array($fuente, $valid_fuente)) {
    $_SESSION['ajustes_error'] = 'Valores de ajustes no válidos.';
    header("Location: " . asset('/panel?mod=ajustes'));
    exit;
}

// Insertar o actualizar
$stmt = $pdo->prepare("SELECT id FROM usuarios_ajustes WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $stmt = $pdo->prepare("
        UPDATE usuarios_ajustes
        SET tema = ?, fuente = ?, animaciones = ?
        WHERE usuario_id = ?
    ");
    $stmt->execute([$tema, $fuente, $animaciones, $usuario_id]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO usuarios_ajustes (usuario_id, tema, fuente, animaciones)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $tema, $fuente, $animaciones]);
}

$_SESSION['ajustes_mensaje'] = 'Ajustes visuales guardados correctamente.';

header("Location: " . asset('/panel?mod=ajustes'));
exit;
