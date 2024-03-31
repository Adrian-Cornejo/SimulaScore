<?php
require '../config/db.php';
$db = new db();
$con = $db->conexion();

// Preparar la consulta sql
$sqlHelp = $con->prepare("SELECT id_pregunta, pregunta, respuesta1, respuesta2,respuesta3,respuesta4,texto_adicional,url_imagen FROM espanol");
$sqlHelp->execute();
$resultado = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
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
    
<style>

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6 margin">
  <img src="../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
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

<div class="info-section">
  

  <div class="info-group">
    <span class="label">Alumno(a):</span>
    <span class="value">Nombre del Alumno</span>
  </div>

  <div class="info-group">
    <span class="label">Grupo:</span>
    <span class="value">Grupo del Alumno</span>
  </div>

  <div class="info-group">
    <span class="label">Escuela:</span>
    <span class="value">Nombre de la Escuela</span>
  </div>

  <!-- Más información según sea necesario -->
</div>


<main>
<div class="container">
<form id="exam-form" method="post" action="procesarRespuestas.php">
  <div class="exam-section">
    <?php 
    $questionNumber = 1; // Contador para el número de pregunta
    foreach($resultado as $row): ?>
      <div class="question-section" data-question-id="<?php echo $row['id_pregunta']; ?>">
        <p class="question"><strong>Pregunta <?php echo $questionNumber; ?>:</strong> <?php echo htmlspecialchars($row['pregunta']); ?></p>

        <!-- Mostrar texto adicional si existe -->
        <?php if (!empty($row['texto_adicional'])): ?>
          <p class="additional-text"><?php echo htmlspecialchars($row['texto_adicional']); ?></p>
        <?php endif; ?>

        <!-- Mostrar imagen si existe -->
        <?php if (!empty($row['url_imagen'])): ?>
          <img src="<?php echo htmlspecialchars($row['url_imagen']); ?>" alt="Imagen relacionada" class="question-image">
        <?php endif; ?>

        <div class="answers-section">
          <label class="answer"><input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta1">A) <?php echo htmlspecialchars($row['respuesta1']); ?></label>
          <label class="answer"><input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta2">B) <?php echo htmlspecialchars($row['respuesta2']); ?></label>
          <label class="answer"><input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta3">C) <?php echo htmlspecialchars($row['respuesta3']); ?></label>
          <label class="answer"><input type="radio" name="answer-<?php echo $row['id_pregunta']; ?>" value="respuesta4">D) <?php echo htmlspecialchars($row['respuesta4']); ?></label>
        </div>
      </div>
    <?php 
    $questionNumber++;
    endforeach; ?>
  </div>
  <input type="hidden" id="datosArray" name="datosArray">
  <button type="submit">Enviar respuestas</button>
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


<form id="miFormulario" action="procesarRespuestas.php" method="post">
    <!-- Campo oculto para los datos del array -->
    <input type="hidden" name="datosArray" id="datosArray">
    <button type="submit">Enviar Datos</button>
</form>






<script>
document.querySelectorAll('.exam-section .answer input[type="radio"]').forEach((input) => {
  input.addEventListener('change', function() {
    const questionNumber = this.name.split('-')[1]; // Obtiene el número de la pregunta
  
    const selectedOption = this.value; // Obtiene el valor seleccionado

    // Limpia la selección anterior en la hoja de respuestas para esta pregunta
    document.querySelectorAll(`.answer-sheet-section div[data-question="${questionNumber}"] .option`).forEach((option) => {
      option.classList.remove('selected'); // Remueve la clase 'selected' de todas las opciones
    });

    // Marca la opción seleccionada en la hoja de respuestas
    document.querySelector(`.answer-sheet-section div[data-question="${questionNumber}"] .option[data-option="${selectedOption}"]`).classList.add('selected');
  });
});


// agrupacion de todos las respuestas

document.getElementById('exam-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Previene el envío tradicional del formulario

    var results = []; // Almacenará objetos con el id de la pregunta y la respuesta seleccionada
    var questions = document.querySelectorAll('.question-section');
    questions.forEach(function(question) {
        var questionId = question.getAttribute('data-question-id'); // Obtiene el id de la pregunta
        var selectedAnswer = document.querySelector(`input[name="answer-${questionId}"]:checked`);
        
        if(selectedAnswer) {
            results.push({ questionId: questionId, answerValue: selectedAnswer.value });
        } else {
            results.push({ questionId: questionId, answerValue: 'No respondida' }); // O lo que prefieras para indicar una pregunta no respondida
        }
    });

    // Convertir el array a una cadena JSON y asignarlo al valor del campo oculto
    document.getElementById('datosArray').value = JSON.stringify(results);

    // Ahora enviamos el formulario manualmente
    this.submit();
});








</script>
</main>
</body>
</html>
