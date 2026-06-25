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
    $archivos = array_values(array_diff(scandir($rutaLogs), ['.', '..']));
}

// Paginación
$por_pagina = 10;
$pagina_actual = max(1, intval($_GET['p'] ?? 1));
$total_registros = count($archivos);
$offset = ($pagina_actual - 1) * $por_pagina;

$archivos_pagina = array_slice($archivos, $offset, $por_pagina);

$pagina = 'logs';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Logs del sistema</h2>

        <a href="guardar_log.php?test=1" class="btn btn-generar">
            <i class="fa-solid fa-file-circle-plus"></i>
            Generar log de prueba
        </a>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'borrado') {
                echo '<p class="alerta-exito">Log borrado correctamente.</p>';
            }
            if ($_GET['msg'] === 'noexiste') {
                echo '<p class="alerta-error">El archivo no existe.</p>';
            }
        }
        ?>

        <div class="tabla-responsive">
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
                            <td data-label="Archivo">No hay logs generados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($archivos_pagina as $archivo): ?>
                            <tr>
                                <td data-label="Archivo"><?= htmlspecialchars($archivo) ?></td>
                                <td data-label="Tamaño"><?= filesize($rutaLogs . $archivo) ?> bytes</td>
                                <td class="acciones-botones">
                                    <a href="ver_log.php?f=<?= urlencode($archivo) ?>" class="btn btn-ver">
                                        <i class="fa-solid fa-eye"></i>
                                        Ver
                                    </a>

                                    <a href="<?= '../../../log/' . urlencode($archivo) ?>"
                                        download="<?= htmlspecialchars($archivo) ?>"
                                        class="btn btn-descargar">
                                        <i class="fa-solid fa-download"></i>
                                        Descargar
                                    </a>

                                    <a href="borrar_log.php?f=<?= urlencode($archivo) ?>"
                                        class="btn btn-borrar"
                                        onclick="return confirm('¿Seguro que deseas borrar este log?');">
                                        <i class="fa-solid fa-trash"></i>
                                        Borrar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        if ($total_registros > $por_pagina) {
            echo paginador($total_registros, $por_pagina, $pagina_actual, $_GET);
        }
        ?>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>