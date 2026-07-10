<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado, no tiene sentido mostrar esta página
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$clave_actual = $_POST['clave_actual'] ?? '';
$clave_nueva  = $_POST['clave_nueva'] ?? '';
$clave_nueva2 = $_POST['clave_nueva2'] ?? '';

if ($clave_actual === '' || $clave_nueva === '' || $clave_nueva2 === '') {
    $_SESSION['seguridad_error'] = 'Rellena todos los campos.';
} elseif ($clave_nueva !== $clave_nueva2) {
    $_SESSION['seguridad_error'] = 'Las nuevas contraseñas no coinciden.';
} else {

    // Obtener contraseña actual
    $stmt = $pdo->prepare("SELECT clave FROM usuarios_frontend WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($clave_actual, $user['clave'])) {
        $_SESSION['seguridad_error'] = 'La contraseña actual no es correcta.';
    } else {

        // Actualizar contraseña
        $hash = password_hash($clave_nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios_frontend SET clave = ? WHERE id = ?");
        $stmt->execute([$hash, $usuario_id]);

        $_SESSION['seguridad_mensaje'] = 'Tu contraseña se ha actualizado correctamente.';
    }
}

header("Location: " . asset('/panel?mod=seguridad'));
exit;
