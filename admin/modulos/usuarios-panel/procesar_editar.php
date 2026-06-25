<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Validar campos
if (
    empty($_POST['id']) ||
    empty($_POST['nombre']) ||
    empty($_POST['correo']) ||
    empty($_POST['rol'])
) {
    die("Error: faltan datos obligatorios.");
}

$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$rol = $_POST['rol'];
$nueva_clave = !empty($_POST['clave']) ? password_hash($_POST['clave'], PASSWORD_BCRYPT) : null;

// Verificar si el correo ya existe en otro usuario
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :id LIMIT 1");
$stmt->execute([
    ':correo' => $correo,
    ':id' => $id
]);

if ($stmt->fetch()) {
    die("Error: ya existe otro usuario con ese correo.");
}

// Actualizar datos
if ($nueva_clave) {
    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET nombre = :nombre, correo = :correo, rol = :rol, clave = :clave
        WHERE id = :id
    ");
    $stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':rol' => $rol,
        ':clave' => $nueva_clave,
        ':id' => $id
    ]);
} else {
    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET nombre = :nombre, correo = :correo, rol = :rol
        WHERE id = :id
    ");
    $stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':rol' => $rol,
        ':id' => $id
    ]);
}

header("Location: users_panel.php?editado=1");
exit;
