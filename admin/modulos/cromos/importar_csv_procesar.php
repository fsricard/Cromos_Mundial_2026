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

// Validar datos recibidos
if (!isset($_POST['data'])) {
    echo "<main class='content'><section><h2>Error</h2><p>No se recibieron datos para importar.</p></section></main>";
    include('../../includes/footer.php');
    exit;
}

$rows = json_decode($_POST['data'], true);

if (!is_array($rows) || empty($rows)) {
    echo "<main class='content'><section><h2>Error</h2><p>Los datos recibidos no son válidos.</p></section></main>";
    include('../../includes/footer.php');
    exit;
}

// Variables de resultado
$importados = [];
$errores = [];

$imagen_defecto = 'uploads/cromos/default/Default.png';
$carpeta_importar = __DIR__ . '/../../../uploads/importar_cromos/';
$carpeta_destino  = __DIR__ . '/../../../uploads/cromos/';

// Procesar cada fila del CSV
foreach ($rows as $r) {

    try {
        // Insertar cromo sin imagen para obtener ID
        $sql_insert = "INSERT INTO cromos (codigo, nombre, seleccion, posicion, rareza, imagen, creado_en, actualizado_en)
                       VALUES (:codigo, :nombre, :seleccion, :posicion, :rareza, '', NOW(), NOW())";

        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([
            ':codigo'    => $r['codigo'],
            ':nombre'    => $r['nombre'],
            ':seleccion' => $r['seleccion'],
            ':posicion'  => $r['posicion'],
            ':rareza'    => $r['rareza']
        ]);

        $id_nuevo = (int) $pdo->lastInsertId();

        // Gestión de la imagen
        $imagen_csv = trim($r['imagen']);
        $ruta_final = $imagen_defecto;

        if ($imagen_csv !== '') {

            $ruta_origen = $carpeta_importar . $imagen_csv;

            if (file_exists($ruta_origen)) {

                $extension = pathinfo($imagen_csv, PATHINFO_EXTENSION);
                $nombre_limpio = limpiarNombreArchivo($r['nombre']);

                $ruta_final_relativa = "uploads/cromos/" . $nombre_limpio . "-" . $id_nuevo . "." . $extension;
                $ruta_destino = $carpeta_destino . $nombre_limpio . "-" . $id_nuevo . "." . $extension;

                copy($ruta_origen, $ruta_destino);

                $ruta_final = $ruta_final_relativa;
            } else {
                $errores[] = "La imagen <strong>{$imagen_csv}</strong> no existe para el cromo <strong>{$r['nombre']}</strong>. Se usó la imagen por defecto.";
            }
        }

        // Actualizar cromo con la imagen final
        $sql_update = "UPDATE cromos SET imagen = :imagen WHERE id = :id";
        $stmt_up = $pdo->prepare($sql_update);
        $stmt_up->execute([
            ':imagen' => $ruta_final,
            ':id'     => $id_nuevo
        ]);

        // Registrar éxito
        $importados[] = [
            'id'      => $id_nuevo,
            'codigo'  => $r['codigo'],
            'nombre'  => $r['nombre'],
            'imagen'  => $ruta_final
        ];
    } catch (Exception $e) {
        $errores[] = "Error al importar el cromo <strong>{$r['nombre']}</strong>: " . $e->getMessage();
    }
}

$pagina = 'cromos_importar_csv';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Importación completada</h2>

        <h3>Cromos importados correctamente</h3>

        <?php if (!empty($importados)): ?>
            <div class="tabla-responsive">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($importados as $c): ?>
                            <tr>
                                <td><?php echo $c['id']; ?></td>
                                <td><?php echo htmlspecialchars($c['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($c['imagen']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No se importó ningún cromo.</p>
        <?php endif; ?>

        <h3>Errores detectados</h3>

        <?php if (!empty($errores)): ?>
            <ul class="lista-errores">
                <?php foreach ($errores as $err): ?>
                    <li><?php echo $err; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hubo errores.</p>
        <?php endif; ?>

        <a href="cromos_listado.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>