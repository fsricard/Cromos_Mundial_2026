<?php
// ==========================================
//  ROUTER PRINCIPAL DEL FRONTEND
// ==========================================

// Sanitizar parámetros
$view  = isset($_GET['view'])  ? trim($_GET['view'])  : 'inicio';
$slug  = isset($_GET['slug'])  ? trim($_GET['slug'])  : null;
$extra = isset($_GET['extra']) ? trim($_GET['extra']) : null;

// ==========================================
//  LISTA DE VISTAS PERMITIDAS
// ==========================================

$rutas_validas = [
    'inicio',
    'contacto',
    'politica-de-privacidad',

    // Autenticación frontend
    'login',
    'registro',
    'restablecer',
    'logout',
    'panel',
    'verificar-email',
    'cambiar-clave'
];

// Si la vista no existe → 404
if (!in_array($view, $rutas_validas)) {
    http_response_code(404);
    $GLOBALS['pagina_actual'] = '404';
    $view = '404';
}

// Define página actual para el header y el footer
$GLOBALS['pagina_actual'] = $view;

// ==========================================
//  EXCLUSIONES DE HEADER Y FOOTER
// ==========================================

$exclusiones_header_footer = [
    'login',
    'registro',
    'restablecer',
    'logout',
    'panel',
    'verificar-email',
    'cambiar-clave'
];

// ==========================================
//  CARGAR HEADER (si procede)
// ==========================================

if (!in_array($view, $exclusiones_header_footer)) {
    require_once __DIR__ . '/includes/header.php';
}

// ==========================================
//  ROUTER DE VISTAS
// ==========================================

switch ($view) {

    case 'inicio':
        require __DIR__ . '/views/inicio.php';
        break;

    case 'contacto':
        require __DIR__ . '/views/contacto.php';
        break;

    case 'politica-de-privacidad':
        require __DIR__ . '/views/politica-de-privacidad.php';
        break;

    // ============================
    //  AUTENTICACIÓN FRONTEND
    // ============================

    case 'login':
        require __DIR__ . '/views/login.php';
        break;

    case 'registro':
        require __DIR__ . '/views/registro.php';
        break;

    case 'restablecer':
        require __DIR__ . '/views/restablecer.php';
        break;

    case 'logout':
        require __DIR__ . '/views/logout.php';
        break;

    case 'panel':
        require __DIR__ . '/views/panel.php';
        break;

    case 'verificar-email':
        require __DIR__ . '/views/verificar-email.php';
        break;

    case 'cambiar-clave':
        require __DIR__ . '/views/cambiar-clave.php';
        break;

    // ============================
    //  404
    // ============================

    default:
        require __DIR__ . '/views/404.php';
        break;
}

// ==========================================
//  CARGAR FOOTER (si procede)
// ==========================================

if (!in_array($view, $exclusiones_header_footer)) {
    require_once __DIR__ . '/includes/footer.php';
}
