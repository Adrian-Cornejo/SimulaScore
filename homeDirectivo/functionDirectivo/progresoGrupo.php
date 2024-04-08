<?php
session_start();
if (!isset($_SESSION['correo'])) {
    header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
    exit;
}
require '../../config/db.php';
   
$db = new db();
$con =$db->conexion();

$codigoMaestro = $_GET['codigoMaestro'];



    // Ahora, obtenemos los códigos de los alumnos a cargo de este maestro
    $sqlAlumnos = $con->prepare("SELECT codigoAlumno, apellido, nombre FROM alumno WHERE codigoProfesor = :codigoMaestro ORDER BY apellido, nombre");
    $sqlAlumnos->bindParam(':codigoMaestro', $codigoMaestro, PDO::PARAM_STR);
    $sqlAlumnos->execute();
    $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);




// Suponiendo que $resultados es tu array de códigos de alumnos
$codigosAlumnos = array_map(function($item) {
    return $item['codigoAlumno'];
}, $alumnos);

// Crear una cadena delimitada por comas para usar en la consulta SQL
$codigosParaSql = "'" . implode("','", $codigosAlumnos) . "'";
$sqlResultadosExamen = "
    SELECT * 
    FROM resultados_examen_mejoredu 
    WHERE codigoAlumno IN ($codigosParaSql)
    ORDER BY fecha ASC";
// Ejecutar la consulta
// Asumiendo $con es tu conexión PDO y $codigosParaSql ya está definido
$resultadosExamen = $con->query($sqlResultadosExamen)->fetchAll(PDO::FETCH_ASSOC);

$alumnosDatos = []; // Array para almacenar los datos finales

