<?php
        session_start();
        
    require '../config/db.php';
   
    $db = new db();
    $con =$db->conexion();

    
    $correoDirectivo = $_SESSION['correo'];
    //Preparar la consulta sql
    $sqlHelp = $con->prepare("SELECT nombre, apellido, codigoDirectivo, correo, escuela, codigoEscuela, urlImagen FROM directivo WHERE correo = :correoDirectivo");
    $sqlHelp->bindParam(':correoDirectivo', $correoDirectivo, PDO::PARAM_STR);
    $sqlHelp->execute();
    $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>

    <!-- Google Fonts and Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../build/css/directivos.css">
</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;">
      <b>Simula</b>Score
    </a>


      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="logoutDirectivo.php" class="boton" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;">
        Cerrar sesión
      </a>

  </div>
</nav>


<!-- Main Content -->
<main>
    <?php foreach($resultadoaux as $row){ ?>
    <div class="contenedor__informacion">
        <h1>Bienvenido <?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></h1>
    </div>
    <div class="controles-carrusel">
    <button id="btnExamen1">Examen MEJOREDU </button>
    <button id="btnExamen2">Examen Olimpiadas</button>
</div>
    
    <div class="grid contenedor"> <!--Incio grid 2 columnas-->

        <div>
        <a href="./functionDirectivo/perfilDirectivo.php">
            <div class="contenedorPerfil">
                <div class="card user-info">
                    <img src="<?php echo htmlspecialchars($row['urlImagen']); ?>" class="profile-img"  alt="Student profile" class="profile-img">
                    <p class="student-name"><?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></p>
                    <p  style="color: #687483" class="student-role">Directivo </p>
                    <div class="info-group">
                        <div class="info">
                            <p style="color: #687483" class="label">Codigo Directivo</p>
                            <p class="value"><?php echo htmlspecialchars($row['codigoDirectivo']); ?></p>
                        </div>
                        <div class="info">
                            <p  style="color: #687483" class="label">Codigo Trabajo</p>
                            <p class="value"><?php echo htmlspecialchars($row['codigoEscuela']); ?></p>
                        </div>
                    </div>
                    <div class="program">
                        <p style="color: #687483" class="label">Correo</p>
                        <p class="value"><?php echo htmlspecialchars($row['correo']); ?></p>
                    </div>
                    <div class="program">
                        <p style="color: #687483" class="label">INSTITUCIÓN</p>
                        <p class="value"><?php echo htmlspecialchars($row['escuela']); ?></p>
                    </div>
                </div>
                <?php } ?>
            </div> <!--Fin del contnedorPerfil-->
        </a>


        
        </div> <!--Fin de la primera columna-->

        <div class="segundaColumna"> <!--Incio segunda columna-->
                
            
            
                <div id="examen1" class="contenedor-examen">
                <div class="grid2columnas">
    
    
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/administrarDocentes.php">
                        <div class="no-image">
                            <img src="../src/img/examen olimpiada del conocimiento.jpeg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Administrar progreso por docentes MEJOREDU</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/verAlumnos.php">
                        <div class="no-image">
                            <img src="../src/img/beneficios-estudiantes-min.jpg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Administrar progreso del alumno: MEJOREDU</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
                
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/verExamenesProgesoDirectivo.php">
                        <div class="no-image">
                            <img src="../src/img/Examen_Prueba_Enlace-2.jpg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Ver examenes activos MEJOREDU</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="#">
                        <div class="no-image">
                            <img src="../src/img/grupo.jpeg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Funcion Pendiente</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
    </div>
    </div>
    
    
    
    
                
                <div id="examen2" class="contenedor-examen" style="display: none;">
                <div class="grid2columnas">
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/resultadosOlimpiada/administrarDocentesOlimpiada.php">
                        <div class="no-image">
                            <img src="../src/img/alumnosOlimpiada.jpg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Administrar Progreso por docentes:Olimpiada</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/resultadosOlimpiada/verAlumnosOlimpiada.php">
                        <div class="no-image">
                            <img src="../src/img/olim.jpeg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Administrar progreso del alumno: Olimpiada</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
                
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="functionDirectivo/resultadosOlimpiada/verExamenesProgresoDirectivoOlimpiada.php">
                        <div class="no-image">
                            <img src="../src/img/Examen_Prueba_Enlace-2.jpg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Ver Examenes en progreso: Olimpiada</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->
    
    
                <div> <!-- Contenedor card -->
                    <div class="card1">
                        <a href="#">
                        <div class="no-image">
                            <img src="../src/img/grupo.jpg" alt="Logo" style="width: 300px; position:absolute; padding:1rem;">
                            <svg
                            class="icon"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.1"
                                d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                            ></path>
                            </svg>
                        </div>
                        <div class="content">
                            <p class="name">Funcion pendiente</p>
                            <p class="time"></p>
                        </div>
                        </a>
                    </div>
                </div> <!-- Fin contenedor card -->          
    
                </div>
    
            </div><!--Fin de la segunda columna-->
    </div> <!--Fin grid mayor-->




</main>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("btnExamen1").addEventListener("click", function() {
        document.getElementById("examen1").style.display = "block";
        document.getElementById("examen2").style.display = "none";
    });

    document.getElementById("btnExamen2").addEventListener("click", function() {
        document.getElementById("examen1").style.display = "none";
        document.getElementById("examen2").style.display = "block";
    });
});
</script>
</body>
</html>
