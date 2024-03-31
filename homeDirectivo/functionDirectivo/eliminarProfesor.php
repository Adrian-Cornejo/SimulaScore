<?php
session_start();
if (!isset($_SESSION['correo'])) {
    header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
    exit;
}
require '../../config/db.php';

if(isset($_GET['codigoProfesor'])) {
    $codigoProfesor = $_GET['codigoProfesor'];

    try {
        $db = new db();
        $con = $db->conexion();

        // Intenta eliminar el profesor
        $sql = $con->prepare("DELETE FROM profesor WHERE codigoProfesor = :codigoProfesor");
        $sql->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
        $sql->execute();

        // Si todo va bien, redirige con un mensaje de éxito
        $_SESSION['mensaje'] = "Profesor eliminado con éxito.";
        header("Location:administrarDocentes.php");
    } catch (PDOException $e) {
        $errorCode = $e->errorInfo[1];
        if($errorCode == 1451) {
            // Si el error es por violación de integridad referencial
            $_SESSION['error'] = "No se puede eliminar el profesor porque tiene alumnos asignados.";
        } else {
            // Otro tipo de error de base de datos
            $_SESSION['error'] = "Error al eliminar el profesor: " . $e->getMessage();
        }
        header("Location:administrarDocentes.php");
    }
} else {
    // No se proporcionó el código del profesor
    header("Location:administrarDocentes.php");
}
?>
