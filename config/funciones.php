<?php
// Función para restringir contenido solo para el rol "admin"
function esAdmin()
{
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
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
        ''                          => 'Cromos Mundial 2026',
        'contacto'                  => 'Contacto - Cromos Mundial 2026',
    ];

    // Imprime el texto correspondiente o uno por defecto
    echo $textos[$pagina] ?? 'Cromos Mundial 2026';
}

// Función para mostrar el CopyRight en el footer
function CopyrightRicardFS($startYear = 2021)
{
    $currentYear = date('Y');
    $yearDisplay = ($startYear == $currentYear) ? $currentYear : "$startYear – $currentYear";
    return "&copy; $yearDisplay Cromos Mundial 2026 - Todos los derechos reservados";
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

// Función que genera rutas absolutas correctas para assets.
function asset(string $ruta): string
{
    // Asegura que base_url() NO termina con "/"
    $base = rtrim(base_url(), '/');

    // Asegura que la ruta SÍ empieza con "/"
    $ruta = '/' . ltrim($ruta, '/');

    return $base . $ruta;
}

// Función universal para guardar logs del panel de administración
function guardarLog($nombre, $mensaje)
{
    $rutaLogs = __DIR__ . '/../log/';

    if (!file_exists($rutaLogs)) {
        mkdir($rutaLogs, 0777, true);
    }

    $fecha = date('Y-m-d');
    $archivo = $rutaLogs . "{$nombre}-{$fecha}.log";

    $hora = date('H:i:s');
    $linea = "[$hora] $mensaje" . PHP_EOL;

    file_put_contents($archivo, $linea, FILE_APPEND);
}

// Función universal para cargar el editor visual de Quill
function editor_quill($nombreCampo, $valor = '')
{
    $id = htmlspecialchars($nombreCampo);

    return '
        <div class="quill-editor" data-target="' . $id . '"></div>
        <textarea id="' . $id . '" name="' . $id . '" class="editor-html" style="display:none;">'
        . $valor .
        '</textarea>
    ';
}

// Función para limpiar cadenas de texto
function limpiar($cadena)
{
    if (!isset($cadena)) return '';
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    $cadena = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
    return $cadena;
}

// Función para limpiar los nombres de los archivos
function limpiarNombreArchivo($nombre)
{
    // Pasar a minúsculas
    $nombre = mb_strtolower($nombre, 'UTF-8');

    // Reemplazar espacios por guiones
    $nombre = preg_replace('/\s+/', '-', $nombre);

    // Eliminar caracteres no permitidos (solo letras, números, guiones y guiones bajos)
    $nombre = preg_replace('/[^a-z0-9\-_.]/', '', $nombre);

    // Evitar múltiples guiones seguidos
    $nombre = preg_replace('/-+/', '-', $nombre);

    // Trim de guiones al principio y final
    $nombre = trim($nombre, '-');

    return $nombre;
}

// Función para cargar los datos de los usuarios del FrontEnd
function cargarDatosUsuarioFrontend($email)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM usuarios_frontend
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['usuario_frontend'] = [
            'id'             => $usuario['id'],
            'nombre'         => $usuario['nombre'],
            'email'          => $usuario['email'],
            'telefono'       => $usuario['telefono'],
            'ciudad'         => $usuario['ciudad'],
            'provincia'      => $usuario['provincia'],
            'foto'           => $usuario['foto'],
            'creado_en'      => $usuario['creado_en'],
            'actualizado_en' => $usuario['actualizado_en'],
            'ultimo_acceso'  => $usuario['ultimo_acceso'],
            'estado'         => $usuario['estado']
        ];
    }
}

// Función para crear el archivo "log/restablecer_clave_frontend.log"
function logCorreo($destinatario, $asunto, $estado)
{
    $ruta = __DIR__ . '/../log/restablecer_clave_frontend.log';

    // Crear carpeta si no existe
    $carpeta = dirname($ruta);
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $fecha = date('Y-m-d H:i:s');
    $ip    = $_SERVER['REMOTE_ADDR'] ?? 'IP desconocida';

    $linea = "[$fecha] [$ip] Destinatario: $destinatario | Asunto: $asunto | Estado: $estado" . PHP_EOL;

    file_put_contents($ruta, $linea, FILE_APPEND);
}

// Función para enviar correos electrónicos de restablecimiento de contraseña del FrontEnd
function enviarCorreo($destinatario, $asunto, $mensajeHTML)
{
    require_once __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../includes/PHPMailer/SMTP.php';
    require_once __DIR__ . '/../includes/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ricardofernandezsoriano@gmail.com';
        $mail->Password   = 'ofnx sluc mtev hzwg';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remitente
        $mail->setFrom('ricardofernandezsoriano@gmail.com', 'Cromos Mundial 2026');

        // Destinatario
        $mail->addAddress($destinatario);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;

        // Plantilla elegante
        $mail->Body = plantillaCorreoHTML($asunto, $mensajeHTML);

        // Alternativa en texto plano
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $mensajeHTML));

        // Enviar
        $mail->send();

        logCorreo($destinatario, $asunto, 'OK');
        return true;
    } catch (Exception $e) {
        logCorreo($destinatario, $asunto, 'ERROR: ' . $mail->ErrorInfo);
        return false;
    }
}

// Función para generar una plantilla HTML elegante para los correos electrónicos
function plantillaCorreoHTML($titulo, $contenido)
{
    return '
    <div style="
        background: #0e0e0e;
        padding: 40px;
        font-family: system-ui, sans-serif;
        color: #eaeaea;
    ">
        <div style="
            max-width: 600px;
            margin: auto;
            background: #1a1a1a;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #222;
        ">
            <h2 style="
                margin-top: 0;
                color: #00b4d8;
                text-align: center;
                font-size: 1.6rem;
            ">
                ' . htmlspecialchars($titulo) . '
            </h2>

            <div style="font-size: 1rem; line-height: 1.6;">
                ' . $contenido . '
            </div>

            <hr style="border: none; border-top: 1px solid #333; margin: 25px 0;">

            <p style="font-size: 0.85rem; color: #888; text-align: center;">
                Cromos Mundial 2026<br>
                Este es un mensaje automático, por favor no respondas.
            </p>
        </div>
    </div>';
}

// Función para generar el contenido del correo de restablecimiento de contraseña
function generarContenidoRestablecerClave($enlace)
{
    return "
        <p>Hola,</p>

        <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>

        <p style='margin-top: 25px; text-align: center;'>
            <a href='$enlace' style='
                background: #00b4d8;
                color: #000;
                padding: 12px 22px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                display: inline-block;
            '>
                Restablecer contraseña
            </a>
        </p>

        <p style='margin-top: 25px;'>
            Si no has solicitado este cambio, puedes ignorar este mensaje.
        </p>

        <p style='margin-top: 15px; font-size: 0.9rem; color: #aaa;'>
            Este enlace es válido durante 1 hora.
        </p>
    ";
}
