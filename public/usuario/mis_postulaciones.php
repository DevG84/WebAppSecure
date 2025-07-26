<?php
session_start();
include '../../includes/BD.php';

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

$id_usuario = $_SESSION['usuario']['id'];
$conn = (new Connection())->connect();

// Obtener postulaciones del usuario
$query = "
    SELECT v.nombre_vacante, e.nombre, p.estado, p.fecha_postulacion
    FROM postulaciones AS p
    INNER JOIN vacantes AS v ON p.id_vacante = v.id_vacante
    INNER JOIN empresas AS e ON v.id_empresa = e.id_empresa
    WHERE p.id_usuario = :id_usuario
    ORDER BY p.fecha_postulacion DESC
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <meta charset="UTF-8">
    <title>Mis Postulaciones</title>
    <link rel="stylesheet" href="../css/ver_postulaciones.css">
</head>
<header>
    <nav class="navbar">
        <div class="logo">WebApp<span>Secure</span></div>

        <div class="botones">
            <a href="./perfil_usuario.php" class="btn-perfil">Perfil</a>

            <form method="POST" id="form-cerrar-sesion" style="display:inline;">
                <a href="" style="color: white" class="btn-cerrar" onclick="document.getElementById('form-cerrar-sesion').submit(); return false;">
                    Cerrar sesión
                </a>
                <input type="hidden" name="cerrar_sesion" value="1">
            </form>
        </div>
    </nav>
</header>
<body>
<div class="form-container">
    <div class="form-card">
        <div class="botones">
            <a href="./inicio.php" class="btn-regresar">Regresar</a>
        </div>
        <h2>Mis Postulaciones</h2>

        <?php if (count($postulaciones) === 0): ?>
            <p>No has realizado ninguna postulación aún.</p>
        <?php else: ?>
            <table class="postulantes-table">
                <thead>
                <tr>
                    <th>Vacante</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                    <th>Fecha de Postulación</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($postulaciones as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre_vacante']) ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td>
                            <span class="estado <?= $p['estado'] ?>">
                                <?= ucfirst($p['estado']) ?>
                            </span>
                        </td>
                        <td><?= date("d/m/Y", strtotime($p['fecha_postulacion'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">WebAppSecure</div>
        <div class="footer-links"><a href="#">Contactos</a></div>
        <div class="footer-copy">&copy; 2025 WebAppSecure.</div>
    </div>
</footer>
</html>
