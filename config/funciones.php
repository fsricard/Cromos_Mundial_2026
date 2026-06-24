<?php
// Función para restringir contenido solo para el rol "admin"
function tienePermiso(): bool
{
    $rolesPermitidos = ['admin'];

    return in_array($_SESSION['rol'], $rolesPermitidos, true);
}

// Función para detectar dispositos móviles
function esSoloMovil()
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    return preg_match('/(android.*mobile|iphone|ipod|blackberry|windows phone|webos)/i', $ua);
}

// Función para imprimir textos personalizados en "header.php" del FrontEnd
function mostrarTextoPersonalizado()
{
    // Recupera la ruta desde la variable global
    $pagina = $GLOBALS['pagina_actual'] ?? '';

    // Define los textos personalizados
    $textos = [
        ''                          => 'Yo soy el Diablillo y usted ... no lo es',
    ];

    // Imprime el texto correspondiente o uno por defecto
    echo $textos[$pagina] ?? 'Yo soy el Diablillo y usted ... no lo es';
}

// Función para mostrar el CopyRight en el footer
function CopyrightRicardFS($startYear = 2021)
{
    $currentYear = date('Y');
    $yearDisplay = ($startYear == $currentYear) ? $currentYear : "$startYear – $currentYear";
    return "&copy; $yearDisplay Riqui Mundial 2026 - Todos los derechos reservados";
}

// Función para crear un sistema de paginación modular
function paginador($total_registros, $por_pagina, $pagina_actual, $filtros = [], $param_pagina = 'p')
{

    $total_paginas = max(1, ceil($total_registros / $por_pagina));

    // No queremos arrastrar el parámetro de página en los filtros
    unset($filtros[$param_pagina]);

    // Construir query string con el resto de filtros
    $query = '';
    if (!empty($filtros)) {
        $query = '&' . http_build_query($filtros);
    }

    $html = '<div class="paginacion">';

    // Anterior
    if ($pagina_actual > 1) {
        $html .= '<a class="btn-pag" href="?' . $param_pagina . '=' . ($pagina_actual - 1) . $query . '">Anterior</a>';
    }

    // Números
    for ($i = 1; $i <= $total_paginas; $i++) {
        $activo = ($i == $pagina_actual) ? 'activo' : '';
        $html .= '<a class="btn-pag ' . $activo . '" href="?' . $param_pagina . '=' . $i . $query . '">' . $i . '</a>';
    }

    // Siguiente
    if ($pagina_actual < $total_paginas) {
        $html .= '<a class="btn-pag" href="?' . $param_pagina . '=' . ($pagina_actual + 1) . $query . '">Siguiente</a>';
    }

    $html .= '</div>';

    return $html;
}

// Función para el sistema de mensajes de alerta modular
function mostrarAlerta(string $mensaje, string $tipo = 'success'): string
{
    $tipos = [
        'success' => [
            'icon' => 'fa-circle-check',
            'color' => 'var(--color-success)',
            'bg'    => 'var(--bg-success)'
        ],
        'error' => [
            'icon' => 'fa-circle-xmark',
            'color' => 'var(--color-danger)',
            'bg'    => 'var(--bg-danger)'
        ],
        'info' => [
            'icon' => 'fa-circle-info',
            'color' => 'var(--color-info)',
            'bg'    => 'var(--bg-info)'
        ],
        'warning' => [
            'icon' => 'fa-triangle-exclamation',
            'color' => 'var(--color-warning)',
            'bg'    => 'var(--bg-warning)'
        ]
    ];

    $t = $tipos[$tipo] ?? $tipos['success'];

    return '
        <div class="alerta-global" 
             style="
                background:' . $t['bg'] . ';
                border-left:4px solid ' . $t['color'] . ';
                color:' . $t['color'] . ';
             ">
            <i class="fa-solid ' . $t['icon'] . '"></i>
            ' . htmlspecialchars($mensaje) . '
        </div>
    ';
}

// Función para crear rutas absolutas
function base_url(): string
{
    // Detectar protocolo
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https://'
        : 'http://';

    // Host (dominio + puerto)
    $host = $_SERVER['HTTP_HOST'];

    // Ruta absoluta del proyecto
    $projectPath = realpath(__DIR__ . '/..');

    // Ruta absoluta del DOCUMENT_ROOT
    $rootPath = realpath($_SERVER['DOCUMENT_ROOT']);

    // Calcular subcarpeta correctamente
    $subcarpeta = str_replace('\\', '/', $projectPath);
    $rootPath   = str_replace('\\', '/', $rootPath);

    $subcarpeta = str_replace($rootPath, '', $subcarpeta);

    // Asegurar que empieza con "/"
    $subcarpeta = '/' . ltrim($subcarpeta, '/');

    // Asegurar que NO termina con "/"
    return rtrim($protocolo . $host . $subcarpeta, '/');
}

// Genera rutas absolutas correctas para assets.
function asset(string $ruta): string
{
    // Asegura que base_url() NO termina con "/"
    $base = rtrim(base_url(), '/');

    // Asegura que la ruta SÍ empieza con "/"
    $ruta = '/' . ltrim($ruta, '/');

    return $base . $ruta;
}
