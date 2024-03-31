<?php

require '../../config/db.php';
$db = new db();
$con = $db->conexion();
if(isset($_GET['codigoExamen'])) {
    $codigoExamen = $_GET['codigoExamen'];  
   
    $sqlresultados = $con->prepare("SELECT * FROM resultados_examen_mejoredu WHERE id = :id");
    $sqlresultados->bindParam(':id', $codigoExamen, PDO::PARAM_STR);
    $sqlresultados->execute();
    $resultadosExamen = $sqlresultados->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultadosExamen as $resultado) {
        echo '<p>Fecha: <span>' . $resultado['fecha'] . '</span></p>';
       
        

        $respuestaJSON = $resultado['respuestas'];
        $respuestaArray = json_decode($respuestaJSON, true);


    }

        $respuestasES = [];
        $respuestasESCL = [];
        $respuestasMA = [];
        $respuestasMAT = [];
        $respuestasFCE = [];


foreach ($respuestaArray as $respuestaUsuario) {
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

 
function mostrarAreasDeMejora($preguntas, $tablaPreguntas, $con, $materia, $tablaRespuestas) {
  
    if (!empty($preguntas)) {
        echo '<div class="exam-section">';
        echo '<h3>Áreas de mejora: ' . $materia . '</h3>';

        foreach ($preguntas as $pregunta) {
            $id = $pregunta['questionId'];
            $sql = $con->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2, respuesta3, respuesta4, texto_adicional, url_imagen, retroa FROM $tablaPreguntas WHERE id_pregunta = :idPregunta");
            $sql->bindParam(':idPregunta', $id, PDO::PARAM_STR);
            $sql->execute();
            $detallePregunta = $sql->fetch(PDO::FETCH_ASSOC);


            $sqlaux = $con->prepare("SELECT respuesta_correcta FROM $tablaRespuestas WHERE id_pregunta = :idPregunta");
            $sqlaux->bindParam(':idPregunta', $id, PDO::PARAM_STR);
            $sqlaux->execute();
            $aux = $sqlaux->fetchAll(PDO::FETCH_ASSOC);
    
            $resp = $aux[0]['respuesta_correcta'];
            $pregunta['answerText'];
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
                    $claseRespuesta = '';
                    if($resp !=$detallePregunta[$respuesta] && $pregunta['answerText']!=$detallePregunta[$respuesta]){
                        $claseRespuesta = '';  
                    }elseif ($resp == $detallePregunta[$respuesta]) {
                        $claseRespuesta = 'correcta';
                    }else{
                        $claseRespuesta = 'incorrecta';
                    }




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


echo 
    
mostrarAreasDeMejora($respuestasES, 'espanol', $con,'Español', 'respuestas_correctas_espanol'); // Asegúrate de ajustar 'espanol' por el nombre real de tu tabla.

mostrarAreasDeMejora($respuestasESCL, 'espcomplec', $con, 'Comprencion lectora','respuestas_espcomplec');

mostrarAreasDeMejora($respuestasMA, 'matematicas', $con,'Matematicas','respuestas_correctas_matematicas');

mostrarAreasDeMejora($respuestasMAT, 'matematicas_frac', $con,'Matematicas con fracciones','respuestas_correctas_matematicas_frac');

mostrarAreasDeMejora($respuestasFCE, 'formacion_civica_etica', $con, 'Formacion civica y etica','respuestas_correctas_formacion_civica_etica');



} else {
    echo "Error: No se recibió el ID del intento del examen";
}

?>
