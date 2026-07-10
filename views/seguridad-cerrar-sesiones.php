<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado, no tiene sentido mostrar esta página
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

// Regenerar ID de sesión para invalidar sesiones previas
secureSessionRegenerate();

$_SESSION['seguridad_mensaje'] = 'Se han cerrado las sesiones en todos los dispositivos.';

header("Location: " . asset('/panel?mod=seguridad'));
exit;
