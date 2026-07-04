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

$stmt = $pdo->query("SELECT * FROM rarezas_cromos ORDER BY id ASC");
$rarezas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'rareza_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Gestión de rarezas de cromos</h2>

        <?php if (esAdmin()): ?>
            <a href="rareza_nueva.php" class="btn btn-generar">
                <i class="fa-solid fa-plus"></i> Nueva rareza
            </a>
        <?php endif; ?>

        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($rarezas as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['nombre']) ?></td>
                            <td><?= htmlspecialchars($r['descripcion']) ?></td>
                            <td><?= $r['activo'] ? 'Sí' : 'No' ?></td>

                            <td>

                                <?php if (esAdmin()): ?>
                                    <a href="rareza_editar.php?id=<?= $r['id'] ?>" class="btn btn-ver">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>

                                    <a href="rareza_eliminar.php?id=<?= $r['id'] ?>"
                                        class="btn btn-borrar"
                                        onclick="return confirm('¿Eliminar esta rareza?');">
                                        <i class="fa-solid fa-trash"></i> Borrar
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