<?php include('../header-footer/headerAlumno.php');
require '../config/db.php';
$db = new db();
$con =$db->conexion();


$correoAlumno = $_SESSION['usuario'];
//Preparar la consulta sql
$sqlHelp = $con->prepare("SELECT codigoProfesor, nombre, apellido, codigoAlumno, escuela, codigoEscuela  FROM alumno WHERE correo = :correoAlumno");
$sqlHelp->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
$sqlHelp->execute();
$alumno = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);


$fecha = date('Y-m-d');
$codigoAlumno = $alumno[0]['codigoAlumno'];
$sqlInsert = $con->prepare("INSERT INTO resultados_examen_olimpiada (codigoAlumno, en_progreso,fecha) VALUES (:codigoAlumno, 1,:fecha)");
$sqlInsert->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
$sqlInsert->bindParam(':fecha', $fecha);
$sqlInsert->execute();

echo $ultimoIdInsertado = $con->lastInsertId();

$_SESSION['ultimoIdInsertado'] = $ultimoIdInsertado; // Guarda el ID en la sesión


?>

<div class="cronometro" id="cronometro">00:00:00</div>


<h1 style="font-size: 3rem; color:black;">Examen MejorEdu 2022</h1>
  <?php foreach($alumno as $row){ ?>
  <div class="info-section">
    <div class="info-group">
      <span class="label">Alumno(a):</span>
      <span class="value"><?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></span>
    </div>
    <div class="info-group">
      <span class="label">Codigo alumno:</span>
      <span class="value"><?php echo htmlspecialchars($row['codigoAlumno']); ?></span>
    </div>
    <div class="info-group">
      <span class="label">Escuela:</span>
      <span class="value"><?php echo htmlspecialchars($row['escuela']); ?></span>
    </div>
 
  </div>
  <?php } ?>

  <main>
  

  <?php
  function generarFormularioExamen($nombreTabla, $conexion, $materia,$numPag) {
echo $materia;
    //ORDER BY RAND()
    // Preparar y ejecutar la consulta SQL
    $sqlHelp = $conexion->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2, respuesta3, respuesta4, url_imagen FROM $nombreTabla WHERE materia = :materia");
    $sqlHelp->bindParam(':materia', $materia);
    $sqlHelp->execute();
    $resultado = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<div class="container exam-container" id="exam-container-' . $numPag . '">';
    // Comenzar el formulario
    echo '<form id="exam-form-' . htmlspecialchars($nombreTabla) . '" method="post" action="procesarRespuestas.php">';
    echo '<div class="exam-section " id="exam-section-'.$numPag.'">';
    
    // Variables para iterar
    $i = 1;
    echo '<p> Seccion ' . $nombreTabla. ' </p>';
    // Generar las preguntas
    foreach ($resultado as $row) {
        // Sección de pregunta
        echo '<div class="question-section" data-question-id="' . htmlspecialchars($row['id_pregunta']) . '">';
        echo '<p class="question"><strong>Pregunta ' . $i . ':</strong> ' . htmlspecialchars($row['pregunta']) . '</p>';

        // Texto adicional
        if (!empty($row['texto_adicional'])) {
            echo '<p class="additional-text">' . htmlspecialchars($row['texto_adicional']) . '</p>';
        }

        // Imagen
        if (!empty($row['url_imagen'])) {
            echo '<img src="' . htmlspecialchars($row['url_imagen']) . '" alt="Imagen relacionada" class="question-image">';
        }

        // Opciones de respuesta
        echo '<div class="answers-section" id-question="' . $i . '">';
        for ($j = 1; $j <= 4; $j++) {
            $respuesta = 'respuesta' . $j;
            echo '<label class="answer">';
            echo '<input type="radio" name="answer-' . $row['id_pregunta'] . '" value="' . $respuesta . '" data-answer-text="' . htmlspecialchars($row[$respuesta]) . '">';
            echo chr(64 + $j) . ') ' . htmlspecialchars($row[$respuesta]);
            echo '</label>';
        }
        echo '</div></div>'; // Fin de sección de pregunta
        $i++;
    }

    echo '</div>'; // Fin de exam-section
    echo '<input type="hidden" id="datosArray" name="datosArray">';
    echo '<button type="submit" id="submitButton" style="display:none;"><p>Enviar respuestas</p></button>';

    
    echo '</form>'; // Fin de formulario

    // Generar la hoja de respuestas
    echo '<div class="answer-sheet-section " id="answer-sheet-section-'.$numPag.'">';
    
    for($i = 1; $i <= count($resultado); $i++) {
        echo '<div data-question="' . $i . '">';
        echo '<div class="flex items-center mb-2">';
        echo '<div class="font-bold mr-2">' . $i . '</div>';
        echo '<div class="flex-1">Pregunta</div>';
        echo '</div>';
        echo '<div class="flex justify-around">';
        for ($j = 1; $j <= 4; $j++) {
            echo '<div class="option" data-question="' . $i . '" data-option="respuesta' . $j . '">' . chr(64 + $j) . '</div>';
        }
        echo '</div></div>'; // Fin de cada pregunta en la hoja de respuestas
    }
    echo '</div>'; // Fin de answer-sheet-section
    echo '</div>'; // Fin de answer-sheet-section

}


  ?>



