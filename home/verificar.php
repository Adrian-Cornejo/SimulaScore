<?php
    require_once("c://xampp/htdocs/Proyecto/controller/homeController.php");

    session_start();
    $obj = new homeController();
    
 

    $correo = $obj->limpiarcorreo($_POST['email']);
    $contraseña = $obj->limpiarcadena($_POST['password']);
    $bandera = $obj->verificarusuario($correo,$contraseña);
   
    if($bandera){
        $_SESSION['usuario'] = $correo;
        header("Location:panel_control.php");
    }else{
        $error = "<li>Las claves son incorrectas</li>";
        header("Location:login.php?error=".$error);
    }
?>