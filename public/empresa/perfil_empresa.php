<?php
session_start();

if (!isset($_SESSION['empresa'])) {
    header("Location: ../inicio.php");
    exit();
}

if (isset($_POST['cerrar_sesion'])) {
    session_unset();
    session_destroy();
    header("Location: ../inicio.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../css/perfiles.css"/>
</head>
<body>
<div class="form-container">
    <div class="form-card">
        <h2>Editar Perfil</h2>

        <div class="perfil-img-box">
            <img src="../img/imgIniciar.png" alt="Avatar" class="perfil-img">
        </div>

        <form class="formulario" id="formActualizarPerfil" method="post">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php
            echo htmlspecialchars($_SESSION['empresa']['nombre']);
            ?>" required>

            <label for="rfc">RFC</label>
            <input type="text" id="rfc" name="rfc" value="<?php
            echo htmlspecialchars($_SESSION['empresa']['rfc']);
            ?>" required>

            <label for="correo">Correo Electrónico</label>
            <input type="email" id="correo" name="correo" value="<?php
            echo htmlspecialchars($_SESSION['empresa']['correo']);
            ?>" required>

            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" value="<?php
            echo htmlspecialchars($_SESSION['empresa']['pais']);
            ?>" required>

            <label for="web">Sitio Web</label>
            <input type="text" id="web" name="web" value="<?php
            echo htmlspecialchars($_SESSION['empresa']['sitio_web']);
            ?>" required>

            <label for="textpresentacion">Texto de presentacion</label>
            <textarea type="descripcion" id="textpresentacion"
                      name="textpresentacion"><?php echo htmlspecialchars($_SESSION['empresa']['presentacion']); ?></textarea>

            <button type="button" onclick="abrirModal()" class="btn-guardar">Cambiar contraseña</button>

            <div class="botones">
                <button type="button" class="btn-cancelar" onclick="history.back()">Regresar</button>
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </div>
        </form>

        <div id="modalPass" class="modal">
            <div class="modal-contenido">
                <span class="cerrar" onclick="cerrarModal()">&times;</span>
                <h2>Cambiar contraseña</h2>

                <form id="formCambiarContraseña" method="POST">
                    <label for="actual">Contraseña actual:</label>
                    <input type="password" id="actual" name="actual" class="modal-input" required>

                    <label for="nueva">Nueva contraseña:</label>
                    <input type="password" id="nueva" name="nueva" class="modal-input" required>

                    <label for="confirmar">Confirmar nueva contraseña:</label>
                    <input type="password" id="confirmar" name="confirmar" class="modal-input" required>

                    <div class="botones-modal">
                        <button type="submit" class="btn-guardar">Guardar cambios</button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModal() {
        document.getElementById("modalPass").style.display = "block";
    }

    function cerrarModal() {
        document.getElementById("modalPass").style.display = "none";
        location.reload();
    }

    window.onclick = function (event) {
        var modal = document.getElementById("modalPass");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    document.getElementById('formCambiarContraseña').addEventListener('submit', function (event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        fetch('../utilidad/cambiar_contrasena.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(respuesta => {
                alert(respuesta);
                if (respuesta.includes("correctamente")) {
                    cerrarModal();
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Error al enviar:', error);
            });
    });

    document.getElementById('formActualizarPerfil').addEventListener('submit', function (event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        fetch('./actualizar_perfil.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(respuesta => {
                alert(respuesta);
            })
            .catch(error => {
                console.error('Error al actualizar perfil:', error);
            });
    });

</script>

</body>
</html>
