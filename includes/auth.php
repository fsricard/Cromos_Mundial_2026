<?php
require_once __DIR__ . '/../config/database.php';

// Iniciar sesión de usuario (frontend)
function login($identificador, $clave)
{
    global $pdo;

    // Permitir login por nombre, email o teléfono
    $stmt = $pdo->prepare("
        SELECT * FROM usuarios_frontend 
        WHERE (nombre = :id OR email = :id OR telefono = :id)
        AND estado = 'activo'
        LIMIT 1
    ");

    $stmt->execute(['id' => $identificador]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return false; // Usuario no encontrado o suspendido/eliminado
    }

    if (!password_verify($clave, $usuario['clave'])) {
        return false; // Clave incorrecta
    }

    // Guardar datos en sesión
    $_SESSION['usuario_id']        = $usuario['id'];
    $_SESSION['usuario_nombre']    = $usuario['nombre'];
    $_SESSION['usuario_email']     = $usuario['email'];
    $_SESSION['usuario_telefono']  = $usuario['telefono'];
    $_SESSION['usuario_ciudad']    = $usuario['ciudad'];
    $_SESSION['usuario_provincia'] = $usuario['provincia'];
    $_SESSION['usuario_foto']      = $usuario['foto'];
    $_SESSION['usuario_estado']    = $usuario['estado'];

    // Actualizar último acceso
    $pdo->prepare("
        UPDATE usuarios_frontend 
        SET ultimo_acceso = NOW() 
        WHERE id = ?
    ")->execute([$usuario['id']]);

    return true;
}

// Comprobar si hay sesión activa
function isLoggedIn()
{
    return isset($_SESSION['usuario_id']);
}

// Comprobar si el usuario está activo
function isActiveUser()
{
    return isLoggedIn() && $_SESSION['usuario_estado'] === 'activo';
}
