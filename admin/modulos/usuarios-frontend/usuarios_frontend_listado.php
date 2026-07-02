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

// Filtros
$where = [];
$params = [];

if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['email'])) {
    $where[] = "email LIKE :email";
    $params[':email'] = "%" . $_GET['email'] . "%";
}

if (!empty($_GET['telefono'])) {
    $where[] = "telefono LIKE :telefono";
    $params[':telefono'] = "%" . $_GET['telefono'] . "%";
}

if (!empty($_GET['estado'])) {
    $where[] = "estado = :estado";
    $params[':estado'] = $_GET['estado'];
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Total de registros
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM usuarios_frontend $where_sql");
$stmt_total->execute($params);
$total_usuarios = $stmt_total->fetchColumn();

// Consulta principal
$sql = "
    SELECT id, nombre, email, telefono, estado, creado_en
    FROM usuarios_frontend
    $where_sql
    ORDER BY id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

// Bind dinámico de filtros
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'usuarios_frontend_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Listado de usuarios de la página web</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Email</label>
                <input type="text" name="email" value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= isset($_GET['telefono']) ? htmlspecialchars($_GET['telefono']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="activo" <?= (isset($_GET['estado']) && $_GET['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                    <option value="suspendido" <?= (isset($_GET['estado']) && $_GET['estado'] === 'suspendido') ? 'selected' : '' ?>>Suspendido</option>
                    <option value="eliminado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'eliminado') ? 'selected' : '' ?>>Eliminado</option>
                </select>
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="usuarios_frontend_listado.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Creado en</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:20px;">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['telefono']) ?></td>

                                <td>
                                    <?php if ($u['estado'] === 'activo'): ?>
                                        <span class="badge badge-admin">Activo</span>
                                    <?php elseif ($u['estado'] === 'suspendido'): ?>
                                        <span class="badge badge-visitante" style="background:#f1c40f;">Suspendido</span>
                                    <?php else: ?>
                                        <span class="badge badge-visitante" style="background:#e74c3c;">Eliminado</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= $u['creado_en'] ?></td>

                                <td>
                                    <a href="usuarios_frontend_ver.php?id=<?= $u['id'] ?>" class="btn btn-ver">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </a>

                                    <?php if (esAdmin()): ?>
                                        <a href="usuarios_frontend_editar.php?id=<?= $u['id'] ?>" class="btn btn-ver">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>

                                        <a href="usuarios_frontend_eliminar.php?id=<?= $u['id'] ?>"
                                            class="btn btn-borrar"
                                            onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
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

        <!-- Paginador -->
        <?php
        if ($total_usuarios > $por_pagina) {
            echo paginador($total_usuarios, $por_pagina, $pagina_actual, $_GET, 'pagina');
        }
        ?>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>