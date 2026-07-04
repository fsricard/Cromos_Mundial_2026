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

// Validar ID y cargar cromo
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: cromos_listado.php?error=ID no proporcionado");
    exit;
}

$id_original = (int) $_GET['id'];

$sql = "SELECT * FROM cromos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_original]);
$cromo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cromo) {
    header("Location: cromos_listado.php?error=Cromo no encontrado");
    exit;
}

// Preparar datos del nuevo cromo
$nombre           = $cromo['nombre'];
$seleccion        = $cromo['seleccion'];
$posicion         = $cromo['posicion'];
$rareza           = $cromo['rareza'];
$codigo_original  = $cromo['codigo'];
$imagen_original  = $cromo['imagen']; // puede estar vacío → imagen por defecto

$imagen_defecto   = 'uploads/cromos/default/Default.png';

// Insertar nuevo cromo (sin código ni imagen)
$sql_insert = "INSERT INTO cromos (codigo, nombre, seleccion, posicion, rareza, imagen, creado_en, actualizado_en)
               VALUES ('', :nombre, :seleccion, :posicion, :rareza, '', NOW(), NOW())";

$stmt_insert = $pdo->prepare($sql_insert);
$stmt_insert->execute([
    ':nombre'    => $nombre,
    ':seleccion' => $seleccion,
    ':posicion'  => $posicion,
    ':rareza'    => $rareza
]);

$id_nuevo = (int) $pdo->lastInsertId();

// Generar nuevo código
$codigo_nuevo = $codigo_original . '-' . $id_nuevo;

// Gestión de la imagen
$nueva_ruta_imagen = $imagen_defecto;

// Si el cromo original tiene imagen propia
if (!empty($imagen_original) && $imagen_original !== $imagen_defecto) {

    $extension      = pathinfo($imagen_original, PATHINFO_EXTENSION);
    $nombre_limpio  = limpiarNombreArchivo($nombre); // función creada en funciones.php

    // Nueva imagen: NOMBRELIMPIO-ID.ext
    $nueva_ruta_imagen = "uploads/cromos/" . $nombre_limpio . "-" . $id_nuevo . "." . $extension;

    $ruta_fisica_original = __DIR__ . "/../../../" . $imagen_original;
    $ruta_fisica_nueva    = __DIR__ . "/../../../" . $nueva_ruta_imagen;

    if (file_exists($ruta_fisica_original)) {
        copy($ruta_fisica_original, $ruta_fisica_nueva);
    } else {
        // Si no existe la imagen original, usar la default
        $nueva_ruta_imagen = $imagen_defecto;
    }
}

// Actualizar el registro duplicado
$sql_update = "UPDATE cromos 
               SET codigo = :codigo, imagen = :imagen, actualizado_en = NOW()
               WHERE id = :id";

$stmt_update = $pdo->prepare($sql_update);
$stmt_update->execute([
    ':codigo' => $codigo_nuevo,
    ':imagen' => $nueva_ruta_imagen,
    ':id'     => $id_nuevo
]);

// Redirección final
header("Location: cromo_ver.php?id=$id_nuevo&exito=Cromo duplicado correctamente");
exit;
?>

<main class="content">
    <section>
        <h2>Duplicando cromo...</h2>
        <p>Redirigiendo...</p>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>