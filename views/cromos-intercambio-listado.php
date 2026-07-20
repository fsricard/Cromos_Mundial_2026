<?php
// Filtros
$nombre    = $_GET['nombre']    ?? '';
$seleccion = $_GET['seleccion'] ?? '';
$rareza    = $_GET['rareza']    ?? '';
$pagina    = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

$por_pagina = 18;
$offset     = ($pagina - 1) * $por_pagina;

// Construcción del WHERE
$where = "WHERE 1";
$params = [];

if (!empty($nombre)) {
    $where .= " AND c.nombre LIKE :nombre";
    $params[':nombre'] = "%$nombre%";
}

if (!empty($seleccion)) {
    $where .= " AND c.seleccion LIKE :seleccion";
    $params[':seleccion'] = "%$seleccion%";
}

if (!empty($rareza)) {
    $where .= " AND c.rareza = :rareza";
    $params[':rareza'] = $rareza;
}

// Total
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM cromos c
    $where
");
$stmtTotal->execute($params);
$total_registros = $stmtTotal->fetchColumn();

// Consulta principal
$stmt = $pdo->prepare("
    SELECT c.id AS cromo_id, c.codigo, c.nombre, c.seleccion,
           c.posicion, c.rareza, c.imagen
    FROM cromos c
    $where
    ORDER BY c.nombre ASC
    LIMIT $offset, $por_pagina
");
$stmt->execute($params);
$cromos = $stmt->fetchAll();
?>

<main class="layout-main">

    <section class="content">
        <article>

            <h2 class="content-title">
                <i class="fa-regular fa-people-arrows"></i>
                Cromos disponibles para intercambio
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
                    <label>Rareza</label>
                    <select name="rareza" class="select-rareza">
                        <option value="">Todas</option>
                        <option value="comun" <?= $rareza == 'comun' ? 'selected' : '' ?>>Común</option>
                        <option value="raro" <?= $rareza == 'raro' ? 'selected' : '' ?>>Raro</option>
                        <option value="epico" <?= $rareza == 'epico' ? 'selected' : '' ?>>Épico</option>
                        <option value="legendario" <?= $rareza == 'legendario' ? 'selected' : '' ?>>Legendario</option>
                    </select>
                </div>

                <div class="filtros-botones">
                    <button class="btn btn-buscar" type="submit">
                        <i class="fa-jelly fa-regular fa-magnifying-glass"></i> Buscar
                    </button>

                    <a href="<?= asset('/cromos-intercambio-listado') ?>" class="btn btn-limpiar">
                        <i class="fa-regular fa-broom-wide"></i> Limpiar filtros
                    </a>
                </div>
            </form>

            <!-- Listado -->
            <div class="intercambio-grid">
                <?php if (empty($cromos)): ?>
                    <p>No se encontraron cromos con los filtros aplicados.</p>
                <?php endif; ?>

                <?php foreach ($cromos as $cromo): ?>

                    <?php
                    $ruta_imagen = !empty($cromo['imagen'])
                        ? asset($cromo['imagen'])
                        : asset("/uploads/cromos/default/Default.png");
                    ?>

                    <div class="intercambio-card">

                        <div class="card-img">
                            <img src="<?= $ruta_imagen ?>"
                                alt="<?= htmlspecialchars($cromo['nombre']) ?>">
                        </div>

                        <div class="card-info">
                            <h3><?= htmlspecialchars(str_replace(['-', '_'], ' ', $cromo['nombre'])) ?></h3>
                            <p class="seleccion"><?= htmlspecialchars($cromo['seleccion']) ?></p>
                            <p class="rareza <?= $cromo['rareza'] ?>">
                                <?= ucfirst($cromo['rareza']) ?>
                            </p>
                        </div>

                        <div class="card-actions">
                            <a href="<?= asset('/favorito?cromo=' . $cromo['cromo_id']) ?>"
                                class="btn btn-fav">
                                <i class="fa-solid fa-heart"></i> Favorito
                            </a>

                            <a href="<?= asset('/intercambiar?cromo=' . $cromo['cromo_id']) ?>"
                                class="btn btn-intercambiar">
                                <i class="fa-regular fa-people-arrows"></i> Intercambiar
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