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

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'ID de usuario no válido.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del usuario
$stmt = $pdo->prepare("
    SELECT id, nombre, email, telefono, ciudad, provincia, estado
    FROM usuarios_frontend
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'El usuario no existe.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = limpiar($_POST['nombre']);
    $email = limpiar($_POST['email']);
    $telefono = limpiar($_POST['telefono']);
    $ciudad = limpiar($_POST['ciudad']);
    $provincia = limpiar($_POST['provincia']);
    $estado = limpiar($_POST['estado']);

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($telefono)) {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => 'Nombre, email y teléfono son obligatorios.'
        ];
        header("Location: usuarios_frontend_editar.php?id=$id");
        exit;
    }

    // Comprobar email duplicado
    $stmtCheck = $pdo->prepare("
        SELECT id FROM usuarios_frontend 
        WHERE email = :email AND id != :id
    ");
    $stmtCheck->execute([':email' => $email, ':id' => $id]);

    if ($stmtCheck->fetch()) {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => 'Ya existe un usuario con ese email.'
        ];
        header("Location: usuarios_frontend_editar.php?id=$id");
        exit;
    }

    // Comprobar teléfono duplicado
    $stmtCheckTel = $pdo->prepare("
        SELECT id FROM usuarios_frontend 
        WHERE telefono = :telefono AND id != :id
    ");
    $stmtCheckTel->execute([':telefono' => $telefono, ':id' => $id]);

    if ($stmtCheckTel->fetch()) {
        $_SESSION['mensaje'] = [
            'tipo' => 'error',
            'texto' => 'Ya existe un usuario con ese número de teléfono.'
        ];
        header("Location: usuarios_frontend_editar.php?id=$id");
        exit;
    }

    // Actualizar usuario
    $stmtUpdate = $pdo->prepare("
        UPDATE usuarios_frontend
        SET nombre = :nombre,
            email = :email,
            telefono = :telefono,
            ciudad = :ciudad,
            provincia = :provincia,
            estado = :estado,
            actualizado_en = NOW()
        WHERE id = :id
    ");

    $stmtUpdate->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':telefono' => $telefono,
        ':ciudad' => $ciudad,
        ':provincia' => $provincia,
        ':estado' => $estado,
        ':id' => $id
    ]);

    $_SESSION['mensaje'] = [
        'tipo' => 'exito',
        'texto' => 'Usuario actualizado correctamente.'
    ];

    header("Location: usuarios_frontend_editar.php?id=$id");
    exit;
}

$pagina = 'usuarios_frontend_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Editar usuario <?= htmlspecialchars($usuario['nombre']) ?></h2>

        <a href="usuarios_frontend_ver.php?id=<?= $usuario['id'] ?>" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver a la ficha
        </a>

        <a href="usuarios_frontend_listado.php?id=<?= $usuario['id'] ?>" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <form method="POST" class="form-admin">

            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Ciudad</label>
                <input type="text" name="ciudad" value="<?= htmlspecialchars($usuario['ciudad']) ?>">
            </div>

            <div class="form-grupo">
                <label>Provincia</label>
                <input type="text" name="provincia" value="<?= htmlspecialchars($usuario['provincia']) ?>">
            </div>

            <div class="form-grupo">
                <label>Estado</label>
                <select name="estado">
                    <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="suspendido" <?= $usuario['estado'] === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                    <option value="eliminado" <?= $usuario['estado'] === 'eliminado' ? 'selected' : '' ?>>Eliminado</option>
                </select>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-generar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            <?php endif; ?>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php');
