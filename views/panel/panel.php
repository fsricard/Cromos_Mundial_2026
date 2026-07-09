<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/funciones.php';

// Proteger el panel
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

// Módulo solicitado
$modulo = isset($_GET['mod']) ? trim($_GET['mod']) : 'dashboard';

// Lista de módulos permitidos
$modulos_validos = [
    'dashboard',
    'perfil',
    'mis-cromos',
    'seguridad',
    'ajustes'
];

// Si no existe → dashboard
if (!in_array($modulo, $modulos_validos)) {
    $modulo = 'dashboard';
}

// Cargar header universal del panel
require __DIR__ . '/header.php';

// Cargar módulo
require __DIR__ . "/{$modulo}.php";

// Cargar footer universal del panel
require __DIR__ . '/footer.php';