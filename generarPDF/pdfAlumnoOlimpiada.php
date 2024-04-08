<?php
    session_start();
    if (!isset($_SESSION['Maestro'])) {
      header('Location:../loginMaestro.php'); // Redirigir al login si no está logueado
      exit;
  }
     require_once '../librerias/tcpdf.php';
    require '../config/db.php';
   
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

    $sqlAlumno = $con->prepare("SELECT codigoProfesor, nombre, apellido, correo, codigoAlumno, escuela, codigoEscuela  FROM alumno WHERE codigoalumno = :codigoAlumno");
    $sqlAlumno->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $sqlAlumno->execute();
    $alumno = $sqlAlumno->fetchAll(PDO::FETCH_ASSOC);
    $nombreAlumno = $alumno[0]['nombre'];
    $apellidoAlumno   = $alumno[0]['apellido'];
    $correoAlumno   = $alumno[0]['correo'];
    $escuela   = $alumno[0]['escuela'];

    
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
<p><strong>Correo:</strong> $correoAlumno</p>
<p><strong>Escuela:</strong> $escuela</p>
<div class="descripcion">
<p>Este documento presenta un análisis detallado del progreso de los alumnos a cargo del profesor mencionado. Se incluyen diversos indicadores de rendimiento, desglosados por materia y alumno, ofreciendo una visión clara del desarrollo académico del grupo.</p>
</div>
EOD;
$informacionDocente = <<<EOD
<h2>Datos del Docente</h2>
<p><strong>Nombre:</strong> {$nombreMaestro} {$apellidoMaestro}</p>
<p><strong>Código del Profesor:</strong> {$codigoMaestro}</p>
<p><strong>Correo:</strong> {$correoMaestro}</p>
EOD;



$descripcionEspanol = <<<EOD
<h2>Desempeño en Español</h2>
<p>La sección de Español se enfoca en evaluar la habilidad de los estudiantes para interactuar con el lenguaje en sus múltiples facetas: desde la comprensión de textos complejos hasta la capacidad de expresar ideas de manera clara y creativa. Al adentrarse en la literatura, los estudiantes no solo desarrollan aprecio por la riqueza lingüística, sino que también se fomenta su capacidad de empatía y análisis crítico. Esta evaluación sirve como una herramienta esencial para reforzar las bases comunicativas y promover una exploración profunda de la cultura y la identidad a través de las palabras.</p>
EOD;
$htmlTablaRendimientoGeneral = <<<EOD
<style>
    .tabla-docentes th, .tabla-docentes td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    .tabla-docentes {
        border-collapse: collapse;
        width: 100%;
    }
    .puntaje-bajo {
        background-color: #ffcccc;
    }
</style>
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
        </tr>
    </thead>
    <tbody>
EOD;

foreach($resultadosExamen as $resultado) {
    $clasePuntajeBajo = ($resultado['puntaje_general'] < 6) ? 'puntaje-bajo' : '';
    $htmlTablaRendimientoGeneral .= <<<EOD
        <tr class="$clasePuntajeBajo">
            <td>{$resultado['fecha']}</td>
            <td>{$resultado['total_preguntas']}</td>
            <td>{$resultado['correctas_general']}</td>
            <td>{$resultado['calificacionEspanol']}</td>
            <td>{$resultado['calificacionMatematicas']}</td>
            <td>{$resultado['calificacionCiencias']}</td>
            <td>{$resultado['calificacionHistoria']}</td>
            <td>{$resultado['calificacionGeografia']}</td>
            <td>{$resultado['puntaje_general']}</td>
        </tr>
EOD;
}

$htmlTablaRendimientoGeneral .= <<<EOD
    </tbody>
</table>
EOD;


$htmlTablaEspanol = <<<EOD
<style>
    .tabla-docentes th, .tabla-docentes td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    .tabla-docentes {
        border-collapse: collapse;
        width: 100%;
    }
    .calificacion { font-weight: bold; }
</style>
<table class="tabla-docentes" border="1" cellpadding="4">
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
EOD;

