<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Si ya está logueado
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$id = intval($_GET['id']);

$stmt = $pdo->prepare("
    INSERT INTO panel_alertas_usuarios (alerta_id, usuario_id, leida, leida_en)
    VALUES (?, ?, 1, NOW())
");
$stmt->execute([$id, $usuario_id]);

header("Location: " . asset('/panel'));
exit;
