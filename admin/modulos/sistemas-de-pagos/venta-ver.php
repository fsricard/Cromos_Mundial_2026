<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: venta-listado.php?error=ID no válido");
    exit;
}

$id_venta = intval($_GET['id']);

// Obtener datos de la venta + datos del cromo
$sql = "
    SELECT 
        cv.id AS id_venta,
        cv.precio,
        cv.cantidad,
        cv.estado,
        cv.fecha_publicacion,
        c.id AS id_cromo,
        c.codigo,
        c.nombre,
        c.seleccion,
        c.posicion,
        c.rareza,
        c.imagen,
        c.creado_en,
        c.actualizado_en
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    WHERE cv.id = :id
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    header("Location: venta-listado.php?error=Venta no encontrada");
    exit;
}

// Construir ruta absoluta de la imagen
$ruta_imagen = !empty($venta['imagen'])
    ? asset($venta['imagen'])
    : asset("uploads/cromos/default/Default.png");

$pagina = 'Listado de cromos en venta';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Ficha del cromo en venta: <strong><?= htmlspecialchars($venta['nombre']) ?></strong></h2>

        <div class="ficha-usuario">

            <!-- Imagen -->
            <div class="ficha-bloque">
                <img src="<?= $ruta_imagen ?>"
                    class="imagen-ficha"
                    onclick="verImagenCompleta('<?= $ruta_imagen ?>')">
            </div>

            <!-- Datos del cromo -->
            <div class="ficha-bloque">
                <p><strong>ID Venta:</strong> <?= $venta['id_venta'] ?></p>
                <p><strong>ID Cromo:</strong> <?= $venta['id_cromo'] ?></p>
                <p><strong>Código:</strong> <?= htmlspecialchars($venta['codigo']) ?></p>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($venta['nombre']) ?></p>
                <p><strong>Selección:</strong> <?= htmlspecialchars($venta['seleccion']) ?></p>
                <p><strong>Posición:</strong> <?= htmlspecialchars($venta['posicion']) ?></p>
                <p><strong>Rareza:</strong> <?= ucfirst($venta['rareza']) ?></p>
                <p><strong>Creado en:</strong> <?= $venta['creado_en'] ?></p>
                <p><strong>Actualizado en:</strong> <?= $venta['actualizado_en'] ?></p>
            </div>

            <!-- Datos de la venta -->
            <div class="ficha-bloque">
                <p><strong>Precio:</strong> <?= number_format($venta['precio'], 2) ?> €</p>
                <p><strong>Cantidad:</strong> <?= $venta['cantidad'] ?></p>
                <p><strong>Estado:</strong> <?= ucfirst($venta['estado']) ?></p>
                <p><strong>Fecha de publicación:</strong> <?= $venta['fecha_publicacion'] ?></p>
            </div>

            <!-- Acciones -->
            <div class="ficha-bloque">

                <?php if (esAdmin()): ?>
                    <a href="venta-editar.php?id=<?= $venta['id_venta'] ?>" class="btn btn-ver">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>

                    <a href="venta-eliminar.php?id=<?= $venta['id_venta'] ?>"
                        class="btn btn-borrar"
                        onclick="return confirm('¿Seguro que deseas eliminar esta publicación de venta?');">
                        <i class="fa-solid fa-trash"></i> Borrar
                    </a>
                <?php endif; ?>

                <a href="venta-listado.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver al listado
                </a>

            </div>

        </div>

    </section>
</main>

<!-- Modal imagen -->
<div id="modalImagen" class="modal-imagen" onclick="cerrarModal()">
    <img id="imagenGrande" src="">
</div>

<style>
    .modal-imagen {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-imagen img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
    }
</style>

<script>
    function verImagenCompleta(src) {
        document.getElementById('imagenGrande').src = src;
        document.getElementById('modalImagen').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalImagen').style.display = 'none';
    }
</script>

<?php include('../../includes/footer.php'); ?>