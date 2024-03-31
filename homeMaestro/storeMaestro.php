<?php
    require_once("c://xampp/htdocs/Proyecto/controller/homeController.php");
    require_once("c://xampp/htdocs/Proyecto/homeDirectivo/codigo.php");

    $obj = new homeController();

    // Obtener los datos del fomrualario
    $nombre = $_POST['first_name'];
    $apellido = $_POST['last_name'];
    $codigoDirectivo =$_POST['codigoDirectivo'];
    $correo = $_POST['email'];
    $contraseña = $_POST['password'];
    $confirmarContraseña = $_POST['confirm_password'];
    $codigoProfesor = generarCodigo();
    $error = "";

    
    
    if(empty($nombre) || empty($apellido) || empty($correo) || empty($codigoDirectivo)|| empty($contraseña) || empty($confirmarContraseña)){
        $error .= "<li>Completa los campos</li>";
        $cadena ="Location:singupMaestro.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&email=".$correo."&&codigoDirectivo=".$codigoDirectivo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
        header($cadena);

    }else if($nombre && $apellido && $correo && $codigoDirectivo && $contraseña && $confirmarContraseña){

        if($contraseña == $confirmarContraseña){
            if($obj->guardarUsuarioMaestro($codigoProfesor, $codigoDirectivo, $nombre, $apellido, $correo, $contraseña) == false){
                $error .= "<li>El correo ya esta registrado</li>";
                $cadena ="Location:singupMaestro.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&email=".$correo."&&codigoDirectivo=".$codigoDirectivo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
                header($cadena);
     }else{
                header("Location:loginMaestro.php");
            }
        }else{
            $error .= "<li>Las contraseñas son diferentes</li>";
            $cadena ="Location:singupMaestro.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&email=".$correo."&&codigoDirectivo=".$codigoDirectivo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
                header($cadena);
        }
    }




 
?>