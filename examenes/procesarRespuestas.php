<?php
session_start();
$ultimoIdInsertado = $_SESSION['ultimoIdInsertado'];
require '../config/db.php';
$db = new db();
$con = $db->conexion();
echo $correoAlumno = $_SESSION['usuario'];

$sqlCodigo = $con->prepare("SELECT  codigoAlumno FROM alumno WHERE correo = :correoAlumno");
$sqlCodigo->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
$sqlCodigo->execute();
$alumno = $sqlCodigo->fetchAll(PDO::FETCH_ASSOC);


foreach ($alumno as $codigoAlumno) {
   echo  $codigoAlu = $codigoAlumno['codigoAlumno'];
}


$respuestasUsuario = json_decode($_POST['datosArray'], true);
$_SESSION['respuestasUsuario'] = $respuestasUsuario;
$puntaje = 0; // Variable para almacenar el puntaje del usuario
$respuestasCorrectas = 0; // Contador de respuestas correctas
$preguntasIncorrectas = [];

//print_r($respuestasUsuario);

$respuestasES = [];
$respuestasESCL = [];
$respuestasMA = [];
$respuestasMAT = [];
$respuestasFCE = [];


foreach ($respuestasUsuario as $respuestaUsuario) {
    // Extraer el ID de la pregunta y determinar si comienza con "ES-" o "ESCL-"
    $idPregunta = $respuestaUsuario['questionId'];

    if (strpos($idPregunta, "ES-") === 0) {
        // La clave comienza con "ES-", agregar al arreglo correspondiente
        $respuestasES[] = $respuestaUsuario;
    } elseif (strpos($idPregunta, "ESCL-") === 0) {
        // La clave comienza con "ESCL-", agregar al arreglo correspondiente
        $respuestasESCL[] = $respuestaUsuario;
    }elseif (strpos($idPregunta, "MA-") === 0) {
        // La clave comienza con "ESCL-", agregar al arreglo correspondiente
        $respuestasMA[] = $respuestaUsuario;
    }elseif (strpos($idPregunta, "MAT-") === 0) {
        // La clave comienza con "ESCL-", agregar al arreglo correspondiente
        $respuestasMAT[] = $respuestaUsuario;
    }elseif (strpos($idPregunta, "FCE-") === 0) {
        // La clave comienza con "ESCL-", agregar al arreglo correspondiente
        $respuestasFCE[] = $respuestaUsuario;
    }
}


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

        $resp = $aux[0]['respuesta_correcta'];

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




$tablaRespuestas = 'respuestas_correctas_espanol'; // Asume que esta es tu tabla de respuestas correctas para ES
$resultadosES = evaluarRespuestas($respuestasES, $tablaRespuestas, $con);
$totalPreguntasES = $resultadosES['totalPreguntas'];
$respuestasIncorrectasES = $resultadosES['preguntasIncorrectas'];

// Si tienes otro arreglo de respuestas para ESCL, por ejemplo:

$tablaRespuestasESCL = 'respuestas_espcomplec'; // Asume que esta es tu tabla de respuestas correctas para ESCL
$resultadosESCL = evaluarRespuestas($respuestasESCL, $tablaRespuestasESCL, $con);
$totalPreguntasESCL = $resultadosESCL['totalPreguntas'];
$respuestasIncorrectasESCL = $resultadosESCL['preguntasIncorrectas'];

$tablaRespuestasMA = 'respuestas_correctas_matematicas'; // Asume que esta es tu tabla de respuestas correctas para ESCL
$resultadosMA = evaluarRespuestas($respuestasMA, $tablaRespuestasMA, $con);
$totalPreguntasMA = $resultadosMA['totalPreguntas'];
$respuestasIncorrectasMA = $resultadosMA['preguntasIncorrectas'];

$tablaRespuestasMAT = 'respuestas_correctas_matematicas_frac'; // Asume que esta es tu tabla de respuestas correctas para ESCL
$resultadosMAT= evaluarRespuestas($respuestasMAT, $tablaRespuestasMAT, $con);
$totalPreguntasMAT = $resultadosMAT['totalPreguntas'];
$respuestasIncorrectasMAT = $resultadosMAT['preguntasIncorrectas'];

$tablaRespuestasFCE = 'respuestas_correctas_formacion_civica_etica'; // Asume que esta es tu tabla de respuestas correctas para ESCL
$resultadosFCE = evaluarRespuestas($respuestasFCE, $tablaRespuestasFCE, $con);
$totalPreguntasFCE = $resultadosFCE['totalPreguntas'];
$respuestasIncorrectasFCE = $resultadosFCE['preguntasIncorrectas'];




// Y así sucesivamente para manejar preguntas incorrectas y el total de preguntas.
$totalPreguntasCorrectas = $resultadosES['respuestasCorrectas']+$resultadosESCL['respuestasCorrectas']+ $resultadosMA['respuestasCorrectas']+
$resultadosMAT['respuestasCorrectas']+$resultadosFCE['respuestasCorrectas'];

