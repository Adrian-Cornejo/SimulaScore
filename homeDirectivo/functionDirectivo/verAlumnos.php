<?php
   session_start();
   if (!isset($_SESSION['correo'])) {
     header('Location:../loginDirectvo.php'); // Redirigir al login si no está logueado
     exit;
 }
 
   require '../../config/db.php';
  
   $db = new db();
   $con =$db->conexion();
   
   //Obtener el codigo del Directivo en base a su correo
   $correoDirectivo = $_SESSION['correo'];

   $sqlHelp = $con->prepare("SELECT codigoDirectivo, Nombre, Apellido FROM directivo  WHERE correo = :correoDirectivo");
   $sqlHelp->bindParam(':correoDirectivo', $correoDirectivo, PDO::PARAM_STR);
   $sqlHelp->execute();
   $resultadoaux = $sqlHelp->fetch(PDO::FETCH_ASSOC);
   $codigoDirectivo = $resultadoaux['codigoDirectivo'];
   $nombreDirectivo= $resultadoaux['Nombre'];
   $apellidoDirectivo   = $resultadoaux['Apellido'];
   
   
   $sqlDocentes = $con->prepare("SELECT * FROM profesor WHERE codigoDirectivo = :codigoDirectivo");
   $sqlDocentes->bindParam(':codigoDirectivo', $codigoDirectivo, PDO::PARAM_STR);
   $sqlDocentes->execute();
   $docente = $sqlDocentes->fetchAll(PDO::FETCH_ASSOC);

   function obtenerExamenesEnProgresoPorProfesor($con, $codigoProfesor) {
    $alumnos = [];
    try {
        // Primero, obtén los códigos de alumnos a cargo del profesor
        $sqlAlumnos = $con->prepare("SELECT * FROM alumno WHERE codigoProfesor = :codigoProfesor");
        $sqlAlumnos->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
        $sqlAlumnos->execute();
        $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
  

    } catch (Exception $e) {
        error_log("Error al recuperar examenes en progreso por profesor: " . $e->getMessage());
    }
    return $alumnos;


  }

  function obtenerNombreProfesorPorCodigo($con, $codigoProfesor) {
    $stmt = $con->prepare("SELECT nombre, apellido FROM profesor WHERE codigoProfesor = :codigoProfesor");
    $stmt->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
    $stmt->execute();
    $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profesor) {
        // Concatena el nombre y el apellido con un espacio entre ellos
        return htmlspecialchars($profesor['nombre']) . " " . htmlspecialchars($profesor['apellido']);
    } else {
        return "Nombre no disponible";
    }
}

function mostrarExamenesEnProgresoPorProfesor($con, $codigoProfesor) {
    $nombreProfesor = obtenerNombreProfesorPorCodigo($con, $codigoProfesor);
    $examenesEnProgreso = obtenerExamenesEnProgresoPorProfesor($con, $codigoProfesor);

    echo "<section class='seccion-profesor'>";
    echo "<h2>Exámenes en progreso - Profesor: " . htmlspecialchars($nombreProfesor) . "</h2>";

    if (!empty($examenesEnProgreso)) {
        echo "<table class='tabla-docentes'>";
        echo "<tr>
                <th>Código Alumno</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Acciones</th>
              </tr>";

        foreach ($examenesEnProgreso as $examen) {
            $codigoAlumno = $examen['codigoAlumno'];
            $sqlAlumno = $con->prepare("SELECT nombre, apellido, correo FROM alumno WHERE codigoAlumno = :codigoAlumno");
            $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
            $sqlAlumno->execute();
            $datosAlumno = $sqlAlumno->fetch(PDO::FETCH_ASSOC);

            $nombre = $datosAlumno ? $datosAlumno['nombre'] : "No encontrado";
            $apellido = $datosAlumno ? $datosAlumno['apellido'] : "No encontrado";
            $correo = $datosAlumno ? $datosAlumno['correo'] : "No encontrado";

            echo "<tr>";

            echo "<td>" . htmlspecialchars($codigoAlumno) . "</td>";
            echo "<td>" . htmlspecialchars($nombre) . "</td>";
            echo "<td>" . htmlspecialchars($apellido) . "</td>";
            echo "<td>" . htmlspecialchars($correo) . "</td>";  
            echo "<td>";
            echo "<a href='verProgresoAlumno.php?codigoAlumno=" . urlencode($codigoAlumno) . "'>";
            echo '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-progress" width="34" height="34" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff9300" fill="none" stroke-linecap="round" stroke-linejoin="round">';
            echo '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
            echo '<path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" />';
            echo '<path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />';
            echo '<path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" />';
            echo '<path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />';
            echo '<path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" />';
            echo '</svg>';
            echo "</a>";
            echo "</td>";
            echo "</tr>";
            
            
        }

        echo "</table>";
    } else {
        echo "<p>No hay exámenes en progreso en este momento.</p>";
    }

    echo "</section>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../../build/css/directivos.css">
    <link rel="stylesheet" href="../../build/css/progresoAlumnosDirectivo.css">
    
</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
  
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../panelControlDirectivo.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="../panelControlDirectivo.php" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
    
  </div>
</nav>


<main>
  <div styles="margin 0 15px;"> <h1>Administrar progreso del Alumno </h1></div>



  <select id="selector">
    <option value="">Selecciona un profesor</option>
    <?php
    foreach ($docente as $profesor) {
        $codigoProfesor = $profesor['codigoProfesor'];
        $nombreProfesor = obtenerNombreProfesorPorCodigo($con, $codigoProfesor);
        echo "<option value='" . htmlspecialchars($codigoProfesor) . "'>" . htmlspecialchars($nombreProfesor) . "</option>";
    }
    ?>
</select>
   <?php

foreach ($docente as $profesor) {
  $codigoProfesor = htmlspecialchars($profesor['codigoProfesor']);
  echo "<div id='" . $codigoProfesor . "' class='contenidoProfesor' style='display:none;'>";
  // Suponiendo que mostrarExamenesEnProgresoPorProfesor devuelve el contenido HTML
  mostrarExamenesEnProgresoPorProfesor($con, $codigoProfesor);
  echo "</div>";
}


?>


</main>


<script>
  document.getElementById('selector').addEventListener('change', function() {
    // Ocultar todas las secciones de profesores
    document.querySelectorAll('.contenidoProfesor').forEach(function(elemento) {
        elemento.style.display = 'none';
    });

    // Obtener el ID de la sección a mostrar desde el valor del select
    var codigoProfesorSeleccionado = this.value;
    var seccionProfesor = document.getElementById(codigoProfesorSeleccionado);

    if (seccionProfesor) {
        // Mostrar la sección seleccionada
        seccionProfesor.style.display = 'block'; // o 'grid', 'flex', etc., dependiendo de tu diseño
    }
});

</script>



</body>
</html>