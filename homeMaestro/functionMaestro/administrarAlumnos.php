<?php
    session_start();
    if (!isset($_SESSION['Maestro'])) {
      header('Location:../loginMaestro.php'); // Redirigir al login si no está logueado
      exit;
  }
  
    require '../../config/db.php';
   
    $db = new db();
    $con =$db->conexion();
   
    //Obtener el codigo del maestro en base a su correo
    $correoMaestro = $_SESSION['Maestro'];

    $sqlHelp = $con->prepare("SELECT codigoProfesor, nombre, apellido FROM profesor WHERE correo = :correoMaestro");
    $sqlHelp->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
    $sqlHelp->execute();
    $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
    $codigoMaestro = $resultadoaux[0]['codigoProfesor'];
    $nombreMaestro = $resultadoaux[0]['nombre'];
    $apellidoMaestro   = $resultadoaux[0]['apellido'];
    
    
    $sql= $con->prepare("SELECT codigoAlumno, nombre ,apellido, correo FROM alumno where codigoProfesor = :codigoMaestro");
    $sql->bindParam(':codigoMaestro', $codigoMaestro, PDO::PARAM_STR);
    $sql->execute();
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../../build/css/directivos.css">
    <link rel="stylesheet" href="../../build/css/animaciones.css">

    
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
      <a href="../panelControlMaestro.php" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
    
      
    </div>
  </div>
</nav>


<main>
   
  <!-- <h1>Bienvenido <?=$_SESSION['Maestro'] ?></h1> -->
  <h1>Bienvenido <?=$nombreMaestro ?> <?=$apellidoMaestro?></h1>
            
            

    <br>
    <h2>Administrar alumnos</h2>
    <?php if(isset($_SESSION['error'])): ?>
    <div id="error-message" style="color: red;  font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;text-align: center;"><?= $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); // Limpia el mensaje de error de la sesión ?>
<?php endif; ?>

<div id="confirmationModal" class="modal-container" style="display:none;">
    <div class="modal-content">
        <h2>Confirmación</h2>
        <p>¿Estás seguro de que quieres eliminar este Alumno?</p>
        <button id="confirmBtn">Confirmar</button>
        <button id="cancelBtn" onclick="hideModal()">Cancelar</butt on>
    </div>
</div>


  <table class="tabla-docentes">
  <tr>
    <th>Código</th>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>Correo</th>
    <th>Progreso</th>
    <th>Acciones</th>
  </tr>
    <?php foreach($resultado as $row){ ?>
  <tr>
    <td><?php echo htmlspecialchars($row['codigoAlumno']); ?></td>
    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
    <td><?php echo htmlspecialchars($row['correo']); ?></td>
    <td>
            <a href="verProgresoAlumno.php?codigoAlumno=<?=urlencode($row['codigoAlumno'])?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="200px" width="200px" viewBox="0 0 200 200" class="pencil">
	<defs>
		<clipPath id="pencil-eraser">
			<rect height="30" width="30" ry="5" rx="5"></rect>
		</clipPath>
	</defs>
	<circle transform="rotate(-113,100,100)" stroke-linecap="round" stroke-dashoffset="439.82" stroke-dasharray="439.82 439.82" stroke-width="2" stroke="currentColor" fill="none" r="70" class="pencil__stroke"></circle>
	<g transform="translate(100,100)" class="pencil__rotate">
		<g fill="none">
			<circle transform="rotate(-90)" stroke-dashoffset="402" stroke-dasharray="402.12 402.12" stroke-width="30" stroke="hsl(223,90%,50%)" r="64" class="pencil__body1"></circle>
			<circle transform="rotate(-90)" stroke-dashoffset="465" stroke-dasharray="464.96 464.96" stroke-width="10" stroke="hsl(223,90%,60%)" r="74" class="pencil__body2"></circle>
			<circle transform="rotate(-90)" stroke-dashoffset="339" stroke-dasharray="339.29 339.29" stroke-width="10" stroke="hsl(223,90%,40%)" r="54" class="pencil__body3"></circle>
		</g>
		<g transform="rotate(-90) translate(49,0)" class="pencil__eraser">
			<g class="pencil__eraser-skew">
				<rect height="30" width="30" ry="5" rx="5" fill="hsl(223,90%,70%)"></rect>
				<rect clip-path="url(#pencil-eraser)" height="30" width="5" fill="hsl(223,90%,60%)"></rect>
				<rect height="20" width="30" fill="hsl(223,10%,90%)"></rect>
				<rect height="20" width="15" fill="hsl(223,10%,70%)"></rect>
				<rect height="20" width="5" fill="hsl(223,10%,80%)"></rect>
				<rect height="2" width="30" y="6" fill="hsla(223,10%,10%,0.2)"></rect>
				<rect height="2" width="30" y="13" fill="hsla(223,10%,10%,0.2)"></rect>
			</g>
		</g>
		<g transform="rotate(-90) translate(49,-30)" class="pencil__point">
			<polygon points="15 0,30 30,0 30" fill="hsl(33,90%,70%)"></polygon>
			<polygon points="15 0,6 30,0 30" fill="hsl(33,90%,50%)"></polygon>
			<polygon points="15 0,20 10,10 10" fill="hsl(223,10%,10%)"></polygon>
		</g>
	</g>
</svg>
            </a>
      </td>

    <td>
    <!-- Enlace de eliminación con llamada directa a showModal() -->
      <a href="#" onclick="showModal('<?php echo htmlspecialchars($row['codigoAlumno']); ?>')">

    
        <svg xmlns="http://www.w3.org/2000/svg" class="icon " width="34" height="34" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff9300" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M4 7l16 0" />
        <path d="M10 11l0 6" />
        <path d="M14 11l0 6" />
        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
        </svg>

      </a>
    </td>
</tr>
  <?php } ?>
</table>



</main>

<script src="../../src/js/script.js"></script>




</body>
</html>