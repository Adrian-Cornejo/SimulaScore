<?php
    session_start();
    if (!isset($_SESSION['correo'])) {
      header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
      exit;
  }
  
    require '../../config/db.php';
   
    $db = new db();
    $con =$db->conexion();
   
 

    $codigoAlumno = isset($_GET['codigoAlumno']) ? $_GET['codigoAlumno'] : null;

    $sqlAlumno = $con->prepare("SELECT codigoProfesor, nombre, apellido, codigoAlumno, escuela, codigoEscuela  FROM alumno WHERE codigoalumno = :codigoAlumno");
    $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $sqlAlumno->execute();
    $alumno = $sqlAlumno->fetchAll(PDO::FETCH_ASSOC);
    
    $sqlresultados = $con->prepare("SELECT * FROM resultados_examen_mejoredu WHERE codigoAlumno = :codigoAlumno ORDER BY fecha ASC LIMIT 20");
    $sqlresultados->bindParam(':codigoAlumno',$codigoAlumno,PDO::PARAM_STR);
    $sqlresultados->execute();
    $resultadosExamen = $sqlresultados->fetchAll(PDO::FETCH_ASSOC);

    // Inicializa sumadores
$sumaEspanol = 0;
$sumaComprension = 0;
$sumaMatematicas =0;
$sumaFracciones=0;
$sumaFCE=0;

// Cuenta el número de intentos obtenidos
$numeroDeIntentos = count($resultadosExamen);

foreach ($resultadosExamen as $resultado) {
    $sumaEspanol += $resultado['puntaje_espanol'];
    $sumaComprension += $resultado['puntaje_comprension'];
    $sumaMatematicas += $resultado['puntaje_matematicas'];
    $sumaFracciones += $resultado['puntaje_fracciones'];
    $sumaFCE += $resultado['puntaje_fce'];
}

// Calcula los promedios
$promedioEspanol = $numeroDeIntentos > 0 ? $sumaEspanol / $numeroDeIntentos : 0;
$promedioComprension = $numeroDeIntentos > 0 ? $sumaComprension / $numeroDeIntentos : 0;
$promedioMatematicas = $numeroDeIntentos > 0 ? $sumaMatematicas / $numeroDeIntentos : 0;
$promedioFracciones = $numeroDeIntentos > 0 ? $sumaFracciones / $numeroDeIntentos : 0;
$promedioFCE= $numeroDeIntentos > 0 ? $sumaFCE / $numeroDeIntentos : 0;

$calEspañol=($promedioEspanol+$promedioComprension)/2;
$calMatematicas=($promedioMatematicas+$promedioFracciones)/2;
$calFCE=$promedioFCE;

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
  <img src="../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../panelControlDirectivo.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="verAlumnos.php" style="padding: 1rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
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
<?php
// Inicializa sumadores y contadores para los promedios
$sumaEspanol = 0;
$sumaMatematicas = 0;
$sumaFCE = 0;
$contadorEspanol = 0;
$contadorMatematicas = 0;
$contadorFCE = 0;
$contador=0;
$sumaTotal=0;
$promedioGeneral=0;

// Cuenta el número de intentos obtenidos y suma las calificaciones
foreach ($resultadosExamen as $resultado) {
    $sumaEspanol += $resultado['calificacionEspanol'];
    $sumaMatematicas += $resultado['calificacionMatematicas'];
    $sumaFCE += $resultado['calificacionFce'];
    $sumaTotal+= $resultado['puntaje_general'];
    $contador++;
    $contadorEspanol++;
    $contadorMatematicas++;
    $contadorFCE++;
}

// Calcula los promedios
$promedioEspanol = $contadorEspanol > 0 ? $sumaEspanol / $contadorEspanol : 0;
$promedioMatematicas = $contadorMatematicas > 0 ? $sumaMatematicas / $contadorMatematicas : 0;
$promedioFCE = $contadorFCE > 0 ? $sumaFCE / $contadorFCE : 0;
$promedioGeneral = $contador > 0 ? $sumaTotal / $contador : 0;

$ultimaCalEspanol = 0;
$ultimaCalMatematicas = 0;
$ultimaCalFCE = 0;

