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

if (!isset($_GET['id'])) {
    header("Location: sistemas_de_pagos.php?error=ID no proporcionado");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM sistemas_pagos WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $id);
$stmt->execute();
$metodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$metodo) {
    header("Location: sistemas_de_pagos.php?error=Método no encontrado");
    exit;
}

$pagina = 'sistemas-de-pagos';

include('../../includes/header.php');
?>

<main class="content">
    <section>
        <h2>Editar método de pago</h2>

        <?php if (isset($_GET['exito'])): ?>
            <p class="mensaje-exito"><?= htmlspecialchars($_GET['exito']) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <p class="mensaje-error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="actualizar_pago.php" method="POST" class="filtros-admin">
            <input type="hidden" name="id" value="<?= $metodo['id'] ?>">

            <div class="form-grupo">
                <label>Tipo:</label>
                <select name="tipo" required>
                    <option value="whatsapp" <?= $metodo['tipo'] === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                    <option value="telefono" <?= $metodo['tipo'] === 'telefono' ? 'selected' : '' ?>>Teléfono</option>
                    <option value="bizum" <?= $metodo['tipo'] === 'bizum' ? 'selected' : '' ?>>Bizum</option>
                    <option value="paypal" <?= $metodo['tipo'] === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                    <option value="banco" <?= $metodo['tipo'] === 'banco' ? 'selected' : '' ?>>Cuenta Bancaria</option>
                    <option value="stripe" <?= $metodo['tipo'] === 'stripe' ? 'selected' : '' ?>>Stripe</option>
                    <option value="cripto" <?= $metodo['tipo'] === 'cripto' ? 'selected' : '' ?>>Criptomoneda</option>
                    <option value="direccion" <?= $metodo['tipo'] === 'direccion' ? 'selected' : '' ?>>Dirección física</option>
                </select>
            </div>

            <div class="form-grupo">
                <label>Etiqueta visible:</label>
                <input type="text" name="etiqueta" value="<?= htmlspecialchars($metodo['etiqueta']) ?>" required>
            </div>

            <div class="form-grupo">
                <label>Valor:</label>
                <input type="text" name="valor" value="<?= htmlspecialchars($metodo['valor']) ?>" required>
            </div>

            <?php if (esAdmin()): ?>
                <button type="submit" class="btn btn-ver">
                    <i class="fa-solid fa-floppy-disk"></i> Actualizar método
                </button>
            <?php endif; ?>

            <a href="sistemas_de_pagos.php?id=<?= $metodo['id'] ?>" class="btn btn-volver">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>
        </form>
    </section>
</main>

<?php include('../../includes/footer.php'); ?>