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

// Validar campos obligatorios
if (
    empty($_POST['nombre']) ||
    empty($_POST['correo']) ||
    empty($_POST['clave']) ||
    empty($_POST['rol'])
) {
    die("Error: faltan datos obligatorios.");
}

$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
$rol = $_POST['rol'];

// Verificar si el correo ya existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo LIMIT 1");
$stmt->execute([':correo' => $correo]);

if ($stmt->fetch()) {
    die("Error: ya existe un usuario con ese correo.");
}

// Insertar usuario
$stmt = $pdo->prepare("
    INSERT INTO usuarios (nombre, correo, clave, rol)
    VALUES (:nombre, :correo, :clave, :rol)
");

$stmt->execute([
    ':nombre' => $nombre,
    ':correo' => $correo,
    ':clave' => $clave,
    ':rol' => $rol
]);

header("Location: users_panel.php?creado=1");
exit;
