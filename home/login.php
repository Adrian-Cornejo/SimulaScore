<?php
    session_start();
    if(!empty($_SESSION['usuario'])){
        header("Location:panel_control.php");
    }
    
    

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

    <!-- <link rel="stylesheet" href="../build/css/app.css"> -->
    <link rel="stylesheet" href="../build/css/estilosAlumnos.css">
</head>
<body style="margin:0;">
   
    

    <nav class="navegacion">
        
        <a class="navegacion__enlace "href="../index.php">Acerca de</a>
        <a class="navegacion__enlace"href="#">Como funciona</a>
        <a class="navegacion__enlace navegacion__enlace--activo"href="#">Ingresar</a>
        <a class="navegacion__enlace"href="#">Contacto</a>
        <a class="navegacion__enlace"href="#">Ayuda</a>
        
    </nav>

    <main>
        <div class="texto">
            <h1 >Perfil: Alumno</h1>
        </div>

        <div class="contenedor" >
            <div class="login-container">
                <img  src="../src/img/Logo350x350.png" alt="Logo de la Aplicación" class="logo" style="width:200px;">
                <h1 class="textoInicio">Inicio de Sesión</h1>

                
                
                <form action="verificar.php" method="Post">
                    <input type="email" placeholder="Correo" name="email" id="user" >
                   
                    <input type="password" placeholder="Contraseña" name="password" id="pass" >
                    <?php if(!empty($_GET['error'])):?>
                        <div id="alertError" style="margin: 1rem; color:#5a6770" >
                            <?= !empty($_GET['error']) ? $_GET['error'] : ""?>
                        </div>
                    <?php endif;?>
                    <button type="submit">Iniciar Sesión</button>
                    <div class="help-links">
                        <a href="#">Olvidé mi contraseña</a>
                        <a href="singup.php">Crear una cuenta</a>
                    </div>
                </form>
            </div>
        </div>
        
    </main>

    <footer>
        <footer class="footer" >
            <div class="contenedor">
                <div class="barra">
                    
                    <nav class="navegacion">
                        <a href="#" class="navegacion__enlace">Nosotros</a>
                        <a href="#" class="navegacion__enlace">Cursos</a>
                        <a href="#" class="navegacion__enlace">Contacto</a>
                    </nav>
                   
                </div>
            </div>
        </footer>

    
</body>
</html>