<!-- Sección de español -->
<div class="exam-section-container" id="exam-section-1">
  <?php generarFormularioExamen('examene_olimpiada_2021', $con, 'Matemáticas',1); ?>
</div>
<!-- Sección de comprensión lectora -->
<div class="exam-section-container" id="exam-section-2">
  <?php generarFormularioExamen('examene_olimpiada_2021', $con, 'Español',2); ?>
</div>
<!-- Sección de matemáticas -->
<div class="exam-section-container" id="exam-section-3">
  <?php generarFormularioExamen('examene_olimpiada_2021', $con, 'Historia',3); ?>
</div>
<!-- Sección de matemáticas fracciones -->
<div class="exam-section-container" id="exam-section-4">
  <?php generarFormularioExamen('examene_olimpiada_2021', $con, 'Ciencias Naturales',4); ?>
</div>
<!-- Sección de formación cívica y ética -->
<div class="exam-section-container" id="exam-section-5">
  <?php generarFormularioExamen('examene_olimpiada_2021', $con, 'Geografia',5); ?>
</div>



  </div> <!--Fin class contenedores-->

  <button id="submitAll">Enviar Todo el Examen</button>


  <div class="contenedor-Botones">
  <button   id="prevPage">Anterior</button>
<button id="nextPage">Siguiente</button>
</div>
  <div id="confirmationModal" class="modal-container" style="display:none;">
      <div class="modal-content">
          <h2>Confirmación</h2>
          <p>Por favor, responde a todas las preguntas.</p>
          <button class="button" id="cancelBtn" onclick="hideModal()">Cerrar</button>
      </div>
    </div>
  </main>

  <script>

    // Funcion para la paginacion
    document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const totalPages = document.querySelectorAll('.exam-section-container').length;
    updatePageVisibility();

    document.getElementById('nextPage').addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            updatePageVisibility();
            // Desplazar hacia el inicio de la página
            window.scrollTo(0, 0);
        }
    });

    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updatePageVisibility();
            // Desplazar hacia el inicio de la página
            window.scrollTo(0, 0);
        }
    });

    function updatePageVisibility() {
    document.querySelectorAll('.exam-section-container').forEach((section, index) => {
        if (index + 1 === currentPage) {
            section.style.display = 'block'; // Muestra la sección actual
        } else {
            section.style.display = 'none'; // Oculta las demás secciones
        }
    });

    // Actualizar el estado de los botones de navegación
    document.getElementById('prevPage').disabled = (currentPage === 1);
    document.getElementById('nextPage').disabled = (currentPage === totalPages);

    // Mostrar el botón de "Enviar Examen" solo en la última página
    if(currentPage === totalPages) {
        document.getElementById('submitAll').style.display = 'block';
    } else {
        document.getElementById('submitAll').style.display = 'none';
    }
}

});
document.querySelectorAll('.exam-section .answer input[type="radio"]').forEach((input) => {
    input.addEventListener('change', function() {
        const questionNumber = this.closest('.answers-section').getAttribute('id-question');
        const selectedOption = this.value;
        const examSectionId = this.closest('.exam-section').id;
        const numPag = examSectionId.split('-').pop();

        // Encuentra la hoja de respuestas correspondiente usando numPag
        const answerSheetSection = document.querySelector(`#answer-sheet-section-${numPag}`);
        const options = answerSheetSection.querySelectorAll(`div[data-question="${questionNumber}"] .option`);
        
        if (options) {
            options.forEach(option => {
                option.classList.remove('selected');
            });
        }

        // Marca la opción seleccionada
        const selectedOptionElement = answerSheetSection.querySelector(`div[data-question="${questionNumber}"] .option[data-option="${selectedOption}"]`);
        if (selectedOptionElement) {
            selectedOptionElement.classList.add('selected');
        } else {
            console.error('Elemento seleccionado no encontrado para la pregunta: ' + questionNumber + ' y opción: ' + selectedOption);
        }
    });
});

