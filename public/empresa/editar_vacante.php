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

$id_empresa = $_SESSION['empresa']['id'];
$id_vacante = $_GET['id_vacante'] ?? $_POST['id_vacante'] ?? null;
$vacante = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_vacante = $_POST['nombre_vacante'];
    $modalidad = $_POST['modalidad'];
    $estado = $_POST['estado'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $sueldo = $_POST['sueldo'];
    $horarios = $_POST['horarios'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE vacantes SET
                nombre_vacante = :nombre_vacante,
                modalidad = :modalidad,
                estado = :estado,
                ciudad = :ciudad,
                pais = :pais,
                sueldo = :sueldo,
                horarios = :horarios,
                descripcion = :descripcion
            WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':nombre_vacante' => $nombre_vacante,
        ':modalidad' => $modalidad,
        ':estado' => $estado,
        ':ciudad' => $ciudad,
        ':pais' => $pais,
        ':sueldo' => $sueldo,
        ':horarios' => $horarios,
        ':descripcion' => $descripcion,
        ':id_vacante' => $id_vacante,
        ':id_empresa' => $id_empresa
    ]);

    echo "<script>alert('¡Vacante actualizada correctamente!'); window.location.href = 'ver_vacantes.php';</script>";
    exit();
}

if ($id_vacante) {
    $stmt = $conexion->prepare("SELECT * FROM vacantes WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa");
    $stmt->execute([':id_vacante' => $id_vacante, ':id_empresa' => $id_empresa]);
    $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacante) {
        echo "<script>alert('Vacante no encontrada.'); window.location.href = 'mis_vacantes.php';</script>";
        exit();
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
            <input type="hidden" name="id_vacante" value="<?= $id_vacante ?>">

            <label for="nombre_vacante">Nombre de la vacante</label>
            <input type="text" id="nombre_vacante" name="nombre_vacante" value="<?= htmlspecialchars($vacante['nombre_vacante']) ?>" required>

            <label for="modalidad">Modalidad</label>
            <select id="modalidad" name="modalidad" required>
                <option value="">Selecciona una opción</option>
                <option value="Remoto" <?= $vacante['modalidad'] == 'Remoto' ? 'selected' : '' ?>>Remoto</option>
                <option value="Presencial" <?= $vacante['modalidad'] == 'Presencial' ? 'selected' : '' ?>>Presencial</option>
                <option value="Hibrido" <?= $vacante['modalidad'] == 'Hibrido' ? 'selected' : '' ?>>Híbrido</option>
            </select>

            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" value="<?= htmlspecialchars($vacante['estado']) ?>" required>

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($vacante['ciudad']) ?>" required>

            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" value="<?= htmlspecialchars($vacante['pais']) ?>" required>

            <label for="sueldo">Sueldo (MXN)</label>
            <input type="number" id="sueldo" name="sueldo" step="0.01" value="<?= htmlspecialchars($vacante['sueldo']) ?>" required>

            <label for="horarios">Horarios</label>
            <input type="text" id="horarios" name="horarios" value="<?= htmlspecialchars($vacante['horarios']) ?>" required>

            <label for="descripcion">Descripción del empleo</label>
            <textarea id="descripcion" name="descripcion" rows="5" required><?= htmlspecialchars($vacante['descripcion']) ?></textarea>

            <div class="botones">
                <button type="submit" class="btn-guardar">Guardar cambios</button>
                <button type="button" class="btn-cancelar" onclick="history.back()">Cancelar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>