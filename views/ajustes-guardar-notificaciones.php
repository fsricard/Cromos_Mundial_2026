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

$email_importante = isset($_POST['email_importante']) ? 1 : 0;
$email_info       = isset($_POST['email_info']) ? 1 : 0;
$panel_alertas    = isset($_POST['panel_alertas']) ? 1 : 0;

// Comprobar si existen ajustes
$stmt = $pdo->prepare("SELECT id FROM usuarios_notificaciones WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $stmt = $pdo->prepare("
        UPDATE usuarios_notificaciones
        SET email_importante = ?, email_info = ?, panel_alertas = ?
        WHERE usuario_id = ?
    ");
    $stmt->execute([$email_importante, $email_info, $panel_alertas, $usuario_id]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO usuarios_notificaciones (usuario_id, email_importante, email_info, panel_alertas)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $email_importante, $email_info, $panel_alertas]);
}

$_SESSION['ajustes_mensaje'] = 'Ajustes de notificaciones guardados correctamente.';

header("Location: " . asset('/panel?mod=ajustes'));
exit;
