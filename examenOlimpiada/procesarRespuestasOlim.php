<?php
session_start();
echo $ultimoIdInsertado = $_SESSION['ultimoIdInsertado'];
require '../config/db.php';
$db = new db();
$con = $db->conexion();
$correoAlumno = $_SESSION['usuario'];

$sqlCodigo = $con->prepare("SELECT  codigoAlumno FROM alumno WHERE correo = :correoAlumno");
$sqlCodigo->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
$sqlCodigo->execute();
$alumno = $sqlCodigo->fetchAll(PDO::FETCH_ASSOC);


foreach ($alumno as $codigoAlumno) {
   $codigoAlu = $codigoAlumno['codigoAlumno'];
}


$respuestasUsuario = json_decode($_POST['datosArray'], true);
$_SESSION['respuestasUsuario'] = $respuestasUsuario;
$puntaje = 0; // Variable para almacenar el puntaje del usuario
$respuestasCorrectas = 0; // Contador de respuestas correctas
$preguntasIncorrectas = [];

// print_r($respuestasUsuario);

$respuestasMatematicas = [];
$respuestasEspanol = [];
$respuestasHistoria = [];
$respuestasCienciasNaturales = [];
$respuestasGeografia = [];

foreach ($respuestasUsuario as $respuestaUsuario) {
    $idPregunta = $respuestaUsuario['questionId'];
    if (strpos($idPregunta, "MATE-") === 0) {
        $respuestasMatematicas[] = $respuestaUsuario;
    } elseif (strpos($idPregunta, "ESPA-") === 0) {
        $respuestasEspanol[] = $respuestaUsuario;
    } elseif (strpos($idPregunta, "HIST-") === 0) {
        $respuestasHistoria[] = $respuestaUsuario;
    } elseif (strpos($idPregunta, "CNAT-") === 0) {
        $respuestasCienciasNaturales[] = $respuestaUsuario;
    } elseif (strpos($idPregunta, "GEO-") === 0) {
        $respuestasGeografia[] = $respuestaUsuario;
    }
    // Continúa para las demás categorías
}

// echo '<pre>';
// print_r($respuestasMatematicas);
// echo '</pre>';






function evaluarRespuestas($respuestas, $tablaRespuestas, $con) {
    $respuestasCorrectas = 0;
    $preguntasIncorrectas = [];

    foreach ($respuestas as $respuestaUsuario) {
        $idPregunta = $respuestaUsuario['questionId'];
        $respuestaDada = $respuestaUsuario['answerText'];

        $sql = $con->prepare("SELECT respuesta_correcta FROM $tablaRespuestas WHERE id_pregunta = :idPregunta");
        $sql->bindParam(':idPregunta', $idPregunta, PDO::PARAM_STR);
        $sql->execute();
        $aux = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        if($aux){
            $resp = $aux[0]['respuesta_correcta'];
        
        }else{
        }

        // Comparar la respuesta del usuario con la respuesta correcta
        if ($resp == $respuestaDada) {
            $respuestasCorrectas++;
        } else {
            // Añadir al array tanto el ID de la pregunta, la respuesta incorrecta y la respuesta correcta
            $preguntasIncorrectas[] = [
                'idPregunta' => $idPregunta,
                'respuestaIncorrecta' => $respuestaDada,
                'respuestaCorrecta' => $resp
            ];
        }
    }

    return [
        'respuestasCorrectas' => $respuestasCorrectas,
        'preguntasIncorrectas' => $preguntasIncorrectas,
        'totalPreguntas' => count($respuestas)
    ];
}


$tablaRespuestas = 'resp_correctas_olimp'; // Asume que esta es tu tabla de respuestas correctas para ES

    $resultadosMatematicas = evaluarRespuestas($respuestasMatematicas, $tablaRespuestas, $con);
    $totalPreguntasMatematicas = $resultadosMatematicas['totalPreguntas'];
    $totalPreguntasCorrectas = $resultadosMatematicas['respuestasCorrectas'];
    $respuestasIncorrectasMatematicas = $resultadosMatematicas['preguntasIncorrectas'];

    // Evaluar respuestas de Español
    $resultadosEspanol = evaluarRespuestas($respuestasEspanol, $tablaRespuestas, $con);
    $totalPreguntasEspanol = $resultadosEspanol['totalPreguntas'];
    $totalPreguntasCorrectasEspanol = $resultadosEspanol['respuestasCorrectas'];
    $respuestasIncorrectasEspanol = $resultadosEspanol['preguntasIncorrectas'];

    // Evaluar respuestas de Historia
    $resultadosHistoria = evaluarRespuestas($respuestasHistoria, $tablaRespuestas, $con);
    $totalPreguntasHistoria = $resultadosHistoria['totalPreguntas'];
    $totalPreguntasCorrectasHistoria = $resultadosHistoria['respuestasCorrectas'];
    $respuestasIncorrectasHistoria = $resultadosHistoria['preguntasIncorrectas'];

    // Evaluar respuestas de Ciencias Naturales
    $resultadosCienciasNaturales = evaluarRespuestas($respuestasCienciasNaturales, $tablaRespuestas, $con);
    $totalPreguntasCienciasNaturales = $resultadosCienciasNaturales['totalPreguntas'];
    $totalPreguntasCorrectasCienciasNaturales = $resultadosCienciasNaturales['respuestasCorrectas'];
    $respuestasIncorrectasCienciasNaturales = $resultadosCienciasNaturales['preguntasIncorrectas'];

    // Evaluar respuestas de Geografía
    $resultadosGeografia = evaluarRespuestas($respuestasGeografia,$tablaRespuestas, $con);
    $totalPreguntasGeografia = $resultadosGeografia['totalPreguntas'];
    $totalPreguntasCorrectasGeografia = $resultadosGeografia['respuestasCorrectas'];
    $respuestasIncorrectasGeografia = $resultadosGeografia['preguntasIncorrectas'];





