<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header('Location:../login.php'); // Redirigir al login si no está logueado
    exit;
}

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
  <link rel="stylesheet" href="../build/css/examen.css">
  <script src="https://cdn.tailwindcss.com"></script>

  
  </head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#137271; margin:0;">
  <img src="../src/img/header001.png" alt="Logo" style="width: 280px; position:absolute; margin-top:0; top:0.5px; margin-left:0px">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 30rem;">
      <b>Simula</b>Score
    </a>
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../panel_control.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>

    </div>
</nav>