// Recorre los resultados para encontrar la última calificación de cada materia
foreach ($resultadosExamen as $resultado) {
    $ultimaCalEspanol = $resultado['calificacionEspanol'];
    $ultimaCalMatematicas = $resultado['calificacionMatematicas'];
    $ultimaCalFCE = $resultado['calificacionFce'];
    // Asumiendo que el último elemento ya tiene las últimas calificaciones
}
?>
    <select id="selector">
        <option value="rendimientoGeneral">Rendimiento General</option>
        <option value="tablaEspanol">Tabla y Gráfico de Progresión de Español</option>
        <option value="tablaMate">Tabla y Gráfico de Progresión de Matemáticas</option>
        <option value="tablaFce">Tabla y Gráfico de Progresión de FCE</option>
        <option value="tablaComparativa">Tabla  </option>
        
    </select>
    <a href="../../generarPDF/pdfAlumnoDirectivo.php?codigoAlumno=<?php echo urlencode($codigoAlumno); ?>" target="_blank" class="boton-descarga">Descargar PDF</a>



    <div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    
  </div>
</div>
<div  class="examSection" id="detallesAlumno"></div>

<div id="rendimientoGeneral" class="contenido " >
        <!-- Incio tabla de rendimiento general -->
        <div class="descripcion">
    <p>Esta tabla y gráfica muestran el rendimiento general del alumno en todos los exámenes realizados hasta la fecha. Incluye calificaciones por materia y la calificación general de cada examen, junto con el promedio acumulado. La gráfica de progresión histórica visualiza la evolución del rendimiento del alumno a lo largo del tiempo, permitiendo identificar tendencias y áreas de mejora.</p>
</div>

        <div class="grid2columnas">
            <table class="tabla-docentes">
                <tr>
                    
                    <th>Fecha</th>
                    <th>Calificacion Español</th>
                    <th>Calificacion Matemáticas</th>
                    <th>Calificacion FCE</th>
                    <th>Calificacion</th>
                    <th>Ver examen</th>
                </tr>
                    <?php foreach($resultadosExamen as $resultado): ?>
                <tr class="<?php echo ($resultado['puntaje_general'] < 6) ? 'puntaje-bajo' : ''; ?>">
                    <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['calificacionEspanol']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['calificacionMatematicas']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['calificacionFce']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['puntaje_general']); ?></td>
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
                <tr>
                    <th>Promedio</th>
                    <th><?php echo number_format($promedioEspanol, 2); ?></th>
                    <th><?php echo number_format($promedioMatematicas, 2); ?></th>
                    <th><?php echo number_format($promedioFCE, 2); ?></th>
                    <th><?php echo number_format($promedioGeneral, 2); ?></th>
                    <th></th>
                    
                </tr>
                </table>

            <div>        
                <div class="containerGrafica ">
                    <canvas id="progresoHistorico" width="800" height="400"></canvas>
                </div>

                <div class="containerGrafica ">
                    <canvas id="progresoHistoricoCompleto" width="800" height="400"></canvas>
                </div>
            </div>
            
    </div>

    <!-- Fin tabla de rendimiento general -->
</div>


<div id="tablaEspanol" class="contenido " style="display:none;">
    <div class="descripcion">
        <p>Aquí se detalla el rendimiento del alumno específicamente en el área de Español, desglosando las calificaciones en producción de textos y comprensión lectora. La tabla resume las calificaciones obtenidas en estos componentes en cada examen, y la gráfica adjunta ilustra la progresión del rendimiento del alumno en el tiempo, ofreciendo una vista clara de su evolución en esta materia.</p>
    </div>

