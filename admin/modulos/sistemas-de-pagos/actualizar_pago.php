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

if (!isset($_POST['id'], $_POST['tipo'], $_POST['etiqueta'], $_POST['valor'])) {
    header("Location: sistemas_de_pagos.php?error=Campos incompletos");
    exit;
}

$id = intval($_POST['id']);
$tipo = limpiar($_POST['tipo']);
$etiqueta = limpiar($_POST['etiqueta']);
$valor = limpiar($_POST['valor']);

if ($tipo === '' || $etiqueta === '' || $valor === '') {
    header("Location: editar_pago.php?id=$id&error=Debes completar todos los campos");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE sistemas_pagos
    SET tipo = :tipo, etiqueta = :etiqueta, valor = :valor
    WHERE id = :id
");

$stmt->bindParam(':tipo', $tipo);
$stmt->bindParam(':etiqueta', $etiqueta);
$stmt->bindParam(':valor', $valor);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header("Location: editar_pago.php?id=$id&exito=Método actualizado correctamente");
    exit;
} else {
    header("Location: editar_pago.php?id=$id&error=No se pudo actualizar el método");
    exit;
}
