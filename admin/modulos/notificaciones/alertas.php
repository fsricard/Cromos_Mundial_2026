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

$pagina = 'alertas';

// Cargar todas las alertas globales
$stmt = $pdo->query("
    SELECT id, tipo, mensaje, creada_en
    FROM panel_alertas
    ORDER BY creada_en DESC
");
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Alertas del Panel de Usuario</h2>

        <?php if (esAdmin()): ?>
            <a href="alertas-crear.php" class="btn btn-generar">
                <i class="fa-solid fa-plus"></i> Crear nueva alerta
            </a>
        <?php endif; ?>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($alertas as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td><?= $a['tipo'] ?></td>
                            <td><?= htmlspecialchars($a['mensaje']) ?></td>
                            <td><?= $a['creada_en'] ?></td>
                            <td>
                                <?php if (esAdmin()): ?>
                                    <a href="alertas-eliminar.php?id=<?= $a['id'] ?>"
                                        class="btn btn-small btn-danger"
                                        onclick="return confirm('¿Eliminar esta alerta?');">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>