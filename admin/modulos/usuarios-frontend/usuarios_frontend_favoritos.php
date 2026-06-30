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

if (!empty($_GET['usuario'])) {
    $where[] = "u.nombre LIKE :usuario";
    $params[':usuario'] = "%" . $_GET['usuario'] . "%";
}

if (!empty($_GET['cromo'])) {
    $where[] = "c.nombre LIKE :cromo";
    $params[':cromo'] = "%" . $_GET['cromo'] . "%";
}

if (!empty($_GET['fecha'])) {
    $where[] = "DATE(f.fecha) = :fecha";
    $params[':fecha'] = $_GET['fecha'];
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Total registros
$stmt_total = $pdo->prepare("
    SELECT COUNT(*)
    FROM favoritos f
    INNER JOIN usuarios_frontend u ON f.id_usuario = u.id
    INNER JOIN cromos c ON f.id_cromo = c.id
    $where_sql
");
$stmt_total->execute($params);
$total_favoritos = $stmt_total->fetchColumn();

// Consulta principal
$sql = "
    SELECT 
        f.id,
        f.fecha,
        u.id AS usuario_id,
        u.nombre AS usuario_nombre,
        c.id AS cromo_id,
        c.nombre AS cromo_nombre,
        c.imagen AS cromo_imagen
    FROM favoritos f
    INNER JOIN usuarios_frontend u ON f.id_usuario = u.id
    INNER JOIN cromos c ON f.id_cromo = c.id
    $where_sql
    ORDER BY f.id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'usuarios_frontend_favoritos';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Favoritos de los usuarios</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Usuario</label>
                <input type="text" name="usuario" value="<?= isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Cromo</label>
                <input type="text" name="cromo" value="<?= isset($_GET['cromo']) ? htmlspecialchars($_GET['cromo']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>">
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="usuarios_frontend_favoritos.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Usuario</th>
                        <th>Cromo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($favoritos as $fav): ?>

                        <?php
                        $img = $fav['cromo_imagen']
                            ? asset($fav['cromo_imagen'])
                            : asset("uploads/cromos/default/Default.png");
                        ?>

                        <tr>
                            <td>
                                <img src="<?= $img ?>"
                                    class="miniatura-cromo"
                                    onclick="verImagenCompleta('<?= $img ?>')">
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($fav['usuario_nombre']) ?></strong><br>
                                ID: <?= $fav['usuario_id'] ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($fav['cromo_nombre']) ?></strong><br>
                                ID: <?= $fav['cromo_id'] ?>
                            </td>

                            <td>
                                <?= date("d/m/Y H:i", strtotime($fav['fecha'])) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <?php
        if ($total_favoritos > $por_pagina) {
            echo paginador($total_favoritos, $por_pagina, $pagina_actual, $_GET, 'pagina');
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