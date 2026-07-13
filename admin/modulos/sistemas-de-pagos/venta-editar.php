<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Solo admins pueden editar ventas
if (!isLoggedIn() || !esAdmin()) {
    header("Location: ../../index.php");
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
        cv.estado,
        cv.fecha_publicacion,
        c.id AS id_cromo,
        c.codigo,
        c.nombre,
        c.seleccion,
        c.posicion,
        c.rareza,
        c.imagen
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    WHERE cv.id = :id
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id_venta, PDO::PARAM_INT);
$stmt->execute();
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    header("Location: venta-listado.php?error=Venta no encontrada");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    if ($precio <= 0) {
        $error = "El precio debe ser mayor que 0.";
    } elseif (!in_array($estado, ['disponible', 'reservado', 'vendido', 'cancelado'])) {
        $error = "Estado no válido.";
    } else {

        $sql_update = "
            UPDATE cromos_venta
            SET precio = :precio,
                estado = :estado
            WHERE id = :id
            LIMIT 1
        ";

        $stmt_up = $pdo->prepare($sql_update);
        $stmt_up->execute([
            ':precio' => $precio,
            ':estado' => $estado,
            ':id' => $id_venta
        ]);

        header("Location: venta-ver.php?id={$id_venta}&mensaje=Venta actualizada correctamente");
        exit;
    }
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
        <h2>Editar venta del cromo <strong><?= htmlspecialchars($venta['nombre']) ?></strong></h2>

        <?php if (isset($error)): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="ficha-usuario">

            <!-- Imagen -->
            <div class="ficha-bloque">
                <img src="<?= $ruta_imagen ?>"
                    class="imagen-ficha"
                    onclick="verImagenCompleta('<?= $ruta_imagen ?>')">
            </div>

            <!-- Datos del cromo -->
            <div class="ficha-bloque">
                <p><strong>ID Cromo:</strong> <?= $venta['id_cromo'] ?></p>
                <p><strong>Código:</strong> <?= htmlspecialchars($venta['codigo']) ?></p>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($venta['nombre']) ?></p>
                <p><strong>Selección:</strong> <?= htmlspecialchars($venta['seleccion']) ?></p>
                <p><strong>Posición:</strong> <?= htmlspecialchars($venta['posicion']) ?></p>
                <p><strong>Rareza:</strong> <?= ucfirst($venta['rareza']) ?></p>
            </div>

            <!-- Formulario de edición -->
            <div class="ficha-bloque">

                <form method="POST" class="form-admin">

                    <div class="form-grupo">
                        <label>Precio (€)</label>
                        <input type="number" step="0.01" name="precio"
                            value="<?= htmlspecialchars($venta['precio']) ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label>Estado</label>
                        <select name="estado" required>
                            <option value="disponible" <?= $venta['estado'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                            <option value="reservado" <?= $venta['estado'] === 'reservado' ? 'selected' : '' ?>>Reservado</option>
                            <option value="vendido" <?= $venta['estado'] === 'vendido' ? 'selected' : '' ?>>Vendido</option>
                            <option value="cancelado" <?= $venta['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>

                    <?php if (esAdmin()): ?>
                        <button type="submit" class="btn btn-ver">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                        </button>
                    <?php endif; ?>

                    <a href="venta-ver.php?id=<?= $venta['id_venta'] ?>" class="btn btn-volver">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>

                </form>

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