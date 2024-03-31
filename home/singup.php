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
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../build/css/app.css">
</head>
<body>
    <nav class="navegacion">
        <div class="navegacion--columnas">

        </div>
        <a class="navegacion__enlace "href="../index.php">Acerca de</a>
        <a class="navegacion__enlace"href="#">Como funciona</a>
        <a class="navegacion__enlace navegacion__enlace--activo "href="#">Ingresar</a>
        <a class="navegacion__enlace"href="#">Contacto</a>
        <a class="navegacion__enlace"href="#">Ayuda</a>
    </nav>

 
    <main>
        <div class="contenedor__crearCuenta">
            <div class="login-container">
                <img src="../src/img/Logo-removebg-preview.png" alt="Logo de la Aplicación" class="logo">
                <h1>Crear Cuenta</h1>


                <form class="formulario" action="store.php" method="post">
                    <div class="campo-doble">
                        <input type="text" id="Nombre" placeholder="Nombre(s)" name="first_name" value="<?= (!empty($_GET['first_name'])) ? $_GET['first_name'] : "" ?>" >
                        <input type="text" id="Apellido" placeholder="Apellido(s)" name="last_name" value="<?= (!empty($_GET['last_name'])) ? $_GET['last_name'] : "" ?>">
                    </div>
                    <input type="text" id="codigoProfesor" placeholder="Codigo profesor" name="codigoProfesor" value="<?= (!empty($_GET['codigoProfesor'])) ? $_GET['codigoProfesor'] : "" ?>">
                    <input type="email" id="Correo" placeholder="Correo electrónico" name="email" value="<?= (!empty($_GET['email'])) ? $_GET['email'] : "" ?>">
                    <div class="campo-doble">
                        <input type="password" id="password" placeholder="Contraseña" name="password" value="<?= (!empty($_GET['password'])) ? $_GET['password'] : "" ?>">
                        <input type="password" id="confirm_password" placeholder="Confirmar contraseña" name="confirm_password" value="<?= (!empty($_GET['confirm_password'])) ? $_GET['confirm_password'] : "" ?>">
                    </div>
                    <?php if(!empty($_GET['error'])):?>
                        <div id="alertError" style="margin: 1rem; color:#5a6770" >
                            <?= !empty($_GET['error']) ? $_GET['error'] : ""?>
                        </div>
                    <?php endif;?>
                    <button type="submit" >Registrarse</button>
   
                    <div class="help-links">
                        <a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>

                       
                    </div>
                </form>
            </div>
        </div>







    </main>

    <footer>
        <footer class="footer">
            <div class="contenedor">
                <div class="barra">
                    <a class="logo" href="index.html">
                        
                    </a>
                   
                    <nav class="navegacion">
                        <a href="#" class="navegacion__enlace">Nosotros</a>
                        <a href="#" class="navegacion__enlace">Cursos</a>
                        <a href="#" class="navegacion__enlace">Contacto</a>
                    </nav>
                    <p>2024 Todos los derechos reservados</p>
                </div>
            </div>
        </footer>
    
    <script src="../src/js/script.js"></script>
</body>
</html>