<?php
    session_start();
    if (!isset($_SESSION['correo'])) {
      header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
      exit;
  }
require_once '../librerias/tcpdf.php'; // Asegúrate de ajustar la ruta al directorio de TCPDF
require '../config/db.php';


// Consultas para generar los datos 
$db = new db();
$con =$db->conexion();

if (isset($_GET['codigoProfesor'])) {
   $codigoProfesor = $_GET['codigoProfesor'];

    
}


$sqlHelp = $con->prepare("SELECT codigoProfesor,correo, nombre, apellido FROM profesor WHERE codigoProfesor = :codigoProfesor");
$sqlHelp->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
$sqlHelp->execute();
$resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
$codigoMaestro = $resultadoaux[0]['codigoProfesor'];
$nombreMaestro = $resultadoaux[0]['nombre'];
$apellidoMaestro   = $resultadoaux[0]['apellido'];
$correoMaestro   = $resultadoaux[0]['correo'];



if ($resultadoaux) {
    

    // Ahora, obtenemos los códigos de los alumnos a cargo de este maestro
    $sqlAlumnos = $con->prepare("SELECT codigoAlumno, apellido, nombre FROM alumno WHERE codigoProfesor = :codigoMaestro ORDER BY apellido, nombre");
    $sqlAlumnos->bindParam(':codigoMaestro', $codigoMaestro, PDO::PARAM_STR);
    $sqlAlumnos->execute();
    $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);


}

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
    // Define el contenido del PDF con los datos del profesor
$htmlContent1 = <<<EOD
<h1>Reporte de Progreso del Grupo</h1>
<h2>Datos del Profesor</h2>
<p><strong>Nombre:</strong> $nombreMaestro $apellidoMaestro</p>
<p><strong>Correo:</strong> $correoMaestro</p>
<div class="descripcion">
<p>Este documento presenta un análisis detallado del progreso de los alumnos a cargo del profesor mencionado. Se incluyen diversos indicadores de rendimiento, desglosados por materia y alumno, ofreciendo una visión clara del desarrollo académico del grupo.</p>
</div>
EOD;

$resultadosMayorPorAlumno = $con->query($sqlUltimoIntentoPorAlumno)->fetchAll(PDO::FETCH_ASSOC);


$style = <<<EOD
<style>
table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
.codigoAlumno { width: 15%; }
.nombreApellido { width: 35%; }
.intentos { width: 25%; }
.promedioGeneral { width: 25%; }
</style>
EOD;

// Inicializa la clase TCPDF y configura el documento
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nombre del Autor');
$pdf->SetTitle('Reporte por grupo');
$pdf->SetSubject('Asunto del PDF');
$pdf->SetKeywords('TCPDF, PDF, ejemplo, guía');

// Añade una página
$pdf->AddPage();

// Define el contenido del PDF
$htmlContent = '<h1>Resultados del progreso de los alumno</h1><div class="descripcion">
<p>Esta sección clasifica a los alumnos basándose en su último intento en el examen, proporcionando una visión actualizada de su desempeño. Incluye datos clave como código del alumno, nombre, apellido, y puntajes detallados por materias, permitiendo una rápida identificación de los estudiantes más destacados y aquellos que pueden necesitar apoyo adicional.</p>
</div>';
// Aquí puedes generar dinámicamente el contenido del PDF usando variables PHP, consultas a la base de datos, etc.

// Imprime el contenido HTML en el documento
// Comienza con el encabezado de tu tabla
$htmlTable = $style .<<<EOD
<h3>Clasificación de Alumno</h3>
<table border="0.5" cellpadding="4">
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
EOD;

