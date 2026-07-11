<?php
// Datos del usuario desde la sesión
$usuario = [
    'id'        => $_SESSION['usuario_id'],
    'nombre'    => $_SESSION['usuario_nombre'],
    'email'     => $_SESSION['usuario_email'],
    'telefono'  => $_SESSION['usuario_telefono'],
    'ciudad'    => $_SESSION['usuario_ciudad'],
    'provincia' => $_SESSION['usuario_provincia'],
    'foto'      => $_SESSION['usuario_foto']
];

$foto = $usuario['foto']
    ? asset('/' . $usuario['foto'])
    : asset('/img/default-user.png');
?>

<section class="content">
    <article>

        <h2 class="content-title">
            <i class="fa-light fa-user-doctor-hair"></i> Mi perfil
        </h2>

        <section class="seguridad-section seguridad-cambiar-clave">

            <h3>
                <i class="fa-light fa-user-gear"></i> Modifica tus datos
            </h3>

            <div class="content-block perfil-block">

                <!-- Foto -->
                <div class="perfil-foto">
                    <img src="<?= $foto ?>" alt="Foto de perfil">

                    <form action="<?= asset('/perfil-actualizar') ?>" method="post" enctype="multipart/form-data">
                        <label class="btn-foto">
                            <i class="fa-solid fa-camera"></i> Cambiar foto
                            <input type="file" name="foto" accept="image/*">
                        </label>

                        <button type="submit" class="btn btn-guardar">
                           <i class="fa-solid fa-floppy-disk"></i> Guardar foto
                        </button>
                    </form>
                </div>

                <!-- Datos -->
                <form class="perfil-form" action="<?= asset('/perfil-actualizar') ?>" method="post">

                    <label>Nombre completo</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

                    <label>Email (no editable)</label>
                    <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>

                    <label>Teléfono (no editable)</label>
                    <input type="text" value="<?= htmlspecialchars($usuario['telefono']) ?>" disabled>

                    <label>Ciudad</label>
                    <input type="text" name="ciudad" value="<?= htmlspecialchars($usuario['ciudad']) ?>">

                    <label>Provincia</label>
                    <input type="text" name="provincia" value="<?= htmlspecialchars($usuario['provincia']) ?>">

                    <button type="submit" class="btn btn-guardar">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>

                </form>

            </div>
        </section>

    </article>
</section>