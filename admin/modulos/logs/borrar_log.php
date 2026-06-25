<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

$rutaLogs = __DIR__ . '/../../../log/';

if (!isset($_GET['f'])) {
    header("Location: sesiones.php?msg=error");
    exit;
}

$archivo = basename($_GET['f']); // evita rutas maliciosas
$rutaCompleta = $rutaLogs . $archivo;

if (file_exists($rutaCompleta)) {
    unlink($rutaCompleta);
    header("Location: sesiones.php?msg=borrado");
    exit;
} else {
    header("Location: sesiones.php?msg=noexiste");
    exit;
}
