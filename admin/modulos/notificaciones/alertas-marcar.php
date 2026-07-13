<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE panel_alertas SET leida = 1 WHERE id = ?");
$stmt->execute([$id]);

header("Location: alertas.php");
exit;
