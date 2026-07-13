<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Solo admins pueden eliminar ventas
if (!isLoggedIn() || !esAdmin()) {
    header("Location: ../../index.php");
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: venta-listado.php?error=ID no válido");
    exit;
}

$id_venta = intval($_GET['id']);

// Comprobar que existe la venta
$sql = "SELECT * FROM cromos_venta WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    header("Location: venta-listado.php?error=La venta no existe");
    exit;
}

// Eliminar la venta
$sql_delete = "DELETE FROM cromos_venta WHERE id = :id";
$stmt_del = $pdo->prepare($sql_delete);
$stmt_del->execute([':id' => $id_venta]);

// Redirigir con mensaje de éxito
header("Location: venta-listado.php?mensaje=Venta eliminada correctamente");
exit;