// Sumar todas las respuestas correctas de cada materia
$totalPreguntasCorrectas = $resultadosMatematicas['respuestasCorrectas'] + $resultadosEspanol['respuestasCorrectas'] + $resultadosHistoria['respuestasCorrectas'] + $resultadosCienciasNaturales['respuestasCorrectas'] + $resultadosGeografia['respuestasCorrectas'];

// Sumar el total de preguntas de todas las materias
$totalPreguntas = $totalPreguntasMatematicas + $totalPreguntasEspanol + $totalPreguntasHistoria + $totalPreguntasCienciasNaturales + $totalPreguntasGeografia;

// Calcular el puntaje general del examen
$puntaje = ($totalPreguntasCorrectas * 10) / $totalPreguntas;

// Calcular el puntaje por materia
$puntajeMatematicas = ($resultadosMatematicas['respuestasCorrectas'] * 10) / $totalPreguntasMatematicas;
$puntajeEspanol = ($resultadosEspanol['respuestasCorrectas'] * 10) / $totalPreguntasEspanol;
$puntajeHistoria = ($resultadosHistoria['respuestasCorrectas'] * 10) / $totalPreguntasHistoria;
$puntajeCienciasNaturales = ($resultadosCienciasNaturales['respuestasCorrectas'] * 10) / $totalPreguntasCienciasNaturales;
$puntajeGeografia = ($resultadosGeografia['respuestasCorrectas'] * 10) / $totalPreguntasGeografia;

// Calcular las calificaciones promedio (si aplica)
// Si tienes materias con sub-categorías y necesitas calcular un promedio, puedes hacerlo aquí.
// En este caso, cada materia se evalúa de manera independiente, así que simplemente asignamos el puntaje directamente a la calificación.

$calificacionMatematicas = $puntajeMatematicas;
$calificacionEspanol = $puntajeEspanol;
$calificacionHistoria = $puntajeHistoria;
$calificacionCienciasNaturales = $puntajeCienciasNaturales;
$calificacionGeografia = $puntajeGeografia;

// // Mostrar los puntajes y calificaciones
// echo "Calificación Matemáticas: $calificacionMatematicas\n";
// echo "Calificación Español: $calificacionEspanol\n";
// echo "Calificación Historia: $calificacionHistoria\n";
// echo "Calificación Ciencias Naturales: $calificacionCienciasNaturales\n";
// echo "Calificación Geografía: $calificacionGeografia\n";
// echo "Puntaje General del Examen: $puntaje\n";



//print_r($respuestasUsuario);

$RespuestasJson = json_encode($respuestasUsuario);
$totalPreguntasCorrectas;

function guardarResultadosExamenOlimpiada($con, $codigoAlumno, $puntajeGeneral, $totalPreguntas, $correctasGeneral, $calificacionMatematicas, $calificacionEspanol, $calificacionHistoria, $calificacionCiencias, $calificacionGeografia, $RespuestasJson) {
    $fecha = date('Y-m-d');
     try {
        // Preparar la consulta de inserción
        $statement = $con->prepare("UPDATE resultados_examen_olimpiada SET fecha = :fecha, puntaje_general = :puntaje_general, total_preguntas = :total_preguntas, correctas_general = :correctas_general, calificacionMatematicas = :calificacionMatematicas, calificacionEspanol = :calificacionEspanol, calificacionHistoria = :calificacionHistoria, calificacionCiencias = :calificacionCiencias, calificacionGeografia = :calificacionGeografia, respuestas = :respuestas, en_progreso = 0 WHERE id = :idExamen");

        // Vincular los parámetros a la consulta
        $statement->bindParam(":fecha", $fecha);
        $statement->bindParam(":puntaje_general", $puntajeGeneral);
        $statement->bindParam(":total_preguntas", $totalPreguntas);
        $statement->bindParam(":correctas_general", $correctasGeneral);
        $statement->bindParam(":calificacionMatematicas", $calificacionMatematicas);
        $statement->bindParam(":calificacionEspanol", $calificacionEspanol);
        $statement->bindParam(":calificacionHistoria", $calificacionHistoria);
        $statement->bindParam(":calificacionCiencias", $calificacionCiencias);
        $statement->bindParam(":calificacionGeografia", $calificacionGeografia);
        $statement->bindParam(":respuestas", $RespuestasJson);
        $statement->bindParam(":idExamen", $idExamen);

        // Ejecutar la consulta
        $statement->execute();
        return true;
        return true;
     } catch (Exception $e) {
        
         error_log("Error al insertar en la base de datos: " . $e->getMessage());
         return false;
     }
}


$resultado = guardarResultadosExamenOlimpiada($con, $codigoAlu, $puntaje, $totalPreguntas, $totalPreguntasCorrectas, $calificacionMatematicas, $calificacionEspanol, $calificacionHistoria, $calificacionCienciasNaturales, $calificacionGeografia, $RespuestasJson);
echo $totalPreguntasCorrectas;
if ($resultado) {
    // Redirigir a mostrarResultados.php si la inserción fue exitosa
    header("Location: mostrarResultadosOlimpiada.php?codigoAlumno=" . urlencode($codigoAlu));
    exit;
} else {
    // Manejar el error, por ejemplo, mostrando un mensaje de error o redirigiendo a una página de error
    echo "Se produjo un error al guardar los resultados del examen.";
    // O puedes redirigir a una página de error específica
    // header("Location: paginaDeError.php");
    // exit;
}
?>
