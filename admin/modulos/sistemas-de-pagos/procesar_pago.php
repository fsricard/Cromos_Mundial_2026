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

// Validar que los campos existan
if (!isset($_POST['tipo'], $_POST['etiqueta'], $_POST['valor'])) {
    header("Location: sistemas_de_pagos.php?error=Campos incompletos");
    exit;
}

// Sanitizar entrada
$tipo = limpiar($_POST['tipo']);
$etiqueta = limpiar($_POST['etiqueta']);
$valor = limpiar($_POST['valor']);

// Validación básica
if ($tipo === '' || $etiqueta === '' || $valor === '') {
    header("Location: sistemas_de_pagos.php?error=Debes completar todos los campos");
    exit;
}

// Insertar en la base de datos
$stmt = $pdo->prepare("
    INSERT INTO sistemas_pagos (tipo, etiqueta, valor)
    VALUES (:tipo, :etiqueta, :valor)
");

$stmt->bindParam(':tipo', $tipo);
$stmt->bindParam(':etiqueta', $etiqueta);
$stmt->bindParam(':valor', $valor);

if ($stmt->execute()) {
    header("Location: sistemas_de_pagos.php?exito=Método añadido correctamente");
    exit;
} else {
    header("Location: sistemas_de_pagos.php?error=No se pudo guardar el método");
    exit;
}
