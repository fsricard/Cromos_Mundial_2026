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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cromos_listado.php?error=ID no válido");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del cromo
$stmt = $pdo->prepare("SELECT * FROM cromos WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$cromo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cromo) {
    header("Location: cromos_listado.php?error=Cromo no encontrado");
    exit;
}

$errores = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo     = trim($_POST['codigo']);
    $nombre     = trim($_POST['nombre']);
    $seleccion  = trim($_POST['seleccion']);
    $posicion   = trim($_POST['posicion']);
    $rareza     = trim($_POST['rareza']);

    if ($codigo === "") $errores[] = "El código es obligatorio.";
    if ($nombre === "") $errores[] = "El nombre es obligatorio.";
    if ($seleccion === "") $errores[] = "La selección es obligatoria.";

    // Imagen actual
    $imagen_final = $cromo['imagen'];

    // Si se sube una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {

        $archivo = $_FILES['imagen'];

        // Limpiar nombre del jugador para usarlo como nombre de archivo
        $nombre_limpio = strtolower($nombre);
        $nombre_limpio = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $nombre_limpio);
        $nombre_limpio = preg_replace('/[^a-z0-9]+/', '-', $nombre_limpio);
        $nombre_limpio = trim($nombre_limpio, '-');

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $extensiones_permitidas)) {
            $errores[] = "Formato de imagen no permitido. Solo JPG, PNG, WEBP.";
        } else {

            $nombre_archivo = $nombre_limpio . "." . $extension;
            $ruta_destino = __DIR__ . "/../../../uploads/cromos/" . $nombre_archivo;

            // Eliminar imagen anterior si no es la default
            if ($cromo['imagen'] !== "uploads/cromos/default/Default.png") {
                $ruta_anterior = __DIR__ . "/../../../" . $cromo['imagen'];
                if (file_exists($ruta_anterior)) {
                    unlink($ruta_anterior);
                }
            }

            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                // Ruta relativa correcta
                $imagen_final = "uploads/cromos/" . $nombre_archivo;
            } else {
                $errores[] = "Error al subir la nueva imagen.";
            }
        }
    }

    // Si no hay errores → actualizar
    if (empty($errores)) {

        $sql = "UPDATE cromos SET 
                    codigo = :codigo,
                    nombre = :nombre,
                    seleccion = :seleccion,
                    posicion = :posicion,
                    rareza = :rareza,
                    imagen = :imagen
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':codigo', $codigo);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':seleccion', $seleccion);
        $stmt->bindValue(':posicion', $posicion);
        $stmt->bindValue(':rareza', $rareza);
        $stmt->bindValue(':imagen', $imagen_final);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: cromos_listado.php?exito=1");
            exit;
        } else {
            $errores[] = "Error al actualizar el cromo.";
        }
    }
}

$pagina = 'cromos_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Editar cromo</h2>

        <?php if (!empty($errores)): ?>
            <div class="alerta alerta-error">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        // Construir ruta absoluta con asset()
        $ruta_imagen_actual = !empty($cromo['imagen'])
            ? asset($cromo['imagen'])
            : asset("uploads/cromos/default/Default.png");
        ?>

        <form method="POST" enctype="multipart/form-data" class="form-admin">

            <div class="form-grupo">
                <label>Código *</label>
                <input type="text" name="codigo" value="<?= htmlspecialchars($cromo['codigo']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($cromo['nombre']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Selección *</label>
                <input type="text" name="seleccion" value="<?= htmlspecialchars($cromo['seleccion']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Posición</label>
                <input type="text" name="posicion" value="<?= htmlspecialchars($cromo['posicion']) ?>">
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
                <label>Imagen actual</label><br>
                <img src="<?= $ruta_imagen_actual ?>" class="cromo-img-grande">
            </div>

            <div class="form-grupo">
                <label>Subir nueva imagen</label>
                <input type="file" name="imagen" accept="image/*">
                <small>Si subes una nueva imagen, reemplazará la actual.</small>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>

                <a href="duplicar_cromo.php?id=<?php echo $id; ?>"
                    class="btn btn-duplicar">
                    <i class="fa-solid fa-clone"></i> Duplicar cromo
                </a>
            <?php endif; ?>

            <a href="cromos_listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>

        </form>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>