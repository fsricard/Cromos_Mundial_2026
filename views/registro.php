<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones.php';

// Si ya está logueado → redirigir al panel
if (isLoggedIn()) {
    header("Location: " . asset('/panel'));
    exit;
}

// Generar token CSRF
if (empty($_SESSION['csrf_token_registro'])) {
    $_SESSION['csrf_token_registro'] = bin2hex(random_bytes(16));
}

$errores = [];
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token_registro'], $csrf)) {
        $errores[] = "Solicitud no válida.";
    }

    $nombre    = trim($_POST['nombre'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $ciudad    = trim($_POST['ciudad'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $clave     = $_POST['clave'] ?? '';
    $clave2    = $_POST['clave2'] ?? '';

    // Validaciones básicas
    if ($nombre === '' || $email === '' || $telefono === '' || $clave === '' || $clave2 === '') {
        $errores[] = "Todos los campos obligatorios deben estar completos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    if ($clave !== $clave2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($clave) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Validar duplicados
    if (empty($errores)) {

        $stmt = $pdo->prepare("SELECT id FROM usuarios_frontend WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errores[] = "El email ya está registrado.";
        }

        $stmt = $pdo->prepare("SELECT id FROM usuarios_frontend WHERE telefono = :telefono LIMIT 1");
        $stmt->execute(['telefono' => $telefono]);
        if ($stmt->fetch()) {
            $errores[] = "El teléfono ya está registrado.";
        }
    }

    // Validar imagen
    $fotoRuta = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmpName  = $_FILES['foto']['tmp_name'];
        $nombreOriginal = basename($_FILES['foto']['name']);
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $extPermitidas)) {
            $nombreSeguro = uniqid('foto_', true) . '.' . $ext;
            $destino = __DIR__ . '/../uploads/usuarios/' . $nombreSeguro;

            if (!is_dir(__DIR__ . '/../uploads/usuarios')) {
                mkdir(__DIR__ . '/../uploads/usuarios', 0775, true);
            }

            if (move_uploaded_file($tmpName, $destino)) {
                // Ruta relativa para usar en el panel
                $fotoRuta = 'uploads/usuarios/' . $nombreSeguro;
            }
        }
    }

    // Insertar usuario
    if (empty($errores)) {

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios_frontend (nombre, email, telefono, clave, ciudad, provincia, foto)
            VALUES (:nombre, :email, :telefono, :clave, :ciudad, :provincia, :foto)
        ");

        $stmt->execute([
            'nombre'    => $nombre,
            'email'     => $email,
            'telefono'  => $telefono,
            'clave'     => $hash,
            'ciudad'    => $ciudad,
            'provincia' => $provincia,
            'foto'      => $fotoRuta
        ]);

        unset($_SESSION['csrf_token_registro']);

        header("Location: " . asset('/login') . "?registro=ok");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>

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

    <link rel="stylesheet" href="css/frontend-login.css">
</head>

<body>

    <main class="login-container">

        <?php if (!empty($errores)): ?>
            <div class="alert alert-error">
                <?php foreach ($errores as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>

            <h1 class="login-title">Crear una cuenta</h1>

            <label for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo electrónico *</label>
            <input type="email" id="email" name="email" required>

            <label for="telefono">Teléfono *</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia">

            <label for="foto">Foto de perfil (opcional)</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <label for="clave">Contraseña *</label>
            <input type="password" id="clave" name="clave" required>

            <label for="clave2">Repetir contraseña *</label>
            <input type="password" id="clave2" name="clave2" required>

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token_registro']) ?>">

            <button type="submit" class="btn-login">
                <i class="fa-thin fa-person-circle-plus"></i> Crear cuenta
            </button>

            <div class="login-links">
                <a href="<?= asset('/login'); ?>"><i class="fa-sharp fa-light fa-person-to-portal"></i> ¿Ya tienes cuenta? Inicia sesión</a>
            </div>

        </form>

    </main>

</body>

</html>