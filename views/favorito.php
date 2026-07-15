<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Validar login
if (!isset($_SESSION['usuario_id'])) {
    crearAlertaGlobal('error', 'Debes iniciar sesión para marcar favoritos.');
    header("Location: " . asset('/login'));
    exit;
}

$idUsuario = $_SESSION['usuario_id'];
$idCromo   = isset($_GET['cromo']) ? intval($_GET['cromo']) : 0;

// Validar cromo
$stmt = $pdo->prepare("SELECT id FROM cromos WHERE id = ?");
$stmt->execute([$idCromo]);

if (!$stmt->fetch()) {
    crearAlertaGlobal('error', 'El cromo seleccionado no existe.');
    header("Location: " . asset('/'));
    exit;
}

// Ejecutar favorito
$resultado = toggleFavorito($idUsuario, $idCromo);

if ($resultado === 'agregado') {
    crearAlertaGlobal('success', 'Cromo añadido a favoritos.');
} elseif ($resultado === 'eliminado') {
    crearAlertaGlobal('info', 'Cromo eliminado de favoritos.');
} else {
    crearAlertaGlobal('error', 'No se pudo actualizar el favorito.');
}

// Redirección
$volver = $_SERVER['HTTP_REFERER'] ?? asset('/');
header("Location: $volver");
exit;
