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
$filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$filtro_email  = isset($_GET['email']) ? trim($_GET['email']) : '';
$filtro_asunto = isset($_GET['asunto']) ? trim($_GET['asunto']) : '';

// Paginación
$por_pagina = 10;
$pagina_actual = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Construcción dinámica del WHERE
$where = [];
$params = [];

if ($filtro_nombre !== '') {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%$filtro_nombre%";
}

if ($filtro_email !== '') {
    $where[] = "email LIKE :email";
    $params[':email'] = "%$filtro_email%";
}

if ($filtro_asunto !== '') {
    $where[] = "asunto LIKE :asunto";
    $params[':asunto'] = "%$filtro_asunto%";
}

$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// Total de registros
$sql_total = "SELECT COUNT(*) FROM mensajes_contacto $where_sql";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();

// Consulta principal
$sql = "SELECT id, nombre, email, asunto, fecha
        FROM mensajes_contacto
        $where_sql
        ORDER BY fecha DESC
        LIMIT :offset, :por_pagina";

$stmt = $pdo->prepare($sql);

// Parámetros de filtros
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Parámetros de paginación
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

$stmt->execute();
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'contacto_listado';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Mensajes de la página de contacto</h2>

        <!-- Filtros -->
        <form method="GET" class="filtros-admin">
            <div class="form-grupo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($filtro_nombre) ?>">
            </div>

            <div class="form-grupo">
                <label>Email</label>
                <input type="text" name="email" value="<?= htmlspecialchars($filtro_email) ?>">
            </div>

            <div class="form-grupo">
                <label>Asunto</label>
                <input type="text" name="asunto" value="<?= htmlspecialchars($filtro_asunto) ?>">
            </div>

            <button type="submit" class="btn btn-filtrar"><i class="fa-solid fa-filter"></i> Filtrar</button>
            <a href="contacto_listado.php" class="btn btn-limpiar"><i class="fa-solid fa-eraser"></i> Limpiar</a>
        </form>

        <?php if (esAdmin()): ?>
            <div class="pdf-container">
                <?php
                $query_pdf = http_build_query($_GET);
                ?>
                <a href="contacto_pdf.php?<?= $query_pdf ?>" class="btn btn-pdf" target="_blank">
                    <i class="fa-solid fa-file-pdf"></i> Descargar en PDF
                </a>
            </div>
        <?php endif; ?>

        <!-- Tabla -->
        <div class="tabla-responsive">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mensajes)): ?>
                        <tr>
                            <td colspan="6" class="no-registros">No hay mensajes</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($mensajes as $m): ?>
                            <tr>
                                <td><?= $m['id'] ?></td>
                                <td><?= htmlspecialchars($m['nombre']) ?></td>
                                <td><?= htmlspecialchars($m['email']) ?></td>
                                <td><?= htmlspecialchars($m['asunto']) ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($m['fecha'])) ?></td>

                                <?php if (esAdmin()): ?>
                                    <td>
                                        <a href="contacto_editar.php?id=<?= $m['id'] ?>" class="btn btn-ver">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>

                                        <a href="contacto_borrar.php?id=<?= $m['id'] ?>"
                                            class="btn btn-borrar"
                                            onclick="return confirm('¿Seguro que deseas borrar este mensaje?');">
                                            <i class="fa-solid fa-trash"></i> Borrar
                                        </a>
                                    </td>
                                <?php endif; ?>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <?php
        if ($total_registros > $por_pagina) {
            echo paginador($total_registros, $por_pagina, $pagina_actual, $_GET);
        }
        ?>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>