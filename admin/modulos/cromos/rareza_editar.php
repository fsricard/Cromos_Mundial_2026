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

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM rarezas_cromos WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$rareza = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rareza) {
    header("Location: rareza_listado.php?error=Rareza no encontrada");
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre === "") {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("
            UPDATE rarezas_cromos
            SET nombre = :nombre, descripcion = :descripcion, activo = :activo
            WHERE id = :id
        ");

        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':activo', $activo);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            header("Location: rareza_editar.php?exito=1");
            exit;
        } else {
            $errores[] = "Error al actualizar la rareza.";
        }
    }
}

$pagina = 'rareza_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Editar rareza</h2>

        <?php if (!empty($errores)): ?>
            <div class="alerta alerta-error">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-admin">

            <div class="form-grupo">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($rareza['nombre']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Descripción</label>
                <textarea name="descripcion"><?= htmlspecialchars($rareza['descripcion']) ?></textarea>
            </div>

            <div class="form-grupo">
                <label>Activo</label>
                <input type="checkbox" name="activo" <?= $rareza['activo'] ? 'checked' : '' ?>>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
            <?php endif; ?>

            <a href="rareza_listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>