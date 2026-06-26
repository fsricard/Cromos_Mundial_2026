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
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'ID de usuario no válido.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

$id = intval($_GET['id']);

// Comprobar que el usuario existe
$stmtUser = $pdo->prepare("SELECT id, nombre FROM usuarios_frontend WHERE id = :id LIMIT 1");
$stmtUser->execute([':id' => $id]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'El usuario no existe.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

// Obtener sesiones del usuario
$stmt = $pdo->prepare("
    SELECT id, token, ip, user_agent, fecha_inicio, fecha_fin
    FROM sesiones_usuarios
    WHERE id_usuario = :id
    ORDER BY fecha_inicio DESC
");
$stmt->execute([':id' => $id]);
$sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'usuarios_frontend_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>

        <h2>Sesiones activas de <?= htmlspecialchars($usuario['nombre']) ?></h2>

        <a href="usuarios_frontend_ver.php?id=<?= $usuario['id'] ?>" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver a la ficha
        </a>

        <a href="usuarios_frontend_listado.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <div class="tabla-responsive" style="margin-top:20px;">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Token</th>
                        <th>IP</th>
                        <th>Navegador</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($sesiones)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:20px;">
                                Este usuario no tiene sesiones registradas.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sesiones as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= htmlspecialchars($s['token']) ?></td>
                                <td><?= htmlspecialchars($s['ip']) ?></td>
                                <td><?= htmlspecialchars($s['user_agent']) ?></td>
                                <td><?= $s['fecha_inicio'] ?></td>
                                <td><?= $s['fecha_fin'] ?: '<span style="color:green;font-weight:bold;">Activa</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>