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
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
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
        <h2>Editar usuario</h2>

        <a href="users_panel.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <form action="procesar_editar.php" method="POST" class="form-admin">

            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <div class="form-grupo">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="form-grupo">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <div class="form-grupo">
                <label for="rol">Rol del usuario</label>
                <select id="rol" name="rol" required>
                    <option value="visitante" <?= $usuario['rol'] === 'visitante' ? 'selected' : '' ?>>Visitante</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <div class="form-grupo">
                <label for="clave">Nueva contraseña (opcional)</label>
                <input type="password" id="clave" name="clave" placeholder="Déjalo vacío para no cambiarla">
            </div>

            <button type="submit" class="btn btn-generar">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>