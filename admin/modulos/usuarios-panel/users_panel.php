<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
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

if (!empty($_GET['correo'])) {
    $where[] = "correo LIKE :correo";
    $params[':correo'] = "%" . $_GET['correo'] . "%";
}

if (!empty($_GET['rol'])) {
    $where[] = "rol = :rol";
    $params[':rol'] = $_GET['rol'];
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Contar total con filtros
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM usuarios $where_sql");
$stmt_total->execute($params);
$total_usuarios = $stmt_total->fetchColumn();

// Obtener usuarios paginados
$sql = "
    SELECT id, nombre, correo, rol, creado_en
    FROM usuarios
    $where_sql
    ORDER BY id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'users_panel';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Gestión de usuarios del sistema</h2>

        <a href="crear.php" class="btn btn-generar">
            <i class="fa-solid fa-user-plus"></i> Crear usuario
        </a>

        <form method="GET" class="filtros-admin">

            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Correo</label>
                <input type="text" name="correo" value="<?= isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : '' ?>">
            </div>

            <div class="form-grupo">
                <label>Rol</label>
                <select name="rol">
                    <option value="">Todos</option>
                    <option value="admin" <?= (isset($_GET['rol']) && $_GET['rol'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                    <option value="visitante" <?= (isset($_GET['rol']) && $_GET['rol'] === 'visitante') ? 'selected' : '' ?>>Visitante</option>
                </select>
            </div>

            <button type="submit" class="btn btn-filtrar">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>

            <a href="users_panel.php" class="btn btn-limpiar">
                <i class="fa-solid fa-eraser"></i> Limpiar
            </a>

        </form>

        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Creado en</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:20px;">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['correo']) ?></td>
                                <td>
                                    <?php if ($u['rol'] === 'admin'): ?>
                                        <span class="badge badge-admin">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge badge-visitante">Visitante</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $u['creado_en'] ?></td>
                                <td>
                                    <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-ver">
                                        <i class="fa-solid fa-eye"></i>
                                        Editar
                                    </a>

                                    <a href="eliminar.php?id=<?= $u['id'] ?>"
                                        class="btn btn-borrar"
                                        onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                        <i class="fa-solid fa-trash"></i>
                                        Borrar
                                    </a>
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