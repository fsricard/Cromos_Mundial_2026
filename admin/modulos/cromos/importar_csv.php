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

$pagina = 'cromos_importar_csv';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Importar cromos desde CSV</h2>

        <p>Selecciona un archivo CSV con el formato correcto para importar cromos al sistema.</p>

        <div class="form-admin">
            <h3><i class="fa-solid fa-file-csv icon-csv"></i> Formato del CSV</h3>
            
            <p>El archivo debe contener las siguientes columnas:</p>

            <ul>
                <li><strong>codigo</strong> — Código del cromo</li>
                <li><strong>nombre</strong> — Nombre del jugador</li>
                <li><strong>seleccion</strong> — Selección</li>
                <li><strong>posicion</strong> — Posición</li>
                <li><strong>rareza</strong> — ID de rareza</li>
                <li><strong>imagen</strong> — Nombre del archivo de imagen (opcional)</li>
            </ul>

            <p>Si la columna <strong>imagen</strong> está vacía, se usará la imagen por defecto.</p>
            <p>Si contiene un nombre de archivo, debe existir en:</p>

            <pre>uploads/importar_cromos/</pre>
        </div>

        <form action="importar_csv_preview.php" method="POST" enctype="multipart/form-data" class="form-admin">
            <div class="form-grupo">
                <label for="csv">Seleccionar archivo CSV:</label>
                <input type="file" name="csv" id="csv" accept=".csv" required>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-importar">
                    <i class="fa-solid fa-file-csv"></i> Importar CSV
                </button>
            <?php endif; ?>

        </form>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>