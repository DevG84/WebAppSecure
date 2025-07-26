<?php
session_start();
include '../../includes/BD.php';
$conexion = (new Connection())->connect(); // PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_SESSION['empresa'])) {
        $tabla = "empresas";
        $id = $_SESSION['empresa']['id'];
        $columna_id = "id_empresa";
    } elseif (isset($_SESSION['usuario'])) {
        $tabla = "usuarios";
        $id = $_SESSION['usuario']['id'];
        $columna_id = "id_usuario";
    } else {
        http_response_code(401);
        echo "Sesión no válida.";
        exit;
    }

    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    if ($nueva !== $confirmar) {
        echo json_encode(["error" => true, "mensaje" => "Las contraseñas no coinciden."]);
    }

    // Obtener contraseña actual
    $stmt = $conexion->prepare("SELECT contrasena FROM $tabla WHERE $columna_id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($actual, $usuario['contrasena'])) {
        die("Contraseña actual incorrecta.");
    }

    // Guardar nueva contraseña
    $nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);
    $update = $conexion->prepare("UPDATE $tabla SET contrasena = :nueva WHERE $columna_id = :id");
    $update->bindParam(":nueva", $nuevaHash);
    $update->bindParam(":id", $id);

    if ($update->execute()) {
        session_unset();
        session_destroy();
        echo "Contraseña actualizada correctamente.";
    } else {
        echo "Error al actualizar contraseña.";
    }
}
?>
