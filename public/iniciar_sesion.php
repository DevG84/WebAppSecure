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
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $tabla = "usuarios";

    // Buscar primero en usuarios
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no está, buscar en empresas
    if (!$user) {
        $tabla = "empresas";
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE correo = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($user) {
        if (password_verify($password, $user["contrasena"])) {
            session_regenerate_id(true);

            if ($tabla == "usuarios") {
                $_SESSION['usuario'] = [
                    'tipo' => 'usuario',
                    'id' => $user['id_usuario'],
                    'nombre' => $user['nombre'],
                    'apellidoP' => $user['apellido_paterno'],
                    'apellidoM' => $user['apellido_materno'],
                    'telefono' => $user['telefono'],
                    'correo' => $user['correo'],
                    'presentacion' => $user['texto_presentacion']
                ];

                header("Location: usuario/inicio.php");
            } else {
                $_SESSION['empresa'] = [
                    'tipo' => 'empresa',
                    'id' => $user['id_empresa'],
                    'nombre' => $user['nombre'],
                    'rfc' => $user['rfc'],
                    'correo' => $user['correo'],
                    'pais' => $user['pais'],
                    'sitio_web' => $user['sitio_web'],
                    'presentacion' => $user['texto_presentacion']
                ];

                header("Location: empresa/inicio.php");
            }
            exit();
        } else {
            header("Location: iniciar_sesion.php?status=error&mensaje=" . urlencode("Contraseña incorrecta. Intente de nuevo"));
        }
    } else {
        header("Location: iniciar_sesion.php?status=error&mensaje=" . urlencode("Usuario no encontrado. Intente de nuevo"));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Iniciar Sesión - WAS</title>
    <link rel="stylesheet" href="css/paleta_colores.css"/>
    <link rel="stylesheet" href="css/iniciar_sesion.css"/>
</head>
<body>
<div class="registro-wrapper">

    <div class="registro-right">
        <div class="botones">
            <a href="./inicio.php" class="btn-regresar">Regresar</a>
        </div>

        <div class="form-box">
            <h2>Bienvenido de vuelta</h2>
            <p class="subtitulo">Por favor ingrese sus datos</p>
            <form class="form-registro" action="" method="POST" autocomplete="off">
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required placeholder="ejemplo123@ejemplo.com">

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Escribe tu contraseña">

                <button type="submit" class="btn-registro">Ingresar</button>
            </form>
        </div>
    </div>

    <div class="registro-left">
    </div>
</div>

<?php if (isset($_GET["status"]) && isset($_GET["mensaje"])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const mensaje = <?php echo json_encode($_GET["mensaje"]); ?>;
            const tipo = "<?php echo $_GET["status"] === 'ok' ? 'exito' : 'error'; ?>";

            const alerta = document.createElement("div");
            alerta.className = "alerta-flotante " + tipo;
            alerta.textContent = mensaje;
            document.body.appendChild(alerta);

            setTimeout(() => {
                alerta.remove();
                window.history.replaceState(null, "", window.location.pathname);
            }, 4000);
        });
    </script>
<?php endif; ?>

</body>
</html>