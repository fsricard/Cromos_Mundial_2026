<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Obtener contenido actual
$stmt = $pdo->query("SELECT * FROM politica_privacidad LIMIT 1");
$politica = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, lo creamos vacío
if (!$politica) {
    $pdo->query("INSERT INTO politica_privacidad (contenido) VALUES ('')");
    $stmt = $pdo->query("SELECT * FROM politica_privacidad LIMIT 1");
    $politica = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("UPDATE politica_privacidad SET contenido = ? WHERE id = ?");
    $stmt->execute([$_POST['contenido'], $politica['id']]);

    header("Location: politica_de_privacidad.php?msg=guardado");
    exit;
}

$pagina = 'politica_de_privacidad';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Texto de la página de política de privacidad</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
            <div class="alert alert-success">Contenido actualizado correctamente.</div>
        <?php endif; ?>

        <form method="POST" class="form-quill">
            <div class="form-grupo">
                <label>Contenido</label>
                <?= editor_quill('contenido', $politica['contenido']) ?>
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>
        </form>

    </section>
</main>

<?php include('../../includes/footer.php');
