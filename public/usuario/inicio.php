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

include '../../includes/BD.php';
$conn = (new Connection())->connect();

$id_usuario = $_SESSION['usuario']['id'];

// Vacantes recientes
$sqlVacantes = "
    SELECT v.nombre_vacante, e.nombre, v.sueldo, v.modalidad
    FROM vacantes v
    JOIN empresas e ON v.id_empresa = e.id_empresa
    ORDER BY v.fecha_publicacion DESC
    LIMIT 3
";
$stmtVacantes = $conn->query($sqlVacantes);
$vacantes = $stmtVacantes->fetchAll(PDO::FETCH_ASSOC);

// Mis postulaciones
$sqlPostulaciones = "
    SELECT v.nombre_vacante, p.estado
    FROM postulaciones p
    JOIN vacantes v ON p.id_vacante = v.id_vacante
    WHERE p.id_usuario = :id_usuario
    ORDER BY p.fecha_postulacion DESC
    LIMIT 3
";
$stmtPostulaciones = $conn->prepare($sqlPostulaciones);
$stmtPostulaciones->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmtPostulaciones->execute();
$postulaciones = $stmtPostulaciones->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="./perfil_usuario.php" class="btn-perfil">Perfil</a>

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

<div class="main">
    <div class="bienvenida">
        <h1 style="margin: 10px 0 10px 0">¡Hola! <?php echo $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellidoP'] ?></h1>
        <p style="font-size: 20px">¿Qué quieres hacer hoy?</p>
    </div>

    <div class="paneles">
        <div class="panel">
            <h2>Vacantes más recientes</h2>
            <div class="tarjetas">
                <?php foreach ($vacantes as $v): ?>
                    <div class="tarjeta">
                        <strong><?= htmlspecialchars($v['nombre_vacante']) ?></strong><br>
                        Empresa: <?= htmlspecialchars($v['nombre']) ?><br>
                        Sueldo: $<?= number_format($v['sueldo'], 2) ?><br>
                        Modalidad: <?= htmlspecialchars($v['modalidad']) ?><br>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn-normal" onclick="location.href='ver_vacantes.php'">Ver vacantes</button>
        </div>

        <div class="panel">
            <h2>Mis postulaciones recientes</h2>
            <ul>
                <?php if (count($postulaciones) === 0): ?>
                    <li>No tienes postulaciones recientes.</li>
                <?php else: ?>
                    <?php foreach ($postulaciones as $p): ?>
                        <li>
                            <strong><?= htmlspecialchars($p['nombre_vacante']) ?></strong><br>
                            Estado: <span class="estado-<?= htmlspecialchars($p['estado']) ?>"><?= ucfirst(htmlspecialchars($p['estado'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <button class="btn-normal" onclick="location.href='mis_postulaciones.php'">Ver mis postulaciones</button>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">WebAppSecure</div>
        <div class="footer-links"><a href="#">Contáctanos</a></div>
        <div class="footer-copy">&copy; 2025 WebAppSecure.</div>
    </div>
</footer>
</body>
</html>
