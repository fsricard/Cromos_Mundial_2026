<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$tipo    = $_POST['tipo'];
$mensaje = trim($_POST['mensaje']);

crearAlertaGlobal($tipo, $mensaje);

header("Location: alertas.php?ok=1");
exit;
