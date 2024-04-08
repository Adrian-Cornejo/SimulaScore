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

$codigoMaestro = $_GET['codigoMaestro'];

$sqlHelp = $con->prepare("SELECT  nombre, apellido FROM profesor WHERE codigoProfesor = :codigoMaestro");
$sqlHelp->bindParam(':codigoMaestro', $codigoMaestro, PDO::PARAM_STR);
$sqlHelp->execute();
$resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);

$nombreMaestro = $resultadoaux[0]['nombre'];
$apellidoMaestro   = $resultadoaux[0]['apellido'];



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
    FROM resultados_examen_olimpiada 
    WHERE codigoAlumno IN ($codigosParaSql)
    ORDER BY fecha ASC";
// Ejecutar la consulta
// Asumiendo $con es tu conexión PDO y $codigosParaSql ya está definido
$resultadosExamen = $con->query($sqlResultadosExamen)->fetchAll(PDO::FETCH_ASSOC);
// print_r($resultadosExamen);
$alumnosDatos = []; // Array para almacenar los datos finales

// Iterar sobre los resultados de los exámenes
foreach ($resultadosExamen as $resultado) {
    $codigoAlumno = $resultado['codigoAlumno'];
    // Obtener nombre y apellido del alumno
    $sqlAlumnos = $con->prepare("SELECT apellido, nombre FROM alumno WHERE codigoAlumno = :codigoAlumno");
    $sqlAlumnos->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $sqlAlumnos->execute();
    $alumnos = $sqlAlumnos->fetchAll(PDO::FETCH_ASSOC);
    $nombre = $alumnos[0]['nombre'];
    $apellido = $alumnos[0]['apellido'];

    if (!isset($alumnosDatos[$codigoAlumno])) {
        $alumnosDatos[$codigoAlumno] = [
            'codigoAlumno' => $codigoAlumno,
            'nombre' => $nombre, 
            'apellido' => $apellido,
            'sumaPuntajes' => 0,
            'intentos' => 0,
            // Sumas para todas las materias
            'sumaCalificacionesEspanol' => 0,
            'sumaCalificacionesMatematicas' => 0,
            'sumaCalificacionesCiencias' => 0,
            'sumaCalificacionesGeografia' => 0,
            'sumaCalificacionesHistoria' => 0,
            'fechaUltimoIntento' => '1900-01-01'
        ];
    }
    
    // Sumar los puntajes y calificaciones para todas las materias
    $alumnosDatos[$codigoAlumno]['sumaPuntajes'] += $resultado['puntaje_general'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesEspanol'] += $resultado['calificacionEspanol'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesMatematicas'] += $resultado['calificacionMatematicas'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesCiencias'] += $resultado['calificacionCiencias'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesGeografia'] += $resultado['calificacionGeografia'];
    $alumnosDatos[$codigoAlumno]['sumaCalificacionesHistoria'] += $resultado['calificacionHistoria'];
    $alumnosDatos[$codigoAlumno]['intentos']++;
}

// Calcular promedios para todas las materias
foreach ($alumnosDatos as $codigo => $datos) {
    $alumnosDatos[$codigo]['promedioGeneral'] = $datos['intentos'] > 0 ? $datos['sumaPuntajes'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioEspanol'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesEspanol'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioMatematicas'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesMatematicas'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioCiencias'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesCiencias'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioGeografia'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesGeografia'] / $datos['intentos'] : 0;
    $alumnosDatos[$codigo]['promedioHistoria'] = $datos['intentos'] > 0 ? $datos['sumaCalificacionesHistoria'] / $datos['intentos'] : 0;

    // Eliminar sumas, ya no son necesarias
    unset($alumnosDatos[$codigo]['sumaPuntajes'], $alumnosDatos[$codigo]['sumaCalificacionesEspanol'], $alumnosDatos[$codigo]['sumaCalificacionesMatematicas'], $alumnosDatos[$codigo]['sumaCalificacionesCiencias'], $alumnosDatos[$codigo]['sumaCalificacionesGeografia'], $alumnosDatos[$codigo]['sumaCalificacionesHistoria']);
    if ($resultado['fecha'] > $alumnosDatos[$codigoAlumno]['fechaUltimoIntento']) {
        $alumnosDatos[$codigoAlumno]['fechaUltimoIntento'] = $resultado['fecha'];
    }
}

