<?php
session_start();
include '../../includes/BD.php';
$conexion = (new Connection())->connect();

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

$id = $_SESSION['empresa']['id'];
$nombre = $_POST['nombre'];
$rfc = $_POST['rfc'];
$correo = $_POST['correo'];
$pais = $_POST['pais'];
$web = $_POST['web'];
$presentacion = $_POST['textpresentacion'];


try {
    $stmt = $conexion->prepare("UPDATE empresas SET 
        nombre = :nombre,
        rfc = :rfc,
        correo = :correo,
        pais = :pais,
        sitio_web = :web,
        texto_presentacion = :presentacion
        WHERE id_empresa = :id");

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':rfc', $rfc);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':pais', $pais);
    $stmt->bindParam(':web', $web);
    $stmt->bindParam(':presentacion', $presentacion);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['empresa']['nombre'] = $nombre;
        $_SESSION['empresa']['rfc'] = $rfc;
        $_SESSION['empresa']['correo'] = $correo;
        $_SESSION['empresa']['pais'] = $pais;
        $_SESSION['empresa']['sitio_web'] = $web;
        $_SESSION['empresa']['presentacion'] = $presentacion;

        echo "Perfil actualizado correctamente.";
    } else {
        echo "Error al actualizar perfil.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
