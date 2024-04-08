<?php
    session_start();
    if (!isset($_SESSION['Maestro'])) {
      header('Location:../loginMaestro.php'); // Redirigir al login si no está logueado
      exit;
  }
  
    require '../../../config/db.php';
   
    $db = new db();
    $con =$db->conexion();
   
    //Obtener el codigo del maestro en base a su correo
    $correoMaestro = $_SESSION['Maestro'];

    $sqlHelp = $con->prepare("SELECT codigoProfesor, nombre, apellido FROM profesor WHERE correo = :correoMaestro");
    $sqlHelp->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
    $sqlHelp->execute();
    $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
    $codigoMaestro = $resultadoaux[0]['codigoProfesor'];
    $nombreMaestro = $resultadoaux[0]['nombre'];
    $apellidoMaestro   = $resultadoaux[0]['apellido'];

    $codigoAlumno = isset($_GET['codigoAlumno']) ? $_GET['codigoAlumno'] : null;

    $sqlAlumno = $con->prepare("SELECT codigoProfesor, nombre, apellido, codigoAlumno, escuela, codigoEscuela  FROM alumno WHERE codigoalumno = :codigoAlumno");
    $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $sqlAlumno->execute();
    $alumno = $sqlAlumno->fetchAll(PDO::FETCH_ASSOC);
    
    $sqlresultados = $con->prepare("SELECT * FROM resultados_examen_olimpiada WHERE codigoAlumno = :codigoAlumno ORDER BY fecha ASC LIMIT 20");
    $sqlresultados->bindParam(':codigoAlumno',$codigoAlumno,PDO::PARAM_STR);
    $sqlresultados->execute();
    $resultadosExamen = $sqlresultados->fetchAll(PDO::FETCH_ASSOC);


// Inicializa sumadores y contadores
$sumaEspanol = $sumaMatematicas = $sumaCiencias = $sumaHistoria = $sumaGeografia = 0;
$contadorEspanol = $contadorMatematicas = $contadorCiencias = $contadorHistoria = $contadorGeografia = 0;

foreach ($resultadosExamen as $resultado) {
    if (isset($resultado['calificacionEspanol'])) {
        $sumaEspanol += $resultado['calificacionEspanol'];
        $contadorEspanol++;
    }
    if (isset($resultado['calificacionMatematicas'])) {
        $sumaMatematicas += $resultado['calificacionMatematicas'];
        $contadorMatematicas++;
    }
    if (isset($resultado['calificacionCiencias'])) {
        $sumaCiencias += $resultado['calificacionCiencias'];
        $contadorCiencias++;
    }
    if (isset($resultado['calificacionHistoria'])) {
        $sumaHistoria += $resultado['calificacionHistoria'];
        $contadorHistoria++;
    }
    if (isset($resultado['calificacionGeografia'])) {
        $sumaGeografia += $resultado['calificacionGeografia'];
        $contadorGeografia++;
    }
}
$promedioEspanol = $contadorEspanol > 0 ? $sumaEspanol / $contadorEspanol : 0;
$promedioMatematicas = $contadorMatematicas > 0 ? $sumaMatematicas / $contadorMatematicas : 0;
$promedioCiencias = $contadorCiencias > 0 ? $sumaCiencias / $contadorCiencias : 0;
$promedioHistoria = $contadorHistoria > 0 ? $sumaHistoria / $contadorHistoria : 0;
$promedioGeografia = $contadorGeografia > 0 ? $sumaGeografia / $contadorGeografia : 0;


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

    <link rel="stylesheet" href="../../../build/css/directivos.css">
    <link rel="stylesheet" href="../../../build/css/progresoAlumnos.css">

    <style>
    .correcta {
    color: green; /* Color verde para respuestas correctas */
}

.incorrecta {
    color: red; /* Color rojo para respuestas incorrectas */
}

.correcta:before {
    content: '\2714'; /* Símbolo de marca de verificación para respuestas correctas */
    margin-right: 5px;
}

.incorrecta:before {
    content: '\2718'; /* Símbolo de cruz para respuestas incorrectas */
    margin-right: 5px;
}
/* Estilo para el modal */
.modal {
    display: none; /* Ocultar el modal por defecto */
    position: fixed; /* Posición fija */
    z-index: 1; /* Hacer que el modal esté por encima de otros elementos */
    left: 0;
    top: 0;
    width: 100%; /* Ancho del modal */
    height: 100%; /* Altura del modal */
    overflow: auto; /* Permite hacer scroll si el contenido es demasiado grande */
    background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro semi-transparente */
}

