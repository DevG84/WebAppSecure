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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_vacante'])) {
    $id_vacante = $_POST['id_vacante'];

    // Eliminar primero las postulaciones relacionadas
    $stmt = $conn->prepare("DELETE FROM postulaciones WHERE id_vacante = ?");
    $stmt->execute([$id_vacante]);

    // Luego eliminar la vacante
    $stmt = $conn->prepare("DELETE FROM vacantes WHERE id_vacante = ?");
    $stmt->execute([$id_vacante]);

    header("Location: ver_vacantes.php");
    exit;
}