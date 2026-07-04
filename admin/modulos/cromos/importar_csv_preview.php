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

// Vañodar subida de CSV
if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
    echo "<main class='content'><section><h2>Error</h2><p>No se ha subido ningún archivo CSV.</p></section></main>";
    include('../../includes/footer.php');
    exit;
}

$csv_tmp = $_FILES['csv']['tmp_name'];

// Leer CSV
$rows = [];
$handle = fopen($csv_tmp, "r");

if (!$handle) {
    echo "<main class='content'><section><h2>Error</h2><p>No se pudo leer el archivo CSV.</p></section></main>";
    include('../../includes/footer.php');
    exit;
}

// Leer encabezados
$headers = fgetcsv($handle, 0, ";");

$headers = array_map('trim', $headers);

// Validar columnas
$required = [
    'codigo',
    'nombre',
    'seleccion',
    'posicion',
    'rareza',
    'imagen'
];

foreach ($required as $col) {
    if (!in_array($col, $headers)) {
        echo "<main class='content'><section><h2>Error</h2>
              <p>El CSV no contiene la columna obligatoria: <strong>$col</strong></p>
              </section></main>";
        include('../../includes/footer.php');
        exit;
    }
}

// Leer todas las filas
while (($data = fgetcsv($handle, 0, ";")) !== false) {
    if (count($data) !== count($headers)) {
        // Saltar filas corruptas
        continue;
    }

    $row = array_combine($headers, $data);

    // Limpiar espacios
    foreach ($row as $k => $v) {
        $row[$k] = trim($v);
    }

    $rows[] = $row;
}

fclose($handle);

// Si no hay filas
if (empty($rows)) {
    echo "<main class='content'><section><h2>Error</h2><p>El CSV está vacío.</p></section></main>";
    include('../../includes/footer.php');
    exit;
}

$pagina = 'cromos_importar_csv';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Previsualización del CSV</h2>
        <p>Revisa los datos antes de confirmar la importación.</p>

        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Selección</th>
                        <th>Posición</th>
                        <th>Rareza</th>
                        <th>Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($r['seleccion']); ?></td>
                            <td><?php echo htmlspecialchars($r['posicion']); ?></td>
                            <td><?php echo htmlspecialchars($r['rareza']); ?></td>
                            <td><?php echo htmlspecialchars($r['imagen']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form action="importar_csv_procesar.php" method="POST" class="form-admin">
            <input type="hidden" name="data" value="<?php echo htmlspecialchars(json_encode($rows)); ?>">

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-check"></i> Confirmar importación
                </button>
            <?php endif; ?>

        </form>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>