// Iterar sobre los resultados de los exámenes
foreach ($resultadosExamen as $resultado) {
    $codigoAlumno = $resultado['codigoAlumno'];
     $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
                $nombre =$alumnos[0]['nombre'];
                $apellido =$alumnos[0]['apellido'];
    // Inicializar el alumno en el array si no existe
    if (!isset($alumnosDatos[$codigoAlumno])) {
        $alumnosDatos[$codigoAlumno] = [
            'codigoAlumno' => $codigoAlumno,
            // Supongamos que puedes obtener nombre y apellido de alguna forma, quizás otra consulta o un array existente
            'nombre' => $nombre, // Deberás rellenar estos campos
            'apellido' => $apellido, // Deberás rellenar estos campos
            'sumaPuntajes' => 0,
            'sumaCalificacionesEspanol' => 0,
            'sumaCalificacionesMatematicas' => 0,
            'sumaCalificacionesFCE' => 0,
            'intentos' => 0
        ];
    }
    
    // Sumar los puntajes y calificaciones
    $alumnosDatos[$codigoAlumno]['sumaPuntajes'] += $resultado['puntaje_general'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesEspanol'] += $resultado['calificacionEspanol'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesMatematicas'] += $resultado['calificacionMatematicas'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesFCE'] += $resultado['calificacionFce'];
    $alumnosDatos[$codigoAlumno]['intentos']++;
}

// Calcular promedios
foreach ($alumnosDatos as $codigo => $datos) {
    $alumnosDatos[$codigo]['promedioGeneral'] = $datos['intentos'] > 0 ? $datos['sumaPuntajes'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioEspanol'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesEspanol'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioMatematicas'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesMatematicas'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioFCE'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesFCE'] / $datos['intentos'] : 0;
    
    // Eliminar sumas, ya no son necesarias
    unset($alumnosDatos[$codigo]['sumaPuntajes'], $alumnosDatos[$codigo]['sumaCalificacionesEspanol'], $alumnosDatos[$codigo]['sumaCalificacionesMatematicas'], $alumnosDatos[$codigo]['sumaCalificacionesFCE']);
}


    $sqlUltimoIntentoPorAlumno = "
        SELECT re.*
        FROM resultados_examen_mejoredu re
        INNER JOIN (
            SELECT codigoAlumno, MAX(id) AS UltimoID
            FROM resultados_examen_mejoredu
            WHERE codigoAlumno IN ($codigosParaSql) AND fecha IN (
                SELECT MAX(fecha)
                FROM resultados_examen_mejoredu
                WHERE codigoAlumno IN ($codigosParaSql)
                GROUP BY codigoAlumno
            )
            GROUP BY codigoAlumno
        ) ultimoIntento ON re.codigoAlumno = ultimoIntento.codigoAlumno AND re.id = ultimoIntento.UltimoID
    ";

    // Ejecutar la consulta
    // Asumiendo $con es tu conexión PDO y $codigosParaSql ya está definido
$resultadosMayorPorAlumno = $con->query($sqlUltimoIntentoPorAlumno)->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>ProgresoAlumno</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../../build/css/directivos.css">
    <link rel="stylesheet" href="../../build/css/progresoAlumnos.css">

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
      <a href="administrarDocentes.php" style="padding: 1rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
    </div>
</nav>



<main>
<h2>Progreso de los Alumnos</h2>
<select id="selector">
        <option value="clasificacionAlumnos">Clasificacion de Alumnos</option>
        <option value="resultadosUltimo">Resultados del ultimo intento</option>
        <option value="resultadosGeneral">Resultados en promedio general</option>
        <option value="resultadosMaterias">Resultados por materias</option>
        <option value="resultadosEspanol">Resultados español</option>
        <option value="resultadosMatematicas">Resultados matematicas</option>
        <option value="resultadosFce">Resultados FCE</option>
        <option value="resultadosMateriasGeneral">Resultados por materias general</option>
        
    </select>
    <a href="../../generarPDF/pdfGrupoDirectivo.php?codigoProfesor=<?php echo urlencode($codigoMaestro); ?>" target="_blank" class="boton-descarga">Descargar PDF</a>



<div id="clasificacionAlumnos" class="contenido" >
 <h3>Clasificacion de alumno</h3>
<table class="tabla-docentes">
        <thead>
            <tr>
                <th>Código Alumno</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Puntaje General</th>
                <th>Calificación Español</th>
                <th>Calificación Matemáticas</th>
                <th>Calificación FCE</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultadosMayorPorAlumno as $resultado): ?>
                <tr>
                    <td><?= htmlspecialchars($resultado['codigoAlumno']) ?></td>
                    <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $resultado['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>

                    <td><?= htmlspecialchars($resultado['puntaje_general']) ?></td>
                    <td><?= htmlspecialchars($resultado['calificacionEspanol']) ?></td>
                    <td><?= htmlspecialchars($resultado['calificacionMatematicas']) ?></td>
                    <td><?= htmlspecialchars($resultado['calificacionFce']) ?></td>
                    <td><?= htmlspecialchars($resultado['fecha']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>        
    <div class="containerGrafica ">
    <canvas id="puntajeGeneralChart" width="400" height="200"></canvas>
</div>
</div>

    
</div>


<div id="resultadosUltimo" class="contenido " style="display:none;">
<?php
// Ordenar resultados de mayor a menor según el puntaje general
usort($resultadosMayorPorAlumno, function($a, $b) {
    return $b['puntaje_general'] - $a['puntaje_general'];
});

$labels = [];
$data = [];

foreach ($resultadosMayorPorAlumno as $resultado) {
    $labels[] = $resultado['codigoAlumno']; // O puedes usar nombre y apellido
    $data[] = $resultado['puntaje_general'];
}

// Convertir los arrays PHP a JSON para su uso en JavaScript
$labelsJson = json_encode($labels);
$dataJson = json_encode($data);

?>

<h3>Resultados del ultimo intento</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Español</th>
            <th>Calificación Matemáticas</th>
            <th>Calificación FCE</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultadosMayorPorAlumno as $resultado): ?>
            <tr>
                <td><?= htmlspecialchars($resultado['codigoAlumno']) ?></td>
                <?php
                    $sqlAlumnos = $con->prepare("SELECT apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                    $sqlAlumnos->bindParam(':codigoAlumno', $resultado['codigoAlumno'], PDO::PARAM_STR);
                    $sqlAlumnos->execute();
                    $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <td><?= htmlspecialchars($alumnos[0]['nombre']) ?></td>
                <td><?= htmlspecialchars($alumnos[0]['apellido']) ?></td>
                <td><?= htmlspecialchars($resultado['puntaje_general']) ?></td>
                <td><?= htmlspecialchars($resultado['calificacionEspanol']) ?></td>
                <td><?= htmlspecialchars($resultado['calificacionMatematicas']) ?></td>
                <td><?= htmlspecialchars($resultado['calificacionFce']) ?></td>
                <td><?= htmlspecialchars($resultado['fecha']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>


<div id="resultadosGeneral" class="contenido " style="display:none;">
<h3>Resultados en promedio general</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Intentos</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                <td><?php echo number_format($alumno['promedioGeneral'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div>        
    <div class="containerGrafica ">
    <canvas id="graficaPromedioGeneral" width="400" height="200"></canvas>
</div>
</div>
</div>


<div id="resultadosMaterias" class="contenido " style="display:none;">
<h3>Resultados por materia</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Intentos</th>
            <th>Promedio Español</th>
            <th>Promedio Matemáticas</th>
            <th>Promedio FCE</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                
                <td><?php echo number_format($alumno['promedioEspanol'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioMatematicas'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioFCE'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div>        
    <div class="containerGrafica ">
    <canvas id="graficaMaterias" width="400" height="200"></canvas>
</div>
</div>

</div>


<div id="resultadosEspanol" class="contenido " style="display:none;">
<h3>Resultados español</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Intentos</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio Español</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo number_format($alumno['promedioEspanol'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioGeneral'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>        
    <div class="containerGrafica ">
    <canvas id="graficaEspanol" width="400" height="200"></canvas>
</div>
</div>
</div>


<div id="resultadosMatematicas" class="contenido " style="display:none;">
<h3>Resultados matematicas</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Intentos</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio Matemáticas</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo number_format($alumno['promedioMatematicas'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioGeneral'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div>        
    <div class="containerGrafica ">
    <canvas id="graficaMatematicas" width="400" height="200"></canvas>
</div>
</div>
</div>


<div id="resultadosFce" class="contenido " style="display:none;">
<h3>Resultados FCE</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Intentos</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio FCE</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo number_format($alumno['promedioFCE'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioGeneral'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div>        
    <div class="containerGrafica ">
    <canvas id="graficaFCE" width="400" height="200"></canvas>
</div>
</div>
</div>


<div id="resultadosMateriasGeneral" class="contenido " style="display:none;">
<h3>Resultados por materia general</h3>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Intentos</th>
            <th>Promedio Español</th>
            <th>Promedio Matemáticas</th>
            <th>Promedio FCE</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alumnosDatos as $alumno): ?>
            <tr>
                <td><?php echo htmlspecialchars($alumno['codigoAlumno']); ?></td>
                <?php
                $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
                $sqlAlumnos->bindParam(':codigoAlumno', $alumno['codigoAlumno'], PDO::PARAM_STR);
                $sqlAlumnos->execute();
                $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);

                
                ?>
                <td><?php echo htmlspecialchars($alumnos[0]['nombre']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumnos[0]['apellido']); // Asegúrate de tener este dato disponible ?></td>
                <td><?php echo htmlspecialchars($alumno['intentos']); ?></td>
                
                <td><?php echo number_format($alumno['promedioEspanol'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioMatematicas'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioFCE'], 2); ?></td>
                <td><?php echo number_format($alumno['promedioGeneral'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div>        
    <div class="containerGrafica ">
    <canvas id="rendimientoGrupo"></canvas>
</div>
</div>
</div>






<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</main>

<script>
// Suponiendo que $alumnosDatos es tu array de PHP con los datos procesados
var datosAlumnos = <?php echo json_encode(array_values($alumnosDatos)); ?>;
var etiquetas = datosAlumnos.map(function(alumno) { return alumno.nombre; }); // O "nombre" si está disponible
var promediosGenerales = datosAlumnos.map(alumno => alumno.promedioGeneral);

// Crear la gráfica de barras
var ctx = document.getElementById('graficaPromedioGeneral').getContext('2d');
var graficaPromedioGeneral = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Promedio General por Alumno',
            data: promediosGenerales,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 10
            }
        }
    }
});

var datosAlumnos = <?php echo json_encode(array_values($alumnosDatos)); ?>;
var etiquetas = datosAlumnos.map(alumno => alumno.codigoAlumno); // Usa el código del alumno o su nombre si está disponible

var promediosEspanol = datosAlumnos.map(alumno => alumno.promedioEspanol);
var promediosMatematicas = datosAlumnos.map(alumno => alumno.promedioMatematicas);
var promediosFCE = datosAlumnos.map(alumno => alumno.promedioFCE);

var ctx = document.getElementById('graficaMaterias').getContext('2d');
var graficaMaterias = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Español',
            data: promediosEspanol,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        }, {
            label: 'Matemáticas',
            data: promediosMatematicas,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'FCE',
            data: promediosFCE,
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 10
            }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});

var datosAlumnos = <?php echo json_encode(array_values($alumnosDatos)); ?>;
var etiquetas = datosAlumnos.map(alumno => alumno.codigoAlumno);

// Promedios de Español
var promediosEspanol = datosAlumnos.map(alumno => alumno.promedioEspanol);
var ctxEspanol = document.getElementById('graficaEspanol').getContext('2d');
var graficaEspanol = new Chart(ctxEspanol, {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Español',
            data: promediosEspanol,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 10
            }
        }
    }
});

// Promedios de Matemáticas
var promediosMatematicas = datosAlumnos.map(alumno => alumno.promedioMatematicas);
var ctxMatematicas = document.getElementById('graficaMatematicas').getContext('2d');
var graficaMatematicas = new Chart(ctxMatematicas, {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Matemáticas',
            data: promediosMatematicas,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 10
            }
        }
    }
});

