<?php
    session_start();
    if (!isset($_SESSION['correo'])) {
      header('Location:../loginDirectivo.php'); // Redirigir al login si no está logueado
      exit;
  }
  
    require '../../../config/db.php';
   
    $db = new db();
    $con =$db->conexion();
   
    //Obtener el codigo del directivo en base a su correo
    $correoDirectivo = $_SESSION['correo'];
    $sqlHelp = $con->prepare("SELECT codigoDirectivo, nombre, apellido FROM directivo WHERE correo = :correoDirectivo");
    $sqlHelp->bindParam(':correoDirectivo', $correoDirectivo, PDO::PARAM_STR);
    $sqlHelp->execute();
    $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
    $codigoDirectivo = $resultadoaux[0]['codigoDirectivo'];
    $nombreDirectivo = $resultadoaux[0]['nombre'];
    $apellidoDirectivo   = $resultadoaux[0]['apellido'];
    
    
    $sql= $con->prepare("SELECT codigoProfesor, nombre ,apellido, correo FROM profesor where codigoDirectivo = :codigoDirectivo");
    $sql->bindParam(':codigoDirectivo', $codigoDirectivo, PDO::PARAM_STR);
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

    <link rel="stylesheet" href="../../../build/css/directivos.css">
    <style>
    .descripcion {
    margin: 20px 0;
    padding: 10px;
    background-color: #f0f0f0; /* Un fondo sutil para destacar el área de descripción */
    border-left: 5px solid #007bff; /* Una línea a la izquierda para llamar la atención */
    
    font-size: 16px; /* Un tamaño de fuente adecuado para la lectura */
    color: #333; /* Un color de texto que contraste bien con el fondo */
    border-radius: 5px; /* Bordes redondeados para suavizar la apariencia */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Una sombra sutil para añadir profundidad */
}

.descripcion p {
    margin: 0; /* Remover el margen por defecto de los párrafos para controlar el espaciado */
    line-height: 1.6; /* Un interlineado que mejora la legibilidad del texto */
    font-family: 'Arial', sans-serif; /* Asegúrate de usar una fuente legible */
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="padding: 1rem; background-color:#e2c4b6">
  <img src="../../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;"><b>Simula</b>Score</a>

      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../../panelControlDirectivo.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="../../panelControlDirectivo.php" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;" class="boton">Regresar</a>
  </div>
</nav>


<main>
   
  <!-- <h1>Bienvenido <?=$_SESSION['correo'] ?></h1> -->
  <h1>Bienvenido <?=$nombreDirectivo ?> <?=$apellidoDirectivo?></h1>
            
  <div class="descripcion">
    <p>Esta página ofrece una herramienta integral para que los docentes administren y visualicen el progreso académico de sus alumnos. A través de una interfaz clara y accesible, los educadores pueden consultar información detallada sobre el rendimiento de cada estudiante, incluyendo calificaciones por materia.</p>
</div>    

    <br>
    <h2>Docentes</h2>
    <?php if(isset($_SESSION['error'])): ?>
    <div id="error-message" style="color: red;  font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;text-align: center;"><?= $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); // Limpia el mensaje de error de la sesión ?>
<?php endif; ?>

<div id="confirmationModal" class="modal-container" style="display:none;">
    <div class="modal-content">
        <h2>Confirmación</h2>
        <p>¿Estás seguro de que quieres eliminar este profesor?</p>
        <button id="confirmBtn">Confirmar</button>
        <button id="cancelBtn" onclick="hideModal()">Cancelar</button>
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
    <td><?php echo htmlspecialchars($row['codigoProfesor']); ?></td>
    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
    <td><?php echo htmlspecialchars($row['correo']); ?></td>
    <td>

             <a href="verprogresoGrupoOlimpiada.php?codigoMaestro=<?=urlencode($row['codigoProfesor'])?>">
              <svg xmlns="http://www.w3.org/2000/svg" class=" icon icon-tabler icon-tabler-progress" width="34" height="34" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff9300" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" />
              <path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
              <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" />
              <path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
              <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" />
              </svg>
            </a>
      </td>

    <td>
    <!-- Enlace de eliminación con llamada directa a showModal() -->
      <a href="#" onclick="showModal('<?php echo htmlspecialchars($row['codigoProfesor']); ?>')">

    
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

<script src="../../../src/js/script.js"></script>




</body>
</html>