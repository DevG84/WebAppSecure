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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="icon" href="./img/logo.png" type="image/png">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">WebApp<span>Secure</span></div>
        <div class="botones">
            <a href="./iniciar_sesion.php" class="btn-login">Iniciar sesión</a>
            <a href="./registro.php" class="btn-register">Registrarse</a>
        </div>
    </nav>
</header>

<section class="hero">
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Encuentra el trabajo<br>de tus sueños</h1>
                <p>Miles de oportunidades laborales te esperan.<br>Conectamos talento con las mejores empresas.</p>
            </div>
            <div class="hero-image">
                <img src="./img/logo.png" alt="logo">
            </div>
        </div>
    </section>
</section>
<main>

</main>
</body>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">WebAppSecure</div>
        <div class="footer-links"><a href="#">Contáctanos</a></div>
        <div class="footer-copy">&copy; 2025 WebAppSecure.</div>
    </div>
</footer>
</html>