$totalPreguntas = $totalPreguntasES + $totalPreguntasESCL + $totalPreguntasMA + $totalPreguntasMAT + $totalPreguntasFCE;


$puntaje = ($totalPreguntasCorrectas*10)/$totalPreguntas;
$puntajeES = ($resultadosES['respuestasCorrectas']*10)/$totalPreguntasES;
$puntajeESCL = ($resultadosESCL['respuestasCorrectas']*10)/$totalPreguntasESCL;
$puntajeMA = ($resultadosMA['respuestasCorrectas']*10)/$totalPreguntasMA;
$puntajeMAT = ($resultadosMAT['respuestasCorrectas']*10)/$totalPreguntasMAT;
$puntajeFCE = ($resultadosFCE['respuestasCorrectas']*10)/$totalPreguntasFCE;

ECHO $calificacionEspanol= ($puntajeESCL+$puntajeES)/2;
$calificacionMatematicas =($puntajeMA+$puntajeMAT)/2;
$calificacionFce =$puntajeFCE;


//print_r($respuestasUsuario);

$RespuestasJson = json_encode($respuestasUsuario);
echo $totalPreguntasCorrectas;
function guardarResultadosExamen($con, $codigoAlumno, $puntajeGeneral, $totalPreguntas, $correctasGeneral, $puntajeEspanol, $puntajeComprension, $puntajeMatematicas, $puntajeFracciones, $puntajeFce, $calificacionEspanol, $calificacionMatematicas, $calificacionFce, $RespuestasJson,$idExamen) {
    $fecha = date('Y-m-d');
    try {
         // Preparar la consulta de actualización
         $statement = $con->prepare("UPDATE resultados_examen_mejoredu SET fecha =:fecha, puntaje_general = :puntaje_general, total_preguntas = :total_preguntas, correctas_general = :correctas_general, puntaje_espanol = :puntaje_espanol, puntaje_comprension = :puntaje_comprension, puntaje_matematicas = :puntaje_matematicas, puntaje_fracciones = :puntaje_fracciones, puntaje_fce = :puntaje_fce, calificacionEspanol = :calificacionEspanol, calificacionMatematicas = :calificacionMatematicas, calificacionFce = :calificacionFce, respuestas = :respuestas, en_progreso = 0 WHERE id = :idExamen");
        


       // $statement = $con->prepare("INSERT INTO resultados_examen_mejoredu (codigoAlumno, fecha, puntaje_general, total_preguntas, correctas_general, puntaje_espanol, puntaje_comprension, puntaje_matematicas, puntaje_fracciones, puntaje_fce, calificacionEspanol, calificacionMatematicas, calificacionFce, respuestas) VALUES (:codigoAlumno, :fecha, :puntaje_general, :total_preguntas, :correctas_general, :puntaje_espanol, :puntaje_comprension, :puntaje_matematicas, :puntaje_fracciones, :puntaje_fce, :calificacionEspanol, :calificacionMatematicas, :calificacionFce, :respuestas)");
        
        
        $statement->bindParam(":fecha", $fecha);
        $statement->bindParam(":puntaje_general", $puntajeGeneral);
        $statement->bindParam(":total_preguntas", $totalPreguntas);
        $statement->bindParam(":correctas_general", $correctasGeneral);
        $statement->bindParam(":puntaje_espanol", $puntajeEspanol);
        $statement->bindParam(":puntaje_comprension", $puntajeComprension);
        $statement->bindParam(":puntaje_matematicas", $puntajeMatematicas);
        $statement->bindParam(":puntaje_fracciones", $puntajeFracciones);
        $statement->bindParam(":puntaje_fce", $puntajeFce);
        $statement->bindParam(":calificacionEspanol", $calificacionEspanol);
        $statement->bindParam(":calificacionMatematicas", $calificacionMatematicas);
        $statement->bindParam(":calificacionFce", $calificacionFce);
        $statement->bindParam(":respuestas", $RespuestasJson);
        $statement->bindParam(":idExamen", $idExamen);
        
        $statement->execute();
        return true;
    } catch (Exception $e) {
     error_log("Error al insertar en la base de datos: " . $e->getMessage());
         return false;
     }
}

$resultado = guardarResultadosExamen($con, $codigoAlu, $puntaje, $totalPreguntas, $totalPreguntasCorrectas, $puntajeES, $puntajeESCL, $puntajeMA, $puntajeMAT, $puntajeFCE, $calificacionEspanol, $calificacionMatematicas, $calificacionFce, $RespuestasJson, $ultimoIdInsertado);

if ($resultado) {
    // Redirigir a mostrarResultados.php si la inserción fue exitosa
    header("Location: mostrarResultados.php?codigoAlumno=" . urlencode($codigoAlu));
    exit;
} else {
    // Manejar el error, por ejemplo, mostrando un mensaje de error o redirigiendo a una página de error
    echo "Se produjo un error al guardar los resultados del examen.";
    // O puedes redirigir a una página de error específica
    // header("Location: paginaDeError.php");
    // exit;
}
?>
