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

// Procesar acción (aceptar/rechazar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_postulacion'], $_POST['accion'])) {
    $id_postulacion = $_POST['id_postulacion'];
    $accion = $_POST['accion'];

    if (in_array($accion, ['aceptar', 'rechazar'])) {
        $nuevo_estado = $accion === 'aceptar' ? 'aceptado' : 'rechazado';

        $stmt = $conn->prepare("UPDATE postulaciones SET estado = :estado WHERE id_postulacion = :id");
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':id', $id_postulacion);
        $stmt->execute();
    }
}

// Verificar que exista id_vacante en GET
if (!isset($_GET['id_vacante'])) {
    echo "No se ha seleccionado una vacante.";
    exit;
}

$id_vacante = $_GET['id_vacante'];

// Preparar consulta para obtener postulantes solo de esa vacante
$query = "
    SELECT p.id_postulacion,
           u.nombre, u.apellido_paterno, u.apellido_materno,
           p.estado, p.fecha_postulacion,
           cv.ruta_archivo,
           v.nombre_vacante
    FROM postulaciones p
    INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
    LEFT JOIN cv_archivos cv ON cv.id_cv = p.id_cv
    INNER JOIN vacantes v ON p.id_vacante = v.id_vacante
    WHERE p.id_vacante = :id_vacante
    ORDER BY p.fecha_postulacion DESC
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
$stmt->execute();
$postulantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombreVacante = count($postulantes) > 0 ? $postulantes[0]['nombre_vacante'] : 'la vacante';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postulantes</title>
    <link rel="stylesheet" href="../css/ver_postulaciones.css">
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
<div class="form-container">
    <div class="form-card">
        <div class="botones">
            <a href="ver_vacantes.php" class="btn-regresar">Regresar</a>
        </div>
        <h2>Postulantes para <?= htmlspecialchars($nombreVacante) ?></h2>

        <?php if (count($postulantes) === 0): ?>
            <p>No hay postulaciones para esta vacante.</p>
        <?php else: ?>
            <h3>Vacante: <?= htmlspecialchars($postulantes[0]['nombre_vacante']) ?></h3>
            <table class="postulantes-table">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>CV</th>
                    <th>Estado</th>
                    <th>Fecha de Postulación</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($postulantes as $row): ?>
                    <?php
                    $id_postulacion = $row['id_postulacion'];
                    $nombreCompleto = $row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno'];
                    $estado = $row['estado'];
                    $fecha = date("d/M/Y", strtotime($row['fecha_postulacion']));
                    $cv = ($row['ruta_archivo']) ? "<a href='{$row['ruta_archivo']}' class='btn-cerrar' target='_blank'>  📄Ver CV</a>" : "Sin CV";
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($nombreCompleto) ?></td>
                        <td><?= $cv ?></td>
                        <td>
                            <?php if ($estado === 'pendiente'): ?>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="id_postulacion" value="<?= $id_postulacion ?>">
                                    <button type="submit" name="accion" value="aceptar" class="btn aceptar">Aceptar
                                    </button>
                                </form>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="id_postulacion" value="<?= $id_postulacion ?>">
                                    <button type="submit" name="accion" value="rechazar" class="btn rechazar">Rechazar
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="estado <?= $estado ?>"><?= ucfirst($estado) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $fecha ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <br>
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
