<?php
session_start();
include '../../includes/BD.php';

$conn = (new Connection())->connect();

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

$id_empresa = $_SESSION['empresa']['id'];

$sql = "SELECT * FROM vacantes WHERE id_empresa = :id_empresa ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_empresa', $id_empresa);
$stmt->execute();
$vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vacantes</title>
    <link rel="stylesheet" href="../css/ver_postulaciones.css">
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

<div class="form-container">
    <div class="form-card">
        <div class="botones">
            <a href="./inicio.php" class="btn-regresar">Regresar</a>
        </div>
        <h2>Mis Vacantes Publicadas</h2>

        <?php if (count($vacantes) === 0): ?>
            <p>No tienes vacantes publicadas aún.</p>
        <?php else: ?>
            <table class="postulantes-table">
                <thead>
                <tr>
                    <th>Vacante</th>
                    <th>Modalidad</th>
                    <th>Ciudad</th>
                    <th>Fecha de publicación</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($vacantes as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['nombre_vacante']) ?></td>
                        <td><?= htmlspecialchars($v['modalidad']) ?></td>
                        <td><?= htmlspecialchars($v['ciudad']) ?></td>
                        <td><?= date("d/m/Y", strtotime($v['fecha_publicacion'])) ?></td>
                        <td class="botones">
                            <form action="ver_postulantes.php" method="GET" style="display: inline-block;">
                                <input type="hidden" name="id_vacante" value="<?= $v['id_vacante'] ?>">
                                <button class="btn-normal" type="submit">Ver Postulantes</button>
                            </form>

                            <form action="editar_vacante.php" method="GET" style="display: inline-block; margin-left: 5px;">
                                <input type="hidden" name="id_vacante" value="<?= $v['id_vacante'] ?>">
                                <button class="btn-editar" type="submit">Editar</button>
                            </form>

                            <form action="eliminar_vacante.php" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta vacante?');">
                                <input type="hidden" name="id_vacante" value="<?= $v['id_vacante'] ?>">
                                <button class="btn-borrar" type="submit">Eliminar Vacante</button>
                            </form>
                        </td>
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
        <div class="footer-links"><a href="#">Contáctanos</a></div>
        <div class="footer-copy">&copy; 2025 WebAppSecure.</div>
    </div>
</footer>
</html>