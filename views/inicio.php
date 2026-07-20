<?php
// Cromos en venta (solo disponibles)
$stmtVenta = $pdo->prepare("
    SELECT cv.id AS venta_id, cv.precio, cv.estado,
           c.id AS cromo_id, c.codigo, c.nombre, c.seleccion, c.posicion, c.rareza, c.imagen
    FROM cromos_venta cv
    INNER JOIN cromos c ON c.id = cv.id_cromo
    WHERE cv.estado = 'disponible'
    ORDER BY cv.fecha_publicacion DESC
    LIMIT 10
");
$stmtVenta->execute();
$cromosVenta = $stmtVenta->fetchAll();

// Cromos para intercambio (últimos doce)
$stmtInter = $pdo->prepare("
    SELECT i.id AS intercambio_id, i.estado,
           c.id AS cromo_id, c.codigo, c.nombre, c.seleccion, c.posicion, c.rareza, c.imagen
    FROM intercambios i
    INNER JOIN cromos c ON c.id = i.id_cromo_ofrecido
    ORDER BY i.fecha DESC
    LIMIT 10
");
$stmtInter->execute();
$cromosInter = $stmtInter->fetchAll();
?>

<main class="layout-main">

    <!-- Cromos en venta -->
    <section class="content">
        <article>

            <h2 class="content-title">
                <i class="fa-duotone fa-cart-shopping"></i>
                Cromos en venta
            </h2>

            <div class="cromos-grid">

                <?php foreach ($cromosVenta as $cromo): ?>
                    <div class="cromo-card venta">

                        <?php
                        $ruta_imagen = !empty($cromo['imagen'])
                            ? asset($cromo['imagen'])
                            : asset("/uploads/cromos/default/Default.png");
                        ?>

                        <div class="cromo-img">
                            <img src="<?= $ruta_imagen ?>"
                                alt="<?= htmlspecialchars($cromo['nombre']) ?>">
                        </div>

                        <div class="cromo-info">
                            <h3><?= htmlspecialchars(str_replace(['-', '_'], ' ', $cromo['nombre'])) ?></h3>
                            <p><?= htmlspecialchars($cromo['seleccion']) ?></p>
                            <p class="rareza <?= $cromo['rareza'] ?>"><?= ucfirst($cromo['rareza']) ?></p>
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

            <div class="btn-ver-todos">
                <a href="<?= asset('/cromos-venta-listado') ?>" class="btn btn-listado">
                    Ver todos los cromos en venta
                </a>
            </div>

        </article>
    </section>


    <!-- Cromos para intercambio -->
    <section class="content">
        <article>

            <h2 class="content-title">
                <i class="fa-regular fa-people-arrows"></i>
                Cromos para intercambio
            </h2>

            <div class="cromos-grid intercambio">

                <?php foreach ($cromosInter as $cromo): ?>
                    <div class="cromo-card intercambio">

                        <?php
                        $ruta_imagen = !empty($cromo['imagen'])
                            ? asset($cromo['imagen'])
                            : asset("/uploads/cromos/default/Default.png");
                        ?>

                        <div class="cromo-img">
                            <img src="<?= $ruta_imagen ?>"
                                alt="<?= htmlspecialchars($cromo['nombre']) ?>">
                        </div>

                        <div class="cromo-info">
                            <h3><?= htmlspecialchars(str_replace(['-', '_'], ' ', $cromo['nombre'])) ?></h3>
                            <p><?= htmlspecialchars($cromo['seleccion']) ?></p>
                            <p class="rareza <?= $cromo['rareza'] ?>"><?= ucfirst($cromo['rareza']) ?></p>
                        </div>

                        <div class="cromo-actions">
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

            <div class="btn-ver-todos">
                <a href="<?= asset('/cromos-intercambio-listado') ?>" class="btn btn-listado">
                    Ver todos los cromos para intercambio
                </a>
            </div>

        </article>
    </section>

</main>