<?php
  
   session_start();
  


    require '../config/db.php';
   
    $db = new db();
    $con =$db->conexion();

    
    $correoDirectivo = $_SESSION['Maestro'];
    //Preparar la consulta sql
    $sqlHelp = $con->prepare("SELECT nombre, apellido, codigoProfesor, correo, escuela, codigoEscuela, urlImagen FROM profesor WHERE correo = :correoDirectivo");
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
    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="logoutMaestro.php" class="boton" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;">
        Cerrar sesión
      </a>
    </div>
  </div>
</nav>


<!-- Main Content -->
<main>
    <?php foreach($resultadoaux as $row){ ?>
    <div class="contenedor__informacion">
        <h1>Bienvenido <?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></h1>
    </div>

    
    <div class="grid contenedor"> <!--Incio grid 2 columnas-->

        <div>
        <a href="./functionMaestro/perfilMaestro.php">
            <div class="contenedorPerfil">
                <div class="card user-info">
                    <img src="<?php echo htmlspecialchars($row['urlImagen']); ?>" class="profile-img"  alt="Student profile" class="profile-img">
                    <p class="student-name"><?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></p>
                    <p  style="color: #687483" class="student-role">Maestro </p>
                    <div class="info-group">
                        <div class="info">
                            <p style="color: #687483" class="label">Codigo profesor</p>
                            <p class="value"><?php echo htmlspecialchars($row['codigoProfesor']); ?></p>
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
            
            <div class="grid2columnas">
            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="functionMaestro/administrarAlumnos.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </i>
                        <p>Administrar alumnos </p>  
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->

            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="functionMaestro/verExamenesRealizados.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>    
                        </i>
                        <p>Ver examenes Realizados</p>
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->
            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="functionMaestro/verExamenesProgreso.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon"></i>
                        <p>Ver Examenes en progreso</p>
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->
            
            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="functionMaestro/verProgresoGrupo.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon"></i>
                        <p>Ver progreso por grupo</p>
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->



            </div>
            </div>
        </div><!--Fin de la segunda columna-->
    </div> <!--Fin grid mayor-->




</main>

</body>
</html>
