<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/funciones.php';

// Registrar evento
if (isset($_SESSION['usuario_email'])) {
    logSessionEvent("Logout confirmado", $_SESSION['usuario_email']);
}

// Vaciar sesión
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// Redirigir al login
header("Location: " . asset('/login'));
exit;
