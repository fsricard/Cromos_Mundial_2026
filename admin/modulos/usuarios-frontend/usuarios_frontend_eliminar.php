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
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'ID de usuario no válido.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

$id = intval($_GET['id']);

// Comprobar si el usuario existe
$stmt = $pdo->prepare("SELECT id, nombre FROM usuarios_frontend WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'El usuario no existe.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

// Eliminar usuario
$stmt = $pdo->prepare("DELETE FROM usuarios_frontend WHERE id = :id");
$stmt->execute([':id' => $id]);

$_SESSION['mensaje'] = [
    'tipo' => 'exito',
    'texto' => 'El usuario "' . htmlspecialchars($usuario['nombre']) . '" ha sido eliminado correctamente.'
];

header("Location: usuarios_frontend_listado.php");
exit;
