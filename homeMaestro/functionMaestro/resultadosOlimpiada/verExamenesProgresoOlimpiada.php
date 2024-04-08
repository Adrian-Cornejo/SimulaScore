<?php
session_start();
if (!isset($_SESSION['Maestro'])) {
    header('Location:../loginMaestro.php'); // Redirigir al login si no está logueado
    exit;
}

require '../../../config/db.php';

$db = new db();
$con = $db->conexion();

// Obtener el código del maestro en base a su correo
$correoMaestro = $_SESSION['Maestro'];

$sqlHelp = $con->prepare("SELECT codigoProfesor, nombre, apellido FROM profesor WHERE correo = :correoMaestro");
$sqlHelp->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
$sqlHelp->execute();
$resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);

$codigoMaestro = $resultadoaux[0]['codigoProfesor'];
$nombreMaestro = $resultadoaux[0]['nombre'];
$apellidoMaestro = $resultadoaux[0]['apellido'];

function obtenerExamenesEnProgresoPorProfesor($con, $codigoProfesor) {
    $examenes = [];
    try {
        // Primero, obtén los códigos de alumnos a cargo del profesor
        $sqlAlumnos = $con->prepare("SELECT codigoAlumno FROM alumno WHERE codigoProfesor = :codigoProfesor");
        $sqlAlumnos->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
        $sqlAlumnos->execute();
        $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($alumnos)) {
            // Convertir los resultados en una lista de códigos de alumno
            $codigosAlumnos = array_map(function($alumno) {
                return $alumno['codigoAlumno'];
            }, $alumnos);

            // Preparar consulta para obtener exámenes en progreso de esos alumnos
            $inQuery = implode(',', array_fill(0, count($codigosAlumnos), '?')); // Crear lista de placeholders
            $stmt = $con->prepare("SELECT * FROM resultados_examen_olimpiada WHERE en_progreso = 1 AND codigoAlumno IN ($inQuery)");
            $stmt->execute($codigosAlumnos);

            // Recoger todos los resultados
            $examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error al recuperar examenes en progreso por profesor: " . $e->getMessage());
    }
    return $examenes;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
          crossorigin="anonymous">
    <link rel="stylesheet" href="../../../build/css/directivos.css">
</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
    
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../../panelControlMaestro.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="../../panelControlMaestro.php" class="boton" style="background-color:#687483; text-decoration: none; color: inherit; padding: 2rem;">Regresar</a>
  </div>
</nav>

<main>
    <div style="margin: 0 15px;">
        <h1>Exámenes en progreso actualmente de el examen: Olimpiada del conocimiento</h1>
    </div>
    <?php
    $examenesEnProgreso = obtenerExamenesEnProgresoPorProfesor($con, $codigoMaestro);
    if (!empty($examenesEnProgreso)) {
        echo "<table class='tabla-docentes'>";
        echo "<tr>
                <th>ID Examen</th>
                <th>Código Alumno</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Progreso</th>
                <th>Acciones</th>
              </tr>";

        foreach ($examenesEnProgreso as $examen) {
            $codigoAlumno = $examen['codigoAlumno'];
            $sqlAlumno = $con->prepare("SELECT nombre, apellido FROM alumno WHERE codigoAlumno = :codigoAlumno");
            $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
            $sqlAlumno->execute();
            $datosAlumno = $sqlAlumno->fetch(PDO::FETCH_ASSOC);

            $nombre = $datosAlumno ? $datosAlumno['nombre'] : "No encontrado";
            $apellido = $datosAlumno ? $datosAlumno['apellido'] : "No encontrado";

            echo "<tr>";
            echo "<td>" . htmlspecialchars($examen['id']) . "</td>";
            echo "<td>" . htmlspecialchars($examen['codigoAlumno']) . "</td>";
            echo "<td>" . htmlspecialchars($nombre) . "</td>";
            echo "<td>" . htmlspecialchars($apellido) . "</td>";
            echo "<td>En Progreso</td>";
            echo "<td>
                    <a href='verProgresoAlumno.php?codigoAlumno=" . urlencode($examen['codigoAlumno']) . "'>
                      Ver Progreso
                    </a>
                  </td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No hay exámenes en progreso en este momento.</p>";
    }
    ?>
</main>

</body>
</html>
