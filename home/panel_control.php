<?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location:../login.php'); // Redirigir al login si no está logueado
            exit;
        }
        require '../config/db.php';
    
        $db = new  db();
        $con =$db->conexion();
    
        $correoAlumno = $_SESSION['usuario'];
    
        $sqlHelp = $con->prepare("SELECT nombre, apellido, codigoProfesor, correo, escuela, codigoEscuela, urlImagen FROM alumno WHERE correo = :correoAlumno");
        $sqlHelp->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
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


    <link rel="stylesheet" href="../build/css/estilosAlumnos.css">

    <style>
   
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" >
  <img src="../src/img/header001.png" alt="Logo" style="width: 280px; position:absolute; margin-top:0; top:0.5px; margin-left:0px">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 30rem;">
      <b>Simula</b>Score
    </a>
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="logout.php" class="boton" style="padding: 2rem; background-color:#154c4b; text-decoration: none; color: #f1fcfb;">
        Cerrar sesión
      </a>
    </div>
</nav>


<!-- Main Content -->
<main>
    <?php foreach($resultadoaux as $row){ ?>
    <div class="contenedor__informacion" style="padding-top:2rem;">
        <h1>Bienvenido <?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></h1>
    </div>

    
    <div class="grid contenedor_Panel"> <!--Incio grid 2 columnas-->

        <div>
        <a href="./functionAlumno/perfilAlumno.php">
            <div class="contenedorPerfil">
                <div class="card user-info">
                    <img src="<?php echo htmlspecialchars($row['urlImagen']); ?>" class="profile-img"  alt="Student profile" class="profile-img">
                    <p class="student-name"><?php echo htmlspecialchars($row['nombre']); ?> <?php echo htmlspecialchars($row['apellido']); ?></p>
                    <p  style="color: #687483" class="student-role">Estudiante </p>
                    <div class="info-group">
                        <div class="info">
                            <p style="color: #687483" class="label">Codigo Profesor</p>
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
                <a href="../examenes/indicacionesExamen.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#782f79" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                             <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" />
                        </svg>
                        </i>
                        <p>Examen MejorEdu</p>  
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->

            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="../examenOlimpiada/idicacionesExamenOlimpiada.php">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#782f79" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>    
                        </i>
                        <p>Examen Olimpiada del conocimiento</p>
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->
            <div> <!--Contenedor card-->

            

            
            <div class="card"> <!--Incio card-->
                <a href="#">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon"></i>
                        <p>Funcion pendiete</p>
                    </div>
                        <p class="card-content"></p>
                </div>
                </a>
            </div> <!--Fin card-->
            </div> <!--Fin contenedor card-->
            
            <div> <!--Contenedor card-->
            <div class="card"> <!--Incio card-->
                <a href="#">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-open icon"></i>
                        <p>Funcion pendiete</p>
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

    <div class="seccion--imagen">

    </div>



<div class="buho">
    <div class="bar">
        
    <img src="../src/img/buho1.png" alt="Img" style="width: 185px; margin: auto; ">
    <!-- <div class="ball">

    </div> -->
    </div>
</div>

</main>

</body>
</html>