foreach($resultadosExamen as $resultado) {
    $htmlTablaEspanol .= <<<EOD
        <tr>
            <td>{$resultado['fecha']}</td>
            <td>{$resultado['totalPreguntasEspanol']}</td>
            <td>{$resultado['correctasEspanol']}</td>
            <td>{$resultado['calificacionEspanol']}</td>
            <td class="calificacion">{$resultado['puntaje_general']}</td>
        </tr>
EOD;
}

$htmlTablaEspanol .= <<<EOD
    </tbody>
</table>
EOD;
$descripcionMatematicas = <<<EOD
<h2>Desempeño en Matemáticas</h2>
<p>El análisis del desempeño en Matemáticas ofrece una visión detallada de cómo los estudiantes comprenden y aplican conceptos matemáticos fundamentales, abarcando desde la aritmética básica hasta aspectos más complejos como el álgebra y la geometría. Este enfoque no solo valora la precisión en la resolución de problemas, sino que también pone a prueba la habilidad de los estudiantes para emplear el razonamiento lógico y el pensamiento crítico en situaciones prácticas y teóricas. Al fomentar estas competencias, se prepara a los estudiantes para enfrentar desafíos futuros en campos científicos, tecnológicos y cotidianos con confianza y creatividad.</p>
EOD;
$htmlTablaMatematicas = '<h2>Resultados de Matemáticas</h2><table border="1" cellpadding="4">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Matemáticas</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>';

foreach ($resultadosExamen as $resultado) {
    $htmlTablaMatematicas .= '<tr>
        <td>' . htmlspecialchars($resultado['fecha']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionMatematicas']) . '</td>
        <td>' . htmlspecialchars($resultado['totalPreguntasMatematicas']) . '</td>
        <td>' . htmlspecialchars($resultado['correctasMatematicas']) . '</td>
        <td class="calificacion">' . htmlspecialchars($resultado['puntaje_general']) . '</td>
    </tr>';
}
$htmlTablaMatematicas .= '</tbody></table>';


$descripcionCiencias = <<<EOD
<h2>Desempeño en Ciencias</h2>
<p>La evaluación en Ciencias se centra en medir el grado de comprensión y aplicación de principios científicos en Biología, Química y Física, destacando la importancia del método científico como herramienta de investigación. Al sumergir a los estudiantes en el estudio de los fenómenos naturales, se busca cultivar una curiosidad intrínseca por el mundo que les rodea, así como la capacidad de formular hipótesis, experimentar y analizar resultados. Este enfoque integral promueve no solo el conocimiento científico, sino también el desarrollo de una actitud crítica y reflexiva ante los retos medioambientales y tecnológicos de nuestro tiempo.</p>
EOD;
$htmlTablaCiencias = '<h2>Resultados de Ciencias</h2><table border="1" cellpadding="4">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Ciencias</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>';

foreach ($resultadosExamen as $resultado) {
    $htmlTablaCiencias .= '<tr>
        <td>' . htmlspecialchars($resultado['fecha']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionCiencias']) . '</td>
        <td>' . htmlspecialchars($resultado['totalPreguntasCiencias']) . '</td>
        <td>' . htmlspecialchars($resultado['correctasCiencias']) . '</td>
        <td class="calificacion">' . htmlspecialchars($resultado['puntaje_general']) . '</td>
    </tr>';
}
$htmlTablaCiencias .= '</tbody></table>';

$descripcionHistoria = <<<EOD
<h2>Desempeño en Historia</h2>
<p>La sección de Historia invita a los estudiantes a embarcarse en un viaje a través del tiempo, explorando eventos, culturas y personajes que han modelado el mundo actual. Más allá de la memorización de fechas y hechos, se enfatiza en el análisis de causas y consecuencias, fomentando una comprensión profunda de los procesos históricos y su relevancia contemporánea. Esta perspectiva crítica anima a los estudiantes a reflexionar sobre su papel como ciudadanos globales y a valorar la diversidad cultural, política y social, preparándolos para contribuir de manera informada y responsable a la sociedad.</p>
EOD;

$htmlTablaHistoria = '<h2>Resultados de Historia</h2><table border="1" cellpadding="4">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Historia</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>';

