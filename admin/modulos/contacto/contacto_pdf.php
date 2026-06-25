<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../../../config/funciones.php');

// Horario europeo
date_default_timezone_set('Europe/Madrid');

use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . '/../../../includes/dompdf/autoload.inc.php';

// Si no está logueado, fuera
if (!isLoggedIn()) {
    exit("Acceso no autorizado");
}

// Filtros
$filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$filtro_email  = isset($_GET['email']) ? trim($_GET['email']) : '';
$filtro_asunto = isset($_GET['asunto']) ? trim($_GET['asunto']) : '';

// Construcción del WHERE
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

// Consulta
$sql = "SELECT id, nombre, email, asunto, fecha
        FROM mensajes_contacto
        $where_sql
        ORDER BY fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HTML del PDF
$fecha_hoy = date("d/m/Y H:i");

ob_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .portada {
            text-align: center;
            margin: 80px 0;
        }

        .portada img {
            width: 180px;
            margin-bottom: 20px;
        }

        .titulo {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .fecha {
            font-size: 14px;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        th {
            background: #4F7CFF;
            color: #fff;
            padding: 8px;
            font-size: 13px;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #ccc;
        }

        .no-registros {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>

    <!-- Portada -->
    <div class="portada">
        <img src="<?= asset('/img/cromos-mexico-2026.png'); ?>">
        <div class="titulo">Listado de Mensajes de Contacto</div>
        <div class="fecha">Generado el <?= $fecha_hoy ?></div>
    </div>

    <!-- Salto de página 
    <div style="page-break-after: always;"></div>
    -->

    <h2>Mensajes filtrados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Asunto</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mensajes)): ?>
                <tr>
                    <td colspan="5" class="no-registros">No hay mensajes</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mensajes as $m): ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        <td><?= htmlspecialchars($m['asunto']) ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($m['fecha'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>

<?php
$html = ob_get_clean();

// Configuración DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar
$dompdf->stream("contacto_listado.pdf", ["Attachment" => true]);
exit;
