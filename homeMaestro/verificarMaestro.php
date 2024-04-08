<?php
    require_once("../controller/homeController.php");

    session_start();
    $obj = new homeController();
    
 

    $correo = $obj->limpiarcorreo($_POST['email']);
    $contraseña = $obj->limpiarcadena($_POST['password']);
    $bandera = $obj->verificarMaestro($correo,$contraseña);
  
    if($bandera){
        $_SESSION['Maestro'] = $correo;
        header("Location:panelControlMaestro.php");
    }else{
        $error = "<li>Las claves son incorrectas</li>";
        header("Location:loginMaestro.php?error=".$error);
    }
?>