// enviar todas las respuestas del formulario
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('submitAll').addEventListener('click', function() {
        const allResults = [];
        let allQuestionsAnswered = true;

        // Seleccionar todos los formularios de examen
        document.querySelectorAll('form[id^="exam-form-"]').forEach(function(form) {
            const questions = form.querySelectorAll('.question-section');
            questions.forEach(function(question) {
                const questionId = question.getAttribute('data-question-id');
                const selectedAnswer = question.querySelector('input[type="radio"]:checked');
                
                if (selectedAnswer) {
                    allResults.push({
                        questionId: questionId,
                        answerValue: selectedAnswer.value,
                        answerText: selectedAnswer.getAttribute('data-answer-text'),
                    });
                } else {
                    allQuestionsAnswered = false;
                }
            });
        });

        if (!allQuestionsAnswered) {
            showModal();
            return;
        }

        // Opción 1: Enviar datos mediante un formulario oculto
        const hiddenForm = document.createElement('form');
        hiddenForm.method = 'POST';
        hiddenForm.action = 'procesarRespuestasOlim.php'; // Ajusta la acción según sea necesario
        hiddenForm.innerHTML = `<input type="hidden" name="datosArray" value='${JSON.stringify(allResults)}'>`;
        document.body.appendChild(hiddenForm);
        hiddenForm.submit();

      
    });
});



function showModal() {
  document.getElementById('confirmationModal').style.display = 'flex';
}

function hideModal() {
  document.getElementById('confirmationModal').style.display = 'none';
}


let tiempo = { horas: 0, minutos: 0, segundos: 0 };
let cronometroID = null;
let tiempoTotalHoras = 5; // Define aquí el total de horas que deseas para el cronómetro
let avisarAntesDeFinalizar = 10; // Minutos antes de finalizar para enviar el aviso

function iniciarCronometro() {
  if (cronometroID !== null) return; // Previene múltiples intervalos en funcionamiento

  cronometroID = setInterval(() => {
    tiempo.segundos++;
    if (tiempo.segundos >= 60) {
      tiempo.minutos++;
      tiempo.segundos = 0;
    }
    if (tiempo.minutos >= 60) {
      tiempo.horas++;
      tiempo.minutos = 0;

      // Aviso cada hora
      alert("Ha pasado una hora.");
    }

    // Comprobar si faltan 'avisarAntesDeFinalizar' minutos para el final
    if (tiempo.horas === tiempoTotalHoras - 1 && tiempo.minutos === (60 - avisarAntesDeFinalizar)) {
      alert(`Quedan ${avisarAntesDeFinalizar} minutos.`);
    }

    // Detener el cronómetro al alcanzar el tiempo total definido
    if (tiempo.horas === tiempoTotalHoras) {
      detenerCronometro();
      alert("Tiempo finalizado.");
    }

    actualizarCronometro();
  }, 1000);
}

function detenerCronometro() {
  clearInterval(cronometroID);
  cronometroID = null;
}

function actualizarCronometro() {
  const { horas, minutos, segundos } = tiempo;
  document.getElementById('cronometro').textContent = 
    `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
}

// Iniciar automáticamente cuando la página se termine de cargar
document.addEventListener('DOMContentLoaded', iniciarCronometro);

window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    // Chrome requiere que se establezca el valor de returnValue.
    e.returnValue = '¿Estás seguro de que quieres salir? El progreso de tu examen se perderá.';
});

document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        alert('Has cambiado de pestaña. Por favor, mantente en el examen.');
    }
});
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
}, false);

document.addEventListener('keydown', function(e) {
    // Deshabilita F5, Ctrl+R, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r') || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) || (e.ctrlKey && e.key === 'u')) {
        e.preventDefault();
    }
});
  </script>

  </body>
  </html>
