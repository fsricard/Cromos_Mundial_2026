<?php
// Filtros
$nombre     = $_GET['nombre']     ?? '';
$seleccion  = $_GET['seleccion']  ?? '';
$precio_max = $_GET['precio_max'] ?? '';
$pagina     = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

$por_pagina = 12;
$offset     = ($pagina - 1) * $por_pagina;

// Construcción dinámica del WHERE
$where = "WHERE cv.estado = 'disponible'";
$params = [];

if (!empty($nombre)) {
    $where .= " AND c.nombre LIKE :nombre";
    $params[':nombre'] = "%$nombre%";
}

if (!empty($seleccion)) {
    $where .= " AND c.seleccion LIKE :seleccion";
    $params[':seleccion'] = "%$seleccion%";
}

if (!empty($precio_max)) {
    $where .= " AND cv.precio <= :precio_max";
    $params[':precio_max'] = $precio_max;
}

// Paginación
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    $where
");
$stmtTotal->execute($params);
$total_registros = $stmtTotal->fetchColumn();

// Consulta principal
$stmt = $pdo->prepare("
    SELECT cv.id AS venta_id, cv.precio, cv.estado,
           c.id AS cromo_id, c.codigo, c.nombre, c.seleccion,
           c.posicion, c.rareza, c.imagen
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    $where
    ORDER BY cv.fecha_publicacion DESC
    LIMIT $offset, $por_pagina
");
$stmt->execute($params);
$cromos = $stmt->fetchAll();
?>

<main class="layout-main">

    <section class="content">
        <article>

            <h2 class="content-title">
                <i class="fa-duotone fa-cart-shopping"></i>
                Listado de cromos en venta
            </h2>

            <!-- Filtros -->
            <form class="filtros-busqueda" method="GET">
                <div class="filtro-item">
                    <label>Nombre del jugador</label>
                    <input type="text" name="nombre"
                        value="<?= htmlspecialchars($nombre) ?>"
                        placeholder="Ej: Messi, Mbappé...">
                </div>

                <div class="filtro-item">
                    <label>Selección / País</label>
                    <input type="text" name="seleccion"
                        value="<?= htmlspecialchars($seleccion) ?>"
                        placeholder="Ej: España, Brasil...">
                </div>

                <div class="filtro-item">
                    <label>Precio máximo</label>
                    <input type="number" name="precio_max" step="0.01"
                        value="<?= htmlspecialchars($precio_max) ?>"
                        placeholder="Ej: 10.00">
                </div>

                <div class="filtros-botones">
                    <button class="btn btn-buscar" type="submit">
                        <i class="fa-jelly fa-regular fa-magnifying-glass"></i> Buscar
                    </button>

                    <a href="<?= asset('/cromos-venta-listado') ?>" class="btn btn-limpiar">
                        <i class="fa-regular fa-broom-wide"></i> Limpiar filtros
                    </a>
                </div>
            </form>

            <!-- Listado de cromos -->
            <div class="cromos-listado">
                <?php if (empty($cromos)): ?>
                    <p>No se encontraron cromos con los filtros aplicados.</p>
                <?php endif; ?>

                <?php foreach ($cromos as $cromo): ?>

                    <?php
                    $ruta_imagen = !empty($cromo['imagen'])
                        ? asset($cromo['imagen'])
                        : asset("/uploads/cromos/default/Default.png");
                    ?>

                    <div class="cromo-item">

                        <div class="cromo-thumb">
                            <img src="<?= $ruta_imagen ?>"
                                alt="<?= htmlspecialchars($cromo['nombre']) ?>">
                        </div>

                        <div class="cromo-data">
                            <h3><?= htmlspecialchars($cromo['nombre']) ?></h3>
                            <p class="seleccion"><?= htmlspecialchars($cromo['seleccion']) ?></p>
                            <p class="rareza <?= $cromo['rareza'] ?>">
                                <?= ucfirst($cromo['rareza']) ?>
                            </p>
                            <p class="precio"><?= number_format($cromo['precio'], 2) ?> €</p>
                        </div>

                        <div class="cromo-actions">
                            <a href="<?= asset('/favorito?cromo=' . $cromo['cromo_id']) ?>"
                                class="btn btn-fav">
                                <i class="fa-solid fa-heart"></i> Favorito
                            </a>

                            <a href="<?= asset('/comprar?venta=' . $cromo['venta_id']) ?>"
                                class="btn btn-comprar">
                                <i class="fa-duotone fa-cart-shopping"></i> Comprar
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <div class="paginacion-wrapper">
                <?= paginador($total_registros, $por_pagina, $pagina, $_GET, 'p') ?>
            </div>

        </article>
    </section>

</main>