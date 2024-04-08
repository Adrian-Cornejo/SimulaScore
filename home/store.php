<?php
    require_once("../controller/homeController.php");


    require_once("../homeDirectivo/codigo.php");
    $obj = new homeController();

    // Obtener los datos del fomrualario
    $nombre = $_POST['first_name'];
    $apellido = $_POST['last_name'];
    $codigoProfesor =$_POST['codigoProfesor'];
    $correo = $_POST['email'];
    $contraseña = $_POST['password'];
    $confirmarContraseña = $_POST['confirm_password'];
    $codigoAlumno = generarCodigo();
    $error = "";

     

    if(empty($nombre) || empty($apellido) || empty($codigoProfesor) || empty($correo) || empty($contraseña) || empty($confirmarContraseña)){
        $error .= "<li>Completa los campos</li>";
        $cadena ="Location:singup.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&codigoProfesor=".$codigoProfesor."&&email=".$correo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
        header($cadena);

    }else if($nombre && $apellido && $codigoProfesor && $correo && $contraseña && $confirmarContraseña){

        if($contraseña == $confirmarContraseña){
            if($obj->guardarUsuario($codigoAlumno, $nombre, $apellido, $codigoProfesor, $correo, $contraseña) == false){
                $error .= "<li>El correo ya esta registrado</li>";
                $cadena ="Location:singup.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&codigoProfesor=".$codigoProfesor."&&email=".$correo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
                header($cadena);
     }else{
                header("Location:login.php");
            }
        }else{
            $error .= "<li>Las contraseñas son diferentes</li>";
            $cadena ="Location:singup.php?error=".$error."&&first_name=".$nombre."&&last_name=".$apellido."&&codigoProfesor=".$codigoProfesor."&&email=".$correo."&&password=".$contraseña."&&confirm_password=".$confirmarContraseña;
                header($cadena);
        }
    }




 
?>