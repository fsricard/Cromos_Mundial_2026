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

// Filtros
$where = [];
$params = [];

if (!empty($_GET['codigo'])) {
    $where[] = "codigo LIKE :codigo";
    $params[':codigo'] = "%" . $_GET['codigo'] . "%";
}

if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['seleccion'])) {
    $where[] = "seleccion LIKE :seleccion";
    $params[':seleccion'] = "%" . $_GET['seleccion'] . "%";
}

if (!empty($_GET['posicion'])) {
    $where[] = "posicion LIKE :posicion";
    $params[':posicion'] = "%" . $_GET['posicion'] . "%";
}

if (!empty($_GET['rareza'])) {
    $where[] = "rareza = :rareza";
    $params[':rareza'] = $_GET['rareza'];
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Total de registros
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM cromos $where_sql");
$stmt_total->execute($params);
$total_cromos = $stmt_total->fetchColumn();

// Consulta principal
$sql = "
    SELECT id, codigo, nombre, seleccion, posicion, rareza, imagen, creado_en
    FROM cromos
    $where_sql
    ORDER BY id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

// Bind dinámico de filtros
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$cromos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'cromos_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Listado de cromos</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Código</label>
                <input type="text" name="codigo" value="<?= isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Selección</label>
                <input type="text" name="seleccion" value="<?= isset($_GET['seleccion']) ? htmlspecialchars($_GET['seleccion']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Posición</label>
                <input type="text" name="posicion" value="<?= isset($_GET['posicion']) ? htmlspecialchars($_GET['posicion']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Rareza</label>
                <select name="rareza">
                    <option value="">Todas</option>
                    <option value="comun" <?= (isset($_GET['rareza']) && $_GET['rareza'] === 'comun') ? 'selected' : '' ?>>Común</option>
                    <option value="raro" <?= (isset($_GET['rareza']) && $_GET['rareza'] === 'raro') ? 'selected' : '' ?>>Raro</option>
                    <option value="epico" <?= (isset($_GET['rareza']) && $_GET['rareza'] === 'epico') ? 'selected' : '' ?>>Épico</option>
                    <option value="legendario" <?= (isset($_GET['rareza']) && $_GET['rareza'] === 'legendario') ? 'selected' : '' ?>>Legendario</option>
                </select>
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="cromos_listado.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Selección</th>
                        <th>Posición</th>
                        <th>Rareza</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($cromos)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:20px;">
                                No hay cromos registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cromos as $c): ?>

                            <?php
                            $ruta_imagen = !empty($c['imagen'])
                                ? asset($c['imagen'])
                                : asset("/uploads/cromos/default/Default.png");
                            ?>

                            <tr>
                                <td>
                                    <img src="<?= $ruta_imagen ?>"
                                        class="miniatura-cromo"
                                        onclick="verImagenCompleta('<?= $ruta_imagen ?>')">
                                </td>

                                <td><?= htmlspecialchars($c['codigo']) ?></td>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['seleccion']) ?></td>
                                <td><?= htmlspecialchars($c['posicion']) ?></td>
                                <td><?= ucfirst($c['rareza']) ?></td>

                                <td>

                                    <?php if (esAdmin()): ?>
                                        <a href="cromo_editar.php?id=<?= $c['id'] ?>" class="btn btn-ver">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>

                                        <a href="cromo_eliminar.php?id=<?= $c['id'] ?>"
                                            class="btn btn-borrar"
                                            onclick="return confirm('¿Seguro que deseas eliminar este cromo?');">
                                            <i class="fa-solid fa-trash"></i> Borrar
                                        </a>
                                    <?php endif; ?>

                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <?php
        if ($total_cromos > $por_pagina) {
            echo paginador($total_cromos, $por_pagina, $pagina_actual, $_GET, 'pagina');
        }
        ?>

    </section>
</main>

<!-- Modal imagen -->
<div id="modalImagen" class="modal-imagen" onclick="cerrarModal()">
    <img id="imagenGrande" src="">
</div>

<style>
    .miniatura-cromo {
        width: 50px;
        height: 50px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 4px;
    }

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