<div class="grid2columnas">            
        <table class="tabla-docentes">
        <tr>
            <th>Fecha</th>
            <th>Produccion de Textos</th>
            <th>Comprensión lectora</th>
            <th>Calificacion</th>
        </tr>
            <?php foreach($resultadosExamen as $resultado): ?>
            <tr class="<?php echo ($resultado['calificacionEspanol'] < 6) ? 'puntaje-bajo' : ''; ?>">
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
        
            <td><?php echo htmlspecialchars($resultado['puntaje_espanol']); ?></td>
            <td><?php echo htmlspecialchars($resultado['puntaje_comprension']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionEspanol']); ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        
        <div class="graficas" styles="heigth:400px" >
            <canvas id="graficaEspComp" width="400" height="200"></canvas>
        </div>
        
        </div>
        </div>



<div id="tablaMate" class="contenido " style="display:none;">

        <div class="descripcion">
            <p>Esta sección enfoca el rendimiento del alumno en Matemáticas, incluyendo tanto operaciones básicas como el trabajo con fracciones. Similar a la sección de Español, se presenta una tabla con las calificaciones detalladas por examen y una gráfica que muestra la trayectoria del alumno en Matemáticas, facilitando la identificación de fortalezas y áreas para reforzar.</p>
        </div>

        <div class="grid2columnas">
        <table class="tabla-docentes">
        <tr>
            <th>Fecha</th>
            <th>Logica Matemática</th>
            <th>Fracciones</th>
            <th>Calificacion</th>
        </tr>
            <?php foreach($resultadosExamen as $resultado): ?>
            <tr class="<?php echo ($resultado['calificacionMatematicas'] < 6) ? 'puntaje-bajo' : ''; ?>">
            <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
            <td><?php echo htmlspecialchars($resultado['puntaje_matematicas']); ?></td>
            <td><?php echo htmlspecialchars($resultado['puntaje_fracciones']); ?></td>
            <td><?php echo htmlspecialchars($resultado['calificacionMatematicas']); ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        <div class="graficas ">
            <canvas id="graficaMate" width="400" height="200"></canvas>
        </div>

        
</div>
</div>



<div id="tablaFce" class="contenido " style="display:none;">

        <div class="descripcion">
            <p>Presenta un análisis del desempeño del alumno en Formación Cívica y Ética. La tabla resume las calificaciones recibidas en cada evaluación, mientras que la gráfica acompaña para visualizar la evolución y consistencia del estudiante en esta área a lo largo del tiempo.</p>
        </div>
        <div class="grid2columnas">
        <table class="tabla-docentes">
                <tr>
                    <th>Fecha</th>
                    <th>Formacion civica y etica</th>
                </tr>
                    <?php foreach($resultadosExamen as $resultado): ?>
                    <tr class="<?php echo ($resultado['puntaje_fce'] < 6) ? 'puntaje-bajo' : ''; ?>">
                    <td><?php echo htmlspecialchars($resultado['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($resultado['puntaje_fce']); ?></td>
                </tr>
                <?php endforeach; ?>
                </table>
            <div class="graficas mt-5">
                <canvas id="graficaFce" width="400" height="200"></canvas>
            </div>
</div>
</div>

<div id="tablaComparativa" class="contenido" style="display:none;">
<div class="descripcion">
    <p>Esta tabla comparativa ofrece una vista consolidada de las últimas calificaciones del alumno en contraste con sus promedios en Español, Matemáticas y Formación Cívica y Ética. Es una herramienta útil para comparar el rendimiento reciente con el desempeño general histórico del alumno, identificando tanto logros como posibles desafíos actuales.</p>
</div>

    <div class="container ">
    <canvas id="comparacionPorMateria" width="800" height="600"></canvas>
    </div>
</div>








<button onclick="descargarPDF()">Descargar PDF</button>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script>
var ctx = document.getElementById('progresoHistorico').getContext('2d');
var progresoHistorico = new Chart(ctx, {
    type: 'line', // Tipo de gráfica
    data: {
        labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Eje X: Fechas de los exámenes
        datasets: [{
            label: 'Puntaje General',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>], // Eje Y: Puntajes generales
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
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
var ctx = document.getElementById('progresoHistoricoCompleto').getContext('2d');
var graficaEspComp = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Fechas de examen como eje X
        datasets: [{
            label: 'Puntaje General',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_general'] . ','; } ?>], // Eje Y: Puntajes generales
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }, {
            label: 'Español',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionEspanol'] . ','; } ?>], // Puntajes de Comprensión
            borderColor: 'rgb(54, 162, 2)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        },{
            label: 'Matematicas',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionMatematicas'] . ','; } ?>], // Puntajes de Comprensión
            borderColor: 'rgb(5, 12, 235)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        },{
            label: 'FCE',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['calificacionFce'] . ','; } ?>], // Puntajes de Comprensión
            borderColor: 'rgb(54, 162, 235)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        }
    ]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true // Comenzar el eje Y desde cero
            }
        }
    }
});




var ctx = document.getElementById('graficaEspComp').getContext('2d');
var graficaEspComp = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Fechas de examen como eje X
        datasets: [{
            label: 'Produccion de textos',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_espanol'] . ','; } ?>], // Puntajes de Español
            borderColor: 'rgb(255, 99, 132)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        }, {
            label: 'Comprensión',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_comprension'] . ','; } ?>], // Puntajes de Comprensión
            borderColor: 'rgb(54, 162, 235)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true // Comenzar el eje Y desde cero
            }
        }
    }
});
var ctx = document.getElementById('graficaMate').getContext('2d');
var graficaEspComp = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Fechas de examen como eje X
        datasets: [{
            label: 'Logica Matemática',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_matematicas'] . ','; } ?>], // Puntajes de Español
            borderColor: 'rgb(255, 99, 132)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        }, {
            label: 'Fracciones',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_fracciones'] . ','; } ?>], // Puntajes de Comprensión
            borderColor: 'rgb(54, 162, 235)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true // Comenzar el eje Y desde cero
            }
        }
    }
});

