<?php
session_start();
require '../../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location:../login.php'); // Redirigir al login si no está logueado
    exit;
}
$db = new db();
$con =$db->conexion();


$correoAlumno = $_SESSION['usuario'];
//Preparar la consulta sql
$sqlHelp = $con->prepare("SELECT nombre, apellido, codigoAlumno, escuela, codigoEscuela, urlImagen FROM alumno WHERE correo = :correoAlumno");
$sqlHelp->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
$sqlHelp->execute();
$alumno = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>

        <!-- Google Fonts and Bootstrap CSS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../build/css/estilosAlumnos.css"> <!-- Asegúrate de usar la ruta correcta a tu CSS -->
</head>
<body>
    
<nav class="navbar navbar-expand-lg" >
  <img src="../../src/img/Logo-removebg-preview.png" alt="Logo" style="width: 100px; position:absolute; padding:1rem;">
  <div class="container-fluid">
    <a class="navbar-brand" href="../panelControlMaestro.php" style="font-size:3rem; padding:0.5rem; margin-left: 15rem;">
      <b>Simula</b>Score
    </a>

      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../panel_control.php" style="font-size:1.8rem; padding:1rem;">Home</a>
        </li>
      </ul>
      <a href="../logout.php" class="boton" style="padding: 2rem; background-color:#687483; text-decoration: none; color: inherit;">
        Cerrar sesión
      </a>
    </div>
</nav>


<?php if(isset($_SESSION['error'])): ?>
    <div id="error-message" style="color: red;  font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;text-align: center;"><?= $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); // Limpia el mensaje de error de la sesión ?>
<?php endif; ?>


<div class="container" style="background-color:#33cec3;">
    <h2>Editar Perfil</h2>
    <?php foreach($alumno as $row){ ?>
    <form action="modificarInfoAlumno.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
              <label for="imagenPerfil" class="image-upload-label">
                  <div class="image-container">
                      <img src="<?php echo $alumno[0]['urlImagen']; ?>" alt="Perfil" class="profile-img">
                        <div class="overlay">
                            <div class="text">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon  icon-tabler-photo-plus" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                              <path d="M15 8h.01" />
                              <path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v6.5" />
                              <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" />
                              <path d="M14 14l1 -1c.67 -.644 1.45 -.824 2.182 -.54" />
                              <path d="M16 19h6" />
                              <path d="M19 16v6" />
                            </svg>
                            </div>
                        </div>
                  </div>
              </label>
            <input type="file" id="imagenPerfil" name="imagenPerfil" style="display: none;" onchange="document.getElementById('nombreImagen').value = this.files[0].name">
        </div>
        <div class="form-group" >
            <input type="text" id="nombreImagen" name="nombreImagen" placeholder="Nombre del archivo..." readonly  style="display:none">
        </div>
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $codigoMaestro = $alumno[0]['nombre'];?>" required>
        </div>
        <div class="form-group" >
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?php echo $codigoMaestro = $alumno[0]['apellido'];?>" required>
        </div>


        
        <div class="form-group">
            <label for="escuela">Escuela:</label>
            <input type="text" id="escuela" name="escuela" value="<?php echo $codigoMaestro = $alumno[0]['escuela'];?>" readonly>
        </div>
        
        <button class="boton" type="submit">Actualizar Perfil</button>
    </form>
</div>
<?php } ?>

<script src="../../src/js/script.js"></script>
</body>
</html>
