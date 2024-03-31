<?php
session_start();
if (!isset($_SESSION['Maestro'])) {
    header('Location: ../loginMaestro.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>ProgresoAlumno</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../../build/css/directivos.css">
    <link rel="stylesheet" href="../../build/css/progresoAlumnos.css">

</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../panelControlMaestro.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="administrarAlumnos.php" style="padding: 1rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
    </div>
  </div>
</nav>


<h1>Buscar Alumnos por Nombre o Apellido</h1>
<div class="contenedor">
<input type="text" id="busqueda" placeholder="Ingresa nombre o apellido">
<div id="resultados"></div>
</div>

<div id="detallesAlumno"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#busqueda").keyup(function(){
        var termino = $(this).val();
        $.ajax({
            url: 'busquedaAlumnos.php', // Ruta al archivo PHP que realizará la búsqueda
            method: 'GET',
            data: {busqueda: termino},
            success: function(data) {
                $("#resultados").html(data);
            }
        });
    });
  });


  $(document).on('click', '#resultados li', function(){
    var codigoAlumno = $(this).data('id');
    $.ajax({
        url: 'busquedaDetallesAlumno.php', // Cambia a tu archivo PHP correcto
        method: 'GET',
        data: {codigoAlumno: codigoAlumno},
        success: function(data) {
            $('#detallesAlumno').html(data);
        }
    });
});

</script>
</body>
</html>