foreach ($resultadosExamen as $resultado) {
    $htmlTablaHistoria .= '<tr>
        <td>' . htmlspecialchars($resultado['fecha']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionHistoria']) . '</td>
        <td>' . htmlspecialchars($resultado['totalPreguntasHistoria']) . '</td>
        <td>' . htmlspecialchars($resultado['correctasHistoria']) . '</td>
        <td class="calificacion">' . htmlspecialchars($resultado['puntaje_general']) . '</td>
    </tr>';
}
$htmlTablaHistoria .= '</tbody></table>';

$descripcionGeografia = <<<EOD
<h2>Desempeño en Geografía</h2>
<p>En Geografía, los estudiantes exploran la complejidad de la Tierra y sus múltiples dimensiones, desde la diversidad física y biológica hasta las intrincadas relaciones entre los seres humanos y su entorno. Esta materia amplía horizontes, promoviendo una comprensión holística de temas como el cambio climático, la urbanización y la gestión de recursos naturales. Al equipar a los estudiantes con el conocimiento para navegar por estos temas complejos, se busca incentivar un compromiso activo y responsable con el planeta, preparándolos para liderar esfuerzos sostenibles y equitativos en el futuro.</p>
EOD;
$htmlTablaGeografia = '<h2>Resultados de Geografía</h2><table border="1" cellpadding="4">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Calificación Geografía</th>
            <th>Preguntas totales</th>
            <th>Preguntas correctas</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>';

foreach ($resultadosExamen as $resultado) {
    $htmlTablaGeografia .= '<tr>
        <td>' . htmlspecialchars($resultado['fecha']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionGeografia']) . '</td>
        <td>' . htmlspecialchars($resultado['totalPreguntasGeografia']) . '</td>
        <td>' . htmlspecialchars($resultado['correctasGeografia']) . '</td>
        <td class="calificacion">' . htmlspecialchars($resultado['puntaje_general']) . '</td>
    </tr>';
}

$htmlTablaGeografia .= '</tbody></table>';

$descripcionComparacionTiempo = <<<EOD
<h2>Comparación de Desempeño a lo Largo del Tiempo</h2>
<p>Esta análisis comparativo permite visualizar la evolución del aprendizaje y el rendimiento académico de los estudiantes a lo largo del tiempo, proporcionando insights valiosos sobre la efectividad de estrategias pedagógicas y programas de estudio. Al identificar tendencias y patrones en el progreso educativo, educadores y padres pueden tomar decisiones informadas para apoyar el desarrollo integral de cada estudiante. Este enfoque longitudinal no solo celebra los logros acumulados, sino que también destaca oportunidades para intervenciones focalizadas y personalizadas, asegurando que todos los estudiantes puedan alcanzar su máximo potencial.</p>
EOD;
$htmlTablaComparacionTiempo = '<h2>Resultados a lo Largo del Tiempo</h2><table border="1" cellpadding="4">
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
    <tbody>';

foreach ($resultadosExamen as $resultado) {
    $htmlTablaComparacionTiempo .= '<tr>
        <td>' . htmlspecialchars($resultado['fecha']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionEspanol']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionMatematicas']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionCiencias']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionHistoria']) . '</td>
        <td>' . htmlspecialchars($resultado['calificacionGeografia']) . '</td>
        <td>' . htmlspecialchars($resultado['puntaje_general']) . '</td>
    </tr>';
}

$htmlTablaComparacionTiempo .= '</tbody></table>';


$pdf->writeHTML($datosAlumno, true, false, true, false, '');
$pdf->writeHTML($informacionDocente, true, false, true, false, '');
$pdf->writeHTML($htmlTablaRendimientoGeneral, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionEspanol, true, false, true, false, '');
$pdf->writeHTML($htmlTablaEspanol, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionMatematicas, true, false, true, false, '');
$pdf->writeHTML($htmlTablaMatematicas, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionCiencias, true, false, true, false, '');
$pdf->writeHTML($htmlTablaCiencias, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionHistoria, true, false, true, false, '');
$pdf->writeHTML($htmlTablaHistoria, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionGeografia, true, false, true, false, '');
$pdf->writeHTML($htmlTablaGeografia, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionComparacionTiempo, true, false, true, false, '');
$pdf->writeHTML($htmlTablaComparacionTiempo, true, false, true, false, '');


// Cierra y envía el documento PDF
$pdf->Output('Reporte por grupo.pdf', 'I');


?>