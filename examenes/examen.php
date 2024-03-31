<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location:../login.php'); // Redirigir al login si no está logueado
    exit;
}
$db = new db();
$con =$db->conexion();


$correoAlumno = $_SESSION['usuario'];
//Preparar la consulta sql
$sqlHelp = $con->prepare("SELECT codigoProfesor, nombre, apellido, codigoAlumno, escuela, codigoEscuela  FROM alumno WHERE correo = :correoAlumno");
$sqlHelp->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
$sqlHelp->execute();
$alumno = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
  <html lang="es">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulador de Exámenes y Hoja de Respuestas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../build/css/examen.css">
  
  </head>
  <body>

  <nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6; margin: 0;">
    <img src="../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="../panelControlDirectivo.php" style="font-size:1.8rem; padding:1rem;">Home</a>
          </li>
        </ul>
        <a href="../logoutDirectivo.php" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <h1>Olimpiada del Conocimiento Infantil 2022</h1>
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
  <div class="container">

  <form id="exam-form" method="post" action="procesarRespuestas.php">
  <div class="exam-section">
    <?php 
   
    $sqlHelp = $con->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2, respuesta3, respuesta4, texto_adicional,url_imagen FROM espanol ORDER BY RAND() LIMIT 20");
    $sqlHelp->execute();
    $resultado = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
    $i=1;
    foreach($resultado as $row): ?>
      <div class="question-section" data-question-id="<?php echo $row['id_pregunta']; ?>">
        <p class="question"><strong>Pregunta <?php echo $i; ?>:</strong> <?php echo htmlspecialchars($row['pregunta']); ?></p>
      
               <!-- Mostrar texto adicional si existe -->
        <?php if (!empty($row['texto_adicional'])): ?>
          <p class="additional-text"><?php echo htmlspecialchars($row['texto_adicional']); ?></p>
        <?php endif; ?>

        <!-- Mostrar imagen si existe -->
        <?php if (!empty($row['url_imagen'])): ?>
          <img src="<?php echo htmlspecialchars($row['url_imagen']); ?>" alt="Imagen relacionada" class="question-image">
        <?php endif; ?>


        <div class="answers-section" id-question="<?php echo $i; ?>">
          <label class="answer">
            <input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta1" data-answer-text="<?php echo htmlspecialchars($row['respuesta1']); ?>">
            A) <?php echo htmlspecialchars($row['respuesta1']); ?>
          </label>
          <label class="answer">
            <input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta2" data-answer-text="<?php echo htmlspecialchars($row['respuesta2']); ?>">
            B) <?php echo htmlspecialchars($row['respuesta2']); ?>
          </label>
          <label class="answer">
            <input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta3" data-answer-text="<?php echo htmlspecialchars($row['respuesta3']); ?>">
            C) <?php echo htmlspecialchars($row['respuesta3']); ?>
          </label>
          <label class="answer">
            <input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta4" data-answer-text="<?php echo htmlspecialchars($row['respuesta4']); ?>">
            D) <?php echo htmlspecialchars($row['respuesta4']); ?>
          </label>
        </div>
      </div>
    <?php $i++; endforeach; ?>
  </div>
  <input type="hidden" id="datosArray" name="datosArray">
  <button type="submit"id="submitButton" style="display:none;" ><p>Enviar respuestas</p></button>
</form>

  <div class="answer-sheet-section">
      <?php for($i = 1; $i <= count($resultado); $i++): ?>
        <div  data-question="<?php echo $i; ?>">
          <div class="flex items-center mb-2">
            <div class="font-bold mr-2"><?php echo $i; ?></div>
            <div class="flex-1">Pregunta</div>
          </div>
          <div class="flex justify-around">
            <div class="option" data-question="<?php echo $i; ?>" data-option="respuesta1">A</div>
            <div class="option" data-question="<?php echo $i; ?>" data-option="respuesta2">B</div>
            <div class="option" data-question="<?php echo $i; ?>" data-option="respuesta3">C</div>
            <div class="option" data-question="<?php echo $i; ?>" data-option="respuesta4">D</div>
          </div>
        </div>
      <?php endfor; ?>
    </div>






  </div>

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
  document.querySelectorAll('.exam-section .answer input[type="radio"]').forEach((input) => {
    input.addEventListener('change', function() {
      const questionNumber = this.closest('.answers-section').getAttribute('id-question');
      const selectedOption = this.value;

      // Encuentra todos los elementos de opción para la pregunta y limpia la selección
      const options = document.querySelectorAll(`.answer-sheet-section div[data-question="${questionNumber}"] .option`);
      if (options) {
        options.forEach(option => {
          option.classList.remove('selected');
        });
      }

      // Encuentra el elemento específico para marcar como seleccionado
      const selectedOptionElement = document.querySelector(`.answer-sheet-section div[data-question="${questionNumber}"] .option[data-option="${selectedOption}"]`);
      if (selectedOptionElement) {
        selectedOptionElement.classList.add('selected');
      } else {
        console.error('Elemento seleccionado no encontrado para la pregunta: ' + questionNumber + ' y opción: ' + selectedOption);
      }
    });
  });

  

  document.getElementById('exam-form').addEventListener('submit', function(event) {
  event.preventDefault(); // Previene el envío tradicional del formulario

  var totalQuestions = document.querySelectorAll('.question-section').length;
  var answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;

  // Verificar si todas las preguntas fueron respondidas
  if(answeredQuestions === totalQuestions) {
    var results = [];
    var questions = document.querySelectorAll('.question-section');
    questions.forEach(function(question, index) {
      var questionId = question.getAttribute('data-question-id');
      var selectedAnswer = document.querySelector(`input[name="answer-${questionId}"]:checked`);

      if(selectedAnswer) {
        results.push({
          questionId: questionId,
          answerValue: selectedAnswer.value,
          answerText: selectedAnswer.getAttribute('data-answer-text')
        });
      } else {
        results.push({
          questionId: questionId,
          answerValue: 'No respondida',
          answerText: ''
        });
      }
    });

    // Convertir el array a una cadena JSON y asignarlo al valor del campo oculto
    document.getElementById('datosArray').value = JSON.stringify(results);

    // Finalmente, enviar el formulario
    this.submit();
  } else {
    showModal();
  }
});
function showModal() {
  document.getElementById('confirmationModal').style.display = 'flex';
}

function hideModal() {
  document.getElementById('confirmationModal').style.display = 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const questionsPerPage = 5;
    const totalQuestions = document.querySelectorAll('.question-section').length;
    const totalPages = Math.ceil(totalQuestions / questionsPerPage);

    function showPage(page) {
        const start = (page - 1) * questionsPerPage;
        const end = start + questionsPerPage;
        document.querySelectorAll('.question-section').forEach((el, index) => {
            if (index >= start && index < end) {
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        });
        const submitButton = document.getElementById('submitButton');
    if(page === totalPages) {
        submitButton.style.display = '';
    } else {
        submitButton.style.display = 'none';
    }
    }

    function setupPagination() {
        // Aquí puedes agregar tu lógica para mostrar botones de paginación
        // Por simplicidad, voy a mostrar solo un ejemplo básico
        document.querySelector('#prevPage').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        document.querySelector('#nextPage').addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });
    }

    showPage(currentPage); // Muestra la primera página al cargar
    setupPagination(); // Configura la paginación
});


  </script>

  </body>
  </html>
