<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['test'])) {
    guardarLog("sesiones", "Log de prueba generado correctamente.");
}

header("Location: sesiones.php");
exit;
