<?php
session_start();

if (!isset($_SESSION['usuario'])) {
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

        <form class="formulario" id="formActualizarUsuario" method="post">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>" required>

            <label for="apellidop">Apellido Paterno</label>
            <input type="text" id="apellidop" name="apellidop" value="<?php echo htmlspecialchars($_SESSION['usuario']['apellidoP']); ?>"
                   required>

            <label for="apellidom">Apellido materno</label>
            <input type="text" id="apellidom" name="apellidom" value="<?php echo htmlspecialchars($_SESSION['usuario']['apellidoM']); ?>"
                   required>

            <label for="correo">Correo Electrónico</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_SESSION['usuario']['correo']); ?>"
                   required>

            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_SESSION['usuario']['telefono']); ?>"
                   required>

            <label for="textpresentacion">Texto de presentación</label>
            <textarea id="textpresentacion" name="textpresentacion"
                      required><?php echo htmlspecialchars($_SESSION['usuario']['presentacion']); ?></textarea>

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

    document.getElementById('formActualizarUsuario').addEventListener('submit', function (event) {
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

