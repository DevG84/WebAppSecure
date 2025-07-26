<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SESSION['usuario'])) {
    header("Location: ./usuario/inicio.php");
    exit();
}

if (isset($_SESSION['empresa'])) {
    header("Location: ./empresa/inicio.php");
    exit();
}

include("../includes/BD.php");
$conn = (new Connection())->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST["tipo"];

    if ($tipo === "candidato") {

        // Recolectar datos
        $nombre = trim($_POST["nombre"] ?? "");
        $apellidoP = trim($_POST["apellido_paterno"] ?? "");
        $apellidoM = trim($_POST["apellido_materno"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $email = strtolower(trim($_POST["email"] ?? ""));
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";
        $presentacion = trim($_POST["presentacion"] ?? "");

        // Validaciones obligatorias
        if (!$nombre || !$apellidoP || !$apellidoM || !$telefono || !$email || !$password || !$confirm) {
            header("Location: registro.php?status=error&mensaje=" . urlencode("Todos los campos son obligatorios. Intente de nuevo"));
            exit();
        }

        if ($password !== $confirm) {
            header("Location: registro.php?status=error&mensaje=" . urlencode("Las contraseñas no coinciden. Intente de nuevo"));
            exit();
        }

        // Verificar que el correo no esté duplicado
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM (
                SELECT correo FROM usuarios WHERE correo = :email
                UNION ALL
                SELECT correo FROM empresas WHERE correo = :email
            ) AS correos_repetidos
        ");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            header("Location: registro.php?status=error&mensaje=" . urlencode("El correo ya está registrado. Intente de nuevo.") . "&tipo=" . $tipo);
            exit();
        }

        // Insertar
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO 
                usuarios (nombre, apellido_paterno, apellido_materno, telefono, correo, contrasena, texto_presentacion) 
                VALUES (:nombre, :apellidoP, :apellidoM, :telefono, :correo, :contrasena, :presentacion)");

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellidoP", $apellidoP);
        $stmt->bindParam(":apellidoM", $apellidoM);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":correo", $email);
        $stmt->bindParam(":contrasena", $hash);
        $stmt->bindParam(":presentacion", $presentacion);
        $stmt->execute();
        header("Location: iniciar_sesion.php?status=ok&mensaje=" . urlencode("Registro exitoso. Ahora puedes iniciar sesión."));

        exit();

    } elseif ($tipo === "empresa") {
        // Datos del formulario
        $nombre = strtoupper(trim($_POST["nombre"]));
        $rfc = strtoupper(trim($_POST["rfc"]));
        $email = strtolower(trim($_POST["email_empresa"]));
        $pais = trim($_POST["pais"]);
        $password = $_POST["password_empresa"];
        $sitio = $_POST["sitio_web"] ?? null;
        $descripcion = $_POST["presentacion_empresa"] ?? '';

        // Validar email duplicado
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM (
                SELECT correo FROM usuarios WHERE correo = :email
                UNION ALL
                SELECT correo FROM empresas WHERE correo = :email
            ) AS correos_repetidos
        ");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            header("Location: registro.php?status=error&mensaje=" . urlencode("El correo ya está registrado. Intente de nuevo.") . "&tipo=" . $tipo);
            exit();
        }

        // Insertar
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO empresas (nombre, rfc, correo, contrasena, pais, sitio_web, texto_presentacion)
                VALUES (:nombre, :rfc, :correo, :contrasena, :pais, :web, :presentacion)");
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":rfc", $rfc);
        $stmt->bindParam(":correo", $email);
        $stmt->bindParam(":contrasena", $hash);
        $stmt->bindParam(":pais", $pais);
        $stmt->bindParam(":web", $sitio);
        $stmt->bindParam(":presentacion", $descripcion);
        $stmt->execute();
        header("Location: iniciar_sesion.php?status=ok&mensaje=" . urlencode("Empresa registrada con éxito. Inicia sesión."));

        exit();

    } else {
        echo "Tipo de usuario no válido.";
        header("Location: registro.php?status=error&mensaje=" . urlencode("El correo ya está registrado. Intente de nuevo"));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro</title>
    <link rel="stylesheet" href="css/paleta_colores.css"/>
    <link rel="stylesheet" href="css/registro.css"/>
</head>
<body>
<div class="registro-wrapper">
    <div class="registro-left">
    </div>
    <div class="registro-right">
        <div class="botones">
            <a href="./inicio.php" class="btn-regresar">Regresar</a>
        </div>

        <div class="form-box">
            <h2>Bienvenido</h2>
            <p class="subtitulo">Por favor ingrese sus datos</p>
            <div class="selector">
                <label for="tipoUsuario">Tipo de usuario:</label>
                <select id="tipoUsuario" onchange="mostrarFormulario()">
                    <option value="">Seleccione...</option>
                    <option value="candidato">Candidato</option>
                    <option value="empresa">Empresa</option>
                </select>
            </div>


            <form id="formCandidato" class="form-registro" action="registro.php" method="POST" autocomplete="off" style="display:none;">
                <input type="hidden" name="tipo" value="candidato"/>
                <label for="nombre">Nombre(s)</label>
                <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="50"
                       placeholder="Escribe tu nombre">
                <label for="apellido_paterno">Apellido paterno</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" required minlength="3" maxlength="100"
                       placeholder="Escribe tu apellido paterno">
                <label for="apellido_materno">Apellido materno</label>
                <input type="text" id="apellido_materno" name="apellido_materno" required minlength="3" maxlength="100"
                       placeholder="Escribe tu apellido materno">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" required pattern="[0-9]{10,15}"
                       placeholder="00-0000-0000" title="Ingresa solo números (mínimo 10 dígitos)">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@ejemplo.com" required>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required minlength="8">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                <label for="presentacion">Texto para Presentarte</label>

                <textarea id="presentacion" name="presentacion" rows="2" maxlength="300"
                          placeholder="Habla sobre ti y tus habilidades"></textarea>

                <button type="submit" class="btn-registro">Registrar</button>
            </form>

            <form id="formEmpresa" class="form-registro" action="registro.php" method="POST" autocomplete="off" style="display:none;">
                <input type="hidden" name="tipo" value="empresa"/>

                <label for="nombre">Nombre Empresa</label>
                <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="100"
                       placeholder="Nombre">
                <label for="rfc">RFC o ID Fiscal</label>
                <input type="text" id="rfc" name="rfc" required pattern="[A-Za-z0-9]{10,13}"
                       title="Debe tener entre 10 y 13 caracteres alfanuméricos">
                <label for="email_empresa">Email</label>
                <input type="email" id="email_empresa" name="email_empresa" placeholder="ejemplo@ejemplo.com" required>
                <label for="pais">País</label>
                <input type="text" id="pais" name="pais" required maxlength="50">
                <label for="password_empresa">Contraseña</label>
                <input type="password" id="password_empresa" name="password_empresa" required minlength="8">
                <label for="confirm_password_empresa">Confirmar Contraseña</label>
                <input type="password" id="confirm_password_empresa" name="confirm_password_empresa" required
                       minlength="8">
                <label for="sitio_web">Sitio web</label>
                <input type="url" id="sitio_web" name="sitio_web" placeholder="https://www.ejemplo.com">
                <label for="presentacion_empresa">Texto para presentar a la empresa</label>
                <textarea id="presentacion_empresa" name="presentacion_empresa" rows="2" maxlength="300"
                          placeholder="Habla sobre lo que busca tu empresa"></textarea>

                <button type="submit" class="btn-registro">Registrar</button>
            </form>

        </div>

    </div>

</div>

<script>
    function mostrarFormulario() {
        const tipo = document.getElementById("tipoUsuario").value;
        document.getElementById("formCandidato").style.display = tipo === "candidato" ? "block" : "none";
        document.getElementById("formEmpresa").style.display = tipo === "empresa" ? "block" : "none";
    }
</script>
<?php if (isset($_GET["status"]) && isset($_GET["mensaje"])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mensaje = <?php echo json_encode($_GET["mensaje"]); ?>;
            const tipoAlerta = "<?php echo $_GET["status"] === 'ok' ? 'exito' : 'error'; ?>";
            const tipoFormulario = "<?php echo $_GET["tipo"] ?? ''; ?>";

            const alerta = document.createElement("div");
            alerta.className = "alerta-flotante " + tipoAlerta;
            alerta.innerText = mensaje;

            document.body.appendChild(alerta);

            setTimeout(() => {
                alerta.remove();
            }, 4000);

            // Mostrar el formulario seleccionado si hay error
            if (tipoFormulario) {
                const selector = document.getElementById("tipoUsuario");
                selector.value = tipoFormulario;
                mostrarFormulario(); // ya definida en tu script
            }
        });
    </script>
<?php endif; ?>
</body>


</html>
