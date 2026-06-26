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

// Obtener contenido actual
$stmt = $pdo->query("SELECT * FROM intro_contacto LIMIT 1");
$intro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, lo creamos vacío
if (!$intro) {
    $pdo->query("INSERT INTO intro_contacto (contenido) VALUES ('')");
    $stmt = $pdo->query("SELECT * FROM intro_contacto LIMIT 1");
    $intro = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("UPDATE intro_contacto SET contenido = ? WHERE id = ?");
    $stmt->execute([$_POST['contenido'], $intro['id']]);

    header("Location: contacto_intro.php?msg=guardado");
    exit;
}

$pagina = 'contacto_intro';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Texto de presentación de la página de contacto</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
            <div class="alert alert-success">Contenido actualizado correctamente.</div>
        <?php endif; ?>

        <form method="POST" class="form-quill">
            <div class="form-grupo">
                <label>Contenido</label>
                <?= editor_quill('contenido', $intro['contenido']) ?>
            </div>

            <div class="acciones">

                <?php if (esAdmin()): ?>
                    <button type="submit" class="btn btn-ver">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>
                <?php endif; ?>

                <a href="contacto_listado.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>