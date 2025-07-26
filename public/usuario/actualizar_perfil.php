<?php
session_start();
include '../../includes/BD.php';
$conexion = (new Connection())->connect();

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

$id = $_SESSION['usuario']['id'];
$nombre = $_POST['nombre'];
$apellidop = $_POST['apellidop'];
$apellidom = $_POST['apellidom'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$presentacion = $_POST['textpresentacion'];

try {
    $stmt = $conexion->prepare("UPDATE usuarios SET 
        nombre = :nombre,
        apellido_paterno = :apellidop,
        apellido_materno = :apellidom,
        correo = :correo,
        telefono = :telefono,
        texto_presentacion = :presentacion
        WHERE id_usuario = :id");

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidop', $apellidop);
    $stmt->bindParam(':apellidom', $apellidom);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':presentacion', $presentacion);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Refrescar datos en sesión
        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['apellidoP'] = $apellidop;
        $_SESSION['usuario']['apellidoM'] = $apellidom;
        $_SESSION['usuario']['correo'] = $correo;
        $_SESSION['usuario']['telefono'] = $telefono;
        $_SESSION['usuario']['presentacion'] = $presentacion;

        echo "Perfil actualizado correctamente.";
    } else {
        echo "Error al actualizar el perfil.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