$sqlUltimoIntentoPorAlumno = "
SELECT re.*
FROM resultados_examen_olimpiada re
INNER JOIN (
    SELECT codigoAlumno, MAX(id) AS UltimoID
    FROM resultados_examen_olimpiada
    WHERE codigoAlumno IN ($codigosParaSql) AND fecha IN (
        SELECT MAX(fecha)
        FROM resultados_examen_olimpiada
        WHERE codigoAlumno IN ($codigosParaSql)
        GROUP BY codigoAlumno
    )
    GROUP BY codigoAlumno
) ultimoIntento ON re.codigoAlumno = ultimoIntento.codigoAlumno AND re.id = ultimoIntento.UltimoID
";

// Ejecutar la consulta
// Asumiendo $con es tu conexión PDO y $codigosParaSql ya está definido
$resultadosMayorPorAlumno = $con->query($sqlUltimoIntentoPorAlumno)->fetchAll(PDO::FETCH_ASSOC);
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

//print_r($resultadosMayorPorAlumno);

// Inicializa la clase TCPDF y configura el documento
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nombre del Autor');
$pdf->SetTitle('Reporte por grupo');
$pdf->SetSubject('Asunto del PDF');
$pdf->SetKeywords('TCPDF, PDF, ejemplo, guía');

// Añade una página
$pdf->AddPage();
$descripcionTablaPromedioGeneral = '<h1>Resultados del progreso de los alumno</h1><p>Esta sección clasifica a los alumnos basándose en su desempeño general a lo largo del tiempo, destacando tanto a los estudiantes sobresalientes como a aquellos que pueden necesitar apoyo adicional. Es una herramienta útil para el seguimiento y la intervención temprana.</p>
';

$html = '<h2>Clasificación de Alumnos</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Puntaje General</th>
            <th>Calificación Español</th>
            <th>Calificación Matemáticas</th>
            <th>Calificación Ciencias</th>
            <th>Calificación Geografía</th>
            <th>Calificación Historia</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

foreach ($alumnosDatos as $codigo => $datos) {
    $html .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']. $datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioEspanol'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioMatematicas'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioCiencias'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeografia'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioHistoria'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
              </tr>';
}

$html .= '</tbody></table>';

$descripcionTablaPromedioGeneral1 = '<h1>Resultados del progreso de los alumno</h1><p>Presenta un promedio general de los resultados obtenidos por todos los alumnos, brindando una visión panorámica del rendimiento del grupo. Esta sección es esencial para evaluar la efectividad general del plan de estudios y las metodologías de enseñanza.</p>
';

$html2 = '<h2>Resultados en Promedio General</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Intentos</th>
            <th>Promedio General</th>
        </tr>
    </thead>
    <tbody>';

// Asumiendo que ya tienes la información de alumnosDatos adecuadamente poblada
foreach ($alumnosDatos as $alumno) {
    // Aquí asumimos que ya has realizado la consulta a la base de datos y $alumnos contiene los datos necesarios
    $html2 .= '<tr>
                <td>' . htmlspecialchars($alumno['codigoAlumno']) . '</td>
                <td>' . htmlspecialchars($alumno['nombre']) . '</td> <!-- Asegúrate de ajustar esto según cómo obtienes los nombres -->
                <td>' . htmlspecialchars($alumno['apellido']) . '</td> <!-- Asegúrate de ajustar esto según cómo obtienes los apellidos -->
                <td>' . htmlspecialchars($alumno['intentos']) . '</td>
                <td>' . number_format($alumno['promedioGeneral'], 2) . '</td>
               </tr>';
}

$html2 .= '</tbody></table>';

$descripcionTablaPorMateria = '<h1>Resultados del progreso de los alumno</h1><p>Muestra los resultados obtenidos por los alumnos en cada materia específica, permitiendo identificar áreas de fortaleza y oportunidades de mejora en el currículo escolar. Facilita la toma de decisiones enfocadas en el refuerzo académico particular.</p>';
$html3 = '<h2>Resultados por Materia</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Español</th>
            <th>Calificación Matemáticas</th>
            <th>Calificación Ciencias</th>
            <th>Calificación Geografía</th>
            <th>Calificación Historia</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Generar las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html3 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioEspanol'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioMatematicas'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioCiencias'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeografia'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioHistoria'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html3 .= '</tbody></table>';

