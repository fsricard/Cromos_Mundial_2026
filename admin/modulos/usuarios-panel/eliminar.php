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
    die("ID de usuario no válido.");
}

$id = intval($_GET['id']);

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT id, nombre, correo, rol FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

$pagina = 'users_panel';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Eliminar usuario</h2>

        <a href="users_panel.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <div class="form-admin" style="margin-top:20px;">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
            <p><strong>Rol:</strong> <?= $usuario['rol'] ?></p>

            <p class="p-warning">
                ¿Seguro que deseas eliminar este usuario? Esta acción no se puede deshacer.
            </p>

            <form action="procesar_eliminar.php" method="POST">
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                <button type="submit" class="btn btn-borrar">
                    <i class="fa-solid fa-skull-crossbones"></i> Eliminar definitivamente
                </button>
            </form>
        </div>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>
