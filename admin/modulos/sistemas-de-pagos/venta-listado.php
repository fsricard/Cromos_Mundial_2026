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

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Filtros
$filtros = [];
$where = [];

if (!empty($_GET['nombre'])) {
    $where[] = "c.nombre LIKE :nombre";
    $filtros['nombre'] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['seleccion'])) {
    $where[] = "c.seleccion = :seleccion";
    $filtros['seleccion'] = $_GET['seleccion'];
}

if (!empty($_GET['rareza'])) {
    $where[] = "c.rareza = :rareza";
    $filtros['rareza'] = $_GET['rareza'];
}

if (!empty($_GET['estado'])) {
    $where[] = "cv.estado = :estado";
    $filtros['estado'] = $_GET['estado'];
}

$where_sql = empty($where) ? "" : "WHERE " . implode(" AND ", $where);

// Contar registros
$sql_total = "
    SELECT COUNT(*) 
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    $where_sql
";

$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute($filtros);
$total_registros = $stmt_total->fetchColumn();

// Obtener registros
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
    $where_sql
    ORDER BY cv.fecha_publicacion DESC
    LIMIT :offset, :por_pagina
";

$stmt = $pdo->prepare($sql);

foreach ($filtros as $k => $v) {
    $stmt->bindValue(":$k", $v);
}

$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":por_pagina", $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$cromos = $stmt->fetchAll(PDO::FETCH_ASSOC);


$pagina = 'Listado de cromos en venta';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Listado de cromos en venta</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Selección</label>
                <input type="text" name="seleccion" value="<?= isset($_GET['seleccion']) ? htmlspecialchars($_GET['seleccion']) : '' ?>">
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

            <div class="form-grupo">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="disponible" <?= (isset($_GET['estado']) && $_GET['estado'] === 'disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="reservado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'reservado') ? 'selected' : '' ?>>Reservado</option>
                    <option value="vendido" <?= (isset($_GET['estado']) && $_GET['estado'] === 'vendido') ? 'selected' : '' ?>>Vendido</option>
                    <option value="cancelado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="venta-listado.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cromo</th>
                        <th>Selección</th>
                        <th>Rareza</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Publicado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($cromos)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:20px;">
                                No hay cromos en venta.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cromos as $c): ?>
                            <tr>
                                <td><?= $c['id_venta'] ?></td>

                                <td>
                                    <strong><?= htmlspecialchars($c['nombre']) ?></strong><br>
                                    <small><?= htmlspecialchars($c['codigo']) ?></small>
                                </td>

                                <td><?= htmlspecialchars($c['seleccion']) ?></td>
                                <td><?= htmlspecialchars($c['rareza']) ?></td>

                                <td><?= number_format($c['precio'], 2) ?> €</td>

                                <td>
                                    <span class="badge badge-admin"><?= ucfirst($c['estado']) ?></span>
                                </td>

                                <td><?= $c['fecha_publicacion'] ?></td>

                                <td>
                                    <?php if (esAdmin()): ?>

                                        <a href="venta-ver.php?id=<?= $c['id_venta'] ?>" class="btn btn-ver">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </a>

                                        <a href="venta-editar.php?id=<?= $c['id_venta'] ?>" class="btn btn-ver">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>

                                        <a href="venta-eliminar.php?id=<?= $c['id_venta'] ?>"
                                            class="btn btn-borrar"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta publicación de venta?');">
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

        <!-- Paginación -->
        <?php
        if ($total_registros > $por_pagina) {
            echo paginador($total_registros, $por_pagina, $pagina_actual, $_GET, 'pagina');
        }
        ?>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>