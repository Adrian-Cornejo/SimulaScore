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

$calificacionEspanol= ($puntajeESCL+$puntajeES)/2;
$calificacionMatematicas =($puntajeMA+$puntajeMAT)/2;
$calificacionFce =$puntajeFCE;

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
   
    mostrarResultadoExamen($puntajeES, $resultadosES['respuestasCorrectas'], $totalPreguntasES,'Produccion de textos');
    mostrarResultadoExamen($puntajeESCL, $resultadosESCL['respuestasCorrectas'], $totalPreguntasESCL,'Comprencion lectora');
    mostrarResultadoExamen($puntajeMA, $resultadosMA['respuestasCorrectas'], $totalPreguntasMA  ,'Matematicas: Operaciones basicas');
    mostrarResultadoExamen($puntajeMAT, $resultadosMAT['respuestasCorrectas'], $totalPreguntasMAT,'Fracciones ');
    mostrarResultadoExamen($puntajeFCE, $resultadosFCE['respuestasCorrectas'], $totalPreguntasFCE,'Formacion civica y etica');
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
            $sql = $con->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2, respuesta3, respuesta4, texto_adicional, url_imagen, retroa FROM $tablaPreguntas WHERE id_pregunta = :idPregunta");
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
// Llamada a la función
mostrarAreasDeMejora($respuestasIncorrectasES, 'espanol', $con,'Español'); // Asegúrate de ajustar 'espanol' por el nombre real de tu tabla.

mostrarAreasDeMejora($respuestasIncorrectasESCL, 'espcomplec', $con, 'Comprencion lectora');

mostrarAreasDeMejora($respuestasIncorrectasMA, 'matematicas', $con,'Matematicas');

mostrarAreasDeMejora($respuestasIncorrectasMAT, 'matematicas_frac', $con,'Matematicas con fracciones');

mostrarAreasDeMejora($respuestasIncorrectasFCE, 'formacion_civica_etica', $con, 'Formacion civica y etica');
?>




</body>
</html>