/* Estilo para el contenido del modal */
.modal-content {
    top:0;
    background-color: #fefefe; /* Fondo blanco */
    margin: 2% auto; /* Centrar verticalmente el modal */
    padding: 20px;
    border: 1px solid #888;
    width: 60%; /* Ancho del contenido del modal */
}

/* Estilo para el botón de cerrar */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}


</style>

</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../../panelControlMaestro.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="administrarAlumnosOlimpiada.php" style="padding: 1rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
    </div>
  </div>
</nav>


<main>



  <?php foreach($alumno as $row){ ?>
    <div class="info-group">
      <span style ="margin:auto; font-size:2rem"class="value">Alumno(a):<?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></span>
    </div>
    <div class="info-group">
      <span style ="margin:auto; font-size:2rem"  class="value">Codigo alumno:<?php echo htmlspecialchars($row['codigoAlumno']); ?></span>
    </div>
    <div class="info-group">
      <span style ="margin:auto; font-size:2rem" class="value">Escuela:<?php echo htmlspecialchars($row['escuela']); ?></span>
    </div>
  <?php } ?>

  <select id="selector">
    <option value="rendimientoGeneral">Rendimiento General</option>
    <option value="desempenoEspanol">Desempeño en Español</option>
    <option value="desempenoMatematicas">Desempeño en Matemáticas</option>
    <option value="desempenoCiencias">Desempeño en Ciencias</option>
    <option value="desempenoHistoria">Desempeño en Historia</option>
    <option value="desempenoGeografia">Desempeño en Geografía</option>
    <option value="comparacionTiempo">Comparación de Desempeño a lo Largo del Tiempo</option>

</select>
    <a href="../../../generarPDF/pdfAlumnoOlimpiada.php?codigoAlumno=<?php echo urlencode($codigoAlumno); ?>" target="_blank" class="boton-descarga">Descargar PDF</a>

    



 
    <div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    
  </div>
</div>
<div  class="examSection" id="detallesAlumno"></div>

<div id="rendimientoGeneral" class="contenido" >
<!-- Rendimiento General -->
<div class="descripcion" id="rendimientoGeneral">
    <p>Esta sección crucial brinda un panorama exhaustivo del desempeño académico de los estudiantes, compilando datos de diversas materias para ofrecer una visión integral del aprendizaje y el progreso. A través de un análisis meticuloso, se destacan tanto las fortalezas como las áreas que requieren atención, permitiendo a educadores y padres diseñar estrategias específicas de apoyo y enriquecimiento. Al evaluar el rendimiento general, se busca no solo reconocer el éxito académico, sino también identificar oportunidades para fomentar un desarrollo equilibrado y completo en todos los ámbitos del saber.</p>
</div>
<div class="contenedorTabla">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación Español</th>
            <th>Calificación Matemáticas</th>
            <th>Calificación Ciencias</th>
            <th>Calificación Historia</th>
            <th>Calificación Geografía</th>
            <th>Calificacion total</th>
            <th>Ver examen</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
            <tr class="<?php echo ($resultado['puntaje_general'] < 6) ? 'puntaje-bajo' : ''; ?>">
                <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
                <td><?php echo htmlspecialchars($resultado['total_preguntas']); ?></td>
                <td><?php echo htmlspecialchars($resultado['correctas_general']); ?></td>
                <td><?php echo htmlspecialchars($resultado['calificacionEspanol']); ?></td>
                <td><?php echo htmlspecialchars($resultado['calificacionMatematicas']); ?></td>
                <td><?php echo htmlspecialchars($resultado['calificacionCiencias']); ?></td>
                <td><?php echo htmlspecialchars($resultado['calificacionHistoria']); ?></td>
                <td><?php echo htmlspecialchars($resultado['calificacionGeografia']); ?></td>
                <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
                <td>
                <a href="#" class="exam-btn" data-id="<?php echo $resultado['id']; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-notebook" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1m3 0v18" />
            <path d="M13 8l2 0" />
            <path d="M13 12l2 0" />
        </svg>
    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

            <div>        
                <div class="containerGrafica ">
                    <canvas id="histogramaProgreso" width="800" height="400"></canvas>
                </div>

                <div class="containerGrafica ">
                    <canvas id="progresoHistoricoCompleto" width="800" height="400"></canvas>
                </div>
            </div>
            



</div>