var ctx = document.getElementById('graficaFce').getContext('2d');
var graficaEspComp = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php foreach($resultadosExamen as $resultado) { echo '"' . $resultado['fecha'] . '",'; } ?>], // Fechas de examen como eje X
        datasets: [{
            label: 'Formación civica y etica',
            data: [<?php foreach($resultadosExamen as $resultado) { echo $resultado['puntaje_fce'] . ','; } ?>], // Puntajes de Español
            borderColor: 'rgb(255, 99, 132)', // Color de la línea
            fill: false, // No rellenar bajo la línea
        },]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true // Comenzar el eje Y desde cero
            }
        }
    }
});

var ctx = document.getElementById('comparacionPorMateria').getContext('2d');
var comparacionPorMateria = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Español', 'Matemáticas', 'FCE'],
        datasets: [{
            label: 'Calificación ultimo examen',
            data: [<?php echo $ultimaCalEspanol;?>, <?php echo $ultimaCalMatematicas;?>,<?php echo $ultimaCalFCE;?>],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        }, {
            label: 'Promedio',
            data: [<?php echo $promedioEspanol;?>, <?php echo $promedioMatematicas;?>,<?php echo $promedioFCE;?> ],
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

function descargarPDF() {
    const doc = new jspdf.jsPDF();
    const imgDataProgreso = document.getElementById('progresoHistorico').toDataURL('image/png');
    // Continúa con la preparación de datos de imagen...

    // Agregar texto
    doc.text('Reporte de Progreso del Alumno', 10, 10);
    // Agrega tus gráficos como antes
    doc.addImage(imgDataProgreso, 'PNG', 10, 20, 180, 100);
    doc.addPage();
    // Continúa con tus imágenes...

    // Ejemplo de agregar una tabla (asegúrate de que el plugin jsPDF-AutoTable está incluido)
    const head = [['ID', 'Nombre', 'Asignatura', 'Calificación']];
    const body = [
        [1, 'Juan Pérez', 'Matemáticas', 'A'],
        // Más filas...
    ];
    doc.autoTable({
        head: head,
        body: body,
        startY: 150, // Ajusta según sea necesario
    });

    // Finalmente, guarda el PDF
    doc.save('reporte-completo.pdf');
}


 
$(document).ready(function() {
    // Tu código aquí

    $(document).on('click', '.exam-btn', function(event) {
        event.preventDefault(); // Evita que el enlace ejecute su comportamiento predeterminado (recargar la página)
        var codigoExamen = $(this).data('id');
        console.log('ID del alumno:', codigoExamen);
        $.ajax({
            url: 'mostrarExamen.php',
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