$descripcionTablaEspanol = '<h1>Resultados del progreso de los alumno</h1><p>Expone los resultados específicos obtenidos en la materia de Español, incluyendo habilidades de lectura, escritura y comprensión lectora. Es útil para detectar necesidades específicas en el dominio del idioma.</p>
';

$html4 = '<h2>Resultados en Español</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Español</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Añadir las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html4 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioEspanol'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html4 .= '</tbody></table>';

$descripcionTablaMate= '<h1>Resultados del progreso de los alumno</h1><p>Detalla los resultados obtenidos en la materia de Matemáticas, abarcando desde aritmética básica hasta conceptos más avanzados. Esta sección es crucial para identificar cómo los alumnos aplican el razonamiento lógico y matemático en problemas prácticos.</p>
';

$html5 = '<h2>Resultados en Matemáticas</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Matemáticas</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Genera las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html5 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioMatematicas'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html5 .= '</tbody></table>';

$descripcionTablaCiencias= '<h1>Resultados del progreso de los alumno</h1><p>Presenta los resultados obtenidos en la materia de Ciencias Naturales, mostrando el entendimiento de los estudiantes sobre conceptos científicos fundamentales y su capacidad para aplicar el método científico. Esencial para fomentar una mentalidad de exploración y curiosidad.</p>
';
$html6 = '<h2>Resultados en Ciencias Naturales</h2>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Ciencias</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Generar las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html6 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioCiencias'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html6 .= '</tbody></table>';



$descripcionTablaHistoria= '<h1>Resultados del progreso de los alumno</h1> <p>Expone los resultados obtenidos en la materia de Historia, destacando el conocimiento de los alumnos sobre eventos históricos significativos y su habilidad para analizar el impacto de estos eventos en el mundo contemporáneo. Importante para desarrollar un sentido crítico de la historia.</p>
';
$html7 = '<h2>Resultados en Historia</h2>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Historia</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Genera las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html7 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioHistoria'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html7 .= '</tbody></table>';

$descripcionTablaGeografia= '<h1>Resultados del progreso de los alumno</h1> <p>Muestra los resultados específicos obtenidos en la materia de Geografía, incluyendo el conocimiento de los alumnos sobre geografía física y política, así como su comprensión de los temas medioambientales actuales. Útil para evaluar la conciencia global de los estudiantes.</p>
';
$html8 = '<h2>Resultados en Geografía</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Código Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Puntaje General</th>
            <th>Calificación Geografía</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>';

// Genera las filas de la tabla para cada alumno
foreach ($alumnosDatos as $codigo => $datos) {
    $html8 .= '<tr>
                <td>' . htmlspecialchars($codigo) . '</td>
                <td>' . htmlspecialchars($datos['nombre']) . '</td>
                <td>' . htmlspecialchars($datos['apellido']) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeneral'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($datos['promedioGeografia'], 2)) . '</td>
                <td>' . htmlspecialchars($datos['fechaUltimoIntento']) . '</td>
               </tr>';
}

$html8 .= '</tbody></table>';

// Imprimir el HTML en el PDF
$pdf->writeHTML($descripcionTablaPromedioGeneral, true, false, true, false, '');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->AddPage();
$pdf->writeHTML($descripcionTablaPromedioGeneral1, true, false, true, false, '');
$pdf->writeHTML($html2, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaPorMateria, true, false, true, false, '');
$pdf->writeHTML($html3, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaEspanol, true, false, true, false, '');
$pdf->writeHTML($html4, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaMate, true, false, true, false, '');
$pdf->writeHTML($html5, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaCiencias, true, false, true, false, '');
$pdf->writeHTML($html6, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaHistoria, true, false, true, false, '');
$pdf->writeHTML($html7, true, false, true, false, '');

$pdf->AddPage();
$pdf->writeHTML($descripcionTablaGeografia, true, false, true, false, '');
$pdf->writeHTML($html8, true, false, true, false, '');

// Cierra y envía el documento PDF
$pdf->Output('Reporte por grupo.pdf', 'I');


?>