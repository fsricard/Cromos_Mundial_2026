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
if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
    die("ID no válido.");
}

$id = intval($_POST['id']);
$usuario_actual = $_SESSION['usuario_id'];

// No permitir que un usuario se elimine a sí mismo
if ($id === $usuario_actual) {
    die("No puedes eliminar tu propio usuario.");
}

// Obtener datos del usuario a eliminar
$stmt = $pdo->prepare("SELECT id, rol FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Si es admin, verificar que no sea el último admin
if ($usuario['rol'] === 'admin') {
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
    $total_admins = $stmt->fetchColumn();

    if ($total_admins <= 1) {
        die("No puedes eliminar al último administrador del sistema.");
    }
}

// Eliminar usuario
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);

header("Location: users_panel.php?eliminado=1");
exit;