<div id="desempenoEspanol" class="contenido " style="display:none;">
<!-- Desempeño en Español -->
<div class="descripcion" id="desempenoEspanol">
    <p>La sección de Español se enfoca en evaluar la habilidad de los estudiantes para interactuar con el lenguaje en sus múltiples facetas: desde la comprensión de textos complejos hasta la capacidad de expresar ideas de manera clara y creativa. Al adentrarse en la literatura, los estudiantes no solo desarrollan aprecio por la riqueza lingüística, sino que también se fomenta su capacidad de empatía y análisis crítico. Esta evaluación sirve como una herramienta esencial para reforzar las bases comunicativas y promover una exploración profunda de la cultura y la identidad a través de las palabras.</p>
</div>
<div class="grid2columnas">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>

            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación Español</th>
            <th>Calificacion</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['totalPreguntasEspanol']); ?></td>
            <td><?php echo htmlspecialchars($resultado['correctasEspanol']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionEspanol']); ?></td>
            <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


            <div>        
                <div class="containerGrafica ">
                <canvas id="calificacionesLineChart"></canvas>
                </div>

                <div class="containerGrafica ">
                <canvas id="preguntasBarChart"></canvas>

                </div>
            </div>
            
</div>
</div>



<div id="desempenoMatematicas" class="contenido " style="display:none;">
<!-- Desempeño en Matemáticas -->
<div class="descripcion" id="desempenoMatematicas">
    <p>El análisis del desempeño en Matemáticas ofrece una visión detallada de cómo los estudiantes comprenden y aplican conceptos matemáticos fundamentales, abarcando desde la aritmética básica hasta aspectos más complejos como el álgebra y la geometría. Este enfoque no solo valora la precisión en la resolución de problemas, sino que también pone a prueba la habilidad de los estudiantes para emplear el razonamiento lógico y el pensamiento crítico en situaciones prácticas y teóricas. Al fomentar estas competencias, se prepara a los estudiantes para enfrentar desafíos futuros en campos científicos, tecnológicos y cotidianos con confianza y creatividad.</p>
</div>
<div class="grid2columnas">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Matemáticas</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionMatematicas']); ?></td>
            <td><?php echo htmlspecialchars($resultado['totalPreguntasMatematicas']); ?></td>
            <td><?php echo htmlspecialchars($resultado['correctasMatematicas']); ?></td>
            <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>        
                <div class="containerGrafica ">
                <canvas id="calificacionesMatematicasLineChart"></canvas>
                </div>

                <div class="containerGrafica ">
                <canvas id="preguntasMatematicasBarChart"></canvas>

                </div>
            </div>

</div>
</div>



<div id="desempenoCiencias" class="contenido " style="display:none;">
<!-- Desempeño en Ciencias -->
<div class="descripcion" id="desempenoCiencias">
    <p>La evaluación en Ciencias se centra en medir el grado de comprensión y aplicación de principios científicos en Biología, Química y Física, destacando la importancia del método científico como herramienta de investigación. Al sumergir a los estudiantes en el estudio de los fenómenos naturales, se busca cultivar una curiosidad intrínseca por el mundo que les rodea, así como la capacidad de formular hipótesis, experimentar y analizar resultados. Este enfoque integral promueve no solo el conocimiento científico, sino también el desarrollo de una actitud crítica y reflexiva ante los retos medioambientales y tecnológicos de nuestro tiempo.</p>
</div>
<div class="grid2columnas">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Ciencias</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionCiencias']); ?></td>
            <td><?php echo htmlspecialchars($resultado['totalPreguntasCiencias']); ?></td>
            <td><?php echo htmlspecialchars($resultado['correctasCiencias']); ?></td>
            <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>        
    <div class="containerGrafica">
        <canvas id="calificacionesCienciasLineChart"></canvas>
    </div>

    <div class="containerGrafica">
        <canvas id="preguntasCienciasBarChart"></canvas>
    </div>
</div>

</div>
</div>



<div id="desempenoHistoria" class="contenido" style="display:none;">
<!-- Desempeño en Historia -->
<div class="descripcion" id="desempenoHistoria">
    <p>La sección de Historia invita a los estudiantes a embarcarse en un viaje a través del tiempo, explorando eventos, culturas y personajes que han modelado el mundo actual. Más allá de la memorización de fechas y hechos, se enfatiza en el análisis de causas y consecuencias, fomentando una comprensión profunda de los procesos históricos y su relevancia contemporánea. Esta perspectiva crítica anima a los estudiantes a reflexionar sobre su papel como ciudadanos globales y a valorar la diversidad cultural, política y social, preparándolos para contribuir de manera informada y responsable a la sociedad.</p>
</div>
<div class="grid2columnas">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Historia</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionHistoria']); ?></td>
            <td><?php echo htmlspecialchars($resultado['totalPreguntasHistoria']); ?></td>
            <td><?php echo htmlspecialchars($resultado['correctasHistoria']); ?></td>
            <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>        
    <div class="containerGrafica">
        <canvas id="calificacionesHistoriaLineChart"></canvas>
    </div>

    <div class="containerGrafica">
        <canvas id="preguntasHistoriaBarChart"></canvas>
    </div>
</div>


</div>
</div>


<div id="desempenoGeografia" class="contenido" style="display:none;">
<!-- Desempeño en Geografía -->
<div class="descripcion" id="desempenoGeografia">
    <p>En Geografía, los estudiantes exploran la complejidad de la Tierra y sus múltiples dimensiones, desde la diversidad física y biológica hasta las intrincadas relaciones entre los seres humanos y su entorno. Esta materia amplía horizontes, promoviendo una comprensión holística de temas como el cambio climático, la urbanización y la gestión de recursos naturales. Al equipar a los estudiantes con el conocimiento para navegar por estos temas complejos, se busca incentivar un compromiso activo y responsable con el planeta, preparándolos para liderar esfuerzos sostenibles y equitativos en el futuro.</p>
</div>
<div class="grid2columnas">
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Geografía</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionGeografia']); ?></td>
            <td><?php echo htmlspecialchars($resultado['totalPreguntasGeografia']); ?></td>
            <td><?php echo htmlspecialchars($resultado['correctasGeografia']); ?></td>
            <td class="calificacion"><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>        
    <div class="containerGrafica">
        <canvas id="calificacionesGeografiaLineChart"></canvas>
    </div>

    <div class="containerGrafica">
        <canvas id="preguntasGeografiaBarChart"></canvas>
    </div>
</div>


</div>
</div>




<div id="comparacionTiempo" class="contenido" style="display:none;">
<!-- Comparación de Desempeño a lo Largo del Tiempo -->
<div class="descripcion" id="comparacionTiempo">
    <p>Esta análisis comparativo permite visualizar la evolución del aprendizaje y el rendimiento académico de los estudiantes a lo largo del tiempo, proporcionando insights valiosos sobre la efectividad de estrategias pedagógicas y programas de estudio. Al identificar tendencias y patrones en el progreso educativo, educadores y padres pueden tomar decisiones informadas para apoyar el desarrollo integral de cada estudiante. Este enfoque longitudinal no solo celebra los logros acumulados, sino que también destaca oportunidades para intervenciones focalizadas y personalizadas, asegurando que todos los estudiantes puedan alcanzar su máximo potencial.</p>
</div>
<table class="tabla-docentes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Español</th>
            <th>Matemáticas</th>
            <th>Ciencias</th>
            <th>Historia</th>
            <th>Geografía</th>
            <th>Puntaje General</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($resultadosExamen as $resultado): ?>
        <tr>
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionEspanol']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionMatematicas']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionCiencias']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionHistoria']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionGeografia']); ?></td>
            <td><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>



