
<?php
    require_once("c://xampp/htdocs/Proyecto/controller/homeController.php");

    session_start();
    $obj = new homeController();
    
 

    $correo = $obj->limpiarcorreo($_POST['email']);
    $contraseña = $obj->limpiarcadena($_POST['password']);
    $bandera = $obj->verificarDirectivo($correo,$contraseña);

    if($bandera){
        $_SESSION['correo'] = $correo;
        header("Location:panelControlDirectivo.php");
    }else{
        $error = "<li>Las claves son incorrectas</li>";
        header("Location:loginDirectivo.php?error=".$error);
    }
?>