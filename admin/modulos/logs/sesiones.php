<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado, redirigimos al login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Carpeta de logs
$rutaLogs = __DIR__ . '/../../../log/';
$archivos = [];

if (file_exists($rutaLogs)) {
    $archivos = array_diff(scandir($rutaLogs), ['.', '..']);
}

$pagina = 'logs';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Logs del sistema</h2>

        <div style="margin-bottom:20px;">
            <a href="guardar_log.php?test=1" class="btn btn-primary">Generar log de prueba</a>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Tamaño</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($archivos)): ?>
                    <tr>
                        <td colspan="3">No hay logs generados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($archivos as $archivo): ?>
                        <tr>
                            <td><?= htmlspecialchars($archivo) ?></td>
                            <td><?= filesize($rutaLogs . $archivo) ?> bytes</td>
                            <td>
                                <a href="ver.php?f=<?= urlencode($archivo) ?>" class="btn btn-sm btn-info">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>