// Itera sobre tus datos para añadir cada fila
foreach ($resultadosMayorPorAlumno as $resultado) {
    // Asume que ya has obtenido los datos del alumno como en tu ejemplo
    $htmlTable .= "<tr>";
    $htmlTable .= "<td>" . htmlspecialchars($resultado['codigoAlumno']) . "</td>";
    
    $sqlAlumnos = $con->prepare("SELECT  apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
    $sqlAlumnos->bindParam(':codigoAlumno', $resultado['codigoAlumno'], PDO::PARAM_STR);
    $sqlAlumnos->execute();
    $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
    $htmlTable .= "<td>" . htmlspecialchars($alumnos[0]['nombre']) . "</td>"; // Ajusta según tus variables
    $htmlTable .= "<td>" . htmlspecialchars($alumnos[0]['apellido']) . "</td>"; // Ajusta según tus variables
    $htmlTable .= "<td>" . htmlspecialchars($resultado['puntaje_general']) . "</td>";
    $htmlTable .= "<td>" . htmlspecialchars($resultado['calificacionEspanol']) . "</td>";
    $htmlTable .= "<td>" . htmlspecialchars($resultado['calificacionMatematicas']) . "</td>";
    $htmlTable .= "<td>" . htmlspecialchars($resultado['calificacionFce']) . "</td>";
    $htmlTable .= "<td>" . htmlspecialchars($resultado['fecha']) . "</td>";
    $htmlTable .= "</tr>";
}
// Cierra tu tabla
$htmlTable .= <<<EOD
    </tbody>
</table>
EOD;


//Segunda tabla
$descripcionTablaPromedioGeneral = '<h1>Resultados del progreso de los alumno</h1><p>Presenta un análisis del promedio general de todos los intentos de los exámenes por parte de los alumnos. Esta vista panorámica es esencial para evaluar la consistencia y la evolución del rendimiento académico de los estudiantes a lo largo del tiempo, identificando patrones de mejora o áreas de estancamiento.</p>';
$TablaPromedioGeneral = $style . <<<EOD

    
<h3>Resultados en promedio general</h3>
<table border="0.5" cellpadding="4">
    <thead>
        <tr>
        <th class='codigoAlumno'>Código del Alumno</th>
        <th class='nombreApellido'>Nombre y Apellido</th>
        <th class='intentos'>Intentos</th>
        <th class='promedioGeneral'>Promedio General</th>
        </tr>
    </thead>
    <tbody>
EOD;

// Asume que $alumnosDatos contiene los datos necesarios
foreach ($alumnosDatos as $alumno) {
    $TablaPromedioGeneral .= "<tr>";
    $TablaPromedioGeneral .= "<td class='codigoAlumno'>" . htmlspecialchars($alumno['codigoAlumno']) . "</td>";
    $TablaPromedioGeneral .= "<td class='nombreApellido'>" . htmlspecialchars($alumno['nombre']) . ' ' . htmlspecialchars($alumno['apellido']) . "</td>";
    $TablaPromedioGeneral .= "<td class='intentos'>" . htmlspecialchars($alumno['intentos']) . "</td>";
    $TablaPromedioGeneral .= "<td class='promedioGeneral'>" . number_format($alumno['promedioGeneral'], 2) . "</td>";
    $TablaPromedioGeneral .= "</tr>";
}


// Cierra la tabla
$TablaPromedioGeneral .= <<<EOD
    </tbody>
</table>
EOD;

//Tercera tabla

$descripcionTablaResultadosPorMateria = '<h1>Resultados del progreso de los alumno</h1><p>Desglosa el rendimiento académico de los alumnos por materia, basándose en el promedio de todos sus intentos. Ofrece una visión detallada del progreso en Español, Matemáticas, y FCE, facilitando el análisis específico de fortalezas y debilidades en áreas de estudio particulares.</p>';



// Empieza a construir tu tabla HTML
$htmlTablaResultadosPorMateria = $style . <<<EOD
<h3>Resultados por materia</h3>
<table border="0.5" cellpadding="4">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Intentos</th>
            <th>Promedio Español</th>
            <th>Promedio Matemáticas</th>
            <th>Promedio FCE</th>
        </tr>
    </thead>
    <tbody>
EOD;

// Agrega las filas de la tabla dinámicamente
foreach ($alumnosDatos as $alumno) {
    $htmlTablaResultadosPorMateria .= "<tr>";
    $htmlTablaResultadosPorMateria .= "<td>" . htmlspecialchars($alumno['codigoAlumno']) . "</td>";
    $htmlTablaResultadosPorMateria .= "<td>" . htmlspecialchars($alumno['nombre']) . ' ' . htmlspecialchars($alumno['apellido']) . "</td>";
    $htmlTablaResultadosPorMateria .= "<td>" . htmlspecialchars($alumno['intentos']) . "</td>";
    $htmlTablaResultadosPorMateria .= "<td>" . number_format($alumno['promedioEspanol'], 2) . "</td>";
    $htmlTablaResultadosPorMateria .= "<td>" . number_format($alumno['promedioMatematicas'], 2) . "</td>";
    $htmlTablaResultadosPorMateria .= "<td>" . number_format($alumno['promedioFCE'], 2) . "</td>";
    $htmlTablaResultadosPorMateria .= "</tr>";
}

// Cierra tu tabla
$htmlTablaResultadosPorMateria .= <<<EOD
    </tbody>
</table>
EOD;

$descripcionTablaResultadosEspanol= '<h1>Resultados del progreso de los alumno</h1>
<p>Aquí se detalla el rendimiento del alumno específicamente en el área de Español, desglosando las calificaciones en producción de textos y comprensión lectora. La tabla resume las calificaciones obtenidas en estos componentes en cada examen, y la gráfica adjunta ilustra la progresión del rendimiento del alumno en el tiempo, ofreciendo una vista clara de su evolución en esta materia.</p>';


$htmlTablaResultadosEspanol = $style . <<<EOD
<h3>Resultados español</h3>
<table border="0.5" cellpadding="4">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio Español</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
EOD;

// Itera sobre los datos de los alumnos para añadir cada fila
foreach ($alumnosDatos as $alumno) {
    $htmlTablaResultadosEspanol .= "<tr>";
    $htmlTablaResultadosEspanol .= "<td>" . htmlspecialchars($alumno['codigoAlumno']) . "</td>";
    $htmlTablaResultadosEspanol .= "<td>" . htmlspecialchars($alumno['nombre']) . "</td>";
    $htmlTablaResultadosEspanol .= "<td>" . htmlspecialchars($alumno['apellido']) . "</td>";
    $htmlTablaResultadosEspanol .= "<td>" . number_format($alumno['promedioEspanol'], 2) . "</td>";
    $htmlTablaResultadosEspanol .= "<td>" . number_format($alumno['promedioGeneral'], 2) . "</td>";
    $htmlTablaResultadosEspanol .= "</tr>";
}

// Cierra tu tabla
$htmlTablaResultadosEspanol .= <<<EOD
    </tbody>
</table>
EOD;

$descripcionTablaResultadosMatematicas= '<h1>Resultados del progreso de los alumno</h1>
<p>Esta sección se centra en el rendimiento del alumno en Matemáticas, analizando aspectos como el razonamiento numérico y la resolución de problemas. La información se presenta en tablas que reflejan las calificaciones por examen, acompañadas de gráficas que muestran la trayectoria del estudiante, destacando su progreso o áreas para mejorar.</p>';



$htmlTablaResultadosMatematicas = $style . <<<EOD
<h3>Resultados Matemáticas</h3>
<table border="0.5" cellpadding="4">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio Matemáticas</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
EOD;

foreach ($alumnosDatos as $alumno) {
    $htmlTablaResultadosMatematicas .= "<tr>";
    $htmlTablaResultadosMatematicas .= "<td>" . htmlspecialchars($alumno['codigoAlumno']) . "</td>";
    $htmlTablaResultadosMatematicas .= "<td>" . htmlspecialchars($alumno['nombre']) . "</td>";
    $htmlTablaResultadosMatematicas .= "<td>" . htmlspecialchars($alumno['apellido']) . "</td>";
    $htmlTablaResultadosMatematicas .= "<td>" . number_format($alumno['promedioMatematicas'], 2) . "</td>";
    $htmlTablaResultadosMatematicas .= "<td>" . number_format($alumno['promedioGeneral'], 2) . "</td>";
    $htmlTablaResultadosMatematicas .= "</tr>";
}

$htmlTablaResultadosMatematicas .= <<<EOD
    </tbody>
</table>
EOD;

$descripcionTablaResultadosFce= '<h1>Resultados del progreso de los alumno</h1>
<p>Explora el rendimiento de los alumnos en el área de Formación Cívica y Ética (FCE), destacando su comprensión de los valores y la ética. Las tablas muestran las calificaciones logradas en distintas evaluaciones, y las gráficas complementarias visualizan la evolución de su entendimiento y aplicación de estos conceptos a lo largo del tiempo.</p>
';
$htmlTablaResultadosFCE = $style . <<<EOD
<h3>Resultados FCE</h3>
<table border="0.5" cellpadding="4">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Promedio FCE</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>
EOD;

foreach ($alumnosDatos as $alumno) {
    $htmlTablaResultadosFCE .= "<tr>";
    $htmlTablaResultadosFCE .= "<td>" . htmlspecialchars($alumno['codigoAlumno']) . "</td>";
    $htmlTablaResultadosFCE .= "<td>" . htmlspecialchars($alumno['nombre']) . "</td>";
    $htmlTablaResultadosFCE .= "<td>" . htmlspecialchars($alumno['apellido']) . "</td>";
    $htmlTablaResultadosFCE .= "<td>" . number_format($alumno['promedioFCE'], 2) . "</td>";
    $htmlTablaResultadosFCE .= "<td>" . number_format($alumno['promedioGeneral'], 2) . "</td>";
    $htmlTablaResultadosFCE .= "</tr>";
}

$htmlTablaResultadosFCE .= <<<EOD
    </tbody>
</table>
EOD;


$pdf->writeHTML($htmlContent1, true, false, true, false, '');
$pdf->writeHTML($htmlContent, true, false, true, false, '');
$pdf->writeHTML($htmlTable, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaPromedioGeneral, true, false, true, false, '');
$pdf->writeHTML($TablaPromedioGeneral, true, false, true, false, '');
$pdf->AddPage();
$pdf->writeHTML($descripcionTablaResultadosPorMateria, true, false, true, false, '');
$pdf->writeHTML($htmlTablaResultadosPorMateria, true, false, true, false, '');
$pdf->AddPage();
$pdf->writeHTML($descripcionTablaResultadosEspanol, true, false, true, false, '');
$pdf->writeHTML($htmlTablaResultadosEspanol, true, false, true, false, '');
$pdf->AddPage();
$pdf->writeHTML($descripcionTablaResultadosMatematicas, true, false, true, false, '');
$pdf->writeHTML($htmlTablaResultadosMatematicas, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaResultadosFce, true, false, true, false, '');
$pdf->writeHTML($htmlTablaResultadosFCE, true, false, true, false, '');

// Cierra y envía el documento PDF
$pdf->Output('Reporte por grupo.pdf', 'I');

?>