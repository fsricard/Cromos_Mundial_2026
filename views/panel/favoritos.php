<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/funciones.php';

// Si no está logueado
if (!isLoggedIn()) {
    header("Location: " . asset('/login'));
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Paginación
$por_pagina = 250;
$pagina = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Total favoritos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM favoritos WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$total_favoritos = $stmt->fetchColumn();

// Obtener favoritos con datos del cromo
$stmt = $pdo->prepare("
    SELECT 
        f.id AS fav_id,
        c.id AS cromo_id,
        c.nombre,
        c.imagen,
        c.seleccion,
        c.posicion,
        c.rareza
    FROM favoritos f
    INNER JOIN cromos c ON c.id = f.id_cromo
    WHERE f.id_usuario = ?
    ORDER BY f.fecha DESC
    LIMIT $por_pagina OFFSET $offset
");
$stmt->execute([$usuario_id]);
$favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para detectar tipo de cromo
function tipoCromo(PDO $pdo, int $idCromo): string
{
    // Venta
    $stmt = $pdo->prepare("SELECT id FROM cromos_venta WHERE id_cromo = ? AND estado = 'disponible' LIMIT 1");
    $stmt->execute([$idCromo]);
    if ($stmt->fetch()) {
        return 'venta';
    }

    // Intercambio
    $stmt = $pdo->prepare("
        SELECT id FROM intercambios 
        WHERE (id_cromo_ofrecido = ? OR id_cromo_solicitado = ?)
        AND estado = 'pendiente'
        LIMIT 1
    ");
    $stmt->execute([$idCromo, $idCromo]);
    if ($stmt->fetch()) {
        return 'intercambio';
    }

    return 'normal';
}
?>

<section class="content">
    <article>

        <h2 class="content-title">
            <i class="fa-jelly fa-regular fa-bookmark"></i> Tus cromos favoritos
        </h2>

        <div class="content-block">

            <h3>
                <i class="fa-regular fa-hand-holding-star"></i> Gestiona tus cromos favoritos
            </h3>

            <?php if (!$favoritos): ?>
                <p class="alert alert-info">
                    <i class="fa-light fa-circle-info"></i> No tienes cromos favoritos todavía.
                </p>
            <?php else: ?>

                <?php if (!empty($_SESSION['favorito_mensaje'])): ?>
                    <p class="alert alert-success"><?= $_SESSION['favorito_mensaje'] ?></p>
                    <?php unset($_SESSION['favorito_mensaje']); ?>
                <?php endif; ?>

                <div class="favoritos-grid">
                    <?php foreach ($favoritos as $fav): ?>
                        <?php
                        $tipo = tipoCromo($pdo, $fav['cromo_id']);
                        $tipo_label = [
                            'normal'      => '<span class="fav-tipo normal"><i class="fa-light fa-star"></i> Normal</span>',
                            'venta'       => '<span class="fav-tipo venta"><i class="fa-light fa-cart-shopping"></i> Venta</span>',
                            'intercambio' => '<span class="fav-tipo intercambio"><i class="fa-light fa-arrows-rotate"></i> Intercambio</span>',
                        ][$tipo];
                        ?>

                        <div class="favorito-card">

                            <?php
                            $ruta_imagen = !empty($fav['imagen'])
                                ? asset($fav['imagen'])
                                : asset("/uploads/cromos/default/Default.png");
                            ?>

                            <div class="favorito-img">
                                <img src="<?= $ruta_imagen ?>" alt="<?= htmlspecialchars($fav['nombre']) ?>">
                            </div>

                            <div class="favorito-info">
                                <h4 class="favorito-nombre">
                                    <i class="fa-light fa-user"></i>
                                    <?= htmlspecialchars($fav['nombre']) ?>
                                </h4>

                                <?= $tipo_label ?>

                                <a href="<?= asset('/favorito-toggle?id=' . $fav['cromo_id']) ?>"
                                    class="btn btn-danger favorito-eliminar">
                                    <i class="fa-light fa-trash"></i> Eliminar favorito
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>

    </article>
</section>