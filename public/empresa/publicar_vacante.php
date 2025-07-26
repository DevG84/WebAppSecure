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

include '../../includes/BD.php';
$conexion = (new Connection())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empresa = $_SESSION['empresa']['id'];
    $nombre_vacante = $_POST['nombre_vacante'];
    $modalidad = $_POST['modalidad'];
    $estado = $_POST['estado'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $sueldo = $_POST['sueldo'];
    $horarios = $_POST['horarios'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO vacantes (
                id_empresa, nombre_vacante, modalidad, estado, ciudad, pais, sueldo, horarios, descripcion
            ) VALUES (
                :id_empresa, :nombre_vacante, :modalidad, :estado, :ciudad, :pais, :sueldo, :horarios, :descripcion
            )";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_empresa', $id_empresa);
    $stmt->bindParam(':nombre_vacante', $nombre_vacante);
    $stmt->bindParam(':modalidad', $modalidad);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':ciudad', $ciudad);
    $stmt->bindParam(':pais', $pais);
    $stmt->bindParam(':sueldo', $sueldo);
    $stmt->bindParam(':horarios', $horarios);
    $stmt->bindParam(':descripcion', $descripcion);

    if ($stmt->execute()) {
        echo "<script>alert('¡Vacante publicada correctamente!'); window.location.href = 'inicio.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al publicar la vacante.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Vacante</title>
    <link rel="stylesheet" href="../css/perfiles.css">
</head>
<body>
<div class="form-container">
    <div class="form-card">
        <h2>Publicar nueva vacante</h2>

        <form class="formulario" method="POST">
            <label for="nombre_vacante">Nombre de la vacante</label>
            <input type="text" id="nombre_vacante" name="nombre_vacante" required>

            <label for="modalidad">Modalidad</label>
            <select id="modalidad" name="modalidad" required>
                <option value="">Selecciona una opción</option>
                <option value="Remoto">Remoto</option>
                <option value="Presencial">Presencial</option>
                <option value="Hibrido">Híbrido</option>
            </select>

            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" required>

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required>

            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" value="México" required>

            <label for="sueldo">Sueldo (MXN)</label>
            <input type="number" id="sueldo" name="sueldo" step="0.01" required>

            <label for="horarios">Horarios</label>
            <input type="text" id="horarios" name="horarios" required>

            <label for="descripcion">Descripción del empleo</label>
            <textarea id="descripcion" name="descripcion" rows="5" required></textarea>

            <div class="botones">
                <button type="submit" class="btn-guardar">Publicar vacante</button>
                <button type="button" class="btn-cancelar" onclick="history.back()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
