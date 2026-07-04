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
$exito = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo     = trim($_POST['codigo']);
    $nombre     = trim($_POST['nombre']);
    $seleccion  = trim($_POST['seleccion']);
    $posicion   = trim($_POST['posicion']);
    $rareza     = trim($_POST['rareza']);

    // Validaciones básicas
    if ($codigo === "") $errores[] = "El código es obligatorio.";
    if ($nombre === "") $errores[] = "El nombre es obligatorio.";
    if ($seleccion === "") $errores[] = "La selección es obligatoria.";

    // Procesar imagen
    $imagen_final = null;

    if (!empty($_FILES['imagen']['name'])) {

        $archivo = $_FILES['imagen'];

        // Limpiar nombre del jugador para usarlo como nombre de archivo
        $nombre_limpio = strtolower($nombre);
        $nombre_limpio = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $nombre_limpio);
        $nombre_limpio = preg_replace('/[^a-z0-9]+/', '-', $nombre_limpio);
        $nombre_limpio = trim($nombre_limpio, '-');

        // Obtener extensión real del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            $errores[] = "Formato de imagen no permitido. Solo JPG, PNG, WEBP.";
        } else {

            // Nombre final del archivo
            $nombre_archivo = $nombre_limpio . "." . $extension;

            // Ruta física donde se guardará
            $ruta_destino = __DIR__ . "/../../../uploads/cromos/" . $nombre_archivo;

            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {

                // Ruta relativa que se guardará en la base de datos
                $imagen_final = "uploads/cromos/" . $nombre_archivo;
            } else {
                $errores[] = "Error al subir la imagen.";
            }
        }
    }

    // Si no hay imagen subida → usar la imagen por defecto
    if ($imagen_final === null) {
        $imagen_final = "uploads/cromos/default/Default.png";
    }

    // Si no hay errores → insertar
    if (empty($errores)) {

        $sql = "INSERT INTO cromos (codigo, nombre, seleccion, posicion, rareza, imagen)
                VALUES (:codigo, :nombre, :seleccion, :posicion, :rareza, :imagen)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':codigo', $codigo);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':seleccion', $seleccion);
        $stmt->bindValue(':posicion', $posicion);
        $stmt->bindValue(':rareza', $rareza);
        $stmt->bindValue(':imagen', $imagen_final);

        if ($stmt->execute()) {
            $exito = "Cromo añadido correctamente.";
            header("Location: cromo_nuevo.php?exito=1");
            exit;
        } else {
            $errores[] = "Error al guardar el cromo en la base de datos.";
        }
    }
}

$pagina = 'cromo_nuevo';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Añadir cromo nuevo</h2>

        <?php if (!empty($errores)): ?>
            <div class="alerta alerta-error">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="alerta alerta-exito">
                <?= $exito ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-admin">

            <div class="form-grupo">
                <label>Código *</label>
                <input type="text" name="codigo" required>
            </div>

            <div class="form-grupo">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="form-grupo">
                <label>Selección *</label>
                <input type="text" name="seleccion" required>
            </div>

            <div class="form-grupo">
                <label>Posición</label>
                <input type="text" name="posicion">
            </div>

            <div class="form-grupo">
                <label>Rareza *</label>
                <?php
                $stmt_r = $pdo->query("SELECT * FROM rarezas_cromos WHERE activo = 1 ORDER BY nombre ASC");
                $rarezas = $stmt_r->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <select name="rareza" required>
                    <?php foreach ($rarezas as $r): ?>
                        <option value="<?= $r['nombre'] ?>" <?= ($cromo['rareza'] ?? '') === $r['nombre'] ? 'selected' : '' ?>>
                            <?= ucfirst($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grupo">
                <label>Imagen del cromo</label>
                <input type="file" name="imagen" accept="image/*">
                <small>Si no subes ninguna imagen, se asignará la imagen por defecto.</small>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
            <?php endif; ?>

            <a href="cromos_listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>