</div>










</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script>


document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('histogramaProgreso').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line', // Tipo de gráfico
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Eje X - Fechas de exámenes
            datasets: [{
                label: 'Progreso de Calificaciones Totales',
                backgroundColor: 'rgb(255, 99, 132)', // Color de fondo
                borderColor: 'rgb(255, 99, 132)', // Color del borde
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>], // Eje Y - Calificaciones
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true, 
                    max:10,
                    title: {
                        display: true,
                        text: 'Calificación Total'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fecha del Examen'
                    }
                }
            }
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('progresoHistoricoCompleto').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line', // Usamos un gráfico de línea para comparar las materias a lo largo del tiempo
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [
                {
                    label: 'Español',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionEspanol'] . ','; } ?>],
                    fill: false,
                },
                {
                    label: 'Matemáticas',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionMatematicas'] . ','; } ?>],
                    fill: false,
                },
                {
                    label: 'Ciencias',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionCiencias'] . ','; } ?>],
                    fill: false,
                },
                {
                    label: 'Historia',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionHistoria'] . ','; } ?>],
                    fill: false,
                },
                {
                    label: 'Geografía',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionGeografia'] . ','; } ?>],
                    fill: false,
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10, // Inicia el eje Y desde 0
                    title: {
                        display: true,
                        text: 'Calificación'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fecha del Examen'
                    }
                }
            },
            responsive: true, // Hace que el gráfico sea responsivo
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('calificacionesLineChart').getContext('2d');
    var calificacionesLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Calificación Español',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionEspanol'] . ','; } ?>],
                fill: false,
            }, {
                label: 'Calificación Total',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>],
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10,
                }
            }
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('preguntasBarChart').getContext('2d');
    var preguntasBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Preguntas Totales',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['totalPreguntasEspanol'] . ','; } ?>]
            }, {
                label: 'Preguntas Correctas',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['correctasEspanol'] . ','; } ?>]
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('calificacionesMatematicasLineChart').getContext('2d');
    var calificacionesMatematicasLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Calificación Matemáticas',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionMatematicas'] . ','; } ?>],
                fill: false,
            }, {
                label: 'Calificación Total',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>],
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10,
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('preguntasMatematicasBarChart').getContext('2d');
    var preguntasMatematicasBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Preguntas Totales Matemáticas',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['totalPreguntasMatematicas'] . ','; } ?>] // Asumiendo que tienes esta data específica para Matemáticas
            }, {
                label: 'Preguntas Correctas Matemáticas',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['correctasMatematicas'] . ','; } ?>] // Asumiendo que puedes filtrar las correctas de Matemáticas
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
});

