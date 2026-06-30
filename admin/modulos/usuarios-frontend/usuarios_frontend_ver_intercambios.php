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

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de intercambio no válido.");
}

$id_intercambio = intval($_GET['id']);

// Obtener datos del intercambio
$sql = "
    SELECT 
        i.id,
        i.estado,
        i.fecha,

        ue.id AS emisor_id,
        ue.nombre AS emisor_nombre,
        ue.email AS emisor_email,

        ur.id AS receptor_id,
        ur.nombre AS receptor_nombre,
        ur.email AS receptor_email,

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
    WHERE i.id = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_intercambio]);
$intercambio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$intercambio) {
    die("Intercambio no encontrado.");
}

// Rutas de imágenes
$img_ofrecido = $intercambio['ofrecido_imagen']
    ? asset($intercambio['ofrecido_imagen'])
    : asset("uploads/cromos/default/Default.png");

$img_solicitado = $intercambio['solicitado_imagen']
    ? asset($intercambio['solicitado_imagen'])
    : asset("uploads/cromos/default/Default.png");

$pagina = 'usuarios_frontend_intercambios';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Intercambio #<?= $intercambio['id'] ?></h2>

        <div class="intercambio-detalle">

            <div class="bloque-usuarios">
                <h3>Usuarios implicados</h3>

                <div class="usuario">
                    <p><strong>Emisor:</strong> <?= htmlspecialchars($intercambio['emisor_nombre']) ?></p>
                    <p>Email: <?= htmlspecialchars($intercambio['emisor_email']) ?></p>
                    <p>ID: <?= $intercambio['emisor_id'] ?></p>
                </div>

                <div class="usuario">
                    <strong>Receptor:</strong> <?= htmlspecialchars($intercambio['receptor_nombre']) ?><br>
                    Email: <?= htmlspecialchars($intercambio['receptor_email']) ?><br>
                    ID: <?= $intercambio['receptor_id'] ?>
                </div>
            </div>

            <hr>

            <div class="bloque-cromos">
                <h3>Cromos del intercambio</h3>

                <div class="bloque-cromos-interno">
                    <div class="cromo-grande">
                        <h4>Cromo ofrecido</h4>
                        <img src="<?= $img_ofrecido ?>" class="cromo-img-grande">
                        <p><strong><?= htmlspecialchars($intercambio['ofrecido_nombre']) ?></strong></p>
                    </div>

                    <div class="cromo-grande">
                        <h4>Cromo solicitado</h4>
                        <img src="<?= $img_solicitado ?>" class="cromo-img-grande">
                        <p><strong><?= htmlspecialchars($intercambio['solicitado_nombre']) ?></strong></p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="bloque-info">
                <h3>Información del intercambio</h3>

                <p><strong>Estado:</strong>
                    <span class="estado estado-<?= $intercambio['estado'] ?>">
                        <?= ucfirst($intercambio['estado']) ?>
                    </span>
                </p>

                <p><strong>Fecha:</strong>
                    <?= date("d/m/Y H:i", strtotime($intercambio['fecha'])) ?>
                </p>
            </div>

            <a href="usuarios_frontend_intercambios.php" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>

        </div>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>