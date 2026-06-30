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
    $where[] = "(ue.nombre LIKE :usuario OR ur.nombre LIKE :usuario)";
    $params[':usuario'] = "%" . $_GET['usuario'] . "%";
}

if (!empty($_GET['estado'])) {
    $where[] = "i.estado = :estado";
    $params[':estado'] = $_GET['estado'];
}

if (!empty($_GET['fecha'])) {
    $where[] = "DATE(i.fecha) = :fecha";
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
    FROM intercambios i
    INNER JOIN usuarios_frontend ue ON i.id_usuario_emisor = ue.id
    INNER JOIN usuarios_frontend ur ON i.id_usuario_receptor = ur.id
    INNER JOIN cromos co ON i.id_cromo_ofrecido = co.id
    INNER JOIN cromos cs ON i.id_cromo_solicitado = cs.id
    $where_sql
");
$stmt_total->execute($params);
$total_intercambios = $stmt_total->fetchColumn();

// Consulta principal
$sql = "
    SELECT 
        i.id,
        i.estado,
        i.fecha,

        ue.id AS emisor_id,
        ue.nombre AS emisor_nombre,

        ur.id AS receptor_id,
        ur.nombre AS receptor_nombre,

        co.id AS ofrecido_id,
        co.nombre AS ofrecido_nombre,
        co.imagen AS ofrecido_imagen,

        cs.id AS solicitado_id,
        cs.nombre AS solicitado_nombre,
        cs.imagen AS solicitado_imagen

    FROM intercambios i
    INNER JOIN usuarios_frontend ue ON i.id_usuario_emisor = ue.id
    INNER JOIN usuarios_frontend ur ON i.id_usuario_receptor = ur.id
    INNER JOIN cromos co ON i.id_cromo_ofrecido = co.id
    INNER JOIN cromos cs ON i.id_cromo_solicitado = cs.id
    $where_sql
    ORDER BY i.id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$intercambios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'usuarios_frontend_intercambios';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Intercambios entre usuarios</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Usuario (emisor o receptor)</label>
                <input type="text" name="usuario" value="<?= isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                    <option value="aceptado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'aceptado') ? 'selected' : '' ?>>Aceptado</option>
                    <option value="rechazado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'rechazado') ? 'selected' : '' ?>>Rechazado</option>
                    <option value="completado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'completado') ? 'selected' : '' ?>>Completado</option>
                </select>
            </div>

            <div class="form-grupo">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>">
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="usuarios_frontend_intercambios.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Ofrecido</th>
                        <th>Solicitado</th>
                        <th>Emisor</th>
                        <th>Receptor</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($intercambios as $i): ?>

                        <?php
                        $img_ofrecido = $i['ofrecido_imagen']
                            ? asset($i['ofrecido_imagen'])
                            : asset("uploads/cromos/default/Default.png");

                        $img_solicitado = $i['solicitado_imagen']
                            ? asset($i['solicitado_imagen'])
                            : asset("uploads/cromos/default/Default.png");
                        ?>

                        <tr>
                            <td>
                                <img src="<?= $img_ofrecido ?>"
                                    class="miniatura-cromo"
                                    onclick="verImagenCompleta('<?= $img_ofrecido ?>')">
                                <br>
                                <strong><?= htmlspecialchars($i['ofrecido_nombre']) ?></strong>
                            </td>

                            <td>
                                <img src="<?= $img_solicitado ?>"
                                    class="miniatura-cromo"
                                    onclick="verImagenCompleta('<?= $img_solicitado ?>')">
                                <br>
                                <strong><?= htmlspecialchars($i['solicitado_nombre']) ?></strong>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($i['emisor_nombre']) ?></strong><br>
                                ID: <?= $i['emisor_id'] ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($i['receptor_nombre']) ?></strong><br>
                                ID: <?= $i['receptor_id'] ?>
                            </td>

                            <td>
                                <span class="estado estado-<?= $i['estado'] ?>">
                                    <?= ucfirst($i['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <?= date("d/m/Y H:i", strtotime($i['fecha'])) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <?php
        if ($total_intercambios > $por_pagina) {
            echo paginador($total_intercambios, $por_pagina, $pagina_actual, $_GET, 'pagina');
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

    .estado {
        padding: 4px 8px;
        border-radius: 4px;
        color: #fff;
        font-weight: bold;
    }

    .estado-pendiente {
        background: #f0ad4e;
    }

    .estado-aceptado {
        background: #5cb85c;
    }

    .estado-rechazado {
        background: #d9534f;
    }

    .estado-completado {
        background: #0275d8;
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