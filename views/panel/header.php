<?php
$usuario = $_SESSION['usuario_nombre'];
$foto    = $_SESSION['usuario_foto'] ? asset('/' . $_SESSION['usuario_foto']) : asset('/img/default-user.png');

$usuario_id = $_SESSION['usuario_id'];

// Cargar ajustes de idioma y región
$stmt = $pdo->prepare("
    SELECT idioma, formato_fecha, zona_horaria
    FROM usuarios_region
    WHERE usuario_id = ?
");
$stmt->execute([$usuario_id]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$region) {
    $region = [
        'idioma'        => 'es',
        'formato_fecha' => 'd/m/Y',
        'zona_horaria'  => 'Europe/Madrid'
    ];
}

// Generar hora y fecha según zona horaria y formato
try {
    $tz = new DateTimeZone($region['zona_horaria']);
} catch (Exception $e) {
    $tz = new DateTimeZone('Europe/Madrid'); // fallback
}

$fechaHora = new DateTime('now', $tz);

// Hora
$hora_formateada = $fechaHora->format('H:i');

// Fecha según formato elegido por el usuario
$fecha_formateada = $fechaHora->format($region['formato_fecha']);

// Zona horaria
$zona_formateada = $region['zona_horaria'];

// Cargar ajustes de notificaciones
$stmt = $pdo->prepare("
    SELECT panel_alertas
    FROM usuarios_notificaciones
    WHERE usuario_id = ?
");
$stmt->execute([$usuario_id]);
$notif = $stmt->fetch(PDO::FETCH_ASSOC);

$mostrar_alertas = $notif ? (bool)$notif['panel_alertas'] : true;

// Si el usuario quiere ver alertas, las cargamos
$alertas = [];

if ($mostrar_alertas) {
    $stmt = $pdo->prepare("
        SELECT pa.id, pa.tipo, pa.mensaje
        FROM panel_alertas pa
        LEFT JOIN panel_alertas_usuarios pau
            ON pa.id = pau.alerta_id AND pau.usuario_id = ?
        WHERE pau.leida IS NULL OR pau.leida = 0
        ORDER BY pa.creada_en DESC
        LIMIT 5
    ");
    $stmt->execute([$usuario_id]);
    $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cargar ajustes visuales
$stmt = $pdo->prepare("SELECT tema, fuente, animaciones FROM usuarios_ajustes WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$ajustes = $stmt->fetch(PDO::FETCH_ASSOC);

// Valores por defecto si no existen
if (!$ajustes) {
    $ajustes = [
        'tema'        => 'oscuro',
        'fuente'      => 'normal',
        'animaciones' => 1
    ];
}

// Convertimos los ajustes en clases CSS
$clase_tema        = $ajustes['tema'] === 'claro' ? 'tema-claro' : 'tema-oscuro';
$clase_fuente      = $ajustes['fuente'] === 'grande' ? 'fuente-grande' : 'fuente-normal';
$clase_animaciones = $ajustes['animaciones'] ? 'animaciones-on' : 'animaciones-off';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>Panel de usuario del usuario</title>

    <!-- FontAwesome 7.0.1 CSS -->
    <link href="<?= asset('/css/fontawesome/css/brands.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/chisel-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/etch-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/fontawesome.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-duo-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-fill-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/jelly-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-duo-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/notdog-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-duotone-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/sharp-thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-press-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/slab-regular.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/solid.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/svg-with-js.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thin.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/thumbprint-light.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v4-shims.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/v5-font-face.css') ?>" rel="stylesheet" />
    <link href="<?= asset('/css/fontawesome/css/whiteboard-semibold.css') ?>" rel="stylesheet" />

    <link rel="stylesheet" href="<?= asset('/css/panel.css') ?>">
</head>

<body class="<?= $clase_tema ?> <?= $clase_fuente ?> <?= $clase_animaciones ?>">

    <header class="panel-header">

        <div class="panel-user">
            <img src="<?= $foto ?>" alt="Foto de perfil" class="panel-avatar">
            <span class="panel-username"><?= htmlspecialchars($usuario) ?></span>
        </div>

        <?php if (!esSoloMovil()): ?>
            <div class="panel-header-center">
                <span class="panel-hora"><?= $hora_formateada ?></span>
                <span class="panel-zona"><?= $zona_formateada ?></span>
                <span class="panel-fecha"><?= $fecha_formateada ?></span>
            </div>
        <?php endif; ?>

        <button class="panel-menu-toggle" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="panel-nav">
            <a href="<?= asset('/inicio') ?>">Página de inicio</a>
            <a href="<?= asset('/panel?mod=perfil') ?>">Perfil</a>
            <a href="<?= asset('/panel?mod=mis-cromos') ?>">Mis cromos</a>
            <a href="<?= asset('/panel?mod=seguridad') ?>">Seguridad</a>
            <a href="<?= asset('/panel?mod=ajustes') ?>">Ajustes</a>
            <a href="<?= asset('/logout') ?>" class="logout">Salir</a>
        </nav>

    </header>

    <?php if ($mostrar_alertas && !empty($alertas)): ?>
        <div class="panel-alertas">
            <?php foreach ($alertas as $alert): ?>
                <div class="panel-alert panel-alert-<?= $alert['tipo'] ?>">
                    <span><?= htmlspecialchars($alert['mensaje']) ?></span>
                    <a href="<?= asset('/alerta-marcar-leida?id=' . $alert['id']) ?>" class="alert-close">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <main class="layout-main">