<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../config/database.php');

// Honeypot
if (!empty($_POST['hp_trampa'])) {
    echo json_encode([
        "ok" => false,
        "msg" => "Error al enviar el mensaje."
    ]);
    exit;
}

// Validación básica
$nombre  = trim($_POST['nombre'] ?? '');
$email   = trim($_POST['email'] ?? '');
$asunto  = trim($_POST['asunto'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || $email === '' || $asunto === '' || $mensaje === '') {
    echo json_encode([
        "ok" => false,
        "msg" => "Todos los campos son obligatorios."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "ok" => false,
        "msg" => "El email no es válido."
    ]);
    exit;
}

// Insertar en la BD
$stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, email, asunto, mensaje)
                       VALUES (:nombre, :email, :asunto, :mensaje)");

$stmt->execute([
    ":nombre"  => $nombre,
    ":email"   => $email,
    ":asunto"  => $asunto,
    ":mensaje" => $mensaje
]);

echo json_encode([
    "ok" => true,
    "msg" => "Mensaje enviado correctamente. ¡Gracias por contactar!"
]);
