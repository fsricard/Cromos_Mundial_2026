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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cromos_listado.php?error=ID no válido");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del cromo
$stmt = $pdo->prepare("SELECT imagen FROM cromos WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$cromo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cromo) {
    header("Location: cromos_listado.php?error=Cromo no encontrado");
    exit;
}

// Eliminar imagen física si no es la imagen por defecto
if (!empty($cromo['imagen']) && $cromo['imagen'] !== "uploads/cromos/default/Default.png") {

    $ruta_fisica = __DIR__ . "/../../../" . $cromo['imagen'];

    if (file_exists($ruta_fisica)) {
        unlink($ruta_fisica);
    }
}

// Eliminar registro de la base de datos
$stmt = $pdo->prepare("DELETE FROM cromos WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: cromos_listado.php?exito=1");
    exit;
} else {
    header("Location: cromos_listado.php?error=No se pudo eliminar el cromo");
    exit;
}
