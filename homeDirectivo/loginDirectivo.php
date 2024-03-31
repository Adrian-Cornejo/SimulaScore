<?php
    session_start();
    if(!empty($_SESSION['correo'])){
        header("Location:panelControlDirectivo.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../build/css/app.css">
</head>
<body>
   
    <header class="header">
         

    </header>

    <nav class="navegacion">
        <div class="navegacion--columnas">

        </div>
        <a class="navegacion__enlace "href="../index.php">Acerca de</a>
        <a class="navegacion__enlace"href="#">Como funciona</a>
        <a class="navegacion__enlace navegacion__enlace--activo"href="#">Ingresar</a>
        <a class="navegacion__enlace"href="#">Contacto</a>
        <a class="navegacion__enlace"href="#">Ayuda</a>
        
    </nav>

    <main>
        <div class="texto">
            <h1>Perfil: Directivo</h1>
        </div>

        <div class="contenedor">
            <div class="login-container">
                <img src="../src/img/Logo-removebg-preview.png" alt="Logo de la Aplicación" class="logo">
                <h1>Inicio de Sesión</h1>
                <form class="formulario" action="verificarDirectivo.php" method="post">
                    <input type="text" placeholder="Usuario" name="email" >
                    <input type="password" placeholder="Contraseña" name="password" >
                    <button type="submit">Iniciar Sesión</button>
                    <div class="help-links">
                        <a href="#">Olvidé mi contraseña</a>
                        <a href="#">Crear una cuenta</a>
                    </div>
                </form>
            </div>
        </div>
        
    </main>
    <footer>
        <footer class="footer">
            <div class="contenedor">
                <div class="barra">
                    
                   
                    <nav class="navegacion">
                        <a href="#" class="navegacion__enlace">Nosotros</a>
                        <a href="#" class="navegacion__enlace">Cursos</a>
                        <a href="#" class="navegacion__enlace">Contacto</a>
                    </nav>
                    <p>2024 Todos los derechos reservados</p>
                </div>
            </div>
        </footer>



    
</body>
</html>