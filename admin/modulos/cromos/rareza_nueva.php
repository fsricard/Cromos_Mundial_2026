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

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre === "") {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO rarezas_cromos (nombre, descripcion) VALUES (:nombre, :descripcion)");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);

        if ($stmt->execute()) {
            header("Location: rareza_listado.php?exito=1");
            exit;
        } else {
            $errores[] = "Error al guardar la rareza.";
        }
    }
}

$pagina = 'rareza_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Nueva rareza</h2>

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
                <input type="text" name="nombre" required>
            </div>

            <div class="form-grupo">
                <label>Descripción</label>
                <textarea name="descripcion"></textarea>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
            <?php endif; ?>

            <a href="rareza_listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>