// Promedios de FCE
var promediosFCE = datosAlumnos.map(alumno => alumno.promedioFCE);
var ctxFCE = document.getElementById('graficaFCE').getContext('2d');
var graficaFCE = new Chart(ctxFCE, {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'FCE',
            data: promediosFCE,
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 10
            }
        }
    }
});
var alumnosDatos = <?php echo json_encode(array_values($alumnosDatos)); ?>;
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('rendimientoGrupo').getContext('2d');
    var nombresAlumnos = alumnosDatos.map(function(alumno) {
        return alumno.nombre + ' ' + alumno.apellido;
    });
    var promedioGeneral = alumnosDatos.map(function(alumno) {
        return alumno.promedioGeneral;
    });
    var promedioEspanol = alumnosDatos.map(function(alumno) {
        return alumno.promedioEspanol;
    });
    var promedioMatematicas = alumnosDatos.map(function(alumno) {
        return alumno.promedioMatematicas;
    });
    var promedioFCE = alumnosDatos.map(function(alumno) {
        return alumno.promedioFCE;
    });

    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: nombresAlumnos,
            datasets: [{
                label: 'Promedio General',
                data: promedioGeneral,
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false,
            }, {
                label: 'Español',
                data: promedioEspanol,
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false,
            }, {
                label: 'Matemáticas',
                data: promedioMatematicas,
                borderColor: 'rgba(255, 206, 86, 1)',
                fill: false,
            }, {
                label: 'FCE',
                data: promedioFCE,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 10 
                }
            }
        }
    });
});

var ctx = document.getElementById('puntajeGeneralChart').getContext('2d');
var puntajeGeneralChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo $labelsJson; ?>,
        datasets: [{
            label: 'Puntaje General',
            data: <?php echo $dataJson; ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
document.getElementById('selector').addEventListener('change', function() {
    // Ocultar todas las secciones
    document.querySelectorAll('.contenido').forEach(function(elemento) {
        elemento.style.display = 'none';
    });

    // Mostrar la sección seleccionada
    var seleccionado = document.getElementById(this.value);
    if (seleccionado) {
        // Aquí se asegura de mantener el estilo de 'grid2columnas' aplicado al mostrar
        seleccionado.style.display = 'grid';
    }
});

</script>

</body>
</html>