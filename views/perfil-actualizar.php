<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/funciones.php';

// Si no está logueado, no tiene sentido mostrar esta página
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$id = $_SESSION['usuario_id'];

// Cargar datos actuales
$nombreActual    = $_SESSION['usuario_nombre'];
$ciudadActual    = $_SESSION['usuario_ciudad'];
$provinciaActual = $_SESSION['usuario_provincia'];
$fotoActual      = $_SESSION['usuario_foto'];

// Recibir datos del formulario
$nombre    = trim($_POST['nombre'] ?? $nombreActual);
$ciudad    = trim($_POST['ciudad'] ?? $ciudadActual);
$provincia = trim($_POST['provincia'] ?? $provinciaActual);

$fotoNueva = $fotoActual;

// Procesar foto (si se envía)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $tmp = $_FILES['foto']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $permitidas)) {

        // Generar nombre seguro
        $nombreSeguro = uniqid('foto_', true) . '.' . $ext;
        $destino = __DIR__ . '/../uploads/usuarios/' . $nombreSeguro;

        // Crear carpeta si no existe
        if (!is_dir(__DIR__ . '/../uploads/usuarios')) {
            mkdir(__DIR__ . '/../uploads/usuarios', 0775, true);
        }

        // Subir nueva foto
        if (move_uploaded_file($tmp, $destino)) {

            // Eliminar foto antigua si existe
            if ($fotoActual && file_exists(__DIR__ . '/../' . $fotoActual)) {
                unlink(__DIR__ . '/../' . $fotoActual);
            }

            // Guardar ruta nueva
            $fotoNueva = 'uploads/usuarios/' . $nombreSeguro;
        }
    }
}

// Actualizar base de datos
$stmt = $pdo->prepare("
    UPDATE usuarios_frontend
    SET nombre = :nombre,
        ciudad = :ciudad,
        provincia = :provincia,
        foto = :foto
    WHERE id = :id
");

$stmt->execute([
    'nombre'    => $nombre,
    'ciudad'    => $ciudad,
    'provincia' => $provincia,
    'foto'      => $fotoNueva,
    'id'        => $id
]);

// Actualizar sesión
$_SESSION['usuario_nombre']    = $nombre;
$_SESSION['usuario_ciudad']    = $ciudad;
$_SESSION['usuario_provincia'] = $provincia;
$_SESSION['usuario_foto']      = $fotoNueva;

// Redirigir al perfil
header("Location: " . asset('/panel?mod=perfil'));
exit;
