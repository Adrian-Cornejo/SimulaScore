<?php

session_start();
require '../config/db.php';
$db = new db();
$con = $db->conexion();
$correoAlumno = $_SESSION['usuario'];
if (isset($_SESSION['respuestasUsuario'])) {
    $respuestasUsuario = $_SESSION['respuestasUsuario'];
    // Ahora puedes usar $respuestasUsuario como necesites
    
} else {
    
   header("Location: paginaDeError.php");
}
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
    echo $totalPreguntasCorrectasMatematicas = $resultadosMatematicas['respuestasCorrectas'];
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados Examenes</title>
    <!-- Google Fonts, Bootstrap CSS, and Custom CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
    <link rel="stylesheet" href="../build/css/retroalimentacion.css"> 
    <link rel="stylesheet" href="../build/css/estilosAlumnos.css"> 
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#137271; margin:0;">
  <img src="../src/img/header001.png" alt="Logo" style="width: 280px; position:absolute; margin-top:0; top:0.5px; margin-left:0px">
  <div class="container-fluid">
    <a class="navbar-brand" href="../home/panel_control.php" style="font-size:3rem; padding:0.5rem; margin-left: 30rem;">
      <b>Simula</b>Score
    </a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 " >
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../home/panel_control.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="../home/logout.php" class="boton" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;">
        Cerrar sesión
      </a>
    </div>
  </div>
</nav>



<div class="">
    <?php
function mostrarResultadoExamen($puntaje, $totalPreguntasCorrectas, $totalPreguntas,$seccionExamen) {
    // Determinar la clase y el mensaje basados en el puntaje
    if ($puntaje < 7) {
        $clase = "reprobatorio";
        $mensaje = "Lo sentimos, no has aprobado el examen.";
    } elseif ($puntaje < 9) {
        $clase = "bueno";
        $mensaje = "¡Buen trabajo! Pero puedes mejorar.";
    } else {
        $clase = "excelente";
        $mensaje = "¡Excelente! Has demostrado un alto conocimiento en el tema.";
    }

    // Mostrar el bloque HTML con los resultados
    echo '<div class="margin">';
    echo '<h2 style="color:black;">' . $seccionExamen. '</h2>';
    echo '<div class="resultado ' . $clase . '">';
   
    echo '<h3 style="color:white;">' . $mensaje . '</h3>';
    echo '<p>Tu puntaje es: ' . $puntaje . '</p>';
    echo '<p>Respondiste correctamente ' . $totalPreguntasCorrectas . ' de ' . $totalPreguntas . ' preguntas.</p>';
    echo '</div>';
    echo '</div>';

}
    ?> 



<?php
   
   mostrarResultadoExamen($puntaje, $totalPreguntasCorrectas, $totalPreguntas,'Examen');
?>
    
    <div class="grid3columnas">
    <?php
mostrarResultadoExamen($puntajeMatematicas, $totalPreguntasCorrectasMatematicas, $totalPreguntasMatematicas, 'Matemáticas');

// Mostrar resultado para Español
mostrarResultadoExamen($puntajeEspanol, $totalPreguntasCorrectasEspanol, $totalPreguntasEspanol, 'Español');

// Mostrar resultado para Historia
mostrarResultadoExamen($puntajeHistoria, $totalPreguntasCorrectasHistoria, $totalPreguntasHistoria, 'Historia');

// Mostrar resultado para Ciencias Naturales
mostrarResultadoExamen($puntajeCienciasNaturales, $totalPreguntasCorrectasCienciasNaturales, $totalPreguntasCienciasNaturales, 'Ciencias Naturales');

// Mostrar resultado para Geografía
mostrarResultadoExamen($puntajeGeografia, $totalPreguntasCorrectasGeografia, $totalPreguntasGeografia, 'Geografía');

    ?>
    </div>




</div>


<div>
    <h2>Retroalimentacion de las preguntas incorrectas</h2>
</div>

<?php
function mostrarAreasDeMejora($preguntasIncorrectas, $tablaPreguntas, $con,$materia) {
    if (!empty($preguntasIncorrectas)) {
        echo '<div class="exam-section">';
        echo '<h3>Áreas de mejora: ' .$materia.'</h3>';

        foreach ($preguntasIncorrectas as $pregunta) {
            $id = $pregunta['idPregunta'];
            $sql = $con->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2, respuesta3, respuesta4, url_imagen, retroa FROM $tablaPreguntas WHERE id_pregunta = :idPregunta");
            $sql->bindParam(':idPregunta', $id, PDO::PARAM_STR);
            $sql->execute();
            $detallePregunta = $sql->fetch(PDO::FETCH_ASSOC);

            if ($detallePregunta) {
                echo '<div class="question-section" data-question-id="' . htmlspecialchars($detallePregunta['id_pregunta']) . '">';
                echo '<p class="question"><strong>Pregunta:</strong> ' . htmlspecialchars($detallePregunta['pregunta']) . '</p>';

                if (!empty($detallePregunta['texto_adicional'])) {
                    echo '<p class="additional-text">' . htmlspecialchars($detallePregunta['texto_adicional']) . '</p>';
                }

                if (!empty($detallePregunta['url_imagen'])) {
                    echo '<img src="' . htmlspecialchars($detallePregunta['url_imagen']) . '" alt="Imagen relacionada" class="question-image">';
                }

                echo '<div class="answers-section">';
                for ($i = 1; $i <= 4; $i++) {
                    $respuesta = 'respuesta'.$i;
                    $claseRespuesta = ($detallePregunta[$respuesta] == $pregunta['respuestaCorrecta']) ? 'correcta' : (($detallePregunta[$respuesta] == $pregunta['respuestaIncorrecta']) ? 'incorrecta' : '');

                    echo '<div class="resp"><p class="' . $claseRespuesta . '">' . chr(64 + $i) . ') ' . htmlspecialchars($detallePregunta[$respuesta]) . '</p></div>';
                }
                echo '</div>'; // Cierra answers-section

                if (!empty($detallePregunta['retroa'])) {
                    echo '<h3 style="margin-top:2rem; margin.bottom:0;">Retroalimentación</h3>';
                    echo '<p class="retroalimentacion">' . htmlspecialchars($detallePregunta['retroa']) . '</p>';
                }

                echo '</div>'; // Cierra question-section
            }
        }
        echo '</div>'; // Cierra exam-section
    }
}

?>


<?php
$tabla= 'examene_olimpiada_2021';

// Llamada a la función para Español
mostrarAreasDeMejora($respuestasIncorrectasEspanol, $tabla, $con, 'Español');

// Llamada a la función para Matemáticas
mostrarAreasDeMejora($respuestasIncorrectasMatematicas, $tabla, $con, 'Matemáticas');

// Llamada a la función para Ciencias
mostrarAreasDeMejora($respuestasIncorrectasCienciasNaturales, $tabla, $con, 'Ciencias');

// Llamada a la función para Geografía
mostrarAreasDeMejora($respuestasIncorrectasGeografia, $tabla, $con, 'Geografía');

// Llamada a la función para Historia
mostrarAreasDeMejora($respuestasIncorrectasHistoria, $tabla, $con, 'Historia');


?>




</body>
</html>
