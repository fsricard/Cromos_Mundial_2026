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

if (!isset($_GET['id'])) {
    header("Location: sistemas_de_pagos.php?error=ID no proporcionado");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM sistemas_pagos WHERE id = :id");
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header("Location: sistemas_de_pagos.php?exito=Método eliminado correctamente");
    exit;
} else {
    header("Location: sistemas_de_pagos.php?error=No se pudo eliminar el método");
    exit;
}
