<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Solo admins pueden crear ventas
if (!isLoggedIn() || !esAdmin()) {
    header("Location: ../../index.php");
    exit;
}

// Obtener lista de cromos disponibles para venta (excluyo los que ya están en venta)
$sql_cromos = "
    SELECT c.id, c.codigo, c.nombre, c.imagen
    FROM cromos c
    WHERE c.id NOT IN (SELECT id_cromo FROM cromos_venta)
    ORDER BY c.nombre ASC
";

$stmt_cromos = $pdo->query($sql_cromos);
$cromos = $stmt_cromos->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de cromos que están en la tabla "intercambios"
$sql_inter = "
    SELECT id_cromo_ofrecido AS id FROM intercambios
    UNION
    SELECT id_cromo_solicitado AS id FROM intercambios
";

$ids_intercambios = array_column($pdo->query($sql_inter)->fetchAll(PDO::FETCH_ASSOC), 'id');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_cromo = isset($_POST['id_cromo']) ? intval($_POST['id_cromo']) : 0;
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    if ($id_cromo <= 0) {
        $error = "Debes seleccionar un cromo.";
    } elseif ($precio <= 0) {
        $error = "El precio debe ser mayor que 0.";
    } elseif ($cantidad <= 0) {
        $error = "La cantidad debe ser mayor que 0.";
    } elseif (!in_array($estado, ['disponible', 'reservado', 'vendido', 'cancelado'])) {
        $error = "Estado no válido.";
    } else {

        // Insertar nueva venta
        $sql_insert = "
            INSERT INTO cromos_venta (id_cromo, precio, cantidad, estado)
            VALUES (:id_cromo, :precio, :cantidad, :estado)
        ";

        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([
            ':id_cromo' => $id_cromo,
            ':precio' => $precio,
            ':cantidad' => $cantidad,
            ':estado' => $estado
        ]);

        header("Location: venta-listado.php?mensaje=Cromo publicado en venta correctamente");
        exit;
    }
}

$pagina = 'Añadir nuevo cromo en venta';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Publicar nuevo cromo en venta</h2>

        <?php if (isset($error)): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-admin">

            <!-- Select de cromos -->
            <div class="form-grupo">
                <label>Seleccionar cromo</label>
                <select name="id_cromo" id="selectorCromo" required>
                    <option value="">-- Seleccionar --</option>

                    <?php foreach ($cromos as $c): ?>
                        <?php
                        $marcado = in_array($c['id'], $ids_intercambios)
                            ? ' (En intercambios)'
                            : '';
                        ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nombre']) ?>
                            (<?= htmlspecialchars($c['codigo']) ?>)
                            <?= $marcado ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Precio -->
            <div class="form-grupo">
                <label>Precio (€)</label>
                <input type="number" step="0.01" name="precio" required>
            </div>

            <!-- Cantidad -->
            <div class="form-grupo">
                <label>Cantidad disponible</label>
                <input type="number" name="cantidad" min="1" required>
            </div>

            <!-- Estado -->
            <div class="form-grupo">
                <label>Estado</label>
                <select name="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="reservado">Reservado</option>
                    <option value="vendido">Vendido</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <!-- Vista previa de la imagen -->
            <div class="form-grupo">
                <label>Vista previa</label>
                <img id="previewImagen" src="" style="max-width:150px; display:none; border-radius:6px;">
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Publicar venta
                </button>
            <?php endif; ?>

            <a href="venta-listado.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>

        </form>

    </section>
</main>

<script>
    // Mostrar imagen del cromo seleccionado
    const selector = document.getElementById('selectorCromo');
    const preview = document.getElementById('previewImagen');

    const cromos = <?= json_encode($cromos) ?>;

    selector.addEventListener('change', function() {
        const id = parseInt(this.value);

        const cromo = cromos.find(c => c.id === id);

        if (cromo && cromo.imagen) {
            preview.src = "<?= asset('') ?>" + cromo.imagen;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
</script>

<?php include('../../includes/footer.php'); ?>