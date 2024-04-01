<?php
    session_start();
    if (!isset($_SESSION['correo'])) {
      header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
      exit;
  }
     require_once '../librerias/tcpdf.php';
    require '../config/db.php';
   
    $db = new db();
    $con =$db->conexion();
   
    //Obtener el codigo del maestro en base a su correo


    $codigoAlumno = isset($_GET['codigoAlumno']) ? $_GET['codigoAlumno'] : null;

    $sqlAlumno = $con->prepare("SELECT codigoProfesor, nombre, apellido, codigoAlumno, correo, escuela, codigoEscuela  FROM alumno WHERE codigoalumno = :codigoAlumno");
    $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $sqlAlumno->execute();
    $alumno = $sqlAlumno->fetchAll(PDO::FETCH_ASSOC);
    $nombreAlumno = $alumno[0]['nombre'];
    $apellidoAlumno   = $alumno[0]['apellido'];  
    $correo   = $alumno[0]['correo'];  
    $escuela   = $alumno[0]['escuela'];  
    
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


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nombre del Autor');
$pdf->SetTitle('Reporte Alumno');
$pdf->SetSubject('Asunto del PDF');
$pdf->SetKeywords('TCPDF, PDF, ejemplo, guía');

// Añade una página
$pdf->AddPage();

$datosAlumno = <<<EOD
<h1>Reporte de Progreso del Grupo</h1>
<h2>Datos del Alumno</h2>
<p><strong>Nombre:</strong> $nombreAlumno $apellidoAlumno</p>
<p><strong>Correo:</strong> $correo</p>
<p><strong>Escuela:</strong> $escuela</p>
<div class="descripcion">
<p>Este documento presenta un análisis detallado del progreso de los alumnos a cargo del profesor mencionado. Se incluyen diversos indicadores de rendimiento, desglosados por materia y alumno, ofreciendo una visión clara del desarrollo académico del grupo.</p>
</div>
EOD;

$descripcionTablaRendimeinto= '<h1>Resultados del progreso del alumno</h1>
<p>Esta tabla y gráfica muestran el rendimiento general del alumno en todos los exámenes realizados hasta la fecha. Incluye calificaciones por materia y la calificación general de cada examen, junto con el promedio acumulado. La gráfica de progresión histórica visualiza la evolución del rendimiento del alumno a lo largo del tiempo, permitiendo identificar tendencias y áreas de mejora.</p>
';

$htmlTablaRendimiento = <<<EOD
<style>
.tabla-docentes {
    border-collapse: collapse;
    margin: auto;
    padding: 2rem;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}
.tabla-docentes th, .tabla-docentes td {
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
}

