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

// Validar ID
$id_cromo = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_cromo <= 0) {
    header("Location: " . asset('/panel?mod=favoritos'));
    exit;
}

// Ejecutar toggle
$resultado = toggleFavorito($usuario_id, $id_cromo);

// Mensaje opcional
$_SESSION['favorito_mensaje'] = ($resultado === 'eliminado')
    ? 'Cromo eliminado de favoritos.'
    : 'Cromo añadido a favoritos.';

// Volver a favoritos
header("Location: " . asset('/panel?mod=favoritos'));
exit;
