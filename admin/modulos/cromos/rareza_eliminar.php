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

$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM rarezas_cromos WHERE id = :id");
$stmt->bindValue(':id', $id);

if ($stmt->execute()) {
    header("Location: rareza_listado.php?exito=1");
    exit;
} else {
    header("Location: rareza_listado.php?error=No se pudo eliminar");
    exit;
}
