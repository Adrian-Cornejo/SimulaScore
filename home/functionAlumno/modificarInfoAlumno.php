<?php
session_start();
require '../../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location:../login.php'); // Redirigir al login si no está logueado
    exit;
}
$errorFlag = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new db();
    $con = $db->conexion();

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $codigoEscuela = $_POST['codigoEscuela'];
    $escuela = $_POST['escuela'];
     $correoAlumno= $_SESSION['usuario'];

    

    // Inicia la transacción
   // $con->beginTransaction();
    try {
                $sqlHelp = $con->prepare("SELECT codigoProfesor  FROM alumno WHERE correo = :correoAlumno");
                $sqlHelp->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
                $sqlHelp->execute();
                $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
                $codigoProfesor = $resultadoaux[0]['codigoProfesor'];

                $sqlUpdate = $con->prepare("SELECT escuela, codigoEscuela  FROM profesor WHERE codigoProfesor = :codigoProfesor");
                $sqlUpdate->bindParam(':codigoProfesor', $codigoProfesor, PDO::PARAM_STR);
                $sqlUpdate->execute();
                $datosProf = $sqlUpdate->fetchAll(PDO::FETCH_ASSOC);

                $escuela = $datosProf[0]['escuela'];
                $codigoEscuela = $datosProf[0]['codigoEscuela'];


             


        // Actualiza los datos del directivo
        $sqlUpdate = "UPDATE alumno SET nombre = :nombre, apellido = :apellido, codigoEscuela = :codigoEscuela, escuela = :escuela WHERE correo = :correoAlumno";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':codigoEscuela', $codigoEscuela, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':escuela', $escuela, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
        $stmtUpdate->execute();

      

        if (!empty($_FILES["imagenPerfil"]["name"])) {
        // Carga de la imagen   
     //   $directorio = "uploads/";
        $archivo = $directorio . basename($_FILES["imagenPerfil"]["name"]);

        $directorioRaiz = $_SERVER['DOCUMENT_ROOT'];
        $directorioDestino = $directorioRaiz . '/Proyecto/src/img/imgPerfilesAlumnos/';
        echo $archivo = $directorioDestino . basename($_FILES["imagenPerfil"]["name"]);
        $tipoArchivo = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $checarSiImagen = getimagesize($_FILES["imagenPerfil"]["tmp_name"]);

        
        if($checarSiImagen != false){
            $size = $_FILES["imagenPerfil"]["size"];
            if($size > 500000000    ){
                echo "El archivo tiene que ser menor a 500kb";
            }else{
                //validar tipo de imagen
                if($tipoArchivo == "jpg" || $tipoArchivo == "jpeg" || $tipoArchivo == "png"){
                    // se validó el archivo correctamente
                    if(move_uploaded_file($_FILES["imagenPerfil"]["tmp_name"], $archivo)){
                        echo "El archivo se subió correctamente";
                        // Imprime la ruta donde se guardó el archivo
                        echo "Ruta del archivo: " . $archivo;


                        $rutaRelativa = '/Proyecto/src/img/imgPerfiles/' . basename($_FILES["imagenPerfil"]["name"]); // Ruta relativa para guardar en DB
                        $sqlImage = "UPDATE alumno SET urlImagen = :urlImagen WHERE correo = :correoAlumno";
                        $stmtImage = $con->prepare($sqlImage);
                        $stmtImage->bindParam(':urlImagen', $rutaRelativa, PDO::PARAM_STR);
                        $stmtImage->bindParam(':correoAlumno', $correoAlumno, PDO::PARAM_STR);
                        $stmtImage->execute();

                        
                    }else{
                        $_SESSION['error'] = "Hubo un error en la subida del archivo";
                        $errorFlag = true; // Indicar que ocurrió un error
                       
                    }
                }else{
                    $_SESSION['error'] = "Solo se admiten archivos jpg/jpeg";
                    $errorFlag = true; // Indicar que ocurrió un error
                    
                }
            }
        }else{
            $_SESSION['error'] = "El documento no es una imagen";
            $errorFlag = true; // Indicar que ocurrió un error
            
        }
        
    }





    } catch (PDOException $e) {
        // Si ocurre un error, revierte la transacción
        $con->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
    if ($errorFlag) {
        // Ocurrió un error, redirige de vuelta a perfilDirectivo.php para mostrar el error
        header('Location: perfilAlumno.php');
    } else {
        // Todo bien, redirige al panel de control
        header('Location: ../panel_control.php');
    }
    exit;
}
?>