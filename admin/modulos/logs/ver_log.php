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

// Leer líneas, ignorar vacías, invertir orden
$lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$lineas = array_reverse($lineas);
$contenido = implode(PHP_EOL, $lineas);

$pagina = 'ver logs';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Viendo: <?= htmlspecialchars($archivo) ?></h2>

        <pre class="log-view"><?php echo htmlspecialchars($contenido); ?></pre>

        <a href="sesiones.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>