document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de líneas para Calificaciones de Ciencias y Calificación Total
    var ctxLine = document.getElementById('calificacionesCienciasLineChart').getContext('2d');
    var calificacionesCienciasLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Calificación Ciencias',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionCiencias'] . ','; } ?>],
                fill: false,
            }, {
                label: 'Calificación Total',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>],
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10,
                }
            }
        }
    });

    // Gráfico de barras para Preguntas Totales vs. Preguntas Correctas en Ciencias
    var ctxBar = document.getElementById('preguntasCienciasBarChart').getContext('2d');
    var preguntasCienciasBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Preguntas Totales',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['totalPreguntasCiencias'] . ','; } ?>]
            }, {
                label: 'Preguntas Correctas',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['correctasCiencias'] . ','; } ?>] // Aquí necesitarás ajustar según cómo determines las preguntas correctas de Ciencias
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
});

document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de líneas para Calificaciones de Geografía y Calificación Total
    var ctxLine = document.getElementById('calificacionesGeografiaLineChart').getContext('2d');
    var calificacionesGeografiaLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Calificación Geografía',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionGeografia'] . ','; } ?>],
                fill: false,
            }, {
                label: 'Calificación Total',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>],
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10,
                }
            }
        }
    });

    // Gráfico de barras para Preguntas Totales vs. Preguntas Correctas en Geografía
    var ctxBar = document.getElementById('preguntasGeografiaBarChart').getContext('2d');
    var preguntasGeografiaBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Preguntas Totales',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['totalPreguntasGeografia'] . ','; } ?>]
            }, {
                label: 'Preguntas Correctas',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['correctasGeografia'] . ','; } ?>] // Ajusta según cómo determines las preguntas correctas de Geografía
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
});
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de líneas para Calificaciones de Historia y Calificación Total
    var ctxLine = document.getElementById('calificacionesHistoriaLineChart').getContext('2d');
    var calificacionesHistoriaLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Calificación Historia',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionHistoria'] . ','; } ?>],
                fill: false,
            }, {
                label: 'Calificación Total',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>],
                fill: false,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max:10,
                }
            }
        }
    });

    // Gráfico de barras para Preguntas Totales vs. Preguntas Correctas en Historia
    var ctxBar = document.getElementById('preguntasHistoriaBarChart').getContext('2d');
    var preguntasHistoriaBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>],
            datasets: [{
                label: 'Preguntas Totales',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['totalPreguntasHistoria'] . ','; } ?>]
            }, {
                label: 'Preguntas Correctas',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['correctasHistoria'] . ','; } ?>] // Necesitarás ajustar según cómo determinas las preguntas correctas de Historia
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
$(document).ready(function() {
    // Tu código aquí

    $(document).on('click', '.exam-btn', function(event) {
        event.preventDefault(); // Evita que el enlace ejecute su comportamiento predeterminado (recargar la página)
        var codigoExamen = $(this).data('id');
        console.log('ID del alumno:', codigoExamen);
        $.ajax({
            url: 'mostrarExamenOlimpiada.php',
            method: 'GET',
            data: {codigoExamen: codigoExamen},
            success: function(data) {
                console.log('Todo ok')
                // Asigna el contenido al modal en lugar de #detallesAlumno
                $('.modal-content').html(data);
                // Abre el modal después de cargar el contenido del alumno
                $('#myModal').show(); // Muestra el modal
            }
        });
    });

    // Cierra el modal cuando se hace clic en el botón de cerrar
    $('.close').click(function() {
        $('#myModal').hide(); // Oculta el modal
    });

    // Cierra el modal cuando se hace clic fuera del modal
    $(window).click(function(event) {
        if (event.target == $('#myModal')[0]) {
            $('#myModal').hide(); // Oculta el modal
        }
    });
});
</script>



</body>
</html>