<?php
session_start();
if (!isset($_SESSION['Maestro'])) {
    echo "No tienes permiso para realizar esta acción.";
    exit;
}

require '../../config/db.php';
$db = new db();
$con = $db->conexion();



// Comprobando si es una búsqueda de alumnos
if (isset($_GET['busqueda'])) {
    $termino = "%" . $_GET['busqueda'] . "%";
     $termino;
    $query = $con->prepare("SELECT * FROM alumno WHERE nombre LIKE :termino OR apellido LIKE :termino");
    $query->bindParam(':termino', $termino, PDO::PARAM_STR);
    $query->execute();
    $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultados)) {
        echo "<ul>";
        foreach ($resultados as $alumno) {
            echo "<li data-id='" . $alumno['codigoAlumno'] . "'>" ."".htmlspecialchars($alumno['codigoAlumno']).":   ". htmlspecialchars($alumno['nombre']) . " " . htmlspecialchars($alumno['apellido']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No se encontraron resultados.";
    }
}
// Comprobando si se solicitan los detalles de un alumno específico
else if (isset($_GET['detalleAlumno'])) {
    echo $codigoAlumno = $_GET['detalleAlumno'];
    $sqlresultados = $con->prepare("SELECT * FROM resultados_examen_mejoredu WHERE codigoAlumno = :codigoAlumno ORDER BY fecha ASC LIMIT 20");
    $sqlresultados->bindParam(':codigoAlumno',$codigoAlumno,PDO::PARAM_STR);
    $sqlresultados->execute();
    $resultadosExamen = $sqlresultados->fetchAll(PDO::FETCH_ASSOC);

    echo "<div>Detalle del alumno: " . htmlspecialchars($resultadosExamen['fecha']) . "</div>";
}
?>
