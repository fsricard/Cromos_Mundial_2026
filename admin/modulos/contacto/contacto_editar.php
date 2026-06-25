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
    header("Location: contacto_listado.php?msg=error_id");
    exit;
}

$id = intval($_GET['id']);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Borrar mensaje
    if (isset($_POST['eliminar'])) {
        $stmt = $pdo->prepare("DELETE FROM mensajes_contacto WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: contacto_listado.php?msg=eliminado");
        exit;
    }

    // Guardar cambios
    if (isset($_POST['guardar'])) {

        $stmt = $pdo->prepare("
            UPDATE mensajes_contacto
            SET nombre = ?, email = ?, asunto = ?, mensaje = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['nombre'],
            $_POST['email'],
            $_POST['asunto'],
            $_POST['mensaje'],
            $id
        ]);

        header("Location: contacto_editar.php?id=$id&msg=guardado");
        exit;
    }
}

// Obtener datos del mensaje
$stmt = $pdo->prepare("SELECT * FROM mensajes_contacto WHERE id = ?");
$stmt->execute([$id]);
$mensaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mensaje) {
    header("Location: contacto_listado.php?msg=no_encontrado");
    exit;
}

$pagina = 'contacto_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Editar mensaje de contacto</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
            <div class="alert alert-success">Cambios guardados correctamente.</div>
        <?php endif; ?>

        <form method="POST" class="form-admin">
            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($mensaje['nombre']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($mensaje['email']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Asunto</label>
                <input type="text" name="asunto" value="<?= htmlspecialchars($mensaje['asunto']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="8" required><?= htmlspecialchars($mensaje['mensaje']) ?></textarea>
            </div>

            <div class="form-grupo">
                <label>Fecha de envío</label>
                <input type="text" value="<?= date("d/m/Y H:i", strtotime($mensaje['fecha'])) ?>" disabled>
            </div>

            <button type="submit" name="guardar" class="btn btn-ver">
                <i class="fa-solid fa-floppy-disk"></i> Guardar
            </button>

            <button type="submit" name="eliminar" class="btn btn-borrar"
                onclick="return confirm('¿Seguro que deseas borrar este mensaje?');">
                <i class="fa-solid fa-trash"></i> Borrar
            </button>

            <a href="contacto_listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>