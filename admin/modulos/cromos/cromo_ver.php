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

// Construir ruta absoluta de la imagen
$ruta_imagen = !empty($cromo['imagen'])
    ? asset($cromo['imagen'])
    : asset("uploads/cromos/default/Default.png");

$pagina = 'cromos_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Ficha del cromo del jugador <strong><?= htmlspecialchars($cromo['nombre']) ?></strong></h2>

        <div class="ficha-usuario">

            <div class="ficha-bloque">
                <img src="<?= $ruta_imagen ?>"
                    class="imagen-ficha"
                    onclick="verImagenCompleta('<?= $ruta_imagen ?>')">
            </div>

            <div class="ficha-bloque">
                <p><strong>ID:</strong> <?= $cromo['id'] ?></p>
                <p><strong>Código:</strong> <?= htmlspecialchars($cromo['codigo']) ?></p>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($cromo['nombre']) ?></p>
                <p><strong>Selección:</strong> <?= htmlspecialchars($cromo['seleccion']) ?></p>
                <p><strong>Posición:</strong> <?= htmlspecialchars($cromo['posicion']) ?></p>
                <p><strong>Rareza:</strong> <?= ucfirst($cromo['rareza']) ?></p>
                <p><strong>Creado en:</strong> <?= $cromo['creado_en'] ?></p>
                <p><strong>Actualizado en:</strong> <?= $cromo['actualizado_en'] ?></p>
            </div>

            <div class="ficha-bloque">
                <?php if (esAdmin()): ?>
                    <a href="cromo_editar.php?id=<?= $cromo['id'] ?>" class="btn btn-ver">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>

                    <a href="cromo_eliminar.php?id=<?= $cromo['id'] ?>"
                        class="btn btn-borrar"
                        onclick="return confirm('¿Seguro que deseas eliminar este cromo?');">
                        <i class="fa-solid fa-trash"></i> Borrar
                    </a>
                <?php endif; ?>

                <a href="cromos_listado.php" class="btn btn-volver">
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