.tabla-docentes tr:nth-child(even) {background-color: var(--primario);}
.tabla-docentes tr:hover {background-color: var(--primarioOscuro);}
    .puntaje-bajo { background-color: #ffcccc; }
</style>
<table class="tabla-docentes">
    <tr>
        <th>Fecha</th>
        <th>Calificacion Español</th>
        <th>Calificacion Matemáticas</th>
        <th>Calificacion FCE</th>
        <th>Calificacion</th>
    </tr>
EOD;

foreach($resultadosExamen as $resultado) {
    $clasePuntajeBajo = ($resultado['puntaje_general'] < 6) ? 'puntaje-bajo' : '';
    $htmlTablaRendimiento .= <<<EOD
    <tr class="$clasePuntajeBajo">
        <td>{$resultado['fecha']}</td>
        <td>{$resultado['calificacionEspanol']}</td>
        <td>{$resultado['calificacionMatematicas']}</td>
        <td>{$resultado['calificacionFce']}</td>
        <td>{$resultado['puntaje_general']}</td>
    </tr>
EOD;
}
$htmlTablaRendimiento .= '</table>';


$descripcionTablaEspanol= '<h1>Resultados del progreso del alumno</h1>
<p>Aquí se detalla el rendimiento del alumno específicamente en el área de Español, desglosando las calificaciones en producción de textos y comprensión lectora. La tabla resume las calificaciones obtenidas en estos componentes en cada examen, y la gráfica adjunta ilustra la progresión del rendimiento del alumno en el tiempo, ofreciendo una vista clara de su evolución en esta materia.</p>
';
$htmlTablaEspanol = <<<EOD
<style>
.tabla-docentes {
    border-collapse: collapse;
    margin: auto;
    padding: 2rem;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}
.tabla-docentes th, .tabla-docentes td {
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
}

.tabla-docentes tr:nth-child(even) {background-color: var(--primario);}
.tabla-docentes tr:hover {background-color: var(--primarioOscuro);}
    .puntaje-bajo { background-color: #ffcccc; }
</style>
<table class="tabla-docentes">
    <tr>
        <th>Fecha</th>
        <th>Producción de Textos</th>
        <th>Comprensión Lectora</th>
        <th>Calificación</th>
    </tr>
EOD;

foreach($resultadosExamen as $resultado) {
    $clasePuntajeBajo = ($resultado['calificacionEspanol'] < 6) ? 'puntaje-bajo' : '';
    $htmlTablaEspanol .= <<<EOD
    <tr class="$clasePuntajeBajo">
        <td>{$resultado['fecha']}</td>
        <td>{$resultado['puntaje_espanol']}</td>
        <td>{$resultado['puntaje_comprension']}</td>
        <td>{$resultado['calificacionEspanol']}</td>
    </tr>
EOD;
}

$htmlTablaEspanol .= '</table>';


$descripcionTablaMatematicas= '<h1>Resultados del progreso del alumno</h1>
<p>Esta sección enfoca el rendimiento del alumno en Matemáticas, incluyendo tanto operaciones básicas como el trabajo con fracciones. Similar a la sección de Español, se presenta una tabla con las calificaciones detalladas por examen y una gráfica que muestra la trayectoria del alumno en Matemáticas, facilitando la identificación de fortalezas y áreas para reforzar.</p>
';
$htmlTablaMatematicas = <<<EOD
<style>
.tabla-docentes {
    border-collapse: collapse;
    margin: auto;
    padding: 2rem;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}
.tabla-docentes th, .tabla-docentes td {
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
}

.tabla-docentes tr:nth-child(even) {background-color: var(--primario);}
.tabla-docentes tr:hover {background-color: var(--primarioOscuro);}
    .puntaje-bajo { background-color: #ffcccc; }
</style>
<table class="tabla-docentes">
    <tr>
        <th>Fecha</th>
        <th>Lógica Matemática</th>
        <th>Fracciones</th>
        <th>Calificación</th>
    </tr>
EOD;

foreach($resultadosExamen as $resultado) {
    $clasePuntajeBajo = ($resultado['calificacionMatematicas'] < 6) ? 'puntaje-bajo' : '';
    $htmlTablaMatematicas .= <<<EOD
    <tr class="$clasePuntajeBajo">
        <td>{$resultado['fecha']}</td>
        <td>{$resultado['puntaje_matematicas']}</td>
        <td>{$resultado['puntaje_fracciones']}</td>
        <td>{$resultado['calificacionMatematicas']}</td>
    </tr>
EOD;
}

$htmlTablaMatematicas .= '</table>';

$descripcionTablaFce= '<h1>Resultados del progreso del alumno</h1>
<p>Presenta un análisis del desempeño del alumno en Formación Cívica y Ética. La tabla resume las calificaciones recibidas en cada evaluación, mientras que la gráfica acompaña para visualizar la evolución y consistencia del estudiante en esta área a lo largo del tiempo.</p>
';
$htmlTablaFCE = <<<EOD
<style>
.tabla-docentes {
    border-collapse: collapse;
    margin: auto;
    padding: 2rem;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}
.tabla-docentes th, .tabla-docentes td {
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
}

.tabla-docentes tr:nth-child(even) {background-color: var(--primario);}
.tabla-docentes tr:hover {background-color: var(--primarioOscuro);}
    .puntaje-bajo { background-color: #ffcccc; }
</style>
<table class="tabla-docentes">
    <tr>
        <th>Fecha</th>
        <th>Formación Cívica y Ética</th>
    </tr>
EOD;

foreach($resultadosExamen as $resultado) {
    $clasePuntajeBajo = ($resultado['puntaje_fce'] < 6) ? 'puntaje-bajo' : '';
    $htmlTablaFCE .= <<<EOD
    <tr class="$clasePuntajeBajo">
        <td>{$resultado['fecha']}</td>
        <td>{$resultado['puntaje_fce']}</td>
    </tr>
EOD;
}

$htmlTablaFCE .= '</table>';


// Agregar la tabla al PDF
$pdf->writeHTML($datosAlumno, true, false, true, false, '');
$pdf->writeHTML($descripcionTablaRendimeinto, true, false, true, false, '');
$pdf->writeHTML($htmlTablaRendimiento, true, false, true, false, '');

// Asegúrate de que $pdf es tu objeto TCPDF ya inicializado
$pdf->AddPage(); // Añade una nueva página al documento si es necesario
$pdf->writeHTML($descripcionTablaEspanol, true, false, true, false, '');
$pdf->writeHTML($htmlTablaEspanol, true, false, true, false, '');

// Asumiendo que $pdf es tu objeto TCPDF
$pdf->AddPage();
$pdf->writeHTML($descripcionTablaMatematicas, true, false, true, false, '');
$pdf->writeHTML($htmlTablaMatematicas, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaFce, true, false, true, false, '');
$pdf->writeHTML($htmlTablaFCE, true, false, true, false, '');

// Cierra y envía el documento PDF
$pdf->Output('Reporte por grupo.pdf', 'I');


?>