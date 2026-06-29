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

// Obtener datos del usuario
$stmt = $pdo->prepare("
    SELECT id, nombre, email, telefono, ciudad, provincia, estado, creado_en, actualizado_en, ultimo_acceso
    FROM usuarios_frontend
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'El usuario no existe.'
    ];
    header("Location: usuarios_frontend_listado.php");
    exit;
}

$pagina = 'usuarios_frontend_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Ficha del usuario <?= htmlspecialchars($usuario['nombre']) ?></h2>

        <a href="usuarios_frontend_listado.php" class="btn btn-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>

        <div class="ficha-usuario">

            <div class="ficha-bloque">
                <h3>Datos personales</h3>

                <p><strong>ID:</strong> <?= $usuario['id'] ?></p>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']) ?></p>
                <p><strong>Ciudad:</strong> <?= htmlspecialchars($usuario['ciudad']) ?></p>
                <p><strong>Provincia:</strong> <?= htmlspecialchars($usuario['provincia']) ?></p>
            </div>

            <div class="ficha-bloque">
                <h3>Estado de la cuenta</h3>

                <p>
                    <strong>Estado:</strong>
                    <?php if ($usuario['estado'] === 'activo'): ?>
                        <span class="badge badge-admin">Activo</span>
                    <?php elseif ($usuario['estado'] === 'suspendido'): ?>
                        <span class="badge badge-visitante" style="background:#f1c40f;">Suspendido</span>
                    <?php else: ?>
                        <span class="badge badge-visitante" style="background:#e74c3c;">Eliminado</span>
                    <?php endif; ?>
                </p>

                <p><strong>Creado en:</strong> <?= $usuario['creado_en'] ?></p>
                <p><strong>Actualizado en:</strong> <?= $usuario['actualizado_en'] ?></p>
                <p><strong>Último acceso:</strong> <?= $usuario['ultimo_acceso'] ?: 'Nunca' ?></p>
            </div>

            <div class="ficha-bloque">
                <h3>Acciones</h3>

                <?php if (esAdmin()): ?>
                    <a href="usuarios_frontend_editar.php?id=<?= $usuario['id'] ?>" class="btn btn-ver">
                        <i class="fa-solid fa-pen"></i> Editar usuario
                    </a>

                    <a href="usuarios_frontend_eliminar.php?id=<?= $usuario['id'] ?>"
                        class="btn btn-borrar"
                        onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                        <i class="fa-solid fa-trash"></i> Eliminar usuario
                    </a>
                <?php endif; ?>
            </div>

            <div class="ficha-bloque">
                <h3>Otros módulos</h3>

                <a href="usuarios_frontend_sesiones.php?id=<?= $usuario['id'] ?>" class="btn btn-ver">
                    <i class="fa-solid fa-mobile-screen"></i> Ver sesiones activas
                </a>
            </div>

        </div>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>