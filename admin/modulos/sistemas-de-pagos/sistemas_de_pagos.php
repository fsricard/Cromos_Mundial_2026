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

// Obtener métodos de pago
$stmt = $pdo->prepare("SELECT * FROM sistemas_pagos ORDER BY fecha_creacion DESC");
$stmt->execute();
$metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagina = 'sistemas-de-pagos';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Sistemas de Pago</h2>

        <h3>Añadir nuevo método</h3>

        <?php if (isset($_GET['exito'])): ?>
            <p class="mensaje-exito"><?= htmlspecialchars($_GET['exito']) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <p class="mensaje-error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="procesar_pago.php" method="POST" class="filtros-admin">

            <div class="form-grupo">
                <label>Tipo:</label>
                <select name="tipo" required>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telefono">Teléfono</option>
                    <option value="bizum">Bizum</option>
                    <option value="paypal">PayPal</option>
                    <option value="banco">Cuenta Bancaria</option>
                    <option value="stripe">Stripe</option>
                    <option value="cripto">Criptomoneda</option>
                    <option value="direccion">Dirección física</option>
                </select>
            </div>

            <div class="form-grupo">
                <label>Etiqueta visible:</label>
                <input type="text" name="etiqueta" placeholder="Ej: WhatsApp Ventas" required>
            </div>

            <div class="form-grupo">
                <label>Valor:</label>
                <input type="text" name="valor" placeholder="Número, email, IBAN, URL..." required>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-generar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            <?php endif; ?>
        </form>

        <hr>

        <h3>Métodos registrados</h3>
        <ul>
            <?php foreach ($metodos as $m): ?>
                <li>
                    <strong><?= htmlspecialchars($m['etiqueta']) ?></strong>
                    (<?= htmlspecialchars($m['tipo']) ?>):
                    <?= htmlspecialchars($m['valor']) ?>

                    <a href="editar_pago.php?id=<?= $m['id'] ?>" class="btn btn-ver">
                        <i class="fa-solid fa-pen"></i> 
                        Editar
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    </section>
</main>

<?php include('../../includes/footer.php'); ?>