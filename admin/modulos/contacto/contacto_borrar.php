<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: contacto_listado.php?msg=error_id");
    exit;
}

$id = intval($_GET['id']);

// Comprobar si existe el mensaje
$stmt = $pdo->prepare("SELECT id FROM mensajes_contacto WHERE id = ?");
$stmt->execute([$id]);
$existe = $stmt->fetchColumn();

if (!$existe) {
    header("Location: contacto_listado.php?msg=no_encontrado");
    exit;
}

// Eliminar mensaje
$stmt = $pdo->prepare("DELETE FROM mensajes_contacto WHERE id = ?");
$stmt->execute([$id]);

header("Location: contacto_listado.php?msg=eliminado");
exit;
