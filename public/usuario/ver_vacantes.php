<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../inicio.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    session_unset();
    session_destroy();
    header("Location: ../inicio.php");
    exit();
}

include '../../includes/BD.php';
$conexion = (new Connection())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv']) && isset($_POST['id_vacante'])) {
    $id_usuario = $_SESSION['usuario']['id'];
    $id_vacante = $_POST['id_vacante'];

    $archivo = $_FILES['cv'];
    $nombre_original = basename($archivo['name']);
    $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);

    if ($extension !== 'pdf') {
        die("Error: Solo se permiten archivos PDF.");
    }

    if ($archivo['size'] > 5 * 1024 * 1024) {
        die("Error: El archivo excede el tamaño permitido (5MB).");
    }

    // Guardar el archivo en la carpeta 'cv_files'
    $nombre_nuevo = $id_usuario . '_' . 'cv_' . time() . '.pdf';
    $ruta_destino = '../../cv_files/' . $nombre_nuevo;

    if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        die("Error al subir el archivo.");
    }

    // Guardar en la base de datos
    $stmtCV = $conexion->prepare("INSERT INTO cv_archivos (id_usuario, nombre_archivo, ruta_archivo) VALUES (:id_usuario, :nombre_archivo, :ruta_archivo)");
    $stmtCV->bindParam(':id_usuario', $id_usuario);
    $stmtCV->bindParam(':nombre_archivo', $nombre_nuevo);
    $stmtCV->bindParam(':ruta_archivo', $ruta_destino);
    $stmtCV->execute();

    $id_cv = $conexion->lastInsertId();

    // Verificar si ya se postuló a esta vacante
    $stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM postulaciones WHERE id_usuario = ? AND id_vacante = ?");
    $stmtCheck->execute([$id_usuario, $id_vacante]);
    $ya_postulado = $stmtCheck->fetchColumn();

    if ($ya_postulado > 0) {
        echo "<script>
        alert('Ya te has postulado a esta vacante.');
        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
    </script>";
        exit;
    }

    $stmtPost = $conexion->prepare("INSERT INTO postulaciones (id_usuario, id_vacante, id_cv) VALUES (?, ?, ?)");
    $stmtPost->execute([$id_usuario, $id_vacante, $id_cv]);

    header("Location: " . $_SERVER['PHP_SELF'] . "?exito=1");
    exit;
}

$sql = "SELECT v.*, e.nombre 
        FROM vacantes AS v
        JOIN empresas AS e ON v.id_empresa = e.id_empresa
        WHERE v.id_vacante NOT IN (
            SELECT id_vacante FROM postulaciones WHERE id_usuario = :id_usuario
        )
        ORDER BY v.fecha_publicacion DESC";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes disponibles</title>
    <link rel="stylesheet" href="../css/paleta_colores.css">
    <link rel="stylesheet" href="../css/ver_vacantes.css">
</head>
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
<body>
<div class="form-container">
    <div class="form-card">
        <div class="botones">
            <a href="./inicio.php" class="btn-regresar">Regresar</a>
        </div>
        <h2>Vacantes disponibles</h2>

        <?php if (isset($_GET['exito'])): ?>
            <p style="color: green; font-weight: bold;">¡Postulación enviada con éxito!</p>
        <?php endif; ?>

        <?php if (count($vacantes) === 0): ?>
            <p>No hay vacantes disponibles en este momento.</p>
        <?php else: ?>
            <div class="lista-vacantes">
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="vacante">
                        <h3><?php echo htmlspecialchars($vacante['nombre_vacante']); ?></h3>
                        <p><strong>Empresa:</strong>
                            <a href="perfil_empresa.php?id=<?php echo $vacante['id_empresa']; ?>">
                                <?php echo htmlspecialchars($vacante['nombre']); ?>
                            </a>
                        </p>
                        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($vacante['ciudad'] . ', ' . $vacante['estado'] . ', ' . $vacante['pais']); ?></p>
                        <p><strong>Modalidad:</strong> <?php echo $vacante['modalidad']; ?></p>
                        <p><strong>Sueldo:</strong> $<?php echo number_format($vacante['sueldo'], 2); ?></p>
                        <p><strong>Horarios:</strong> <?php echo htmlspecialchars($vacante['horarios']); ?></p>
                        <p><strong>Publicado el:</strong> <?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?></p>
                        <p><strong>Detalles: </strong>
                            <p><?php echo $vacante['descripcion']; ?></p>
                        </p>

                        <p><strong>Sube tu CV (PDF):</strong></p>

                        <!-- Formulario de postulación -->
                        <form action="" method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
                            <input type="hidden" name="id_vacante" value="<?php echo $vacante['id_vacante']; ?>">
                            <label for="cv_<?php echo $vacante['id_vacante']; ?>"></label>
                            <input type="file" name="cv" id="cv_<?php echo $vacante['id_vacante']; ?>" accept="application/pdf" required><br><br>
                            <button type="submit">Postularse</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
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
