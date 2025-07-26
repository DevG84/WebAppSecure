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
    <title>Principal</title>
    <link rel="stylesheet" href="../css/principal.css"/>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">WebApp<span>Secure</span></div>

        <div class="botones">
            <a href="./perfil_empresa.php" class="btn-perfil">Perfil</a>

            <form method="POST" id="form-cerrar-sesion" style="display:inline;">
                <a href="" style="color: white" class="btn-cerrar"
                   onclick="document.getElementById('form-cerrar-sesion').submit(); return false;">
                    Cerrar sesión
                </a>
                <input type="hidden" name="cerrar_sesion" value="1">
            </form>
        </div>
    </nav>
</header>

<main class="main">
    <div class="bienvenida">
        <h1 style="margin: 10px 0 10px 0">Hola, <?php echo $_SESSION['empresa']['nombre'] ?></h1>
        <p style="font-size: 20px">¿Cuál es el plan de hoy?</p>
    </div>

    <div class="botonera">
        <button class="btn-normal" onclick="location.href='publicar_vacante.php'">Crear vacante</button>
        <button class="btn-normal" onclick="location.href='ver_vacantes.php'">Ver Vacantes</button>
    </div>

</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">WebAppSecure</div>
        <div class="footer-links"><a href="#">Contáctanos</a></div>
        <div class="footer-copy">&copy; 2025 WebAppSecure.</div>
    </div>
</footer>
</body>
</html>
