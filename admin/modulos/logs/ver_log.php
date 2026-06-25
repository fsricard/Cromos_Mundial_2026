<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

$rutaLogs = __DIR__ . '/../../../log/';

if (!isset($_GET['f'])) {
    die("Archivo no especificado.");
}

$archivo = basename($_GET['f']);
$ruta = $rutaLogs . $archivo;

if (!file_exists($ruta)) {
    die("El archivo no existe.");
}

$contenido = file_get_contents($ruta);

$pagina = 'ver logs';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Viendo: <?= htmlspecialchars($archivo) ?></h2>

        <pre class="log-view"><?php echo $contenido = trim(file_get_contents($ruta)); ?></pre>

        <a href="sesiones.php" class="btn btn-secondary">Volver</a>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>