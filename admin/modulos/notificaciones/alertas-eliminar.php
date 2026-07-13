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

// Solo administradores pueden eliminar alertas
if (!esAdmin()) {
    header("Location: alertas.php?error=permiso");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    // Eliminar alerta global
    $stmt = $pdo->prepare("DELETE FROM panel_alertas WHERE id = ?");
    $stmt->execute([$id]);

    // Las lecturas de usuarios se eliminan automáticamente por ON DELETE CASCADE
}

header("Location: alertas.php?ok=eliminada");
exit;
